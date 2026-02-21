<?
/* ===========================
 * CONFIGURATION EMAIL
 * =========================== */

define('MAIL_FROM_EMAIL', 'noreply@maikastel.fr');
define('MAIL_FROM_NAME',  'Dev DD3.5');
define('MAIL_DEBUG',      true); // respecte $_SESSION['debug']

function mailTemplate($title, $contentHtml){
  return '
  <!doctype html>
  <html lang="fr">
  <head>
    <meta charset="utf-8">
    <title>'.$title.'</title>
  </head>
  <body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" style="padding:30px 0;">
          <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">
            <tr>
              <td style="background:#2c2c2c;color:#ffffff;padding:15px 20px;font-size:18px;">
                Dev DD3.5
              </td>
            </tr>
            <tr>
              <td style="padding:20px;color:#333333;font-size:14px;line-height:1.5;">
                '.$contentHtml.'
              </td>
            </tr>
            <tr>
              <td style="background:#eeeeee;padding:10px 20px;font-size:12px;color:#666666;">
                Message automatique � merci de ne pas r�pondre.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
  </html>';
}


function sendMail(
  $to,
  $subject,
  $htmlBody,
  $textBody = '',
  $fromEmail = MAIL_FROM_EMAIL,
  $fromName  = MAIL_FROM_NAME
)
{
  if (empty($to) || empty($subject) || empty($htmlBody)):
    return false;
  endif;

  if ($textBody == ''):
    $textBody = strip_tags($htmlBody);
  endif;

  $boundary = md5(uniqid(time(), true));

  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "From: ".$fromName." <".$fromEmail.">\r\n";
  $headers .= "Reply-To: ".$fromEmail."\r\n";
  $headers .= "Return-Path: ".$fromEmail."\r\n";
  $headers .= "Content-Type: multipart/alternative; boundary=\"".$boundary."\"\r\n";
  $headers .= "X-Mailer: PHP/".phpversion()."\r\n";

  $message  = "--".$boundary."\r\n";
  $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
  $message .= $textBody."\r\n\r\n";

  $message .= "--".$boundary."\r\n";
  $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
  $message .= $htmlBody."\r\n\r\n";

  $message .= "--".$boundary."--";

  $encodedSubject = '=?UTF-8?B?'.base64_encode($subject).'?=';

  /* MODE DEBUG : pas d�envoi r�el */
  if (
    defined('MAIL_DEBUG')
    && MAIL_DEBUG === true
    && isset($_SESSION['debug'], $_SESSION['mj'])
    && $_SESSION['debug'] == 1
    && $_SESSION['mj'] == 1
  ):
    debug(
      '<strong>EMAIL DEBUG</strong><br>'
      .'To: '.$to.'<br>'
      .'Subject: '.$subject.'<br><hr>'
      .$htmlBody
    );
    return true;
  endif;

  return mail($to, $encodedSubject, $message, $headers);
}


function generateToken($length = 32){
  return bin2hex(random_bytes($length));
}



/**********************************************************************************
Autres fonctions
**********************************************************************************/

/* Affichage d'un texte pour du d�bugage */ 
function debug($texte){
  if ($_SESSION['mj']==1 && $_SESSION['debug']==1) echo '<div class="debug">'.$texte.'</div>';
}

// fonction utilis�e pour afficher les variables de sessions et les cookies
function dump_array_readable(array $data): string {
  if (empty($data)) {
      return "<em>(aucune donn�e)</em>";
  }
  $html = "<table border='1' cellpadding='4' cellspacing='0'>";
  $html .= "<tr><th>Cl�</th><th>Valeur</th></tr>";
  foreach ($data as $key => $value) {
      if (is_array($value) || is_object($value)) {
          $val = '<pre>' . htmlspecialchars(print_r($value, true)) . '</pre>';
      } else {
          $val = htmlspecialchars((string)$value);
      }
      $html .= '<tr>';
      $html .= '<td>' . htmlspecialchars((string)$key) . '</td>';
      $html .= '<td>' . $val . '</td>';
      $html .= '</tr>';
  }
  $html .= '</table>';
  return $html;
}

// normalisation du texte avant sa comparaison avec une variable (utilisé notamment dans trt-inseretion-monstre)
function normaliser_chaine($str){
  $str = trim($str);
  $str = mb_strtolower($str, 'UTF-8');

  // Suppression des accents
  $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

  // Nettoyage résiduel
  $str = preg_replace('/[^a-z0-9 ]/', '', $str);

  return $str;
}

?>

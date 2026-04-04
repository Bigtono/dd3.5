<?
include("include/session.php");
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// S'assure que la table de récupération existe pour éviter l'erreur 1146
function ensureRecuperationTable()
{
    $sql = "CREATE TABLE IF NOT EXISTS recuperation (
      id INT AUTO_INCREMENT PRIMARY KEY,
      mail VARCHAR(255) NOT NULL,
      code VARCHAR(64) NOT NULL,
      confirme TINYINT(1) NOT NULL DEFAULT 0,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_mail (mail)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    execPDO($sql);
}
ensureRecuperationTable();

	// traitement de l'envoi de l'email
	if (isset($_POST['recup_submit'],$_POST['recup_mail'])): 
		if (!empty($_POST['recup_mail'])):
	  	$recup_mail = htmlspecialchars($_POST['recup_mail']);
	    if (filter_var($recup_mail,FILTER_VALIDATE_EMAIL)):
				$requete="SELECT j_id,j_pseudo FROM dd_joueurs WHERE j_email = :mail";
        $stmt = $db->prepare($requete);
        $stmt->execute([':mail' => $recup_mail]);
				$mailexist_count=$stmt->rowCount();
	    	if ($mailexist_count == 1): 
					$dn = $stmt->fetch(PDO::FETCH_ASSOC);
	      	$pseudo = $dn['j_pseudo'];
	        $_SESSION['recup_mail'] = $recup_mail;
	        $recup_code = "";
					for ($i=0; $i < 8; $i++): 
						$recup_code .= mt_rand(0,9);
					endfor;
				
					$requete="SELECT id FROM recuperation WHERE mail ='".$recup_mail."'";
					$result=queryPDO($requete);
					$mail_recup_exist=$result->rowCount();

					if ($mail_recup_exist == 1): 
						$requete = "UPDATE recuperation SET code ='".$recup_code."' WHERE mail ='".$recup_mail."'";
	          $resultat = execPDO($requete);
	          else:
	        	$requete = "INSERT INTO recuperation (mail,code) VALUES ('".$recup_mail."','".$recup_code."')";
	          $resultat = execPDO($requete);
					endif;
          $titre="Nouveau de mot de passe - Biblioth�que de Tono";
	        $header="MIME-Version: 1.0\r\n";
	        $header.='From:"[VOUS]"<votremail@mail.com>'."\n";
	        $header.='Content-Type:text/html; charset="utf-8"'."\n";
	        $header.='Content-Transfer-Encoding: 8bit';
	        $message = '
	         <html>
	         <head>
	           <title>'.$titre.' - Biblioth&egrave;que de Tono</title>
	           <meta charset="utf-8" />
	         </head>
	         <body>
            <div id="page">
	           <font color="#303030";>
	             <div align="center">
	               <table width="600px">
	                 <tr>
	                   <td>
	                     <div align="center">Bonjour <b>'.$pseudo.'</b>,<br>
	                     Voici votre code de r&eacute;cup&eacute;ration: <b>'.$recup_code.'</b><br>
	                     A bient&ocirc;t sur le site <a href="http://'.$_SESSION['url_site'].'"><b>'.$_SESSION['titre_site'].'</b></a> !</div>
	                   </td>
	                 </tr>
	                 <tr>
	                   <td align="center">
	                     <font size="2">
	                       Ceci est un email automatique, merci de ne pas y r&eacute;pondre.
	                     </font>
	                   </td>
	                 </tr>
	               </table>
	             </div>
	           </font>
	         </div><!-- page --->
          </body>
	         </html>';
					mail($recup_mail, utf8_encode($titre), $message, $header);
					header("Location:newpwd.php?section=code");
					exit();
	        else:

					$error = "Cette adresse mail n'est pas enregistr&eacute;e";
				endif;
	      else:
	         $error = "Adresse mail invalide";
	      endif;
	   else:
	      $error = "Veuillez entrer votre adresse mail";
	   endif;
	endif;

	if (isset($_POST['verif_submit'],$_POST['verif_code'])):
		if (!empty($_POST['verif_code'])):
			$verif_code = htmlspecialchars($_POST['verif_code']);
			$requete="SELECT id, mail FROM recuperation WHERE mail ='".$_SESSION['recup_mail']."' AND code ='".$verif_code."'";
			$result=queryPDO($requete);
			$verif_req=$result->rowCount();
			if ($verif_req == 1):
				$controle_etape.="<br>verif_req=1";
       	$requete = "UPDATE recuperation SET confirme = 1 WHERE mail='".$_SESSION['recup_mail']."'";
        $up_req=execPDO($requete);
				header('Location:newpwd.php?section=changemdp');
				exit();
	      else:
				$error = "Code invalide";
			endif;	
	   	else:
	      $error = "Veuillez entrer votre code de confirmation";
		endif;
	endif;

	if (isset($_POST['change_submit'])):
		if (isset($_POST['change_mdp'],$_POST['change_mdpc'])):
      if (!empty($_SESSION['recup_mail'])):
        $requete="SELECT confirme FROM recuperation WHERE mail = :mail";
        $stmt = $db->prepare($requete);
        $stmt->execute([':mail' => $_SESSION['recup_mail']]);
        $dn=$stmt->fetch(PDO::FETCH_ASSOC);
        $verif_confirme = $dn ? (int)$dn['confirme'] : 0;
        if ($verif_confirme === 1):
          $mdp = htmlspecialchars($_POST['change_mdp']);
          $mdpc = htmlspecialchars($_POST['change_mdpc']);
          if (!empty($mdp) AND !empty($mdpc)):
            if ($mdp == $mdpc): 
              $hash = password_hash($mdp, PASSWORD_DEFAULT);
              // Mise à jour du mot de passe sur la table utilisée par la connexion
              $requete="UPDATE dd_joueurs SET j_password_hash = :hash WHERE j_email = :mail";
              $stmt = $db->prepare($requete);
              $stmt->execute([':hash' => $hash, ':mail' => $_SESSION['recup_mail']]);
              // On supprime le ticket de récupération et on nettoie la session
              $stmt = $db->prepare("DELETE FROM recuperation WHERE mail = :mail");
              $stmt->execute([':mail' => $_SESSION['recup_mail']]);
              unset($_SESSION['recup_mail']);
              header('Location:login.php?change=ok');
              exit();
              else:
              $error = "Vos mots de passes ne correspondent pas";
            endif;
            else:
            $error = "Veuillez remplir tous les champs";
          endif;
          else:
          $error = "Veuillez valider votre mail avec le code de v&eacute;rification qui vous a &eacute;t&eacute; envoy&eacute; par mail ";
        endif;
        else:
        $error = "Votre session de r&eacute;cup&eacute;ration a expir&eacute;.";
      endif;
			else:
			$error = "Veuillez remplir tous les champs";
		endif;
	endif;
?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
</HEAD>

<body>
<div id="page">
  <? include("include/header.php"); ?>
  <div class="wrapper">
		<div id="connexion" class="login">
			<h1 class="title-element">R&eacute;cup&eacute;ration de mot de passe</h1>
			<?php
			$section=$_GET['section'];
			if($section == 'code') { ?>
			<form method="post">
				<div>Un code de v&eacute;rification vous a &eacute;t&eacute; envoy&eacute; par mail: <?= $_SESSION['recup_mail'] ?></div>
				<div class="mt10"><input type="text" placeholder="Code de v&eacute;rification" name="verif_code"/></div>
				<div class="mt10"><input type="submit" value="Valider" name="verif_submit"/></div>
			</form>
			<?php } elseif($section == "changemdp") { ?>
      
			<form method="post">
				<div>Nouveau mot de passe pour <?= $_SESSION['recup_mail'] ?></div>
				<div><input type="password" placeholder="Nouveau mot de passe" name="change_mdp"/><div/>
				<div class="mt10"><input type="password" placeholder="Confirmation du mot de passe" name="change_mdpc"/></div>
        <div class="mt10"><input type="submit" value="Valider" name="change_submit"/></div>
			</form>
			<?php } else { ?>
			<form method="post">
				<div>Veuillez saisir l'adresse email utilis&eacute;e pour la cr&eacute;ation de votre compte</div>
				<div class="mt10"><input type="email" placeholder="Votre adresse mail" name="recup_mail" size="43"/><div>
				<div class="mt10"><input type="submit" value="Valider" name="recup_submit"/></div>
			</form>
			<?php } ?>
			<p><?php if(isset($error)) { echo '<span style="color:red">'.$error.'</span>'; } else { echo ""; } ?></p>
			<p><?php if(isset($error)) { echo '<span style="color:blue">'.$controle_etape.'</span>'; } else { echo ""; } ?></p>
		</div> <!-- connexion --->	
  </div>
</div><!-- page --->
</body>

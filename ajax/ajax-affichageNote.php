<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$n = isset($_POST['note']) ? (int)$_POST['note'] : 0;
$a = isset($_POST['accreditation']) ? (int)$_POST['accreditation'] : 0;
$p = isset($_POST['perso']) ? (int)$_POST['perso'] : 0;
if ($p <= 0 && !empty($_SESSION['perso'])) $p = (int)$_SESSION['perso'];

if ($n <= 0):
  echo "0@Erreur affichage Note";
  exit;
endif;

$stmtNote = $db->prepare("SELECT * FROM dd_notes WHERE no_id=:id LIMIT 1");
$stmtNote->execute([':id' => $n]);
$dn = $stmtNote->fetch(PDO::FETCH_ASSOC);
if (!$dn):
  echo "0@Erreur affichage Note";
  exit;
endif;

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;
$isAuteur = $userId > 0 && (int)$dn['no_j_id'] === $userId;

$ddMax = ($a > 0) ? $a : 0;
if ($ddMax <= 0 && $p > 0):
  $stmtPno = $db->prepare("
    SELECT pno_dd
    FROM dd_personnages_notes
    WHERE pno_no_id = :no_id
      AND pno_pe_id = :pe_id
      AND (pno_actif = 1 OR pno_actif IS NULL)
    LIMIT 1
  ");
  $stmtPno->execute([
    ':no_id' => $n,
    ':pe_id' => $p,
  ]);
  $dnpno = $stmtPno->fetch(PDO::FETCH_ASSOC);
  if ($dnpno) $ddMax = (int)$dnpno['pno_dd'];
endif;

if ($ddMax <= 0 && ($isAuteur || $isMj)) $ddMax = 999;

if ($ddMax <= 0):
  echo $n . "@<div id=\"note\" class=\"affichage\"><div class=\"nom_objet\">" . htmlspecialchars(stripslashes($dn['no_nom']), ENT_QUOTES, 'UTF-8') . "</div><div class=\"description\"><div class=\"nodata\">Aucun contenu visible pour ce personnage.</div></div></div>";
  exit;
endif;

$sqlContenu = "
  SELECT noc_id, noc_dd, noc_texte
  FROM dd_notes_contenus
  WHERE noc_no_id = :no_id
";
if ($ddMax < 999) $sqlContenu .= " AND noc_dd <= :dd_max";
$sqlContenu .= " ORDER BY noc_dd ASC, noc_id ASC";

$stmtContenu = $db->prepare($sqlContenu);
$stmtContenu->bindValue(':no_id', $n, PDO::PARAM_INT);
if ($ddMax < 999) $stmtContenu->bindValue(':dd_max', $ddMax, PDO::PARAM_INT);
$stmtContenu->execute();
$contenus = $stmtContenu->fetchAll(PDO::FETCH_ASSOC);

$htmlContenu = '';
foreach ($contenus as $bloc):
  $htmlContenu .= '<section class="mb20">';
  $htmlContenu .= '  <div class="texte">' . stripslashes((string)$bloc['noc_texte']) . '</div>';
  $htmlContenu .= '</section>';
endforeach;
if ($htmlContenu === '') $htmlContenu = '<div class="nodata">Aucun contenu disponible.</div>';

[$tocHtml, $htmlWithIds] = buildTocFromHtml($htmlContenu, ['h2']);
// Le premier lien "retour au sommaire" (juste sous le bloc sommaire) est redondant.
$htmlWithIds = preg_replace('/<a[^>]*class="back-to-toc"[^>]*>.*?<\/a>\s*/is', '', $htmlWithIds, 1);
// Si le sommaire est replie, on le deploie au clic sur "retour au sommaire".
$htmlWithIds = preg_replace(
  '/<a([^>]*class="back-to-toc"[^>]*)>/i',
  '<a$1 onclick="if($(\'#toc\').is(\':hidden\')){$(\'#toc\').show();}">',
  $htmlWithIds
);
$tocBloc = '';
if ($tocHtml !== ''):
  if (preg_match('/<details\b[^>]*>\s*<summary\b[^>]*>.*?<\/summary>(.*)<\/details>/is', $tocHtml, $matches)):
    $tocHtml = trim((string)$matches[1]);
  endif;
  $tocBloc .= '<div class="gras mr10 mt10 lien" onCLick="togglePlus(\'toc\')">Sommaire <span id="toggle-note-toc"><i class="fa-solid fa-bars"></i></span></div>';
  $tocBloc .= '<div id="toc" class="box-data accordion-content noDisplay">' . $tocHtml . '</div>';
endif;
$texte2 = $tocBloc . '<article class="article">' . $htmlWithIds . '</article>';

$boutonModif = '';
if ($isMj || $isAuteur) $boutonModif = '<div class="dr lien" onclick="modifierNote(' . (int)$dn['no_id'] . ')"><i class="fa fa-pencil"></i></div>';

$result = '<div id="note" class="affichage">';
$result .= '  <div class="menu2"><div class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div>' . $boutonModif . '</div>';
$result .= '  <div class="nom_objet">' . htmlspecialchars(stripslashes((string)$dn['no_nom']), ENT_QUOTES, 'UTF-8') . '</div>';
$result .= '  <div class="description">';
$result .= '    <div class="texte"><span class="label">Categorie : </span>' . libelle("dd_types_notes", "tyno", "nom", $dn['no_tyno_id']) . '</div>';
$result .= '    <div class="texte"><span class="label">Niveau visible : </span>' . ($ddMax >= 999 ? 'Tous' : (int)$ddMax) . '</div>';
$result .= '    <div class="texte">' . $texte2 . '</div>';
$result .= '  </div>';
$result .= '</div>';

echo $dn['no_id'] . "@" . $result;
?>

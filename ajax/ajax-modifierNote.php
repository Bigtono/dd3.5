<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/diverslib.inc.php");
include_once("../include/noteslib.inc.php");

function optionListDdLocal($selected, $max = 99)
{
  $selected = (int)$selected;
  $options = '';
  for ($i = 1; $i <= $max; $i++):
    $sel = ($i === $selected) ? ' selected="selected"' : '';
    $options .= '<option value="' . $i . '"' . $sel . '>DD ' . $i . '</option>';
  endfor;
  return $options;
}

function renderContenuRow($idx, $dd, $texte)
{
  $idx = (int)$idx;
  $dd = (int)$dd;
  if ($dd <= 0) $dd = 10;

  $html = '';
  $html .= '<div class="note-contenu-row line-data-fr100 mb10" data-row="' . $idx . '">';
  $html .= '  <div class="ligne mb5">';
  $html .= '    <div class="label w90">DD</div>';
  $html .= '    <select class="note-contenu-dd">' . optionListDdLocal($dd) . '</select>';
  $html .= '    <button type="button" class="bouton ml15" onclick="NoteActions.removeContenuRow(this)">Supprimer</button>';
  $html .= '  </div>';
  $html .= '  <textarea id="mp_noc_' . $idx . '" class="input_texte note-contenu-texte">' . stripslashes((string)$texte) . '</textarea>';
  $html .= '  <script>if(window.CKEDITOR){ CKEDITOR.replace("mp_noc_' . $idx . '"); }</script>';
  $html .= '</div>';
  return $html;
}

$n = isset($_POST['note']) ? (string)$_POST['note'] : '';
$p = isset($_POST['perso']) ? (int)$_POST['perso'] : 0;
$tagTablesReady = notes_table_exists('dd_tags') && notes_table_exists('dd_notes_tags');

if ($n === ''):
  echo "0@Erreur";
  exit;
endif;

$no_nom = '';
$no_categorie = 0;
$no_id = 'n';
$contenusRows = [];

if ($n !== 'n'):
  $stmt = $db->prepare('SELECT no_id, no_nom, no_tyno_id FROM dd_notes WHERE no_id=:id LIMIT 1');
  $stmt->execute([':id' => (int)$n]);
  $dn = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$dn):
    echo "0@Erreur";
    exit;
  endif;

  $no_id = (int)$dn['no_id'];
  $no_nom = (string)$dn['no_nom'];
  $no_categorie = (int)$dn['no_tyno_id'];

  $stmtContenu = $db->prepare('SELECT noc_dd, noc_texte FROM dd_notes_contenus WHERE noc_no_id=:id ORDER BY noc_dd ASC, noc_id ASC');
  $stmtContenu->execute([':id' => (int)$n]);
  while ($bloc = $stmtContenu->fetch(PDO::FETCH_ASSOC)):
    $contenusRows[] = [
      'dd' => (int)$bloc['noc_dd'],
      'texte' => (string)$bloc['noc_texte'],
    ];
  endwhile;
endif;

if (empty($contenusRows)):
  $contenusRows[] = ['dd' => 10, 'texte' => ''];
endif;

if ($n === 'n'):
  $libelle = 'Ajouter';
  $titre = 'Ajouter une nouvelle note';
else:
  $libelle = 'Modifier';
  $titre = 'Modifier la note';
endif;

$perso = '';
if (!empty($_SESSION['campagne']) && (int)$_SESSION['campagne'] > 0):
  $stmtPerso = $db->prepare('SELECT pe_id, pe_nom FROM dd_personnages WHERE pe_camp_id=:camp ORDER BY pe_nom');
  $stmtPerso->execute([':camp' => (int)$_SESSION['campagne']]);
  while ($dnpe = $stmtPerso->fetch(PDO::FETCH_ASSOC)):
    $valdif = 0;
    if ($n !== 'n'):
      $stmtPno = $db->prepare('SELECT pno_dd FROM dd_personnages_notes WHERE pno_no_id=:no_id AND pno_pe_id=:pe_id LIMIT 1');
      $stmtPno->execute([
        ':no_id' => (int)$n,
        ':pe_id' => (int)$dnpe['pe_id'],
      ]);
      $dnpno = $stmtPno->fetch(PDO::FETCH_ASSOC);
      if ($dnpno) $valdif = (int)$dnpno['pno_dd'];
    endif;

    $perso .= '<div class="line-data-fr100"><label for="pe' . (int)$dnpe['pe_id'] . '" class="gras mr10">' . htmlspecialchars($dnpe['pe_nom'], ENT_QUOTES, 'UTF-8') . '</label><select id="pe' . (int)$dnpe['pe_id'] . '" name="pe' . (int)$dnpe['pe_id'] . '" class="diffusion">' . optionListDdLocal($valdif, 35) . '</select></div>';
  endwhile;
endif;

$categorie = '<select id="mp_no_tyno_id">' . optionList("dd_types_notes", "tyno", "nom", $no_categorie) . '</select>';
$tagsHtml = '<div class="nodata">La gestion des tags sera active apres creation des tables dd_tags et dd_notes_tags.</div>';

if ($tagTablesReady):
  $selectedTagIds = [];
  if ($n !== 'n'):
    $stmtSelectedTags = $db->prepare('SELECT notag_tag_id FROM dd_notes_tags WHERE notag_no_id=:note_id');
    $stmtSelectedTags->execute([':note_id' => (int)$n]);
    while ($rowTag = $stmtSelectedTags->fetch(PDO::FETCH_ASSOC)):
      $selectedTagIds[(int)$rowTag['notag_tag_id']] = true;
    endwhile;
  endif;

  $stmtTags = $db->prepare('SELECT tag_id, tag_nom FROM dd_tags WHERE tag_j_id=:user_id ORDER BY tag_nom ASC');
  $stmtTags->execute([':user_id' => (int)$_SESSION['user_id']]);
  $allTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

  $tagsHtml = '';
  if (!empty($allTags)):
    foreach ($allTags as $tag):
      $tagId = (int)$tag['tag_id'];
      $checked = isset($selectedTagIds[$tagId]) ? ' checked="checked"' : '';
      $tagsHtml .= '<label class="mr10"><input type="checkbox" class="note-tag-checkbox" value="' . $tagId . '"' . $checked . '> ' . htmlspecialchars((string)$tag['tag_nom'], ENT_QUOTES, 'UTF-8') . '</label>';
    endforeach;
  else:
    $tagsHtml .= '<div class="nodata">Aucun tag existant pour cet utilisateur.</div>';
  endif;
endif;

$rowsHtml = '';
$idx = 0;
foreach ($contenusRows as $row):
  $idx++;
  $rowsHtml .= renderContenuRow($idx, $row['dd'], $row['texte']);
endforeach;

$result = '<div id="note" class="affichage">';
$result .= '  <div class="nom_objet">' . $titre . '</div>';
$result .= '  <input type="hidden" id="mp_no_id" value="' . htmlspecialchars((string)$no_id, ENT_QUOTES, 'UTF-8') . '">';
$result .= '  <div><input id="mp_no_nom" class="input_nom" value="' . htmlspecialchars(stripslashes($no_nom), ENT_QUOTES, 'UTF-8') . '"></div>';
$result .= '  <div class="ligne mt10"><div class="label w90">Categorie</div>' . $categorie . '</div>';

$result .= '  <div class="gras mr10" onCLick="togglePlus(\'diffusion\')">Diffusion <span id="toggle-diffusion"><i class="fa-solid fa-bars"></i></span></div>';
$result .= '  <div id="diffusion" class="box-data accordion-content noDisplay">' . $perso . '</div>';
$result .= '  <div class="gras mr10 mt10" onCLick="togglePlus(\'tags\')">Tags <span id="toggle-tags"><i class="fa-solid fa-bars"></i></span></div>';
$result .= '  <div id="tags" class="box-data accordion-content noDisplay">' . $tagsHtml . '<div class="mt10"><input id="mp_new_tags" class="input_nom" placeholder="Nouveaux tags (separes par virgule)"></div></div>';

$result .= '  <div class="gras mr10 mt10">Contenus</div>';
$result .= '  <div id="note-contenus-list">' . $rowsHtml . '</div>';
$result .= '  <button type="button" class="bouton mt10" onclick="NoteActions.addContenuRow()">Ajouter un contenu</button>';
$result .= '  <input class="bouton ml15" type="button" name="validModifNote" id="validModifNote" value="' . $libelle . '" onClick="validerModifNote(' . (int)$p . ')">';
$result .= '  <input class="bouton ml15" type="button" name="annuleModifNote" id="annuleModifNote" value="Annuler" onClick="annulerPageModif()"></div>';
$result .= '</div>';

echo (string)$no_id . '@' . $result;
?>

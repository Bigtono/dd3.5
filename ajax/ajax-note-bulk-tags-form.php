<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/noteslib.inc.php");

$idsRaw = isset($_POST['note_ids']) ? (string)$_POST['note_ids'] : '';
$noteIds = [];
foreach (explode(',', $idsRaw) as $part):
  $id = (int)trim((string)$part);
  if ($id > 0) $noteIds[$id] = $id;
endforeach;
$noteIds = array_values($noteIds);

if (empty($noteIds)):
  echo "0@Aucune note selectionnee.";
  exit;
endif;

if (!notes_table_exists('dd_tags') || !notes_table_exists('dd_notes_tags')):
  echo "0@La gestion des tags n'est pas disponible.";
  exit;
endif;

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$stmtTags = $db->prepare('SELECT tag_id, tag_nom FROM dd_tags WHERE tag_j_id=:user_id ORDER BY tag_nom ASC');
$stmtTags->execute([':user_id' => $userId]);
$allTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

$tagsHtml = '';
if (!empty($allTags)):
  foreach ($allTags as $tag):
    $tagId = (int)$tag['tag_id'];
    $tagsHtml .= '<label class="mr10"><input type="checkbox" class="note-tag-checkbox" value="' . $tagId . '"> ' . htmlspecialchars((string)$tag['tag_nom'], ENT_QUOTES, 'UTF-8') . '</label>';
  endforeach;
else:
  $tagsHtml = '<div class="nodata">Aucun tag existant pour cet utilisateur.</div>';
endif;

$count = count($noteIds);
$result = '<div class="detail w700">';
$result .= '  <input type="hidden" id="bulk-note-ids" value="' . htmlspecialchars(implode(',', $noteIds), ENT_QUOTES, 'UTF-8') . '">';
$result .= '  <h1>Ajout de tags en masse</h1>';
$result .= '  <div class="texte">Ajouter des tags sur ' . $count . ' note(s) selectionnee(s) sans supprimer les tags existants.</div>';
$result .= '  <div class="gras mr10 mt10">Tags <span id="toggle-tags"><i class="fa-solid fa-bars"></i></span></div>';
$result .= '  <div id="tags" class="box-data">' . $tagsHtml . '<div class="mt10"><input id="mp_new_tags" class="input_nom" placeholder="Nouveaux tags (separes par virgule)"></div></div>';
$result .= '  <div class="mt20">';
$result .= '    <button class="btNoir mr10" onClick="NoteActions.bulkApplyAddTags()">Valider</button>';
$result .= '    <button class="btNoir" onClick="fermerDetail()">Annuler</button>';
$result .= '  </div>';
$result .= '</div>';

echo "1@" . $result;
?>

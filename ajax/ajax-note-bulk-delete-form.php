<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

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

$count = count($noteIds);
$label = ($count > 1) ? "notes" : "note";

$result = '<div class="detail w600">';
$result .= '  <input type="hidden" id="bulk-note-ids" value="' . htmlspecialchars(implode(',', $noteIds), ENT_QUOTES, 'UTF-8') . '">';
$result .= '  <h1>Suppression en masse</h1>';
$result .= '  <div class="texte">Confirmez-vous la suppression de ' . $count . ' ' . $label . ' selectionnee(s) ?</div>';
$result .= '  <div class="mt20">';
$result .= '    <button class="btNoir mr10" onClick="NoteActions.bulkApplyDelete()">Valider</button>';
$result .= '    <button class="btNoir" onClick="fermerDetail()">Annuler</button>';
$result .= '  </div>';
$result .= '</div>';

echo "1@" . $result;
?>

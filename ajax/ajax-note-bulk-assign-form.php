<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$campagneActive = !empty($_SESSION['campagne']) ? (int)$_SESSION['campagne'] : 0;
if ($campagneActive <= 0):
  echo "0@Aucune campagne active.";
  exit;
endif;

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

$stmtPerso = $db->prepare("SELECT pe_id, pe_nom FROM dd_personnages WHERE pe_camp_id=:camp ORDER BY pe_nom ASC");
$stmtPerso->execute([':camp' => $campagneActive]);
$persos = $stmtPerso->fetchAll(PDO::FETCH_ASSOC);
if (empty($persos)):
  echo "0@Aucun personnage disponible dans la campagne active.";
  exit;
endif;

$buildDdOptions = function ($selected = 1) {
  $selected = (int)$selected;
  $html = '';
  for ($i = 1; $i <= 35; $i++):
    $sel = ($i === $selected) ? ' selected="selected"' : '';
    $html .= '<option value="' . $i . '"' . $sel . '>DD ' . $i . '</option>';
  endfor;
  return $html;
};

$result = '<div class="detail w600">';
$result .= '  <input type="hidden" id="bulk-note-ids" value="' . htmlspecialchars(implode(',', $noteIds), ENT_QUOTES, 'UTF-8') . '">';
$result .= '  <h1>Affectation en masse</h1>';
$result .= '  <div class="texte">Affecter un niveau de visibilite pour ' . count($noteIds) . ' note(s) selectionnee(s).</div>';
$result .= '  <div class="ligne mt10">';
$result .= '    <div class="label w200">Niveau global</div>';
$result .= '    <select id="bulk-global-dd">' . $buildDdOptions(1) . '</select>';
$result .= '    <button type="button" class="btNoir ml10" onClick="NoteActions.bulkAssignApplyAll()">Appliquer a tous</button>';
$result .= '  </div>';
$result .= '  <div class="mt10">';
foreach ($persos as $perso):
  $result .= '    <div class="line-data-fr100 mb5">';
  $result .= '      <div class="gras mr10">' . htmlspecialchars((string)$perso['pe_nom'], ENT_QUOTES, 'UTF-8') . '</div>';
  $result .= '      <select class="bulk-assign-dd" data-pe-id="' . (int)$perso['pe_id'] . '">' . $buildDdOptions(1) . '</select>';
  $result .= '    </div>';
endforeach;
$result .= '  </div>';
$result .= '  <div class="mt20">';
$result .= '    <button class="btNoir mr10" onClick="NoteActions.bulkApplyAssign()">Valider</button>';
$result .= '    <button class="btNoir" onClick="fermerDetail()">Annuler</button>';
$result .= '  </div>';
$result .= '</div>';

echo "1@" . $result;
?>

<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/noteslib.inc.php");

header('Content-Type: application/json; charset=utf-8');

$campagneActive = !empty($_SESSION['campagne']) ? (int)$_SESSION['campagne'] : 0;
if ($campagneActive <= 0):
  echo json_encode(['success' => false, 'message' => 'Aucune campagne active.']);
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
  echo json_encode(['success' => false, 'message' => 'Aucune note selectionnee.']);
  exit;
endif;

$assignmentsRaw = isset($_POST['assignments_json']) ? (string)$_POST['assignments_json'] : '';
$assignments = json_decode($assignmentsRaw, true);
if (!is_array($assignments) || empty($assignments)):
  echo json_encode(['success' => false, 'message' => 'Aucune affectation valide.']);
  exit;
endif;

$stmtPerso = $db->prepare("SELECT pe_id FROM dd_personnages WHERE pe_camp_id=:camp");
$stmtPerso->execute([':camp' => $campagneActive]);
$campPersoIds = [];
while ($row = $stmtPerso->fetch(PDO::FETCH_ASSOC)):
  $campPersoIds[(int)$row['pe_id']] = true;
endwhile;

$assignmentsFiltered = [];
foreach ($assignments as $peIdRaw => $ddRaw):
  $peId = (int)$peIdRaw;
  $dd = (int)$ddRaw;
  if ($peId <= 0 || !isset($campPersoIds[$peId])) continue;
  if ($dd < 0 || $dd > 35) continue;
  $assignmentsFiltered[$peId] = $dd;
endforeach;

if (empty($assignmentsFiltered)):
  echo json_encode(['success' => false, 'message' => 'Aucune affectation exploitable pour la campagne active.']);
  exit;
endif;

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

try {
  $db->beginTransaction();

  $stmtFind = $db->prepare("SELECT pno_id FROM dd_personnages_notes WHERE pno_no_id=:no_id AND pno_pe_id=:pe_id LIMIT 1");
  $stmtUpd = $db->prepare("UPDATE dd_personnages_notes SET pno_dd=:dd, pno_actif=1 WHERE pno_id=:id");
  $stmtIns = $db->prepare("INSERT INTO dd_personnages_notes (pno_no_id, pno_pe_id, pno_dd, pno_actif) VALUES (:no_id, :pe_id, :dd, 1)");

  foreach ($noteIds as $noteId):
    $ownerId = notes_get_note_owner_id($db, $noteId);
    if ($ownerId === null) throw new RuntimeException('Note introuvable: ' . $noteId);
    if (!notes_can_edit($db, $noteId, $userId, $isMj)) throw new RuntimeException('Acces refuse pour la note ' . $noteId);

    foreach ($assignmentsFiltered as $peId => $dd):
      $stmtFind->execute([
        ':no_id' => $noteId,
        ':pe_id' => $peId,
      ]);
      $exists = $stmtFind->fetch(PDO::FETCH_ASSOC);
      if ($exists):
        $stmtUpd->execute([
          ':id' => (int)$exists['pno_id'],
          ':dd' => (int)$dd,
        ]);
      else:
        $stmtIns->execute([
          ':no_id' => $noteId,
          ':pe_id' => $peId,
          ':dd' => (int)$dd,
        ]);
      endif;
    endforeach;
  endforeach;

  $db->commit();
  echo json_encode([
    'success' => true,
    'message' => 'Affectation en masse effectuee.',
    'note_count' => count($noteIds),
    'perso_count' => count($assignmentsFiltered),
  ]);
} catch (Throwable $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo json_encode([
    'success' => false,
    'message' => 'Erreur affectation en masse: ' . $e->getMessage(),
  ]);
}
?>

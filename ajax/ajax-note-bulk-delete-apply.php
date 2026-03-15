<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/noteslib.inc.php");

header('Content-Type: application/json; charset=utf-8');

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

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

try {
  $db->beginTransaction();

  $stmtDelTags = null;
  if (notes_table_exists('dd_notes_tags')):
    $stmtDelTags = $db->prepare("DELETE FROM dd_notes_tags WHERE notag_no_id=:id");
  endif;
  $stmtDelCont = $db->prepare("DELETE FROM dd_notes_contenus WHERE noc_no_id=:id");
  $stmtDelPno = $db->prepare("DELETE FROM dd_personnages_notes WHERE pno_no_id=:id");
  $stmtDelCpno = $db->prepare("DELETE FROM dd_campagnes_notes WHERE cpno_no_id=:id");
  $stmtDelNote = $db->prepare("DELETE FROM dd_notes WHERE no_id=:id");

  foreach ($noteIds as $noteId):
    $ownerId = notes_get_note_owner_id($db, $noteId);
    if ($ownerId === null) throw new RuntimeException('Note introuvable: ' . $noteId);
    if (!notes_can_edit($db, $noteId, $userId, $isMj)) throw new RuntimeException('Acces refuse pour la note ' . $noteId);

    if ($stmtDelTags) $stmtDelTags->execute([':id' => $noteId]);
    $stmtDelCont->execute([':id' => $noteId]);
    $stmtDelPno->execute([':id' => $noteId]);
    $stmtDelCpno->execute([':id' => $noteId]);
    $stmtDelNote->execute([':id' => $noteId]);
  endforeach;

  $db->commit();
  echo json_encode([
    'success' => true,
    'deleted_ids' => $noteIds,
    'message' => 'Suppression en masse effectuee.',
  ]);
} catch (Throwable $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo json_encode([
    'success' => false,
    'message' => 'Erreur suppression en masse: ' . $e->getMessage(),
  ]);
}
?>

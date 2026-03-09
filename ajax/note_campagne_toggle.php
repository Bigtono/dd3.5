<?php
include_once("../include/session.php");
include_once("../include/dblib.inc.php");

header('Content-Type: application/json; charset=utf-8');

$noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : 0;
$checkedInput = isset($_POST['checked']) ? (int)$_POST['checked'] : -1;
$checked = ($checkedInput === 1) ? 1 : 0;

$campagneActive = (!empty($_SESSION['campagne']) && $_SESSION['campagne'] > 0)
  ? (int)$_SESSION['campagne']
  : 0;

if ($noteId <= 0):
  echo json_encode(['success' => false, 'checked' => 0, 'message' => 'Parametre note invalide.']);
  exit;
endif;

if ($checkedInput !== 0 && $checkedInput !== 1):
  echo json_encode(['success' => false, 'checked' => 0, 'message' => 'Parametre checked invalide.']);
  exit;
endif;

if ($campagneActive <= 0):
  echo json_encode(['success' => false, 'checked' => 0, 'message' => 'Aucune campagne active.']);
  exit;
endif;

$sqlNote = "
  SELECT no_id, no_j_id
  FROM dd_notes
  WHERE no_id = :note_id
  LIMIT 1
";
$stmtNote = $db->prepare($sqlNote);
$stmtNote->execute([':note_id' => $noteId]);
$note = $stmtNote->fetch(PDO::FETCH_ASSOC);

if (!$note):
  echo json_encode(['success' => false, 'checked' => 0, 'message' => 'Note introuvable.']);
  exit;
endif;

$estMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;
$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$estAuteur = $userId > 0 && (int)$note['no_j_id'] === $userId;

if (!$estMj && !$estAuteur):
  echo json_encode(['success' => false, 'checked' => 0, 'message' => 'Acces refuse.']);
  exit;
endif;

$sqlExists = "
  SELECT cpno_id
  FROM dd_campagnes_notes
  WHERE cpno_no_id = :note_id
    AND cpno_camp_id = :camp_id
  LIMIT 1
";
$stmtExists = $db->prepare($sqlExists);
$stmtExists->execute([
  ':note_id' => $noteId,
  ':camp_id' => $campagneActive,
]);
$exists = $stmtExists->fetch(PDO::FETCH_ASSOC);

if ($checked === 1):
  if (!$exists):
    $sqlInsert = "
      INSERT INTO dd_campagnes_notes (cpno_no_id, cpno_camp_id)
      VALUES (:note_id, :camp_id)
    ";
    $stmtInsert = $db->prepare($sqlInsert);
    $stmtInsert->execute([
      ':note_id' => $noteId,
      ':camp_id' => $campagneActive,
    ]);
  endif;
else:
  if ($exists):
    $sqlDelete = "
      DELETE FROM dd_campagnes_notes
      WHERE cpno_no_id = :note_id
        AND cpno_camp_id = :camp_id
    ";
    $stmtDelete = $db->prepare($sqlDelete);
    $stmtDelete->execute([
      ':note_id' => $noteId,
      ':camp_id' => $campagneActive,
    ]);
  endif;
endif;

$stmtExists->execute([
  ':note_id' => $noteId,
  ':camp_id' => $campagneActive,
]);
$existsAfter = $stmtExists->fetch(PDO::FETCH_ASSOC);
$checkedAfter = $existsAfter ? 1 : 0;

echo json_encode([
  'success' => true,
  'checked' => $checkedAfter,
]);

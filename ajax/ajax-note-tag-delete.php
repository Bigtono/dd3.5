<?php
include_once("../include/session.php");
include_once("../include/dblib.inc.php");
include_once("../include/noteslib.inc.php");

header('Content-Type: application/json; charset=utf-8');

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId <= 0):
  echo json_encode(['success' => false, 'message' => 'Utilisateur non connecte.']);
  exit;
endif;

if (!notes_table_exists('dd_tags') || !notes_table_exists('dd_notes_tags')):
  echo json_encode(['success' => false, 'message' => 'Tables de tags non disponibles.']);
  exit;
endif;

$tagId = isset($_POST['tag_id']) ? (int)$_POST['tag_id'] : 0;
if ($tagId <= 0):
  echo json_encode(['success' => false, 'message' => 'Tag invalide.']);
  exit;
endif;

$stmtTag = $db->prepare("SELECT tag_id FROM dd_tags WHERE tag_id = :tag_id AND tag_j_id = :user_id LIMIT 1");
$stmtTag->execute([
  ':tag_id' => $tagId,
  ':user_id' => $userId,
]);
if (!$stmtTag->fetch(PDO::FETCH_ASSOC)):
  echo json_encode(['success' => false, 'message' => 'Tag introuvable.']);
  exit;
endif;

$stmtUsed = $db->prepare("SELECT notag_id FROM dd_notes_tags WHERE notag_tag_id = :tag_id LIMIT 1");
$stmtUsed->execute([':tag_id' => $tagId]);
if ($stmtUsed->fetch(PDO::FETCH_ASSOC)):
  echo json_encode(['success' => false, 'message' => 'Tag utilise par des notes.']);
  exit;
endif;

$stmtDelete = $db->prepare("DELETE FROM dd_tags WHERE tag_id = :tag_id AND tag_j_id = :user_id");
$stmtDelete->execute([
  ':tag_id' => $tagId,
  ':user_id' => $userId,
]);

echo json_encode(['success' => true]);

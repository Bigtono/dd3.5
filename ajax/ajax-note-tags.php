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

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$noteId = isset($_GET['note_id']) ? (int)$_GET['note_id'] : 0;

$sql = "SELECT tag_id, tag_nom FROM dd_tags WHERE tag_j_id = :user_id";
$params = [':user_id' => $userId];
if ($q !== ''):
  $sql .= " AND (tag_nom LIKE :q OR tag_slug LIKE :q_slug)";
  $params[':q'] = '%' . $q . '%';
  $params[':q_slug'] = '%' . notes_tag_slug($q) . '%';
endif;
$sql .= " ORDER BY tag_nom ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected = [];
if ($noteId > 0):
  $stmtSel = $db->prepare("SELECT notag_tag_id FROM dd_notes_tags WHERE notag_no_id = :note_id");
  $stmtSel->execute([':note_id' => $noteId]);
  while ($row = $stmtSel->fetch(PDO::FETCH_ASSOC)):
    $selected[(int)$row['notag_tag_id']] = true;
  endwhile;
endif;

$out = [];
foreach ($tags as $tag):
  $tagId = (int)$tag['tag_id'];
  $out[] = [
    'tag_id' => $tagId,
    'tag_nom' => (string)$tag['tag_nom'],
    'selected' => isset($selected[$tagId]) ? 1 : 0,
  ];
endforeach;

echo json_encode([
  'success' => true,
  'tags' => $out,
], JSON_UNESCAPED_UNICODE);

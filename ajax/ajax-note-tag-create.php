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

if (!notes_table_exists('dd_tags')):
  echo json_encode(['success' => false, 'message' => 'Table dd_tags non disponible.']);
  exit;
endif;

$tagNom = isset($_POST['tag_nom']) ? trim((string)$_POST['tag_nom']) : '';
if ($tagNom === ''):
  echo json_encode(['success' => false, 'message' => 'Nom du tag obligatoire.']);
  exit;
endif;

$tagSlug = notes_tag_slug($tagNom);
if ($tagSlug === ''):
  echo json_encode(['success' => false, 'message' => 'Nom du tag invalide.']);
  exit;
endif;

$stmtFind = $db->prepare("SELECT tag_id, tag_nom FROM dd_tags WHERE tag_j_id = :user_id AND tag_slug = :slug LIMIT 1");
$stmtFind->execute([
  ':user_id' => $userId,
  ':slug' => $tagSlug,
]);
$existing = $stmtFind->fetch(PDO::FETCH_ASSOC);
if ($existing):
  echo json_encode([
    'success' => true,
    'created' => 0,
    'tag_id' => (int)$existing['tag_id'],
    'tag_nom' => (string)$existing['tag_nom'],
  ], JSON_UNESCAPED_UNICODE);
  exit;
endif;

$stmtInsert = $db->prepare("
  INSERT INTO dd_tags (tag_nom, tag_slug, tag_j_id, tag_date)
  VALUES (:nom, :slug, :user_id, NOW())
");
$stmtInsert->execute([
  ':nom' => $tagNom,
  ':slug' => $tagSlug,
  ':user_id' => $userId,
]);

echo json_encode([
  'success' => true,
  'created' => 1,
  'tag_id' => (int)$db->lastInsertId(),
  'tag_nom' => $tagNom,
], JSON_UNESCAPED_UNICODE);

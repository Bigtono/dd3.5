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

if (!notes_table_exists('dd_tags') || !notes_table_exists('dd_notes_tags')):
  echo json_encode(['success' => false, 'message' => 'La gestion des tags n\'est pas disponible.']);
  exit;
endif;

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

$postTagIds = isset($_POST['mp_no_tags']) ? (string)$_POST['mp_no_tags'] : '';
$postNewTags = isset($_POST['mp_new_tags']) ? (string)$_POST['mp_new_tags'] : '';

$tagIds = [];
foreach (explode(',', $postTagIds) as $part):
  $idTag = (int)trim((string)$part);
  if ($idTag > 0) $tagIds[$idTag] = true;
endforeach;

$stmtCheckTagOwner = $db->prepare("SELECT tag_id FROM dd_tags WHERE tag_id=:tag_id AND tag_j_id=:user_id LIMIT 1");
$ownedTagIds = [];
foreach (array_keys($tagIds) as $tagId):
  $stmtCheckTagOwner->execute([
    ':tag_id' => (int)$tagId,
    ':user_id' => $userId,
  ]);
  if ($stmtCheckTagOwner->fetch(PDO::FETCH_ASSOC)) $ownedTagIds[(int)$tagId] = true;
endforeach;

$rawNewParts = preg_split('/[,\n;\r]+/', $postNewTags);
if (is_array($rawNewParts)):
  $stmtFindTag = $db->prepare("SELECT tag_id FROM dd_tags WHERE tag_j_id=:user_id AND tag_slug=:slug LIMIT 1");
  $stmtInsertTag = $db->prepare("INSERT INTO dd_tags (tag_nom, tag_slug, tag_j_id, tag_date) VALUES (:nom, :slug, :user_id, :date_tag)");
  foreach ($rawNewParts as $newTagName):
    $newTagName = trim((string)$newTagName);
    if ($newTagName === '') continue;
    $slug = notes_tag_slug($newTagName);
    if ($slug === '') continue;

    $stmtFindTag->execute([
      ':user_id' => $userId,
      ':slug' => $slug,
    ]);
    $existingTag = $stmtFindTag->fetch(PDO::FETCH_ASSOC);
    if ($existingTag):
      $ownedTagIds[(int)$existingTag['tag_id']] = true;
    else:
      $stmtInsertTag->execute([
        ':nom' => $newTagName,
        ':slug' => $slug,
        ':user_id' => $userId,
        ':date_tag' => date('Y-m-d H:i:s'),
      ]);
      $ownedTagIds[(int)$db->lastInsertId()] = true;
    endif;
  endforeach;
endif;

if (empty($ownedTagIds)):
  echo json_encode(['success' => false, 'message' => 'Aucun tag valide a ajouter.']);
  exit;
endif;

try {
  $db->beginTransaction();

  $stmtFindRel = $db->prepare("SELECT notag_id FROM dd_notes_tags WHERE notag_no_id=:note_id AND notag_tag_id=:tag_id LIMIT 1");
  $stmtInsRel = $db->prepare("INSERT INTO dd_notes_tags (notag_no_id, notag_tag_id) VALUES (:note_id, :tag_id)");

  $addedLinks = 0;
  foreach ($noteIds as $noteId):
    $ownerId = notes_get_note_owner_id($db, $noteId);
    if ($ownerId === null) throw new RuntimeException('Note introuvable: ' . $noteId);
    if (!notes_can_edit($db, $noteId, $userId, $isMj)) throw new RuntimeException('Acces refuse pour la note ' . $noteId);

    foreach (array_keys($ownedTagIds) as $tagId):
      $stmtFindRel->execute([
        ':note_id' => $noteId,
        ':tag_id' => (int)$tagId,
      ]);
      if ($stmtFindRel->fetch(PDO::FETCH_ASSOC)) continue;
      $stmtInsRel->execute([
        ':note_id' => $noteId,
        ':tag_id' => (int)$tagId,
      ]);
      $addedLinks++;
    endforeach;
  endforeach;

  $db->commit();
  echo json_encode([
    'success' => true,
    'message' => 'Ajout de tags en masse effectue.',
    'note_count' => count($noteIds),
    'tag_count' => count($ownedTagIds),
    'added_links' => $addedLinks,
  ]);
} catch (Throwable $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo json_encode([
    'success' => false,
    'message' => 'Erreur ajout tags en masse: ' . $e->getMessage(),
  ]);
}
?>

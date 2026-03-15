<?php

function notes_table_exists($tableName)
{
  $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tableName);
  if ($tableName === '') return false;
  $stmt = queryPDO("SHOW TABLES LIKE '" . addslashes($tableName) . "'");
  return ($stmt && $stmt->rowCount() > 0);
}

function notes_tag_slug($tagName)
{
  $name = trim((string)$tagName);
  if ($name === '') return '';

  $lower = function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
  if (function_exists('iconv')):
    $tmp = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lower);
    if ($tmp !== false) $lower = $tmp;
  endif;

  $slug = preg_replace('/[^a-z0-9]+/', '-', $lower);
  $slug = trim((string)$slug, '-');
  return $slug;
}

function notes_get_note_owner_id(PDO $db, $noteId)
{
  $stmtNo = $db->prepare("SELECT no_j_id FROM dd_notes WHERE no_id=:id LIMIT 1");
  $stmtNo->execute([':id' => (int)$noteId]);
  $dn = $stmtNo->fetch(PDO::FETCH_ASSOC);
  if (!$dn) return null;
  return (int)$dn['no_j_id'];
}

function notes_can_edit(PDO $db, $noteId, $userId, $isMj)
{
  if ($isMj) return true;
  $ownerId = notes_get_note_owner_id($db, $noteId);
  if ($ownerId === null) return false;
  return ((int)$userId > 0 && $ownerId === (int)$userId);
}

function notes_delete_cascade(PDO $db, $noteId)
{
  $noteId = (int)$noteId;
  if ($noteId <= 0) return false;

  try {
    $db->beginTransaction();

    if (notes_table_exists('dd_notes_tags')):
      $stmt = $db->prepare("DELETE FROM dd_notes_tags WHERE notag_no_id=:id");
      $stmt->execute([':id' => $noteId]);
    endif;

    $stmt = $db->prepare("DELETE FROM dd_notes_contenus WHERE noc_no_id=:id");
    $stmt->execute([':id' => $noteId]);

    $stmt = $db->prepare("DELETE FROM dd_personnages_notes WHERE pno_no_id=:id");
    $stmt->execute([':id' => $noteId]);

    $stmt = $db->prepare("DELETE FROM dd_campagnes_notes WHERE cpno_no_id=:id");
    $stmt->execute([':id' => $noteId]);

    $stmt = $db->prepare("DELETE FROM dd_notes WHERE no_id=:id");
    $stmt->execute([':id' => $noteId]);

    $db->commit();
    return true;
  } catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    return false;
  }
}

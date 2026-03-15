<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/noteslib.inc.php");

$maintenant = new DateTime();
$nRaw = isset($_POST['mp_no_id']) ? (string)$_POST['mp_no_id'] : '';
$nom = isset($_POST['mp_no_nom']) ? trim((string)$_POST['mp_no_nom']) : '';
$typeId = isset($_POST['mp_no_tyno_id']) ? (int)$_POST['mp_no_tyno_id'] : 0;
$diffusion = isset($_POST['diffusion']) ? (string)$_POST['diffusion'] : '';
$postTagIds = isset($_POST['mp_no_tags']) ? (string)$_POST['mp_no_tags'] : '';
$postNewTags = isset($_POST['mp_new_tags']) ? (string)$_POST['mp_new_tags'] : '';
$tagTablesReady = notes_table_exists('dd_tags') && notes_table_exists('dd_notes_tags');

if ($nRaw === ''):
  echo "0@Erreur identifiant note";
  exit;
endif;

if ($nom === '' || $typeId <= 0):
  echo "0@NoNomOuType";
  exit;
endif;

$contenus = [];
if (!empty($_POST['contenus_json'])):
  $json = json_decode((string)$_POST['contenus_json'], true);
  if (is_array($json)):
    foreach ($json as $bloc):
      $dd = isset($bloc['dd']) ? (int)$bloc['dd'] : 0;
      $texte = isset($bloc['texte']) ? trim((string)$bloc['texte']) : '';
      if ($dd > 0 && $texte !== ''):
        $contenus[] = ['dd' => $dd, 'texte' => $texte];
      endif;
    endforeach;
  endif;
endif;

if (empty($contenus)):
  echo "0@AucunContenu";
  exit;
endif;

try {
  $db->beginTransaction();

  if ($nRaw === 'n'):
    $stmtInsert = $db->prepare("INSERT INTO dd_notes (no_nom, no_tyno_id, no_date, no_j_id) VALUES (:nom, :type, :date_note, :user_id)");
    $stmtInsert->execute([
      ':nom' => $nom,
      ':type' => $typeId,
      ':date_note' => $maintenant->format('Y-m-d H:i:s'),
      ':user_id' => (int)$_SESSION['user_id'],
    ]);
    $n = (int)$db->lastInsertId();
  else:
    $n = (int)$nRaw;
    $stmtUpdate = $db->prepare("UPDATE dd_notes SET no_nom=:nom, no_tyno_id=:type, no_date=:date_note, no_j_id=:user_id WHERE no_id=:id");
    $stmtUpdate->execute([
      ':nom' => $nom,
      ':type' => $typeId,
      ':date_note' => $maintenant->format('Y-m-d H:i:s'),
      ':user_id' => (int)$_SESSION['user_id'],
      ':id' => $n,
    ]);

    $stmtDelCont = $db->prepare("DELETE FROM dd_notes_contenus WHERE noc_no_id=:id");
    $stmtDelCont->execute([':id' => $n]);
  endif;

  $stmtAddCont = $db->prepare("INSERT INTO dd_notes_contenus (noc_no_id, noc_dd, noc_texte) VALUES (:no_id, :dd, :texte)");
  foreach ($contenus as $bloc):
    $stmtAddCont->execute([
      ':no_id' => $n,
      ':dd' => (int)$bloc['dd'],
      ':texte' => (string)$bloc['texte'],
    ]);
  endforeach;

  $stmtDelPno = $db->prepare("DELETE FROM dd_personnages_notes WHERE pno_no_id=:id");
  $stmtDelPno->execute([':id' => $n]);

  if ($diffusion !== ''):
    $listdif = explode('pe', $diffusion);
    $stmtAddPno = $db->prepare("INSERT INTO dd_personnages_notes (pno_no_id, pno_pe_id, pno_dd, pno_actif) VALUES (:no_id, :pe_id, :dd, 1)");
    foreach ($listdif as $value):
      if ($value === '') continue;
      $dif = explode('a', $value);
      $peId = isset($dif[0]) ? (int)$dif[0] : 0;
      $dd = isset($dif[1]) ? (int)$dif[1] : 0;
      if ($peId > 0 && $dd > 0):
        $stmtAddPno->execute([
          ':no_id' => $n,
          ':pe_id' => $peId,
          ':dd' => $dd,
        ]);
      endif;
    endforeach;
  endif;

  if ($tagTablesReady):
    $tagIds = [];
    foreach (explode(',', $postTagIds) as $part):
      $idTag = (int)trim((string)$part);
      if ($idTag > 0) $tagIds[$idTag] = true;
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
          ':user_id' => (int)$_SESSION['user_id'],
          ':slug' => $slug,
        ]);
        $existingTag = $stmtFindTag->fetch(PDO::FETCH_ASSOC);
        if ($existingTag):
          $tagIds[(int)$existingTag['tag_id']] = true;
        else:
          $stmtInsertTag->execute([
            ':nom' => $newTagName,
            ':slug' => $slug,
            ':user_id' => (int)$_SESSION['user_id'],
            ':date_tag' => $maintenant->format('Y-m-d H:i:s'),
          ]);
          $tagIds[(int)$db->lastInsertId()] = true;
        endif;
      endforeach;
    endif;

    $stmtDelNoteTags = $db->prepare("DELETE FROM dd_notes_tags WHERE notag_no_id=:note_id");
    $stmtDelNoteTags->execute([':note_id' => $n]);

    if (!empty($tagIds)):
      $stmtInsertNoteTag = $db->prepare("INSERT INTO dd_notes_tags (notag_no_id, notag_tag_id) VALUES (:note_id, :tag_id)");
      $stmtCheckTagOwner = $db->prepare("SELECT tag_id FROM dd_tags WHERE tag_id=:tag_id AND tag_j_id=:user_id LIMIT 1");
      foreach (array_keys($tagIds) as $tagId):
        $stmtCheckTagOwner->execute([
          ':tag_id' => (int)$tagId,
          ':user_id' => (int)$_SESSION['user_id'],
        ]);
        if ($stmtCheckTagOwner->fetch(PDO::FETCH_ASSOC)):
          $stmtInsertNoteTag->execute([
            ':note_id' => $n,
            ':tag_id' => (int)$tagId,
          ]);
        endif;
      endforeach;
    endif;
  endif;

  $db->commit();

  $stmtNo = $db->prepare("SELECT * FROM dd_notes WHERE no_id=:id LIMIT 1");
  $stmtNo->execute([':id' => $n]);
  $dnno = $stmtNo->fetch(PDO::FETCH_ASSOC);

  $accreditation = 999;
  $p = 0;
  include('../include/insert/ligneNote.php');

  echo $n . "@" . $ligne . "@OK";
} catch (Throwable $e) {
  if ($db->inTransaction()) $db->rollBack();
  echo "0@Erreur SQL: " . $e->getMessage();
}
?>

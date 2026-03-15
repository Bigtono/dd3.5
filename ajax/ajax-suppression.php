<?php
session_start();
include_once("../include/dblib.inc.php");
include_once("../include/noteslib.inc.php");

$table = isset($_POST['table']) ? (string)$_POST['table'] : '';
$prefixe = isset($_POST['prefixe']) ? (string)$_POST['prefixe'] : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($table === '' || $prefixe === '' || $id <= 0):
  echo "0@Parametres invalides";
  exit;
endif;

if ($table === 'dd_notes' && $prefixe === 'no'):
  $userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
  $isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

  $ownerId = notes_get_note_owner_id($db, $id);
  if ($ownerId === null):
    echo "no" . $id . "@Note introuvable";
    exit;
  endif;

  if (!notes_can_edit($db, $id, $userId, $isMj)):
    echo "no" . $id . "@Acces refuse";
    exit;
  endif;

  if (notes_delete_cascade($db, $id)):
    echo "no" . $id . "@Suppression note";
  else:
    echo "no" . $id . "@Erreur suppression";
  endif;

  exit;
endif;

$requete = "DELETE FROM " . $table . " WHERE " . $prefixe . "_id='" . $id . "'";
$resultat = execPDO($requete);

echo $prefixe . $id . "@" . $requete;

?>

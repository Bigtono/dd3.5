<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/noteslib.inc.php");

$noteId = isset($_POST['note']) ? (int)$_POST['note'] : 0;
if ($noteId <= 0):
  echo "0@Parametre note invalide";
  exit;
endif;

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$isMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

if (notes_get_note_owner_id($db, $noteId) === null):
  echo "0@Note introuvable";
  exit;
endif;

if (!notes_can_edit($db, $noteId, $userId, $isMj)):
  echo "0@Acces refuse";
  exit;
endif;

if (notes_delete_cascade($db, $noteId)):
  echo 'no' . $noteId . '@Suppression note';
else:
  echo '0@Erreur suppression';
endif;
?>

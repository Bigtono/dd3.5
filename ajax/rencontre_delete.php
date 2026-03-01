<?
include('../include/dblib.inc.php');

header('Content-Type: application/json');

$rencontre_id = isset($_POST['rencontre_id']) ? (int)$_POST['rencontre_id'] : 0;

if ($rencontre_id > 0):

    $stmt = $db->prepare("
    DELETE FROM dd_rencontres
    WHERE re_id = ?
  ");

    $ok = $stmt->execute([$rencontre_id]);

    echo json_encode(['success' => $ok]);

else:

    echo json_encode(['success' => false]);

endif;

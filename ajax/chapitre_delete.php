<?
include('../include/dblib.inc.php');

header('Content-Type: application/json');

$chapitre_id = isset($_POST['chapitre_id']) ? (int)$_POST['chapitre_id'] : 0;
$delete_rencontres = isset($_POST['delete_rencontres']);

if ($chapitre_id <= 0):
    echo json_encode(['success' => false]);
    exit;
endif;

$db->beginTransaction();

try {

    if ($delete_rencontres):

        $stmt = $db->prepare("
      DELETE FROM dd_rencontres
      WHERE re_scc_id = ?
    ");
        $stmt->execute([$chapitre_id]);

    else:

        $stmt = $db->prepare("
      UPDATE dd_rencontres
      SET re_scc_id = NULL
      WHERE re_scc_id = ?
    ");
        $stmt->execute([$chapitre_id]);

    endif;

    $stmt = $db->prepare("
    DELETE FROM dd_scenarios_chapitres
    WHERE scc_id = ?
  ");
    $stmt->execute([$chapitre_id]);

    $db->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {

    $db->rollBack();
    echo json_encode(['success' => false]);
}

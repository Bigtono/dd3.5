<?
include('../include/dblib.inc.php');

$sc_id = (int)$_POST['sc_id'];
$delete_rencontres = isset($_POST['delete_rencontres']);

$db->beginTransaction();

try {

    $stmt = $db->prepare("
    SELECT scc_id
    FROM dd_scenarios_chapitres
    WHERE scc_sc_id = ?
  ");
    $stmt->execute([$sc_id]);
    $chapitres = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($chapitres)):

        $in = implode(',', array_fill(0, count($chapitres), '?'));

        if ($delete_rencontres):

            $stmt = $db->prepare("
        DELETE FROM dd_rencontres
        WHERE re_scc_id IN ($in)
      ");
            $stmt->execute($chapitres);

        else:

            $stmt = $db->prepare("
        UPDATE dd_rencontres
        SET re_scc_id = NULL
        WHERE re_scc_id IN ($in)
      ");
            $stmt->execute($chapitres);

        endif;

        $stmt = $db->prepare("
      DELETE FROM dd_scenarios_chapitres
      WHERE scc_sc_id = ?
    ");
        $stmt->execute([$sc_id]);

    endif;

    $stmt = $db->prepare("
    DELETE FROM dd_scenarios
    WHERE sc_id = ?
  ");
    $stmt->execute([$sc_id]);

    $db->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {

    $db->rollBack();
    echo json_encode(['success' => false]);
}

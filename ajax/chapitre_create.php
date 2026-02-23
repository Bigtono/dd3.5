
<?
include('../include/dblib.inc.php');

header('Content-Type: application/json');

$scenario_id = isset($_POST['scenario_id']) ? (int)$_POST['scenario_id'] : 0;
$scc_nom     = isset($_POST['scc_nom']) ? trim($_POST['scc_nom']) : '';

if ($scenario_id > 0 && $scc_nom !== ''):

    $stmt = $db->prepare("
    INSERT INTO dd_scenarios_chapitres (scc_nom, scc_sc_id)
    VALUES (?, ?)
  ");

    $ok = $stmt->execute([$scc_nom, $scenario_id]);

    echo json_encode(['success' => $ok]);

else:

    echo json_encode([
        'success' => false,
        'debug' => [
            'scenario_id' => $scenario_id,
            'scc_nom' => $scc_nom
        ]
    ]);

endif;

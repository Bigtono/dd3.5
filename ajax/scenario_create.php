<?
include('../include/dblib.inc.php');

$camp_id = (int)$_POST['camp_id'];
$sc_nom = trim($_POST['sc_nom']);

if ($sc_nom !== ''):

    $stmt = $db->prepare("
    INSERT INTO dd_scenarios (sc_nom, sc_camp_id)
    VALUES (?, ?)
  ");
    $stmt->execute([$sc_nom, $camp_id]);

    echo json_encode(['success' => true]);

else:

    echo json_encode(['success' => false]);

endif;

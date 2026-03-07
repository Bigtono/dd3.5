<?

include("../include/session.php");
include('../include/dblib.inc.php');

header('Content-Type: application/json; charset=utf-8');

$camp_id = isset($_POST['camp_id']) ? (int)$_POST['camp_id'] : 0;
$pe_id = isset($_POST['pe_id']) ? (int)$_POST['pe_id'] : 0;
$ruleset = isset($_SESSION['ruleset']) ? (int)$_SESSION['ruleset'] : 0;

if ($camp_id <= 0 || $pe_id <= 0 || $ruleset <= 0):
  echo json_encode(['success' => false, 'message' => 'Paramètres invalides.']);
  exit;
endif;

$sqlCheck = "
  SELECT pe_id
  FROM dd_personnages
  WHERE pe_id = :pe_id
    AND pe_camp_id = 0
    AND pe_ruleset_var_id = :ruleset
  LIMIT 1
";
$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->execute([
  ':pe_id' => $pe_id,
  ':ruleset' => $ruleset,
]);
$exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$exists):
  echo json_encode(['success' => false, 'message' => 'Ce personnage n’est plus disponible.']);
  exit;
endif;

$sqlUpdate = "
  UPDATE dd_personnages
  SET pe_camp_id = :camp_id
  WHERE pe_id = :pe_id
";
$stmtUpdate = $db->prepare($sqlUpdate);
$stmtUpdate->execute([
  ':camp_id' => $camp_id,
  ':pe_id' => $pe_id,
]);

echo json_encode(['success' => true]);


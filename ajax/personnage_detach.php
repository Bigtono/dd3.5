<?

include("../include/session.php");
include('../include/dblib.inc.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['mj']) || (int)$_SESSION['mj'] !== 1):
  echo json_encode(['success' => false, 'message' => 'Acces refuse.']);
  exit;
endif;

$pe_id = isset($_POST['personnage']) ? (int)$_POST['personnage'] : 0;
if ($pe_id <= 0):
  echo json_encode(['success' => false, 'message' => 'Parametre personnage invalide.']);
  exit;
endif;

$sqlCheck = "
  SELECT pe_id, pe_camp_id
  FROM dd_personnages
  WHERE pe_id = :pe_id
  LIMIT 1
";
$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->execute([':pe_id' => $pe_id]);
$perso = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$perso):
  echo json_encode(['success' => false, 'message' => 'Personnage introuvable.']);
  exit;
endif;

if ((int)$perso['pe_camp_id'] <= 0):
  echo json_encode(['success' => false, 'message' => "Ce personnage n'est rattache a aucune campagne."]);
  exit;
endif;

$sqlUpdate = "
  UPDATE dd_personnages
  SET pe_camp_id = 0
  WHERE pe_id = :pe_id
";
$stmtUpdate = $db->prepare($sqlUpdate);
$stmtUpdate->execute([':pe_id' => $pe_id]);

echo json_encode(['success' => true]);

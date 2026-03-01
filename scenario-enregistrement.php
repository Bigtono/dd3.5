<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$sc = isset($_POST['scenario']) ? (int)$_POST['scenario'] : 0;
$action = $_POST['action'] ?? '';

if ($sc <= 0):
  header("Location: campagnes.php");
  exit;
endif;

if ($action == 'cancel'):
  header("Location: scenario.php?scenario=" . $sc);
  exit;
endif;

$camp_id = isset($_POST['mp_sc_camp_id']) ? (int)$_POST['mp_sc_camp_id'] : 0;

// Vérifie que la campagne sélectionnée appartient à l'utilisateur
// et au même ruleset que la campagne actuelle du scénario.
$sqlCampagne = "
  SELECT c_new.camp_id
  FROM dd_scenarios sc
  JOIN dd_campagnes c_current ON c_current.camp_id = sc.sc_camp_id
  JOIN dd_campagnes c_new ON c_new.camp_id = :camp_id
  WHERE sc.sc_id = :sc_id
    AND c_new.camp_j_id = :user_id
    AND c_new.camp_ruleset_var_id = c_current.camp_ruleset_var_id
";
$stmtCampagne = $db->prepare($sqlCampagne);
$stmtCampagne->execute([
  ':camp_id' => $camp_id,
  ':sc_id' => $sc,
  ':user_id' => (int)$_SESSION['user_id'],
]);
$campagneValide = $stmtCampagne->fetchColumn();

if (!$campagneValide):
  // fallback: on garde la campagne actuelle du scénario
  $stmtCurrentCamp = $db->prepare("SELECT sc_camp_id FROM dd_scenarios WHERE sc_id = :sc_id");
  $stmtCurrentCamp->execute([':sc_id' => $sc]);
  $camp_id = (int)$stmtCurrentCamp->fetchColumn();
endif;

$sql = "UPDATE dd_scenarios
        SET sc_nom = :sc_nom,
            sc_description = :sc_description,
            sc_camp_id = :sc_camp_id
        WHERE sc_id = :sc_id";
$stmt = $db->prepare($sql);
$stmt->execute([
  ':sc_nom' => $_POST['mp_sc_nom'],
  ':sc_description' => $_POST['mp_sc_description'],
  ':sc_camp_id' => $camp_id,
  ':sc_id' => $sc,
]);

$_SESSION['scenario'] = $sc;
$_SESSION['campagne'] = $camp_id;

header("Location: scenario.php?scenario=" . $sc . "&msg=M");

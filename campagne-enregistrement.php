<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$camp_id = isset($_POST['camp_id'])
  ? (int) $_POST['camp_id']
  : 0;

$action = $_POST['action'] ?? '';

if ($action == 'cancel'):
  header("Location: campagnes.php");
  exit;
endif;

if ($camp_id > 0):
  // Modification 
  $sql = "UPDATE dd_campagnes
          SET camp_nom = :camp_nom,
              camp_ruleset_var_id = :camp_ruleset_var_id,
              camp_j_id = :camp_j_id,
              camp_resume = :camp_resume,
              camp_description = :camp_description
          WHERE camp_id = :camp_id";
  $resultat = $db->prepare($sql)->execute([
    ':camp_nom' => $_POST['mp_camp_nom'],
    ':camp_ruleset_var_id' => $_POST['mp_camp_ruleset_var_id'],
    ':camp_j_id' => $_POST['mp_camp_j_id'],
    ':camp_resume' => $_POST['mp_camp_resume'],
    ':camp_description' => $_POST['mp_camp_description'],
    ':camp_id' => $camp_id,
  ]);
  $msg = "M";
else:
  $sql = "INSERT INTO dd_campagnes (
            camp_nom,
            camp_ruleset_var_id,
            camp_j_id,
            camp_resume,
            camp_description
          ) VALUES (
            :camp_nom,
            :camp_ruleset_var_id,
            :camp_j_id,
            :camp_resume,
            :camp_description
          )";
  $resultat = $db->prepare($sql)->execute([
    ':camp_nom' => $_POST['mp_camp_nom'],
    ':camp_ruleset_var_id' => $_SESSION['ruleset'],
    ':camp_j_id' => $_POST['mp_camp_j_id'],
    ':camp_resume' => $_POST['mp_camp_resume'],
    ':camp_description' => $_POST['mp_camp_description'],
  ]);
  $camp_id = $db->lastInsertId();
  $msg = "C";
endif;
header("Location: campagne.php?campagne=" . $camp_id . "&msg=" . $msg);

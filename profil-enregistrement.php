<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_id'] <= 0):
  header("location: login.php");
  exit;
endif;

if (isset($_POST['ok'])):
  $userId = (int)$_SESSION['user_id'];

  $modeCampagne = isset($_POST['mp_j_mode_campagne']) ? (int)$_POST['mp_j_mode_campagne'] : 1;
  if ($modeCampagne !== 1) $modeCampagne = 0;
  $affichageRuleset = isset($_POST['mp_j_affichage_ruleset']) ? (int)$_POST['mp_j_affichage_ruleset'] : 0;
  if ($affichageRuleset !== 1) $affichageRuleset = 0;

  $hasModeCampagneColumn = false;
  $hasAffichageRulesetColumn = false;
  try {
    $stmtColumn = $db->query("SHOW COLUMNS FROM dd_joueurs LIKE 'j_mode_campagne'");
    $hasModeCampagneColumn = $stmtColumn && $stmtColumn->fetch(PDO::FETCH_ASSOC) ? true : false;
    $stmtColumnRuleset = $db->query("SHOW COLUMNS FROM dd_joueurs LIKE 'j_affichage_ruleset'");
    $hasAffichageRulesetColumn = $stmtColumnRuleset && $stmtColumnRuleset->fetch(PDO::FETCH_ASSOC) ? true : false;
  } catch (Throwable $e) {
    $hasModeCampagneColumn = false;
    $hasAffichageRulesetColumn = false;
  }

  $sqlUpdate = "
    UPDATE dd_joueurs
    SET
      j_nom = :nom,
      j_prenom = :prenom,
      j_email = :email,
      j_bio = :bio,
      j_default_ruleset_var_id = :ruleset,
      j_dd_onglet_sort = :onglet_sort,
      j_dd_onglet_don = :onglet_don,
      j_dd_onglet_om = :onglet_om";
  if ($hasModeCampagneColumn):
    $sqlUpdate .= ",
      j_mode_campagne = :mode_campagne";
  endif;
  if ($hasAffichageRulesetColumn):
    $sqlUpdate .= ",
      j_affichage_ruleset = :affichage_ruleset";
  endif;
  $sqlUpdate .= "
    WHERE j_id = :id
    LIMIT 1
  ";

  $params = [
    ':nom' => isset($_POST['mp_j_nom']) ? $_POST['mp_j_nom'] : '',
    ':prenom' => isset($_POST['mp_j_prenom']) ? $_POST['mp_j_prenom'] : '',
    ':email' => isset($_POST['mp_j_email']) ? $_POST['mp_j_email'] : '',
    ':bio' => isset($_POST['mp_j_bio']) ? $_POST['mp_j_bio'] : '',
    ':ruleset' => isset($_POST['mp_j_default_ruleset_var_id']) ? (int)$_POST['mp_j_default_ruleset_var_id'] : (int)$_SESSION['ruleset'],
    ':onglet_sort' => isset($_POST['mp_j_dd_onglet_sort']) ? (int)$_POST['mp_j_dd_onglet_sort'] : 0,
    ':onglet_don' => isset($_POST['mp_j_dd_onglet_don']) ? (int)$_POST['mp_j_dd_onglet_don'] : 0,
    ':onglet_om' => isset($_POST['mp_j_dd_onglet_om']) ? (int)$_POST['mp_j_dd_onglet_om'] : 0,
    ':id' => $userId,
  ];
  if ($hasModeCampagneColumn):
    $params[':mode_campagne'] = $modeCampagne;
  endif;
  if ($hasAffichageRulesetColumn):
    $params[':affichage_ruleset'] = $affichageRuleset;
  endif;
  $stmt = $db->prepare($sqlUpdate);
  $stmt->execute($params);

  // Mise a jour des variables de session
  $_SESSION['ruleset'] = isset($_POST['mp_j_default_ruleset_var_id']) ? (int)$_POST['mp_j_default_ruleset_var_id'] : (int)$_SESSION['ruleset'];
  $_SESSION['rulesetRep'] = libvar($_SESSION['ruleset']);
  $_SESSION['onglet_sort'] = isset($_POST['mp_j_dd_onglet_sort']) ? (int)$_POST['mp_j_dd_onglet_sort'] : 0;
  $_SESSION['onglet_don'] = isset($_POST['mp_j_dd_onglet_don']) ? (int)$_POST['mp_j_dd_onglet_don'] : 0;
  $_SESSION['onglet_om'] = isset($_POST['mp_j_dd_onglet_om']) ? (int)$_POST['mp_j_dd_onglet_om'] : 0;
  $_SESSION['mode_campagne'] = $hasModeCampagneColumn ? $modeCampagne : (isset($_SESSION['mode_campagne']) ? (int)$_SESSION['mode_campagne'] : 1);
  $_SESSION['affichage_ruleset'] = $hasAffichageRulesetColumn ? $affichageRuleset : (isset($_SESSION['affichage_ruleset']) ? (int)$_SESSION['affichage_ruleset'] : 0);
else:
  // Annulation: aucun changement en base.
endif;

header("location: profil.php");
?>

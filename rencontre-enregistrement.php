<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$re = isset($_POST['re']) ? (int)$_POST['re'] : 0;

if ($action == 'cancel'):
  header("location: rencontre.php?rencontre='.$re.'");
  exit;
endif;

if ($re > 0):
  // Modification 
  $sql = "UPDATE dd_rencontres
          SET re_nom = :re_nom,
              re_code = :re_code,
              re_description = :re_description,
              re_ruleset_var_id = :re_ruleset_var_id,
              re_sc_id = :re_sc_id,
              re_scc_id = :re_scc_id
          WHERE re_id = :re_id";
  $stmt = $db->prepare($sql);
  $resultat = $stmt->execute([
    ':re_nom' => $_POST['mp_re_nom'],
    ':re_code' => $_POST['mp_re_code'],
    ':re_description' => $_POST['mp_re_description'],
    ':re_ruleset_var_id' => $_SESSION['ruleset'],
    ':re_sc_id' => $_POST['mp_re_sc_id'],
    ':re_scc_id' => $_POST['mp_re_scc_id'],
    ':re_id' => $re,
  ]);
  $msg = "M";
else:
  // Ajout
  $sql = "INSERT INTO dd_rencontres (
            re_nom,
            re_code,
            re_description,
            re_ruleset_var_id,
            re_sc_id,
            re_scc_id,
            re_j_id
          ) VALUES (
            :re_nom,
            :re_code,
            :re_description,
            :re_ruleset_var_id,
            :re_sc_id,
            :re_scc_id,
            :re_j_id
          )";
  $stmt = $db->prepare($sql);
  $resultat = $stmt->execute([
    ':re_nom' => $_POST['mp_re_nom'],
    ':re_code' => $_POST['mp_re_code'],
    ':re_description' => $_POST['mp_re_nom'],
    ':re_ruleset_var_id' => $_SESSION['ruleset'],
    ':re_sc_id' => $_POST['mp_re_sc_id'],
    ':re_scc_id' => $_POST['mp_re_scc_id'],
    ':re_j_id' => $_SESSION['user_id'],
  ]);
  $re = $db->lastInsertId($resultat);
  $msg = "C";
endif;
header("location: rencontre.php?rencontre=" . $re . "&msg=" . $msg);

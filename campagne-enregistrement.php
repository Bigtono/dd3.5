<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$action = $_POST['action'] ?? '';

if($action == 'cancel'):
  header("Location: campagnes.php");
  exit;
endif;

if($camp_id > 0):
  // Modification 
  $sql="UPDATE dd_campagnes SET camp_nom='".addslashes($_POST['mp_camp_nom'])."', camp_ruleset_var_id='".$_POST['mp_camp_ruleset_var_id']."', camp_j_id='".$_POST['mp_camp_j_id']."', camp_resume='".addslashes($_POST['mp_camp_resume'])."', camp_description='".addslashes($_POST['mp_camp_description'])."' WHERE camp_id='".$c."'";
  $resultat = execPDO($sql);		
  $msg="M";
  else:
  $sql="INSERT INTO dd_campagnes (camp_nom, camp_ruleset_var_id, camp_j_id, camp_resume, camp_description) VALUES ('".addslashes($_POST['mp_camp_nom'])."', '".$_POST['mp_camp_ruleset_var_id']."', '".$_POST['mp_camp_j_id']."', '". addslashes($_POST['mp_camp_resume'])."', '". addslashes($_POST['mp_camp_description'])."')";
  $resultat = execPDO($sql);
  $camp_id = $db->lastInsertId();
  $msg="C";
endif;
header("Location: campagne.php?campagne=".$camp_id);

?>

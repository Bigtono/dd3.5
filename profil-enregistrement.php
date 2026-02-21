<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

if (isset($_POST['ok'])):
  $sql="UPDATE dd_joueurs SET j_nom='".addslashes($_POST['mp_j_nom'])."', j_prenom='".$_POST['mp_j_prenom']."', j_email='".$_POST['mp_j_email']."', j_bio='".$_POST['mp_j_bio']."', j_default_ruleset_var_id='".$_POST['mp_j_default_ruleset_var_id']."',  j_dd_onglet_sort='".$_POST['mp_j_dd_onglet_sort']."', j_dd_onglet_don='".$_POST['mp_j_dd_onglet_don']."', j_dd_onglet_om='".$_POST['mp_j_dd_onglet_om']."' WHERE j_id=".$_SESSION['user_id'];
  $resultat = execPDO($sql);		
  $j=$_POST['mp_j_id'];
  // on met à jour les variables de session pour les options
  $_SESSION['ruleset'] = $_POST['mp_j_default_ruleset_var_id'];
  $_SESSION['rulesetRep']=libvar($_POST['mp_j_default_ruleset_var_id']);
  $_SESSION['onglet_sort'] = $_POST['mp_j_dd_onglet_sort'];
  $_SESSION['onglet_don'] = $_POST['mp_j_dd_onglet_don'];
  $_SESSION['onglet_om'] = $_POST['mp_j_dd_onglet_om'];
	else:
	$message=3;
endif;

header("location: profil.php");
?>

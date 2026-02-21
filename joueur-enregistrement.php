<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$j=$_GET['joueur'];

if (isset($_POST['ok'])):
	// gestion des modifications du joueur
	if (!empty($j) && $j!="n"):
		// Modification 
		$sql="UPDATE dd_joueurs SET j_nom='".addslashes($_POST['mp_j_nom'])."', j_prenom='".$_POST['mp_j_prenom']."', j_email='".$_POST['mp_j_email']."', j_pseudo='".$_POST['mp_j_pseudo']."', j_notes='".$_POST['mp_j_notes']."', j_default_ruleset_var_id='".$_POST['mp_j_default_ruleset_var_id']."', j_dd_onglet_sort='".$_POST['mp_j_dd_onglet_sort']."', j_dd_onglet_don='".$_POST['mp_j_dd_onglet_don']."', j_dd_onglet_om='".$_POST['mp_j_dd_onglet_om']."' WHERE j_id=".$_POST['mp_j_id'];
		$resultat = execPDO($sql);		
		$j=$_POST['mp_j_id'];
    $message=$sql;
		elseif ($j=="n"):
		// Ajout
		$sql="INSERT INTO dd_joueurs (j_nom, j_prenom, j_pseudo, j_email, j_notes, j_default_ruleset_var_id, j_dd_onglet_sort, j_dd_onglet_don, j_dd_onglet_om ,j_pass) VALUES ('".addslashes($_POST['mp_j_nom'])."', '".addslashes($_POST['mp_j_prenom'])."', '".addslashes($_POST['mp_j_pseudo'])."', '". addslashes($_POST['mp_j_email'])."', '". addslashes($_POST['mp_j_notes'])."', '". $_POST['mp_j_notes']."', '".$_POST['mp_j_dd_onglet_sort']."', '".$_POST['mp_j_dd_onglet_don']."', '".$_POST['mp_j_dd_onglet_om']."', '".password_hash('Tempo11111', PASSWORD_DEFAULT)."')";
		$resultat = execPDO($sql);
		$j=lastID("joueurs","j"); 
    $message=2;
	endif;
	else:
	$message=3;
endif;

//$page=$_POST['page'];
header("location: joueur.php?joueur=".$j);
?>

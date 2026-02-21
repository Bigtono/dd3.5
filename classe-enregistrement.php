<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_POST['mp_cla_id'])):
	$c = $_POST['mp_cla_id'];
	else:
	$c=0;
endif;
if (isset($_POST['ok']) && $c!=""):
	// gestion des modifications du joueur
	if ($c!="n"):
		// Modification 
		$sql="UPDATE dd_classes SET cla_nom='".addslashes($_POST['mp_cla_nom']).
      "', cla_clt_id='".$_POST['mp_cla_clt_id'].
      "', cla_abreviation='".$_POST['mp_cla_abreviation']. 
      "', cla_dV='".addslashes($_POST['mp_cla_dV']). 
      "', cla_pointsCompetences='".addslashes($_POST['mp_cla_pointsCompetences']).
      "', cla_alignement='".addslashes($_POST['mp_cla_alignement']).
      "', cla_car_id='".addslashes($_POST['mp_cla_car_id']).
      "', cla_po_niveau1='".addslashes($_POST['mp_cla_po_niveau1']).
      "', cla_conditions='".addslashes($_POST['mp_cla_conditions']).
      "', cla_description='".addslashes($_POST['mp_cla_description']).
      "', cla_traits='".addslashes($_POST['mp_cla_traits']).
      "', cla_caracteristiques='".addslashes($_POST['mp_cla_caracteristiques']).
      "', cla_sauvegardes='".addslashes($_POST['mp_cla_sauvegardes']).
      "', cla_competences='".addslashes($_POST['mp_cla_competences']).
      "', cla_armes='".addslashes($_POST['mp_cla_armes']).
      "', cla_armures='".addslashes($_POST['mp_cla_armures']).
      "', cla_outils='".addslashes($_POST['mp_cla_outils']).
      "', cla_equipement='".addslashes($_POST['mp_cla_equipement']).
      "', cla_pouvoir1='".addslashes($_POST['mp_cla_pouvoir1']).
      "', cla_pouvoir2='".addslashes($_POST['mp_cla_pouvoir2']).
      "', cla_pouvoir3='".addslashes($_POST['mp_cla_pouvoir3']).
      "', cla_pouvoir4='".addslashes($_POST['mp_cla_pouvoir4']).
      "', cla_sorts='".$_POST['mp_cla_sorts'].
      "', cla_mag_id='".$_POST['mp_cla_mag_id'].
      "', cla_niveauMax='".$_POST['mp_cla_niveauMax'].
      "', cla_res_id='".$_POST['mp_cla_res_id'].
      "' WHERE cla_id='".$c."'";
		$resultat = execPDO($sql);		
    $message=1;
		else: 
		// Ajout
		$sql="INSERT INTO dd_classes (cla_nom, cla_clt_id, cla_abreviation, cla_dV, cla_pointsCompetences, cla_alignement, cla_car_id, cla_po_niveau1, cla_conditions, cla_description, cla_traits, cla_caracteristiques, cla_sauvegardes, cla_competences, cla_armes, cla_armures, cla_outils, cla_equipement, cla_pouvoir1, cla_pouvoir2, cla_pouvoir3, cla_pouvoir4, cla_sorts, cla_mag_id, cla_niveauMax, cla_res_id, cla_ruleset_var_id) VALUES ('".addslashes($_POST['mp_cla_nom']).
      "', '".$_POST['mp_cla_clt_id'].
      "', '".addslashes($_POST['mp_cla_abreviation']).
      "', '".addslashes($_POST['mp_cla_dV']).
      "', '".addslashes($_POST['mp_cla_pointsCompetences']).
      "', '". addslashes($_POST['mp_cla_alignement']).
      "', '".$_POST['mp_cla_car_id'].
      "', '". addslashes($_POST['mp_cla_po_niveau1']).
      "', '". addslashes($_POST['mp_cla_conditions']).
      "', '". addslashes($_POST['mp_cla_description']).
      "', '". addslashes($_POST['mp_cla_traits']).
      "', '". addslashes($_POST['mp_cla_cla_caracteristiques']).
      "', '". addslashes($_POST['mp_cla_sauvegardes']).
      "', '". addslashes($_POST['mp_cla_competences']).
      "', '". addslashes($_POST['mp_cla_armes']).
      "', '". addslashes($_POST['mp_cla_armures']).
      "', '". addslashes($_POST['mp_cla_outils']).
      "', '". addslashes($_POST['mp_cla_equipement']).
      "', '". addslashes($_POST['mp_cla_pouvoir1']).
      "', '". addslashes($_POST['mp_cla_pouvoir2']).
      "', '". addslashes($_POST['mp_cla_pouvoir3']).
      "', '". addslashes($_POST['mp_cla_pouvoir4']).
      "', '". addslashes($_POST['mp_cla_sorts']).
      "', '".$_POST['mp_cla_mag_id'].
      "', '".$_POST['mp_cla_niveauMax'].
      "', '".$_POST['mp_cla_res_id'].
      "', '".$_SESSION['ruleset'].
      "')";
		$resultat = execPDO($sql);
    $c=$db->lastInsertId($resultat);
    $message=2;
	endif;
	else:
	$message=$_POST['ok']."/".$p;
endif;
header("location: classe.php?classe=".$c."&tri=".$_GET['tri']."&msg=".$_POST['mp_cla_abreviation']);
?>

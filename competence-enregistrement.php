<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");
if	(isset($_POST['comp'])):
	$comp = $_POST['comp'];
	else:
	$comp="";
endif;
if (isset($_POST['ok']) && $comp!=""):
	// gestion des modifications du joueur
	if ($comp!="n"):
		// Modification 
		$sql="UPDATE dd_competences SET comp_nom='".addslashes($_POST['mp_comp_nom'])."', comp_description='".$_POST['mp_comp_description']."' WHERE comp_id='".$comp."'";
		$resultat = execPDO($sql);		
    $message=1;
		else:
		// Ajout
		$sql="INSERT INTO dd_competences (comp_nom, comp_description) VALUES ('".addslashes($_POST['mp_comp_nom'])."', '".addslashes($_POST['mp_comp_description'])."')";
		$resultat = execPDO($sql);
		$comp=lastID("dd_competences","comp"); 
    $message=2;
	endif;
	else:
	$message=$_POST['ok']."/".$comp;
endif;
header("location: competences.php?msg=".$message);
?>

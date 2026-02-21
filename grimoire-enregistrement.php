<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$g = $_POST['idgrimoire'];

if (isset($_POST['ok']) && !empty($g)):
  // Mise à jour des données de présentation du grimoires
	if ($g!="n"):
		// Modification 
		$sql="UPDATE dd_grimoires SET gr_nom='".addslashes($_POST['mp_gr_nom'])."', gr_cla_id='".$_POST['mp_gr_cla_id']."', gr_grf_id='".$_POST['mp_gr_grf_id']."', gr_pe_id='".$_POST['mp_gr_pe_id']."', gr_defaut='".$_POST['mp_gr_defaut']."' WHERE gr_id='".$g."'";
		$resultat = execPDO($sql);
    $message=1;
    // vidage de l'ancienne sélection dans le caddie 
    $requete='DELETE FROM dd_grimoires_contenu WHERE grc_gr_id="'.$g.'"';
    $result_suppr=execPDO($requete);
		else:
		$sql="INSERT INTO dd_grimoires (gr_nom, gr_cla_id, gr_grf_id, gr_pe_id, gr_defaut) VALUES ('".addslashes($_POST['mp_gr_nom'])."', '".$_POST['mp_gr_cla_id']."', '".$_POST['mp_gr_grf_id']."', '".$_POST['mp_gr_pe_id']."', '".$_POST['mp_defaut']."')";
		$resultat = execPDO($sql);
		$g=lastID("dd_grimoires","gr"); 
    $message=0;
	endif;
	// remplissage du caddie avec la nouvelle sélection
	foreach($_POST['s'] as $key=>$value):
    $requete='INSERT INTO dd_grimoires_contenu (grc_gr_id, grc_so_id) VALUES ('.$g.','.$key.')';
    $result_add=execPDO($requete);
    //writeLog("log.txt",$requete);
	endforeach;
  else:
endif;
if ($_POST['retour']=="personnage"):
  header("location: personnage.php?personnage=".$_POST['mp_gr_pe_id']."&onglet=grimoire&retour=personnage");
  else:
  header("location: grimoire.php?grimoire=".$g."&retour=grimoires");
endif;
?>

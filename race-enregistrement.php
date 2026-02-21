<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_POST['mp_ra_id'])):
	$r = $_POST['mp_ra_id'];
	else:
	$r=0;
endif;
if ($r=="n"):
  // Ajout
  $sql="INSERT INTO dd_races (ra_nom, ra_rat_id, ra_origine, ra_modifFor, ra_modifCon, ra_modifDex, ra_modifInt, ra_modifSag, ra_modifCha, ra_mod_niveau, ra_description, ra_ruleset_var_id) VALUES ('".addslashes($_POST['mp_ra_nom']).
    "', '".$_POST['mp_ra_rat_id'].
    "', '".addslashes($_POST['mp_ra_origine']).
    "', '".addslashes($_POST['mp_ra_modifFor']).
    "', '".addslashes($_POST['mp_ra_modifCon']).
    "', '".addslashes($_POST['mp_ra_modifDex']).
    "', '".addslashes($_POST['mp_ra_modifInt']).
    "', '".addslashes($_POST['mp_ra_modifSag']).
    "', '".addslashes($_POST['mp_ra_modifCha']).
    "', '".addslashes($_POST['mp_ra_mod_niveau']).
    "', '".addslashes($_POST['mp_ra_description']).
    "', '".$_SESSION['ruleset'].
    "')";
  $resultat = execPDO($sql);
  //$c=lastID("dd_races","cla");
  $r=$db->lastInsertId($resultat);
  $message=2;
  else: 
  // Modification 
  $sql="UPDATE dd_races SET ra_nom='".addslashes($_POST['mp_ra_nom']).
    "', ra_rat_id='".$_POST['mp_ra_rat_id'].
    "', ra_origine='".addslashes($_POST['mp_ra_origine']). 
    "', ra_modifFor='".addslashes($_POST['mp_ra_modifFor']).
    "', ra_modifCon='".addslashes($_POST['mp_ra_modifCon']).
    "', ra_modifDex='".addslashes($_POST['mp_ra_modifDex']).
    "', ra_modifInt='".addslashes($_POST['mp_ra_modifint']).
    "', ra_modifSag='".addslashes($_POST['mp_ra_modifSag']).
    "', ra_modifCha='".addslashes($_POST['mp_ra_modifCha']).
    "', ra_mod_niveau='".addslashes($_POST['mp_ra_mod_niveau']).
    "', ra_description='".addslashes($_POST['mp_ra_description']).
    "', ra_ruleset_var_id='".$_SESSION['ruleset'].
    "' WHERE ra_id='".$r."'";
  $resultat = execPDO($sql);		
  $message=1;
endif;
header("location: race.php?race=".$r."&tri=".$_GET['tri']."&msg=".$message);
/*if ($c!="n"):
  header("location: race.php?race=".$c."&tri=".$_GET['tri']."&msg=".$message);
  else:
  header("location: races.php");
endif;
*/
?>

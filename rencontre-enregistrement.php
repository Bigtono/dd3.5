<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_POST['mp_re_id'])):
	$re = $_POST['mp_re_id'];
	else:
	$re=0;
endif;
if (isset($_POST['validModifRencontre'])):
  if ($re=="n"):
    // Ajout
    $sql='INSERT INTO dd_rencontres (re_nom, re_abreviation, re_description, re_ruleset_var_id, re_sc_id, re_scc_id, re_j_id) VALUES ("'.addslashes($_POST['mp_re_nom']).'", "'.addslashes($_POST['mp_re_abreviation']).'", "'.addslashes($_POST['mp_re_nom']).'", "'.$_SESSION['ruleset'].'", "'.$_POST['mp_re_sc_id'].'", "'.$_POST['mp_re_scc_id'].'", "'.$_SESSION['user_id'].'")';
    $resultat = execPDO($sql);
    $re=$db->lastInsertId($resultat);
    $message="C";
    else: 
    // Modification 
    $sql="UPDATE dd_rencontres SET re_nom='".addslashes($_POST['mp_re_nom']).
      "', re_abreviation='".addslashes($_POST['mp_re_abreviation']). 
      "', re_description='".addslashes($_POST['mp_re_description']).
      "', re_ruleset_var_id='".$_SESSION['ruleset'].
      "', re_sc_id='".$_POST['mp_re_sc_id'].
      "', re_scc_id='".$_POST['mp_re_scc_id'].
      "' WHERE re_id='".$re."'";
    $resultat = execPDO($sql);
    $message="M";
  endif;
  else:
  $message="E";  
endif;
header("location: rencontre.php?re=".$re."&tri=".$_GET['tri']."&msg=".$message);
?>

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


if	(isset($_GET['regle'])):
	$regle = $_GET['regle'];
  if (isset($_POST['ok']) && $regle!=""):
    if ($regle<>"n"):
      $requete = "UPDATE dd_regles
        SET re_nom='".addslashes($_POST['mp_re_nom']).
        "', re_texte='".addslashes($_POST['mp_re_texte']).
        "', re_re_id='".addslashes($_POST['mp_re_re_id']).
        "', re_ordre='".addslashes($_POST['mp_re_ordre']).
        "', re_ecran='".addslashes($_POST['mp_re_ecran']).
        "' WHERE re_id='".$regle."'";
      $resultat = execPDO($requete);
      else:
      // création de la regle
      $requete = "INSERT INTO dd_regles (re_nom, re_texte, re_re_id, re_ordre, re_ecran, re_ruleset_var_id) VALUES ('".addslashes($_POST['mp_re_nom'])."','".addslashes($_POST['mp_re_texte'])."', '".addslashes($_POST['mp_re_re_id'])."', '".addslashes($_POST['mp_re_ordre'])."', '".addslashes($_POST['mp_re_ecran'])."', '".$_SESSION['ruleset']."')";
      $resultat = execPDO($requete);
      $regle=$db->lastInsertId($resultat);
    endif;
  endif;
  header("location: regle.php?regle=".$regle);
  else:
  header("location: regles.php");
endif;
?>

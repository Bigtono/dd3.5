<?php
include_once("../include/dblib.inc.php");


if(isset($_POST['mp_do_id'])):
  if ($_POST['mp_do_id']=="n"):
    // création d'un don
    $requete = "INSERT INTO dd_dons (do_nom, do_dado_id, do_texte, do_res_id, do_page_source, do_resume, do_ruleset_var_id, do_version) VALUES ('".
      addslashes($_POST['mp_do_nom'])."','".
      $_POST['mp_do_dado_id']."','".
      addslashes($_POST['mp_do_texte'])."','".
      $_POST['mp_do_res_id']."','".
      addslashes($_POST['mp_do_page_source'])."','".
      addslashes($_POST['mp_do_resume'])."','".
      $_SESSION['ruleset']."','".
      addslashes($_POST['mp_do_version'])."')";
    $resultat = execPDO($requete);
    $q=lastID("dd_dons", "do");
    else:
    // MAJ du don
    $requete = "UPDATE dd_dons
      SET do_nom='".addslashes($_POST['mp_do_nom']).
      "', do_dado_id='".$_POST['mp_do_dado_id'].
      "', do_texte='".addslashes($_POST['mp_do_texte']).  
      "', do_res_id='".$_POST['mp_do_res_id'].
      "', do_page_source='".addslashes($_POST['mp_do_page_source']).
      "', do_resume='".addslashes($_POST['mp_do_resume']).
      "', do_ruleset_var_id='".$_SESSION['ruleset'].
      "', do_version='".addslashes($_POST['mp_do_version']).
      "' WHERE do_id='".$_POST['mp_do_id']."'";
    $resultat = execPDO($requete);
    $q=$_POST['mp_do_id'];
  endif;
  // On ajoute les donnnées dans un tableau
  echo $q."@".$requete."@".$_POST['mp_do_nom']."@".libelle("dd__don","dado","nom",$_POST['mp_do_dado_id'])."@".$_POST['mp_do_resume']."@".$_POST['mp_do_resume']."@".libelle("dd_ressources","res","abreviation",$_POST['mp_do_res_id']);
	else:
	echo "0@".$_POST['mp_do_id'];
endif;
?>
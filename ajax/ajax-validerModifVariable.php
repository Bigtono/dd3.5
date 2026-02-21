<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$maintenant = new DateTime();

$v=$_POST['mp_var_id'];

if(!empty($v)):
  if ($n=="v"):
    // création
    $requete = "INSERT INTO dd_variables (var_valeur, var_cat, var_description, var_j_id, var_date) VALUES ('".
      addslashes($_POST['mp_var_valeur'])."','".
      $_POST['mp_var_cat']."','".
      $_POST['mp_var_cumulatif']."','".      
      $_SESSION['user_id']."','".
      $maintenant->format('Y:m:d H:i:s')."')";
    $resultat = execPDO($requete);
    $n=lastID("dd_variables", "var");
    else:
    // Modification
    $requete = "UPDATE dd_variables
      SET var_valeur='".addslashes($_POST['mp_var_valeur']).
      "', var_cat='".$_POST['mp_var_cat'].
      "', var_description='".$_POST['mp_var_description'].
      "', var_j_id='".$_SESSION['user_id'].
      "', var_date='".$maintenant->format('Y:m:d H:i:s').
      "' WHERE var_id='".$v."'";
    $resultat = execPDO($requete);
  endif;

  // on réactualise la liste des variables
  include('../include/insert/'.$_SESSION['rulesetRep'].'/listeVariables.php');
  
  // On ajoute les donnnées dans un tableau
  echo $v."@".$liste_var;
	else:
	echo "0@Erreur";
endif;
?>
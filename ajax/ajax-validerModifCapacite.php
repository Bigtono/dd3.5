<?php
include_once("../include/dblib.inc.php");


if(isset($_POST['mp_cap_id'])):
  if ($_POST['mp_cap_id']=="n"):
    // création d'un sort
    $requete = "INSERT INTO dd_capacites_speciales (
    cap_nom,
    cap_description)
    VALUES ('".
      addslashes($_POST['mp_cap_nom'])."','".
      addslashes($_POST['mp_cap_description']).")";
    $resultat = execPDO($requete);
    $q=lastID("dd_capacites_speciales", "cap");
    else:
    // MAJ du sort
    $requete = "UPDATE dd_capacites_speciales
      SET cap_nom='".addslashes($_POST['mp_cap_nom']).
      "', cap_description='".$_POST['mp_cap_description'].
      "' WHERE cap_id='".$_POST['mp_cap_id']."'";
    $resultat = execPDO($requete);
    $q=$_POST['mp_cap_id'];
  endif;

  // On ajoute les donnnées dans un tableau
  echo $q."@".$_POST['mp_cap_nom']."@".$_POST['mp_cap_description'];
	else:
	echo "prout@".$_POST['mp_cap_id'];
endif;
?>
<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$maintenant = new DateTime();

if(isset($_POST['mp_om_id'])):
  if ($_POST['mp_om_id']=="n"):
    // création d'un OM
    $requete = "INSERT INTO dd_objets_magiques (om_nom, om_com_id, om_fom_id, om_so_id, om_so_niveau, om_modificateurs, om_variantes, om_description, om_visible, om_res_id, om_date, om_redacteur) VALUES ('".
      addslashes($_POST['mp_om_nom'])."','".
      $_POST['mp_om_com_id']."','".
      $_POST['mp_om_fom_id']."','".
      $_POST['mp_om_so_id']."','".
      $_POST['mp_om_so_niveau']."','".
      $_POST['mp_om_modificateurs']."','".
      $_POST['mp_om_variantes']."','".
      addslashes($_POST['mp_om_description'])."','".
      $_POST['mp_om_visible']."','".
      $_POST['mp_om_res_id']."','".
      $maintenant->format('Y:m:d H:i:s')."','".
      $_SESSION['user_id']."')";
    $resultat = execPDO($requete);
    $q=lastID("dd_objets_magiques", "om");
    else:
    // MAJ du don
    $requete = "UPDATE dd_objets_magiques
      SET om_nom='".addslashes($_POST['mp_om_nom']).
      "', om_com_id='".$_POST['mp_om_com_id'].
      "', om_fom_id='".$_POST['mp_om_fom_id'].
      "', om_so_id='".$_POST['mp_om_so_id'].
      "', om_so_niveau='".$_POST['mp_om_so_niveau'].
      "', om_modificateurs='".$_POST['mp_om_modificateurs'].
      "', om_variantes='".$_POST['mp_om_variantes'].
      "', om_description='".addslashes($_POST['mp_om_description']).
      "', om_visible='".$_POST['mp_om_visible'].
      "', om_res_id='".$_POST['mp_om_res_id'].
      "', om_date='".$maintenant->format('Y:m:d H:i:s').
      "', om_redacteur='".$_SESSION['user_id'].
      "' WHERE om_id='".$_POST['mp_om_id']."'";
    $resultat = execPDO($requete);
    $q=$_POST['mp_om_id'];
  endif;
  // On ajoute les donnnées dans un tableau
  echo $q."@".$requete."@".$_POST['mp_om_nom']."@".libelle("dd_categorie_objet_magique","com","nom",$_POST['mp_om_com_id'])."@".libelle("dd_ressources","res","abreviation",$_POST['mp_om_res_id'])."@".$_POST['mp_om_so_niveau']."@".$_POST['mp_om_visible'];
	else:
	echo "0@".$_POST['mp_om_id'];
endif;
?>
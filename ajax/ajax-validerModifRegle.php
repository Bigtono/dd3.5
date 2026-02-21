<?php
include_once("../include/dblib.inc.php");

$r=$_POST['mp_re_id'];

if (!empty($r)):
  if ($r=="n"):
    // création de la regle
    $requete = "INSERT INTO dd_regles (re_nom, re_cr_id, re_texte, re_re_id) VALUES ('".addslashes($_POST['mp_re_nom'])."','".$_POST['mp_re_cr_id']."','".addslashes($_POST['mp_re_texte'])."', '".addslashes($_POST['mp_re_re_id'])."')";
    $resultat = execPDO($requete);
    $r=$db->lastInsertId($resultat);
    else:
    // MAJ de la regle
    $requete = "UPDATE dd_regles
      SET re_nom='".addslashes($_POST['mp_re_nom']).
      "', re_cr_id='".$_POST['mp_re_cr_id'].
      "', re_texte='".addslashes($_POST['mp_re_texte']).
      "', re_re_id='".addslashes($_POST['mp_re_re_id']).
      "' WHERE re_id='".$r."'";
    $resultat = execPDO($requete);
  endif;
  // On ajoute les donnnées dans un tableau
  echo $r."@".$requete."@".$_POST['mp_re_nom']."@".libelle("dd_categorie_regle","cr","nom",$_POST['mp_re_cr_id']);
	else:
	echo "erreur@".$r;
endif;
?>
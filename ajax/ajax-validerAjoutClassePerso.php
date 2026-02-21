<?php
include_once("../include/dblib.inc.php");

// Vérification que la classe n'est pas déjà ajoutée
$requete_verif='SELECT pc_cla_id FROM dd_personnages_classes WHERE pc_cla_id="'.$_POST['classe'].'"';
$result_verif=queryPDO($requete_verif);
$num_rows_verif=$result_verif->rowCount();
if ($num_rows_verif==0):
  // Ajout de la nouvelle classe
  $requete = "INSERT INTO dd_personnages_classes (pc_pe_id, pc_cla_id, pc_niveau) VALUES (".$_POST['perso'].",".$_POST['classe'].",".$_POST['niveau'].")";
  $resultat = execPDO($requete);
  $last = $db->lastInsertId($resultat);
  else:
  $last=0;
endif;

// MAJ de l'affichage des classes
$p=$_POST['perso'];
include('../include/insert/'.$_SESSION['rulesetRep'].'/listeClassesPerso.php');

echo $last."@".$liste."@".$requete_cl;
?>
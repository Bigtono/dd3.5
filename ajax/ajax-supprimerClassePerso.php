<?php
session_start();
include_once("../include/dblib.inc.php");
include_once("../include/diverslib.inc.php");

$requete = "DELETE FROM dd_personnages_classes WHERE pc_id='".$_POST['classe']."'";
$resultat = execPDO($requete);

// MAJ de l'affichage des classes
$p=$_POST['perso'];
include('../include/insert/'.$_SESSION['rulesetRep'].'/listeClassesPerso.php');

echo $p."@".$liste."@".$requete_cl;

?>

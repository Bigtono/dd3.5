<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$eqt=$_POST['eqt'];
$p=$_POST['perso'];

// Suppression de l'équipement
$requete='DELETE FROM dd_personnages_objets_magiques WHERE pom_id="'.$eqt.'"';
$resultat=execPDO($requete);
$nouvelle_dotation=0;

// actualisation de la liste des équipements
include('../include/insert/'.$_SESSION['rulesetRep'].'/listeEqt.php');

// On ajoute les donnnées dans un tableau
echo $eqt."@".$listeEqt."@".$p."@-";
?>
<?php
session_start();
error_reporting(0);

include_once("../include/dblib.inc.php");

$j=$_POST['j_id'];

// suppression du joueur
$requete1 = 'DELETE FROM joueurs WHERE j_id="'.$j.'"';
$resultat = execPDO($requete1);

// on génère à nouveau la liste des combattants
include("../include/insert/'.$_SESSION['rulesetRep'].'/listeJoueurs.php");

echo $j."@".$liste;
?>

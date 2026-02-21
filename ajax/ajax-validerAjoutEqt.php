<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$p=$_POST['perso'];
$om=$_POST['eqt'];
$mod=$_POST['mod'];
$sort_om=$_POST['sort'];
  
// ajout de l'équipement
$requete='INSERT INTO dd_personnages_objets_magiques (pom_om_id, pom_pe_id, pom_modificateur, pom_so_id) VALUES ("'.$om.'","'.$p.'","'.$mod.'","'.$sort_om.'")';
$resultat=execPDO($requete);
$eqt=lastID('dd_personnages_objets_magiques', 'pom');
$msg="";
// actualisation de la liste des équipements
include('../include/insert/'.$_SESSION['rulesetRep'].'/listeEqt.php');

echo $eqt."@".$listeEqt."@".$p."@".$mod."@".$sort_om;
?>
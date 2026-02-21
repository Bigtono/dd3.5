<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$nom=$_POST['mp_gr_nom'];
$classe=$_POST['mp_gr_cla_id'];
$perso=$_POST['mp_gr_pe_id'];
$format=$_POST['mp_gr_grf_id'];

// ajout du grimoire
$requete='INSERT INTO dd_grimoires (gr_nom, gr_cla_id, gr_pe_id, gr_grf_id) VALUES ("'.$nom.'","'.$classe.'","'.$perso.'","'.$format.'")';
$resultat=execPDO($requete);
$gr=lastID('dd_grimoires','gr');

// actualisation de la liste des équipements
$result='';

// On ajoute les donnnées dans un tableau
echo $gr."@".$result;
?>
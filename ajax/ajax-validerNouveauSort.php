<?php
include_once("../include/dblib.inc.php");

$nom=addslashes($_POST["nom"]);
$college=addslashes($_POST["college"]);
$effet=addslashes($_POST["effet"]);
$branche=addslashes($_POST["branche"]);
$portee=addslashes($_POST["portee"]);
$cible=addslashes($_POST["cible"]);
$duree=addslashes($_POST["duree"]);
$texte=addslashes($_POST["texte"]);
$pretre=addslashes($_POST["pretre"]);
$mage=addslashes($_POST["mage"]);
$barde=addslashes($_POST["barde"]);
$druide=addslashes($_POST["druide"]);
$rodeur=addslashes($_POST["rodeur"]);
$paladin=addslashes($_POST["paladin"]);
$vocal_sort=addslashes($_POST["vocal_sort"]);
$gestuel_sort=addslashes($_POST["gestuel_sort"]);
$materiel_sort=addslashes($_POST["materiel_sort"]);	 
$focalisateur=addslashes($_POST["focalisateur"]);
$focalisateur_divin=addslashes($_POST["focalisateur_divin"]);
$duree_incantation=addslashes($_POST["duree_incantation"]);
$resistance_sort=addslashes($_POST["resistance_sort"]);
$source_sort=addslashes($_POST["source_sort"]);
$page_sort=addslashes($_POST["page_sort"]);
$jet_sauv_sort=addslashes($_POST["jet_sauv_sort"]);


/*
acide_registre
bien_registre
chaos_registre
electricite_registre
feu_registre
force_registre
froid_registre
language_registre
loi_registre
lumiere_registre
mal_registre
mental_registre
mort_registre
obscurite_registre
son_registre
teleportation_registre
terreur_registre 	
*/

// Requête SQL
$requete = "INSERT INTO dd_sorts (
nom_sort,
college_sort,
description_courte,
branche_sort,
portee_sort,
cible_sort,
duree_sort,
text_sort,
pretre,
mage,
barde,
druide,
rodeur,
paladin,
vocal_sort,
gestuel_sort,
materiel_sort,
focalisateur,
focalisateur_divin,
duree_incantation,
resistance_sort,
source_sort,
page_sort,
jet_sauv_sort)
VALUES ('".
	 utf8_decode($nom)."','".
	 utf8_decode($college)."','".
	 utf8_decode($effet)."','".
	 utf8_decode($branche)."','".
	 utf8_decode($portee)."','".
	 utf8_decode($cible)."','".
	 utf8_decode($duree)."','".
	 utf8_decode($texte)."','".
	 utf8_decode($pretre)."','".
	 utf8_decode($mage)."','".
	 utf8_decode($barde)."','".
	 utf8_decode($druide)."','".
	 utf8_decode($rodeur)."','".
	 utf8_decode($paladin)."','".
	 utf8_decode($vocal_sort)."','".
	 utf8_decode($gestuel_sort)."','".
 	 utf8_decode($materiel_sort)."','".	 
	 utf8_decode($focalisateur)."','".
	 utf8_decode($focalisateur_divin)."','".
	 utf8_decode($duree_incantation)."','".
	 utf8_decode($resistance_sort)."','".
	 utf8_decode($source_sort)."','".
	 utf8_decode($page_sort)."','".
	 utf8_decode($jet_sauv_sort)."')";
	 
 // Exécution de la requête SQL
  $resultat = execPDO($requete);
	$last = $db->lastInsertId($resultat); 
  // On ajoute les donnnées dans un tableau
  echo $last."@".$requete;

?>
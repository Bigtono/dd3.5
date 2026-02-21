<?php
if(isset($_POST['sort'])):
  // Mot tapé par l'utilisateur
  $q = $_POST['sort'];
 
	// remplissage du tableau des possibilités
	$user='root';
	$pass='';
	$dsn='mysql:host=localhost;dbname=kalimshar';

	try{
		$dbh=new PDO($dsn, $user,$pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		die("Erreur ! : ".$e->getMessage());
	}
 
  // Requête SQL
  $requete = "SELECT * FROM listesort WHERE id_sort='". $q ."'";

  // Exécution de la requête SQL
  $resultat = $dbh->query($requete) or die(print_r($bdd->errorInfo()));
 
  // On parcourt les résultats de la requête SQL
  $donnees = $resultat->fetch(PDO::FETCH_ASSOC);
  // On ajoute les données dans un tableau
  echo $donnees['nom_sort'];
	else:
	echo "prout";
endif;
?>
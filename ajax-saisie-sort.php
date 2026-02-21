<?php
if(isset($_GET['query'])) {
  // Mot tapé par l'utilisateur
  $q = htmlentities($_GET['query']);
 
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
  $requete = "SELECT * FROM listesort WHERE nom_sort LIKE '". $q ."%' LIMIT 0, 10";

  // Exécution de la requête SQL
  $resultat = $dbh->query($requete) or die(print_r($bdd->errorInfo()));
 
  // On parcourt les résultats de la requête SQL
  while($donnees = $resultat->fetch(PDO::FETCH_ASSOC)) {
	  // On ajoute les données dans un tableau
    $suggestions['suggestions'][] = $donnees['nom_sort'];
  }
 
  // On renvoie le données au format JSON pour le plugin
  echo json_encode($suggestions);
} else {
	echo "prout";
}
?>
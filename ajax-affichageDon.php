<?php
if(isset($_POST['don'])):
  // Mot tapé par l'utilisateur
  $q = $_POST['don'];
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
  $requete = "SELECT * FROM table_don WHERE id_don='". $q ."'";
  // Exécution de la requête SQL
  $resultat = $dbh->query($requete) or die(print_r($bdd->errorInfo()));
  // On parcourt les résultats de la requête SQL
  $donnees = $resultat->fetch(PDO::FETCH_ASSOC);
  // On ajoute les données dans un tableau
  echo $donnees['id_don'].":".stripslashes($donnees['texte_don']);
	else:
	echo "prout";
endif;
?>
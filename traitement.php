<?
session_start();
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>
<?php
/*Ouverture du fichier en lecture seule*/
$handle = fopen('spells.sql', 'r');
/*Si on a réussi a ouvrir le fichier*/
if ($handle):
	$i=0; //nb de lignes lues
	$step=0; // étape de traitement d'un sort. REmis a zéro a chaque fois que la chaîne "INSERT" est détectée
	$sort=""; // variable contenant le descriptif total du sort en cours de traitement
	$traitement=0;
	/*Tant que l'on est pas a la fin du fichier*/
	while (!feof($handle)):
		/*On lit la ligne courante*/
		$buffer = fgets($handle);
		/* on traite */
		switch ($step):
			case 0:
				// recherche du début du sort
				$pos=strpos($buffer,"VALUES (");
				if ($pos):
					$step=1; // le début du sort a été trouvé, on incrémente $step a 1
					// vérification que la fin du sort ne figure pas dans la meme ligne
					$pos2=strpos($buffer,"');");
					if ($pos2):
						//echo "<p><span style='color:red'>".$pos2." / ".strlen($buffer)."</span></p>";
						$sort=substr($buffer,$pos+8,-4);
						$traitement=1;
						else:
						$sort=substr($buffer,$pos+8);
					endif;
				endif;
				break;
			case 1:
				// recherche de la fin du sort
				$pos=strpos($buffer,"');");
				if ($pos):
					// vérification que le début du sort suivant ne figure pas dans la meme chaine
					$pos2=strpos($buffer,"INSERT");
					if ($pos2) echo "<span style='color:red'>ERREUR</span>";
					// traitement du contenu
					$sort.=substr($buffer,0,$pos+1);
					$traitement=2;
					else:
					$sort.=$buffer;
				endif;
				break;
		endswitch;

		// On traite le sort compilé
		if ($traitement>0):
			echo "<p>(".$i.")</p>";
			if ($traitement==1):
				echo "<p><span style='color:blue'>[A]</span> ".$sort."</p>";
				else:
				echo "<p>".$sort."</p>";
			endif;
			// traitement
			// modifier le séparateur de champ par un point-virgule
			$sort=str_replace("NULL","''",$sort,$count);
			$sort2=str_replace("','","'¤'",$sort,$count);
			// éclater les valeurs dans un tableau
			$sort3=explode("¤",$sort2);
			if (count($sort3)!=21) echo "<p style='color:red'>[ERREUR]</p>";
			// localiser les chaines '' et les remplacer par \'
			$sql="INSERT INTO spell VALUES(";
			foreach($sort3 as $key=>$value):
				// formatage des données
				$value=substr($value,1,-1);
				$value=utf8_decode(addslashes(str_replace("''","'",$value)));
				$value=str_replace("?","\'",$value,$count);
				if ($key<20):
					$sql.="'".$value."',";
					else:
					$sql.="'".$value."')";
				endif;
			endforeach;
			// afficher le résultat du traitement
			echo "<p style='color:green'>".$sql."</p>";
			// ajout du sort dans la table spells
			// Exécution de la requête SQL
		  $resultat = execPDO($sql);
			// initialisation de la recherche suivante
			$sort="";
			$step=0;
			$traitement=0;
		endif;	
		$i++;		
	endwhile;
	
	/*On ferme le fichier*/
	fclose($handle);
endif;
?>
<body>
</body>
</html>
<?
session_start();
include_once("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<html>
<head>
<? include("include/header.php"); ?>
<script type='text/javascript' src='js/moncode-dons.js'></script>
<script type="text/javascript">
	var nombreOnglets = 26;
  function changeOnglet(numero)
  	{
    	// On commence par tout masquer
      for (var i = 1; i < nombreOnglets+1; i++) {
      	document.getElementById("contenuOnglet" + i).style.display = "none";
				document.getElementById(i).style.color = "black";
			}
        // Puis on affiche celui qui a été sélectionné
        document.getElementById("contenuOnglet" + numero).style.display = "block";
				document.getElementById(numero).style.color = "red";
    }
</script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<DIV id="page">
	<? include("include/head.php"); ?>
	<? include("include/menu.php"); ?>
  <div id="contenu">

<?
//************************************************************************************************************************
// sélection des dons
//************************************************************************************************************************

?>
	<div class="action">
    Liste des dons [<a href="<? echo $_SERVER['PHP_SELF']; ?>">Tous</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=1"; ?>">G&eacute;n&eacute;ral</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=2"; ?>">m&eacute;tamagie</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=3"; ?>">Cr&eacute;ation d'objets</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=5"; ?>">Divin</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=10"; ?>">R&eacute;gional</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=11"; ?>">Exalt&eacute;</a>]
    [<a href="<? echo $_SERVER['PHP_SELF']."?type=12"; ?>">Mal&eacute;fique</a>]
  </div>

<?

	// menu onglets pour choisir le niveau de sort
		//echo '<font class="textecourant_noir">'.chr(64+$i).'</font><br>';
		// Sélection des dons
		if(isset($_GET["type"])):
      $filtre=' do_type='.$_GET["type"].' AND';
			else:
			$filtre="";
		endif;
    //LEFT(nom_don, 1)='".chr(64+$i).
		$requete='SELECT * FROM dons LEFT JOIN ressources ON do_source=id_livre WHERE '.$filtre.' do_source IN '.$selection.' ORDER BY do_nom';
	  $result=queryPDO($requete);
		$num_rows=$result->rowCount();
		$nbl=0; // nb de sorts dans la ligne
		if ($num_rows > 0):
			// formatage des données
		  echo '  <div id="don'.$don['do_id'].'" class="ligne">';
			echo '    <div class="don-nom gras">Nom</div>';
			echo '    <div class="don-categorie gras">Type</div>';
			echo '    <div class="don-description-courte gras">R&eacute;sum&eacute;</div>';
      echo '    <div class="don-source gras">Source</div>';
			echo '  </div>';    
			echo '<div id="liste-dons">';
      // entête
			while($don = $result->fetch(PDO::FETCH_ASSOC)):
				// préparation des données
				if (strlen($don['descrip_courte'])>0):
					$description=$don['descrip_courte'];
					else:
					$description="&nbsp";
				endif;
				// création de la ligne du don
				echo '<div id="don'.$don['do_id'].'" class="desc">';
        echo '  <div class="ligne" onclick="afficherDon('.$don['do_id'].')">';
				echo '    <div class="don-nom">'.stripslashes($don['do_nom']).' ('.$don['do_id'].')</div>';
				echo '    <div class="don-categorie">'.dataDon($don['do_type']).'</div>';
				echo '    <div class="don-description-courte">'.stripslashes($don['do_resume']).'</div>';
        echo '    <div class="don-source" title="'.$don['titre_livre'].'">'.stripslashes($don['abreviation_livre']).'</div>';
				echo '  </div>';
				// création de la ligne description
				echo '  <div id="descDon'.$don['do_id'].'" class="don-description"></div>';
        echo '</div>';
			endwhile;
			echo '</div>'; // liste-dons
		endif;

?>
  </div> <!-- contenu --->
</div>
</body>
</html>
<?
session_start();
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


?>
<!doctype html>
<html>
<head>
<TITLE>BIBLIOTHEQUE DE TONO</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META name="description" content="Impression des fiches de sort">
<META name="keywords" content="Impression fiche sort sorts sortilèges sortilege sortileges DD3 DnD d20 D20">
<!-- Feuilles de styles -->
<link href="include/_styles_.css" rel="stylesheet" type="text/css">
<!--- scripts --->
<script type="text/javascript">
	var nombreOnglets = 10;
  function changeOnglet(numero)
  	{
    	// On commence par tout masquer
      for (var i = 0; i < nombreOnglets; i++) {
      	document.getElementById("contenuOnglet" + i).style.display = "none";
				document.getElementById("niveau" + i).style.color = "black";
			}
        // Puis on affiche celui qui a été sélectionné
        document.getElementById("contenuOnglet" + numero).style.display = "block";
				document.getElementById("niveau" + numero).style.color = "red";
    }
</script>
</head>

<body id="sortilege">
	<div id="content-sort">
		<div id="fermer">
	   	<a href="javascript:window.close();">[X]</A>
	   </div>
<?
//************************************************************************************************************************
// Recherche et affichage du détail d'un sor
//************************************************************************************************************************

	// recherche du sort
	$sql="SELECT * FROM listesort WHERE id_sort='".$_GET['idsort']."'";
	$result = getRowSpec($sql);
	$num_rows = mysql_num_rows( $result );
	if ($num_rows > 0):
		$sort = mysql_fetch_array ($result);
		// collège et branche
		$college_branche = $sort[college_sort];
		if ($sort[branche_sort] != "") $college_branche .= " ($sort[branche_sort])";		
		// catégorie de lanceur de sorts
		$lanceur="";
		$cpt=0;
		if (is_numeric($sort[pretre])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "P".$sort[pretre];
			$cpt++;
		endif;
		if (is_numeric($sort[mage])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "M".$sort[mage];
			$cpt++;
		endif;
		if (is_numeric($sort[paladin])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "Pa".$sort[palaidn];
			$cpt++;
		endif;
		if (is_numeric($sort[rodeur])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "R".$sort[rodeur];
			$cpt++;
		endif;
		if (is_numeric($sort[barde])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "B".$sort[barde];
			$cpt++;
		endif;
		if (is_numeric($sort[druide])):
			if ($cpt!=0) $lanceur .= ",";
			$lanceur .= "D".$sort[druide];
			$cpt++;
		endif;
			// composante
			$composante="";
			$cpt=0;
			if ($sort[vocal_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "V"; 
				$cpt++;
			endif;
			if ($sort[gestuel_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "G"; 
				$cpt++;				
			endif;
			if ($sort[materiel_sort]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "M"; 
				$cpt++;				
			endif;
			if ($sort[focalisateur]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "F"; 
				$cpt++;				
			endif;
			if ($sort[focalisateur_divin]==1):
				if ($cpt!=0) $composante .= ",";
				$composante .= "FD"; 
				$cpt++;				
			endif;
			// RM
			$rm="non";
			if ($sort[resistance_sort]==1) $rm="oui";			
	
?>
		<div id="sort">
    
			<div id="nom"><? echo $sort['nom_sort'];?></div>
      <div id="college"><? echo $college_branche;?></div>
      
	  	<div class="gauche">      
	      <span>Lanceur :</span> <? echo $lanceur; ?><br>
	      <span>Portée :</span> <? echo $sort['portee_sort']; ?><br>
	      <span>Cible :</span> <? echo $sort['cible_sort']; ?><br>
	      <span>Durée :</span> <? echo $sort['duree_sort']; ?><br>
      </div>
      <div class="droite">
	      <span>Composantes :</span> <? echo $composante ?><br>
	      <span>Incantation :</span> <? echo $sort['duree_incantation']; ?><br>
	      <span>RM :</span> <? echo $rm; ?><br>
	      <span>JS :</span> <? echo $sort['jet_sauv_sort']; ?>
    	</div>  
      
      <div id="effet"><span>Effet :</span> <? echo $sort['effet_sort']; ?></div>
      
      <div id="texte"><span>Description du sort :</span><br><? echo $sort['text_sort']; ?></div>
      
	  </div>
<?
	endif;
?>
	</div>
</body>
</html>
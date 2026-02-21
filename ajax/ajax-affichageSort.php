<?php
include("../include/session.php");
include_once("../include/dblib.inc.php");

$s = $_POST['sort'];

if(!empty($s)):
  $requete = "SELECT * FROM dd_sorts WHERE so_id='".$s."'";
  $resultat = queryPDO($requete);
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  
  include($_SESSION['rulesetRep'].'/affichageSort.php');

  // On ajoute les donnnées dans un tableau
  if ($nomFichier=="sort.php"): // cas spécifique ou le fichier est utiliser en dehors d'un appel ajax dans la page sort.php
    echo $result;
    else:
    echo $dn['so_id']."@".$result."@".$nomFichier;
  endif;
	else:
	echo '<div class="nodata">le sort demandé n\'est pas disponible</div>';
endif;
?>
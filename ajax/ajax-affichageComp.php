<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['comp'])):
  $q = $_POST['comp'];
  $requete = "SELECT * FROM dd_competences WHERE comp_id='".$q."'";
  $resultat = queryPDO($requete);
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);

  $result='<div id="competence" class="affichage">';
  $result.='  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien"><a href="competence-modifier.php?comp='.$q.'"><i class="fa fa-pencil"></i></a></div></div>';
  
  $result.='  <div class="nom_objet">'.stripslashes($dn['comp_nom']).'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte">'.stripslashes($dn['comp_description']).'</div>';
  $result.='  </div>';
  $result.='</div>';
  // On ajoute les donnnées dans un tableau
  echo $dn['comp_id']."@".$result;
	else:
	echo "prout";
endif;
?>
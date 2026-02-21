<?php
include_once("../include/dblib.inc.php");

$j=$_POST['j'];

if(!empty($j)):
  // affichage du contenu
  $result='<div class="detail">';
  $result.='  <input  type="hidden" id="mp_j_id" value="'.$j.'">';
  $result.='  <h1>Suppression d\'un joueur</h1>';
  $result.='  <div class="texte">Confirmez-vous la suppression du joueur suivant ?</div>';
  $result.='  <div class="texte gras aucentre mt10 mb10">'.libelle("joueurs", "j", "nom", $j).'</div>';
  $result.='  <div class="mt20">';
  $result.='    <button class="btNoir mr10" onClick="validerSupprimerJoueur()">Valider</button>';
  $result.='    <button class="btNoir" onClick="annulerPageModif()">Annuler</button>';
  $result.='  <div>';
  $result.='</div>';
  // On ajoute les donnnées dans un tableau
  echo $j."@".$result;
	else:
	echo $j."@Erreur";
endif;

?>
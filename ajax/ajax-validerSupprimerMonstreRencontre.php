<?php
include_once("../include/dblib.inc.php");

$re = $_POST['rencontre'];
$mo = $_POST['monstre'];

if(!empty($mo)):
  // affichage du contenu
  $result='<div class="detail">';
  $result.='  <input  type="hidden" id="mp_rem_re_id" value="'.$re.'">';
  $result.='  <input  type="hidden" id="mp_rem_mo_id" value="'.$mo.'">';
  $result.='  <h1>Suppression d\'un Monstre</h1>';
  $result.='  <div class="texte">Confirmez-vous la suppression du montres suivant de la rencontre ?</div>';
  $result.='  <div class="texte gras aucentre mt10 mb10">'.libelle("dd_monstres", "mo", "nom", $mo).'</div>';
  $result.='  <div class="mt20">';
  $result.='    <button class="btNoir mr10" onClick="validerSupprimerMonstreRencontre()">Valider</button>';
  $result.='    <button class="btNoir" onClick="annulerPageModif()">Annuler</button>';
  $result.='  <div>';
  $result.='</div>';
  // On ajoute les donnnées dans un tableau
  echo $re."@".$result;
	else:
	echo $re."@Erreur";
endif;
?>
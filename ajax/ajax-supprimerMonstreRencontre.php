<?php
include_once("../include/dblib.inc.php");

$rem = $_POST['rem'];
$re = libelle("dd_rencontres_monstres","rem", "re_id", $rem);
$mo = libelle("dd_rencontres_monstres","rem", "mo_id", $rem);

if(!empty($rem)):
  // affichage du contenu
  $result='<div class="detail w600">';
  $result.='  <input  type="hidden" id="mp_rem_id" value="'.$rem.'">';
  $result.='  <input  type="hidden" id="mp_rem_re_id" value="'.$re.'">';
  $result.='  <input  type="hidden" id="mp_rem_mo_id" value="'.$mo.'">';
  $result.='  <h1>Suppression d\'un Monstre</h1>';
  $result.='  <div class="texte">Confirmez-vous la suppression du montre suivant de la rencontre ?</div>';
  $result.='  <div class="texte gras aucentre mt10 mb10">'.libelle("dd_monstres", "mo", "nom", $mo).'</div>';
  $result.='  <div class="mt20">';
  $result.='    <button class="btNoir mr10" onClick="validerSupprimerMonstreRencontre()">Valider</button>';
  $result.='    <button class="btNoir" onClick="annulerPageModif()">Annuler</button>';
  $result.='  <div>';
  $result.='</div>';
  // On ajoute les donnnées dans un tableau
  echo $rem."@".$result;
	else:
	echo $rem."@Erreur";
endif;
?>
<?php
include_once("../include/dblib.inc.php");
include_once("../include/diverslib.inc.php");

// recherche des personnages
$classes='<select id="mp_gr_cla_id">'.optionList("dd_classes", "cla", "nom").'</select>';
$personnages='<select id="mp_gr_pe_id">'.optionList("dd_personnages", "pe", "nom").'</select>';
$format='<select id="mp_gr_grf_id">'.optionList("dd_grimoires_format", "grf", "nom").'</select>';

//**********************************************************************
// mise en forme du contenu
$result='<div class="affichage">';
$result.='  <div><input id="mp_gr_nom" class="input_nom" value=""></div>';
$result.='  <div class="ligne><div class="label w90">Classe</div><div>'.$classes.'</div>';
$result.='  <div class="ligne><div class="label w90">Personnage</div><div>'.$personnages.'</div>';
$result.='  <div class="ligne><div class="label w90">Format</div><div>'.$format.'</div>';
$result.='  <input class="bouton" type="button" name="validAjoutGrimoire" value="Ajouter" onClick="validerAjoutGrimoire()">';
$result.='  <input class="bouton ml15" type="button" name="annuleModifNote" value="Annuler" onClick="annulerPageModif()"></div>';
$result.='</div>'; 
// On ajoute les donnnées dans un tableau
echo "1@".$result;
?>
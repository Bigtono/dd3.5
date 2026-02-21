<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['comp'])):
  $q = $_POST['comp'];
  if ($q=="n"):
    else:
    $requete = "SELECT * FROM dd_competences WHERE comp_id='".$q."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $comp_id=$dn['comp_id'];
    $comp_nom=$dn['comp_nom'];  
    $comp_formationNecessaire=$dn['comp_formationNecessaire'];
    $comp_malusArmure=$dn['comp_malusArmure'];
    $comp_caracteristique=$dn['comp_caracteristique'];
    $comp_description=$dn['comp_description'];
    $comp_action=$dn['comp_action'];
    $comp_nouvelleTentative=$dn['comp_nouvelleTentative'];
    $comp_special=$dn['comp_special'];
  endif;

  //**********************************************************************
  // mise en forme du contenu
  $result='<div class="formulaire">';
  $result.='<input  type="hidden" id="mp_comp_id" value="'.$q.'">';
  $result.='<div><input id="mp_comp_nom" class="input_nom" value="'.stripslashes($comp_nom).'"> ('.$q.')</div>';
  $result.='<div class="gras">Description</div><div><textarea id="mp_comp_description" name="mp_comp_description" class="input_texte">'.stripslashes($comp_description).'</textarea></div>';
  $result.="<script>CKEDITOR.replace('mp_comp_description');</script>";
  $result.='<div class="gras">Action</div><div><textarea id="mp_comp_action" name="mp_comp_action" class="input_texte">'.stripslashes($comp_action).'</textarea></div>';
  $result.="<script>CKEDITOR.replace('mp_comp_action');</script>";
  $result.='<div class="gras">Spécial.</div><div><textarea id="mp_comp_special" name="mp_comp_special" class="input_texte">'.stripslashes($comp_special).'</textarea></div>';
  $result.="<script>CKEDITOR.replace('mp_comp_special');</script>";
  if ($q=='n'):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
  endif;
  $result.='<input class="bouton" type="button" name="validModifSort" id="validModifSort" value="'.$libelle.'" onClick="validerModifSort()">';
  $result.='&nbsp; <input class="bouton" type="button" name="annuleModifSort" id="annuleModifSort" value="Annuler" onClick="annulePageModif()"></div>';
  $result.='</div>'; 
		
  // On ajoute les donnnées dans un tableau
  echo $dn['mp_comp_id']."@".$result;
	else:
	echo "prout";
endif;

?>
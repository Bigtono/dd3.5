<?php
include_once("../include/dblib.inc.php");

$v = $_POST['var'];

if(!empty($v)):
  if ($v=="n"):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
    $requete = "SELECT * FROM dd_variables WHERE var_id='".$v."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  endif;
  // mise en forme du contenu
  $categorie='<select id="mp_var_cat" name="mp_var_cat">'.OptionListeCatVar($dn['var_cat']).'</select>';
  //**********************************************************************
  // affichage du contenu
  $result='<div id="regle" class="affichage">';
  $result.='  <input  type="hidden" id="mp_var_id" value="'.$v.'">';
  $result.='  <div><input id="mp_var_valeur" class="input_nom" value="'.stripslashes($dn['var_valeur']).'"></div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Catégorie</div>'.$categorie.'</div>';
  $result.='  <div><input id="mp_var_description" class="input_description" value="'.stripslashes($dn['var_description']).'"></div>';
  $result.='  <input class="bouton" type="button" name="validModifVariable" id="validModifVariable" value="'.$libelle.'" onClick="validerModifVariable()">';
  $result.='  <input class="bouton ml15" type="button" name="annuleModifVariable" id="annuleModifVariable" value="Annuler" onClick="annulerPageModif()"></div>';
  $result.='</div>'; 
  // On ajoute les données dans un tableau
  echo $v."@".$result;
	else:
	echo "0@Erreur";
endif;

?>
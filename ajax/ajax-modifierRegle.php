<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['regle'])):
  $q = $_POST['regle'];
  if ($q=="n"):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
    $requete = "SELECT * FROM dd_regles WHERE re_id='".$q."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  endif;
  // mise en forme du contenu
  $categorie='<select id="mp_re_cr_id" name="mp_re_cr_id">'.optionList("dd_categorie_regle", "cr","nom", $dn['re_cr_id']).'</select>';
  $parent='<select id="mp_re_re_id" name="mp_re_re_id">'.optionList("dd_regles", "re","nom", $dn['re_re_id']).'</select>';
  //**********************************************************************
  // affichage du contenu
  $result='<div id="regle" class="affichage">';
  $result.='  <input  type="hidden" id="mp_re_id" value="'.$q.'">';
  $result.='  <div><input id="mp_re_nom" class="input_nom" value="'.stripslashes($dn['re_nom']).'"> ('.$q.')</div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Catégorie</div>'.$categorie.'</div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Parent</div>'.$parent.'</div>';
  $result.='  <div class="label">Description</div><div><textarea id="mp_re_texte" class="input_texte">'.stripslashes($dn['re_texte']).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_re_texte');</script>";
  $result.='  <input class="bouton" type="button" name="validModifRegle" id="validModifRegle" value="'.$libelle.'" onClick="validerModifRegle()">';
  $result.='  <input class="bouton ml15" type="button" name="annuleModifRegle" id="annuleModifRegle" value="Annuler" onClick="annulerPageModif()"></div>';
  $result.='</div>'; 
  // On ajoute les reglennées dans un tableau
  echo $dn['re_id']."@".$result;
	else:
	echo "0@Erreur";
endif;

?>
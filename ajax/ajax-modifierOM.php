<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/affichageSelectionSources.php"); 

if(isset($_POST['om'])):
  $q = $_POST['om'];
  if ($q=="n"):
    else:
    $requete = "SELECT * FROM dd_objets_magiques WHERE om_id='".$q."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $om_id=$dn['om_id'];
    $om_nom=$dn['om_nom'];  
    $om_categorie=$dn['om_com_id'];  
    $om_format=$dn['om_fom_id'];  
    $om_sort=$dn['om_so_id'];
    $om_sort_niveau=$dn['om_so_niveau'];
    $om_modificateurs=$dn['om_modificateurs'];
    $om_variantes=$dn['om_variantes'];
    $om_description=$dn['om_description'];
    $om_source=$dn['om_res_id'];
    $om_visible=$dn['om_visible'];
  endif;
  // mise en forme du contenu
  if ($q=='n'):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
  endif;
  if ($_POST['type']!='Tout'&& $q=="n")  $om_categorie=$_POST['type']; // si création d'un objet, on charge la catégorie d'objet actuellement affiché à l'écran

  $source='<select id="mp_om_res_id">'.optionList("dd_ressources", "res","nom", $om_source,"res_id IN ".$selection).'</select>';
  $categorie='<select id="mp_om_com_id">'.optionList("dd_categorie_objet_magique", "com","nom", $om_categorie).'</select>';
  $format='<select id="mp_om_fom_id">'.optionList("dd_format_objet_magique", "fom","nom", $om_format).'</select>';
  $sort='<select id="mp_om_so_id">'.listeSorts($om_sort, $selection).'</select>';
  $sort_niveau='<select id="mp_om_so_niveau">'.optionListInt(0, 20, $om_sort_niveau).'</select>';
  $modificateurs='<select id="mp_om_modificateurs">'.optionListInt(0, 5, $om_modificateurs).'</select>';
  $visible='<select id="mp_om_visible">'.optionListOuiNon($om_visible).'</select>';

  //**********************************************************************
  // affichage du contenu
  $result='<div id="om" class="formulaire">';
  $result.='  <input  type="hidden" id="mp_om_id" value="'.$q.'">';
  $result.='  <div></div>';
  $result.='  <div class="ligne"><div class="label">Nom</div><input id="mp_om_nom" class="input_nom" value="'.stripslashes($om_nom).'"></div>';
  $result.='  <div class="ligne mt5"><div class="label">Catégorie</div>'.$categorie.'</div>';
  $result.='  <div class="ligne mt5"><div class="label">Modificateurs <sup>*</sup></div>'.$modificateurs.'</div>';
  $result.='  <div class="ligne"><div class="label">Variantes <sup>**</sup></div><input id="mp_om_variantes" class="input_nom" value="'.stripslashes($om_variantes).'"></div>';
  $result.='    <div><sup>*</sup><span class="precision"> (L\'objet se décline en plusieurs versions avec des modificateurs différents)</span></div>';
  $result.='    <div><sup>**</sup><span class="precision"> (L\'objet se décline en plusieurs variantes (mineur, majeur etc... Voir description))</span></div>';
  $result.='  <div class="trait-gauche">';
  $result.='    <div class="mt5"><b>Uniquement pour les potions, parchemins et baguettes<b></div>';
  $result.='    <div class="ligne"><div class="label">Format</div>'.$format.'</div>';
  $result.='    <div class="ligne"><div class="label">Sort reproduit</div>'.$sort.'</div>';
  $result.='    <div class="ligne"><div class="label">NLS <sup>***</sup></div>'.$sort_niveau.'</div>';
  $result.='    <div><sup>***</sup><span class="precision"> (Uniquement si le NLS est supérieur au niveau minimum pour lancer le sort concerné)</span></div>';
  $result.='  </div>';

  $result.='  <div class="ligne mt5"><div class="label">Visible</div>'.$visible.'</div>';

  $result.='  <div class="label mt10">Description</div><div><textarea id="mp_om_description" name="mp_om_description" class="input_texte">'.stripslashes($om_description).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_om_description');</script>";

  $result.='  <div class="ligne mt10"><div class="label">Source</div>'.$source.'</div>';
  
  if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $result.='<div>Sources : '.$selection.'</div>';
  $result.='  <div class="ligneBouton">';
  $result.='    <button class="btNoir" name="validModifDon" id="validModifDon" onClick="validerModifOM()">'.$libelle.'</button>';
  $result.='    <button class="btNoir" name="annuleModifDon" id="annuleModifDon" onClick="annulerPageModif()">Annuler</button>';
  $result.='  </div>'; 
  $result.='</div>'; 
  // On ajoute les donnnées dans un tableau
  echo $dn['om_id']."@".$result;
	else:
	echo "0@Erreur";
endif;

?>
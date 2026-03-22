<?php
include_once("../include/dblib.inc.php");

if (isset($_POST['don'])):
  $q = $_POST['don'];
  if ($q == "n"):
  else:
    $requete = "SELECT * FROM dd_dons WHERE do_id='" . $q . "'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $don_id = $dn['do_id'];
    $don_nom = $dn['do_nom'];
    $don_categorie = $dn['do_dado_id'];
    $don_texte = $dn['do_texte'];
    $don_conditions = $dn['do_conditions'];
    $don_resume = $dn['do_resume'];
    $don_source = $dn['do_res_id'];
    $don_page_source = $dn['do_page_source'];
    $don_version = $dn['do_version'];
  endif;
  // mise en forme du contenu
  if ($q == 'n'):
    $libelle = 'Ajouter';
  else:
    $libelle = 'Modifier';
  endif;

  $source = '<select id="mp_do_res_id">' . optionList("dd_ressources", "res", "nom", $don_source) . '</select>';
  $categorie = '<select id="mp_do_dado_id">' . optionList("dd_data_don", "dado", "nom", $don_categorie) . '</select>';


  //**********************************************************************
  // affichage du contenu
  $result = '<div id="don" class="affichage">';
  $result .= '  <input  type="hidden" id="mp_do_id" value="' . $q . '">';
  $result .= '  <div><input id="mp_do_nom" class="input_nom" value="' . stripslashes($don_nom) . '"> (' . $q . ')</div>';
  $result .= '  <div class="ligne mt10"><div class="label w90">Catégorie</div>' . $categorie . '</div>';

  $result .= '  <div class="label">Description</div><div><textarea id="mp_do_texte" name="mp_do_texte" class="input_texte">' . stripslashes($don_texte) . '</textarea></div>';
  $result .= "  <script>CKEDITOR.replace('mp_do_texte');</script>";

  $result .= '  <div class="label">Résumé</div><input id="mp_do_resume" class="input_resume" value="' . stripslashes($don_resume) . '">';

  $result .= '  <div class="ligne mt10"><div class="label w90">Source</div>' . $source . '</div>';

  $result .= '<div class="ligneBouton">';
  $result .= '  <input class="btNoir" type="button" name="validModifDon" id="validModifDon" value="' . $libelle . '" onClick="validerModifDon()">';
  $result .= '  <input class="btNoir" type="button" name="annuleModifDon" id="annuleModifDon" value="Annuler" onClick="annulerPageModif()"></div>';
  $result .= '</div>';
  // On ajoute les donnnées dans un tableau
  echo $dn['do_id'] . "@" . $result;
else:
  echo "0@Erreur";
endif;

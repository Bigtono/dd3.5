<?php
include_once("../include/dblib.inc.php");


if(isset($_POST['cap'])):
  $q = $_POST['cap'];
  if ($q=="n"):
    $cap_id=0;
    $cap_nom="";  
    $cap_description="";
    else:
    $requete = "SELECT * FROM dd_capacites_speciales WHERE cap_id='". $q ."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $cap_id=$dn['cap_id'];
    $cap_nom=$dn['cap_nom'];  
    $cap_description=$dn['cap_description'];
  endif;

  //**************************************************************************
  // Formatage des données


  //**********************************************************************
  // mise en forme du contenu
  $result='<div id="capacite">';
  $result.='<input  type="hidden" id="mp_cap_id" value="'.$q.'">';
  $result.='  <div><span class="label">Nom</span> <input id="mp_cap_nom" name="mp_cap_nom" class="input_nom" value="'.stripslashes($cap_nom).'"> ('.$q.')</div>';
  $result.='  <div class="gras">Description de la capacit&eacute; </div><div><textarea id="mp_cap_description" name="mp_cap_description" class="input_texte">'.stripslashes($cap_description).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_cap_description');</script>";
    if ($q=='n'):
      $libelle='Ajouter';
      else:
      $libelle='Modifier';
    endif;
    $result.='<input class="bouton" type="button" name="validModifCapacite" id="validModifCapacite" value="'.$libelle.'" onClick="validerModifCapacite()">';
    $result.='&nbsp; <input class="bouton" type="button" name="annuleModifCapacite" id="annuleModifCapacite" value="Annuler" onClick="annulerPageModif()"></div>';
  $result.='</div>'; 
		
  // On ajoute les donnnées dans un tableau
  echo $dn['mp_cap_id']."@".$result;
	else:
	echo "prout";
endif;


/*
  


*/


?>
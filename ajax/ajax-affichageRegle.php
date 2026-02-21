<?php
include_once("../include/dblib.inc.php");
error_reporting(E_ERROR & ~E_WARNING & ~E_NOTICE);

$re = $_POST['regle'];

if(!empty($re)):
  // Requête SQL
  $requete = "SELECT * FROM dd_regles WHERE re_id='".$re."'";	
  $resultat=queryPDO($requete);
	$num_rows=$resultat->rowCount();
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  if ($dn['re_cr_id']!="") $categorie=libelle("dd_categorie_regle","cr","nom",$dn['re_cr_id']);
  // affichage du contenu
  $result='<div id="regle" class="affichage">';
  $result.='  <div class="menu2">';
  $result.='    <div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div>';
  $result.='    <div class="ce lien" onClick="afficherRegle('.$dn['re_re_id'].')"><i class="fa-regular fa-circle-up"></i></div>';
  $result.='    <div class="dr lien" onclick="modifierRegle('.$dn['re_id'].')"><i class="fa fa-pencil"></i></div>';
  $result.='  </div>';
  $result.='  <div class="nom_objet">'.stripslashes($dn['re_nom']).'</div>';
  $result.='  <div class="description">';
  // sommaire
  $requete2="SELECT * FROM dd_regles WHERE re_re_id='".$re."'";
  $resultat2 = queryPDO($requete2);
  $num_rows2 = $resultat2->rowCount();
  if ($num_rows2>0):
    $result.='<fieldset class="sommaire">';
    $result.='<legend>Sommaire</legend>';
    while($dn2 = $resultat2->fetch(PDO::FETCH_ASSOC)):
      $result.='<div onClick="afficherRegle('.$dn2['re_id'].')" class="lien">'.f_nom($dn2['re_nom']).'</div>'; 
    endwhile;
    $result.='</fieldset>';
  endif;
  $result.='  <div class="texte">'.stripslashes($dn['re_texte']).'</div>';
  $result.='  </div>'; // #description
  $result.='</div>'; // #regle

  // On ajoute les données dans un tableau
  echo $dn['re_id']."@".$result;
	else:
	echo $re."@erreur";
endif;
?>


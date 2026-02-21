<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['don'])):
  $q = $_POST['don'];
  // Requête SQL
  $requete = "SELECT * FROM dd_dons WHERE do_id='". $q ."'";	
  $result=queryPDO($requete);
	$num_rows=$result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  if ($dn['do_res_id']!="") $source=libelle("dd_ressources","res","nom",$dn['do_res_id']);
  // affichhage du contenu
  $result='<div id="don" class="affichage">';
  $result.='  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien" onclick="modifierDon('.$dn['do_id'].')"><i class="fa fa-pencil"></i></div></div>';
  $result.='  <div class="nom_objet">'.stripslashes($dn['do_nom']).'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte"><span class="label">Catégorie : </span>'.libelle("dd_data_don", "dado", "nom", $dn['do_dado_id']).'</div>';
  $result.='    <div class="texte">'.stripslashes($dn['do_texte']).'</div>';
  $result.='    <div class="texte"><span class="label">Source :</span> '.$source.'</div>';
  $result.='    <div class="texte"><span class="label">Version :</span> '.stripslashes($dn['do_version']).'</div>';
  $result.='  </div>';
  $result.='</div>';
  // On ajoute les données dans un tableau
  echo $dn['do_id']."@".$result;
	else:
	echo "prout";
endif;
?>
<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

if(isset($_POST['om'])):
  $q = $_POST['om'];
  // Requête SQL
  $requete = "SELECT * FROM dd_objets_magiques WHERE om_id='". $q ."'";	
  $result=queryPDO($requete);
	$num_rows=$result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  include('../include/insert/'.$_SESSION['rulesetRep'].'/descriptionOM.php');
   
  // préparation du contenu
  $nom=stripslashes(ucfirst($dn['om_nom']));
  if ($dn['om_so_niveau']>0) $nom.=' (niveau '.$dn['om_so_niveau'].')';

  // affichhage du contenu
  $result='<div id="don" class="affichage">';
  $result.='  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien" onclick="modifierOM('.$dn['om_id'].')"><i class="fa fa-pencil"></i></div></div>';

  $result.='  <div class="nom_objet">'.$nom.'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte"><span class="label">Catégorie : </span>'.libelle("dd_categorie_objet_magique", "com", "nom",$dn['om_com_id']).'</div>';
  $result.='    <div class="texte">'.$description.'</div>';
  if ($_SESSION['mj']==1 && $_SESSION['debug']==1) $result.='    <div class="texte">Debug :'.$debug.'</div>';
  $result.='    <div class="texte"><span class="label">Source :</span> '.$source.'</div>';
  $result.='  </div>';
  $result.='</div>';
  // On ajoute les données dans un tableau
  echo $dn['om_id']."@".$result;
	else:
	echo "prout";
endif;
?>
<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$r = $_POST['rencontre'];

if(!empty($r)):
  $requete = "SELECT * FROM dd_rencontres WHERE re_id='".$r."'";	
  $result=queryPDO($requete);
	$num_rows=$result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  //if ($dn['do_res_id']!="") $source=libelle("dd_ressources","res","nom",$dn['do_res_id']);
  // affichhage du contenu
  $result='<div id="don" class="affichage">';
  $result.='  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien"></div></div>';
  $result.='  <div class="nom_objet">'.stripslashes($dn['re_nom']).'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte">'.stripslashes($dn['re_description']).'</div>';
  $result.='  </div>';
  $result.='</div>';
  // On ajoute les données dans un tableau
  echo $r."@".$result;
	else:
	echo "$r@erreur";
endif;
?>
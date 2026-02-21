<?php
include_once("../include/dblib.inc.php");
error_reporting(E_ERROR & ~E_WARNING & ~E_NOTICE);

$e = $_POST['eqt'];

if(!empty($e)):
  $requete = 'SELECT * FROM dd_personnages_objets_magiques JOIN dd_objets_magiques ON pom_om_id=om_id WHERE pom_id="'.$e.'"';	
  $resultat=queryPDO($requete);
	$num_rows=$resultat->rowCount();
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  $nom_obj=f_nom($dn['om_nom']);
  // Gestion des noms de baguettes, potions et parchemin
  if (!empty($dn['pom_so_id']) && $dn['pom_so_id']>0):
    $sort=libelle("dd_sorts", "so", "nom", $dn['pom_so_id']);          
    $premiere_lettre = mb_strtolower(mb_substr($sort, 0, 1));
    // Si le mot commence par une voyelle ou un "h" muet → on met "d'", sinon "de"
    if (in_array($premiere_lettre, ['a', 'e', 'i', 'o', 'u', 'y', 'h', 'é', 'É'])): 
      $nom_obj.=" d'".$sort;
      else: 
      $nom_obj.=" de ".$sort;
    endif;
  endif;
  // Gestion des nom d'armes et objets avec un modificateur
  if (!empty($dn['pom_modificateur']) && $dn['pom_modificateur']!=0):
    if ($dn['pom_modificateur']>0):
      $nom_obj.=" +".$dn['pom_modificateur'];
      else:
      $nom_obj.=" ".$dn['pom_modificateur'];
    endif;
  endif;
  // mise en forme de la description
  if (!empty($dn['pom_so_id']) && $dn['pom_so_id']!="") $dn['om_so_id']=$dn['pom_so_id'];
  if (!empty($dn['pom_modificateur']) && $dn['pom_modificateur']!="") $dn['om_modificateur']=$dn['pom_modificateur'];
  include('../include/insert/'.$_SESSION['rulesetRep'].'/descriptionOM.php');

  // affichage du contenu
  $result='<div id="om" class="affichage">';
  $result.='  <div class="menu2">';
  $result.='    <div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div>';
  $result.='    <div class="ce"></div>';
  $result.='    <div class="dr lien" onclick="modifierEqt('.$dn['re_id'].')"><i class="fa fa-pencil"></i></div>';
  $result.='  </div>';
  $result.='  <div class="nom_objet">'.$nom_obj.'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte">'.$description.'</div>';
  $result.='  </div>'; // #description
  $result.='</div>'; // #regle

  // On ajoute les données dans un tableau
  echo $e."@".$result;
	else:
	echo $e."@erreur...";
endif;
?>


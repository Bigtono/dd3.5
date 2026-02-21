<?php
/**************************************************************************
 Affichage des objets magiques pour ajout à un personnage
 * Le script ne permet pas de les retirer
**************************************************************************/

include_once("../include/dblib.inc.php");
error_reporting(E_ERROR & ~E_WARNING & ~E_NOTICE);

$p = $_POST['perso'];

if(!empty($p)):

  // affichage du contenu
  $result='<div id="ajout_equipement">';
  $result.='  <div class="adroite" onClick="annulerPageModif()"><i class="fa fa-close"></i></div>';
  $result.='  <div class="titre">Objets magiques</div>';
  $requete='SELECT * FROM dd_categorie_objet_magique WHERE com_id NOT IN (29,30) ORDER BY com_nom';
  $resultat=queryPDO($requete);
  $num_rows=$resultat->rowCount();
  if ($num_rows > 0):
    $result.='<div id="accordion">';
    while($dn = $resultat->fetch(PDO::FETCH_ASSOC)):
      $result.='<h3>'.$dn['com_nom'].'</h3>';
      $result.='<div id="cat'.$dn['com_id'].'" class="catEqt">';
      // recherche des équipements de la catégorie
      $requete='SELECT * FROM dd_objets_magiques WHERE om_com_id="'.$dn['com_id'].'" AND om_actif=1 ORDER BY om_nom';
      $resultat_eqt=queryPDO($requete);
      $num_rows_eqt=$resultat_eqt->rowCount();
      if ($num_rows_eqt > 0):
        while($eqt = $resultat_eqt->fetch(PDO::FETCH_ASSOC)):
          $styleLigne='data'; // par défaut, le style utilisé prend toute la largeur utile
          // Gestion du modificateur et du sort
          if ($eqt['om_modificateurs']!=0):
            $parametre='<select id="mod'.$eqt['om_id'].'" name="mod'.$eqt['om_id'].'" class="modificateur">'.optionListInt(-5, 5, 1).'</select>';  
            elseif (in_array($eqt['om_com_id'], [4,14,15])):
              $parametre='<div class="ac-wrap">
                <input type="text" class="js_sort_search" placeholder="Rechercher un sort…">
                <input type="hidden" class="js_so_id" id="so'.$eqt['om_id'].'" name="so'.$eqt['om_id'].'">
                <div id="sort_suggest" class="ac-panel"></div>
                </div>';
              $styleLigne='data2'; // dans le cas des baguettes et potions, le style utilisé est réduit afin de laisser de la place pour la zone de recherche du sort
            else:
            $parametre='';
          endif;
          // affichage
          $result.='
            <div id="eqt'.$eqt['om_id'].'" class="'.$styleLigne.'">
              <div id="nom'.$eqt['om_id'].'" class="nom">'.ucfirst($eqt['om_nom']).'</div>
              <div>'.$parametre.'</div>
              <div class="plus"><i class="fa-solid fa-square-plus" onClick="ajouterEqt('.$eqt['om_id'].','.$p.')"></i></div>
            </div>';  // fin #eqt
        endwhile;
      endif;
      $result.='</div>'; // fin #cat ?eqt
    endwhile;
    $result.='</div>'; // fin #accordion
  endif;
  //**********************************************************************
  // On ajoute les donnnées dans un tableau
  echo $p."@".$result;
	else:
	echo $p."@Erreur";
endif;
?>
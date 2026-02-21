<?php
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
      $result.='<div id="cat'.$dn['com_id'].'">';
      // recherche des équipements de la catégorie
      $requete='SELECT * FROM dd_objets_magiques WHERE om_com_id="'.$dn['com_id'].'" ORDER BY om_nom';
      $resultat_eqt=queryPDO($requete);
      $num_rows_eqt=$resultat_eqt->rowCount();
      if ($num_rows_eqt > 0):
        while($eqt = $resultat_eqt->fetch(PDO::FETCH_ASSOC)):
          for($i=0;$i<=$eqt['om_modificateur'];$i++):
            // recherche d'un équipement équivalent pour ce personnage
            $requete='SELECT * FROM dd_personnages_objets_magiques WHERE pom_om_id="'.$eqt['om_id'].'" AND pom_pe_id="'.$p.'" AND pom_modificateur="'.$i.'"';
            $resultat_dot=queryPDO($requete);
            $num_rows_dot=$resultat_dot->rowCount();
            if ($num_rows_dot>0):
              $dot=$resultat_dot->fetch(PDO::FETCH_ASSOC);
              $qte=$dot['pom_qte'];
              $affich_qte=$qte;
              else:
              $qte=0;
              $affich_qte='';
            endif;      
            // affichage du résultat
            if ($eqt['om_modificateur']>0):
              $modificateur=' (+'.$i.')';
              else: 
              $modificateur='';
            endif;
            if (($eqt['om_modificateur']>0 && $i>0) || ($eqt['om_modificateur']==0 && $i==0)):
              $result.='
                <div id="eqt'.$eqt['om_id'].'" class="data">
                  <div id="nom'.$eqt['om_id'].'" class="nom">'.ucfirst($eqt['om_nom']).$modificateur.'</div>
                  <div class="modificateur"></div>
                  <div id="qte'.$eqt['om_id'].'m'.$i.'" class="qte">'.$affich_qte.'</div>
                  <div class="moins" div id="qte'.$eqt['om_id'].'m'.$i.'">
                    <i class="fa-solid fa-square-minus" onClick="supprimer('.$dot['pom_id'].')"></i>
                  </div>
                  <div class="plus">
                    <i class="fa-solid fa-square-plus" onClick="ajouter('.$eqt['om_id'].','.$p.','.$i.')"></i>
                  </div>
                </div>';  // fin #eqt
            endif;
          endfor;
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
<?
  $listeEqt='';
  $requete='SELECT * FROM dd_personnages_objets_magiques JOIN dd_objets_magiques ON pom_om_id=om_id WHERE pom_pe_id="'.$p.'"';
  if ($_SESSION['mj']==1 && $_SESSION['debug']==1) $listeEqt.='<div>'.$requete.'</div>';
  $result_om=queryPDO($requete);
  $num_rows_om=$result_om->rowCount();
  $listeEqt.='<div id="dotation">';
  if ($num_rows_om>0):
    while($dnom = $result_om->fetch(PDO::FETCH_ASSOC)):
      $nom_obj=f_nom($dnom['om_nom']);
      // Gestion des noms de baguettes et de potions
      if (!empty($dnom['pom_so_id']) && $dnom['pom_so_id']>0):
        $sort=libelle("dd_sorts", "so", "nom", $dnom['pom_so_id']);          
        $premiere_lettre = mb_strtolower(mb_substr($sort, 0, 1));
        // Si le mot commence par une voyelle ou un "h" muet → on met "d'", sinon "de"
        if (in_array($premiere_lettre, ['a', 'e', 'i', 'o', 'u', 'y', 'h', 'é', 'É'])): 
          $nom_obj.=" d'".$sort;
          else: 
          $nom_obj.=" de ".$sort;
        endif;
        else:
        $sort="";
      endif;
      // Gestion des armes et objets avec un modificateur
      if (!empty($dnom['pom_modificateur']) && $dnom['pom_modificateur']!=0):
        if ($dnom['pom_modificateur']>0):
          $nom_obj.=" +".$dnom['pom_modificateur'];
          else:
          $nom_obj.=" ".$dnom['pom_modificateur'];
        endif;
      endif;
      $listeEqt.='<div class="data dflex">';
      // Affichage de lobjet
      if ($_SESSION['mj']==1):
        $listeEqt.='  <div onClick="supprimerEqt('.$dnom['pom_id'].','.$p.')" class="icone lien"><i class="fa-solid fa-trash" aria-hidden="true"></i></div>';
        $listeEqt.='  <div onClick="modifierEqt('.$dnom['pom_id'].','.$p.')" class="icone lien"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></div>';
      endif;
      $listeEqt.='  <div onclick="afficherEqt('.$dnom['pom_id'].')" class="lien ml5">'.$nom_obj.'</div>';
      $listeEqt.='</div>';
    endwhile;
    else:
    $listeEqt.='Aucune possession';
  endif;
  $listeEqt.='</div>'; // fin dotation objets magiques
?>
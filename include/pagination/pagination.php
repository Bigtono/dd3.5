<?
  $result_p=queryPDO($requete);
  $nb=$result_p->rowCount();
  $pm=ceil($nb/$nbp); // nb pages maximum
  $limit='';
  $pagination='';
  $inferieur='';  
  $superieur='';
  if ($nb<=$nbp): // moins d'enregistrements dans la base que d'enregistrements à afficher dans une page
    $pagination='';
    else: // plus d'enregistrements dans la base que d'enregistrements à afficher dans une page
    if ($page==1):
      if ($filtre_url!=""):
        $superieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page+1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">page suivante ></a>';
        else:
        $superieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page+1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">page suivante ></a>';
      endif;
      $limit=' LIMIT '.$nbp;
      $debug='1 - nb item : '.$nb.', nb par page : '.$nbp.', nb page : '.$pm.', page : '.$page;
      else: // page supérieure à 1
      if ($page>$pm) $page=$pm; // la page demandée excède la dernière page
      if ($pm>$page):
        if ($filtre_url!=""):
          $superieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page+1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">page suivante ></a>';
          else:
          $superieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page+1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">page suivante ></a>';
        endif;
      endif;
      if ($filtre_url!=""):
        $inferieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page-1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">< page pr&eacute;c&eacute;dente</a>';
        else:
        $inferieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page-1).'&type='.$_GET['type'].'&incomplet='.$_GET['incomplet'].'">< page pr&eacute;c&eacute;dente</a>';
      endif;   
      $limit=' LIMIT '.$nbp.' OFFSET '.$nbp*($page-1); 
      $debug='nb om : '.$nb.', nb par page : '.$nbp.', nb page : '.$pm.', page : '.$page;
    endif;
  endif;
  $pagination='<div class="pagination"><div class="gauche agauche">'.$inferieur.'</div><div class="droite adroite">'.$superieur.'</div></div>';
?>
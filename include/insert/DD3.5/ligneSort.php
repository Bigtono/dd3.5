<?
// Règles - DD3.5

// vérification de la sélection. on contrôle si le sort est déjà dans le grimoire du personnage sélectionné
/*
if (strlen($p)>0):
  $requete='SELECT grc_id FROM dd_grimoires JOIN dd_grimoires_contenu ON gr_id=grc_gr_id WHERE grc_gr_id="'.$g.'" AND gr_cla_id="'.$c.'" AND grc_so_id="'.$sort['so_id'].'"';
  $result_gr= queryPDO($requete);
  $num_rows_gr = $result_gr->rowCount();
  if ($num_rows_gr > 0):
    $check=" checked";
    $value=$sort['so_id'];
    else:
    $check="";
    $value=0;
  endif;
endif;*/
    $check="";
    $value=0;

// recherche d'un domaine
$requete='SELECT * from dd_sortdomaine WHERE sd_so_id="'.$sort['so_id'].'"';
$result_do=queryPDO($requete);
$num_rows_do=$result_do->rowCount();
$domaines='';
if ($num_rows_do>0):
  while($dnd=$result_do->fetch(PDO::FETCH_ASSOC)):
    if (strlen($domaines)>0) $domaines.=', ';
    $domaines.=libelle("dd_domaines","do","nom", $dnd['sd_do_id']);
  endwhile;
endif;
$idsort='';
// Collège de sort
$college=libelle("dd_colleges","co","nom",$sort['so_co_id']);
if ($_SESSION['debug']==2 && $_SESSION['mj']==1) $idsort=' ('.$sort['so_id'].')';

// création de la ligne de sort
echo '<div id="so'.$sort['so_id'].'" class="item data">';
if (basename($_SERVER['PHP_SELF'])=="grimoire-modifier.php"):
  echo '  <div class="icone_select"><input type="checkbox" name="s['.$sort['so_id'].']" value="'.$value.'"'.$check.'></div>';
  elseif ($_SESSION['mj']>0):
  echo '	<div class="icone_suppr" onClick="suppression(\'dd_sorts\',\'so\','.$sort['so_id'].')"><i class="fa fa-trash"></i></div>';
  echo '	<div class="icone_modif" onclick="modifierSort('.$sort['so_id'].')"><i class="fa-solid fa-pen-to-square"></i></div>';
endif;
if ($_SESSION['onglet_sort']==1):
  if (basename($_SERVER['PHP_SELF'])!="grimoire-modifier.php"):
    echo '  <div class="icone_onglet">';
    echo '    <a href="sort.php?sort='.$sort['so_id'].'" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>';
    echo '  </div>';
  endif;
endif;
echo '  <div id="nomSort'.$sort['so_id'].'" class="nom_sort" onClick="afficherSort('.$sort['so_id'].')">'.$sort['so_nom'].$idsort.'</div>';
echo '  <div id="catSort'.$sort['so_id'].'" class="ecole_sort" onClick="afficherSort('.$sort['so_id'].')">'.$college.'</div>';
echo '  <div id="domSort'.$sort['so_id'].'" class="domaine_sort" onClick="afficherSort('.$sort['so_id'].')">'.$domaines.'</div>';
echo '  <div id="shortDescSort'.$sort['so_id'].'" class="description_courte_sort" onClick="afficherSort('.$sort['so_id'].')">'.$sort['so_resume'].'</div>';
echo '  <div class="ressource_sort" title="'.$sort['res_nom'].'" onClick="afficherSort('.$sort['so_id'].')">'.stripslashes($sort['res_abreviation']).'</div>';
echo '</div>';
?>
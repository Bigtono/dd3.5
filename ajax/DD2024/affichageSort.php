<?
  // Règles - DD2024

  // collège et branche
  $college_branche = libelle("dd_colleges","co","nom",$dn['so_co_id']);
  if ($dn['so_branche'] != "") $college_branche .= " [".$dn['so_branche']."]";
  // catégorie de lanceur de sorts
  $requete2 = 'SELECT cla_abreviation, sc_niveau FROM dd_sortclasse LEFT JOIN dd_classes ON sc_cla_id=cla_id WHERE sc_so_id="'.$dn['so_id'].'"';
  $resultat2 = queryPDO($requete2);
  $num_rows2=$resultat2->rowCount();
  $lanceur="";
  if ($num_rows2>0):
    while ($dnls=$resultat2->fetch(PDO::FETCH_ASSOC)):
      if ($lanceur!="") $lanceur.=", ";
      $lanceur.=$dnls['cla_abreviation'].$dnls['sc_niveau'];
      $cpt++;
    endwhile;
  endif;

  // composante
  $composante="";
  $cpt=0;
  if ($dn['so_vocal']==1):
    if ($cpt!=0) $composante .= ",";
    $composante .= "V"; 
    $cpt++;
  endif;
  if ($dn['so_gestuel']==1):
    if ($cpt!=0) $composante .= ",";
    $composante .= "G"; 
    $cpt++;				
  endif;
  if ($dn['so_materiel']==1):
    if ($cpt!=0) $composante .= ",";
    $composante .= "M"; 
    $cpt++;				
  endif;
  if ($dn['so_focalisateur']==1):
    if ($cpt!=0) $composante .= ",";
    $composante .= "F"; 
    $cpt++;				
  endif;
  if ($dn['so_focalisateur_divin']==1):
    if ($cpt!=0) $composante .= ",";
    $composante .= "FD"; 
    $cpt++;				
  endif;

  // mise en forme du contenu
  $result='<div id="sort" class="affichage">';

  $result.='  <div class="menu2">';
  $result.='    <div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div>';
  $result.='    <div class="ce"></div>';
  $result.='    <div class="dr">';
  if ($_SESSION['onglet_sort']==1)
    $result.='    <a href="sort.php?sort='.$s.'" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>';
  if ($_SESSION['mj']>0)
    $result.='     <i class="icon fa-solid fa-pen-to-square" onClick="modifierSort(\''.$s.'\')"></i>';
  $result.='    </div>';
  $result.='  </div>';


  $result.='  <div class="nom_objet">'.stripslashes($dn['so_nom']).'</div>';
  $result.='  <div class="ligne">'.stripslashes($college_branche).'</div>';

  $result.='  <div class="ligne"><span class="gras">Lanceur :</span> '.stripslashes($lanceur).'</div>';
  $result.='  <div class="ligne"><span class="gras">Incantation :</span> '.stripslashes($dn['so_duree_incantation']).'</div>';
  $result.='  <div class="ligne"><span class="gras">Portée :</span> '.stripslashes($dn['so_portee']).'</div>';
  $result.='  <div class="ligne"><span class="gras">Composantes :</span> '.stripslashes($composante).'</div>';
  $result.='  <div class="ligne"><span class="gras">Durée :</span> '.stripslashes($dn['so_duree_sort']).'</div>';

  $result.='  <div class="texte">'.stripslashes($dn['so_texte']).'</div>';

  $result.='  <div class="texte"><span class="label">Source : </span> '.libelle("dd_ressources", "res","nom", $dn['so_res_id']).'</div>';

  $result.='</div>';
?>
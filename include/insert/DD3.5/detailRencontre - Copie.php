  <?
  //************************************************************************************************************************
  // Recherche et affichage du détail d'une rencontre
  //************************************************************************************************************************
  $onglets='';
  $contenu='';        
  $requete="SELECT * FROM dd_monstres LEFT JOIN dd_rencontres_monstres ON rem_mo_id=mo_id JOIN dd_rencontres ON rem_re_id=re_id WHERE re_id='".$re."'";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows>0):  
    $i=1;
    if ($num_rows>1) $onglets='<div class="menu_main contenu">'; // au moins deux monstres détectés, traitement de l'onglet
    while($dn = $result->fetch(PDO::FETCH_ASSOC)):         
      if ($num_rows>1): // gestion d'un onglet
        if ($i==1):
          $classe_monstre=" contenuMainV"; // ongler du 1er monstre, affiché par défaut
          else:
          $classe_monstre=" contenuMain"; // onglet des monstres après le premier, caché par défaut
        endif;
        $onglets.='
        <div class="btMain" onClick="afficherContenu(\'mo'.$dn['mo_id'].'\')">
          <span class="titre_menu gras">'.f_nom($dn['mo_nom']).'</span>
        </div>';
      endif;
      $contenu.='     
      <div id="mo'.$dn['mo_id'].'" class="contenu'.$classe_monstre.'">
        <div class="titreAction">
          <div class="titreMonstre"><a id="#m'.$dn['mo_id'].'" name="#m'.$dn['mo_id'].'">'.$dn['mo_nom'].' ('.$dn['rem_effectif'].')</a></div>
          <div>
            <span class="lien" onClick="supprimerMonstreRencontre(\''.$dn['rem_id'].'\')"><i class="icon fa fa-trash"></i></span>
            <a href="monstre-modifier.php?mo='.$dn['mo_id'].'&re='.$re.'&retour=rencontre"><i class="icon fa fa-pencil"></i></a>
          </div>
        </div>            
        <div>'.stripslashes($dn['mo_stats']).'</div>
      </div>';          
      $i++;
    endwhile;
    if ($onglets!='') $onglets.='</div> <!-- menu_main --->';
    $rencontre=$onglets;
    $rencontre.=$contenu;
    else:
    $rencontre='<div class="nodata">Aucun monstre dans cette rencontre</div>';
  endif;
  ?>
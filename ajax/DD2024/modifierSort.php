<?
  // ModifierSort - DD2024
  $q = $_POST['sort'];
  if ($q=="n"):
    $so_id=0;
    $so_nom="";  
    $so_branche="";
    $so_co_id="";
    $so_vocal=0;
    $so_gestuel=0;
    $so_materiel=0;
    $so_focalisateur=0;
    $so_focalisateur_divin=0;
    $so_resistance=0;
    $so_duree_incantation="";
    $so_portee="";
    $so_cible="";
    $so_zone_effet="";
    $so_duree_sort="";
    $so_jet_sauvegarde="";
    $so_res_id=0;
    $so_page="";
    $so_texte="";
    $so_resume="";
    else:
    $requete = "SELECT * FROM dd_sorts WHERE so_id='". $q ."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $so_id=$dn['so_id'];
    $so_nom=$dn['so_nom'];  
    $so_branche=$dn['so_branche'];
    $so_co_id=$dn['so_co_id'];
    $so_vocal=$dn['so_vocal'];
    $so_gestuel=$dn['so_gestuel'];
    $so_materiel=$dn['so_materiel'];
    $so_focalisateur=$dn['so_focalisateur'];
    $so_focalisateur_divin=$dn['so_focalisateur_divin'];
    $so_resistance=$dn['so_resistance'];
    $so_duree_incantation=$dn['so_duree_incantation'];
    $so_portee=$dn['so_portee'];
    $so_cible=$dn['so_cible'];
    $so_zone_effet=$dn['so_zone_effet'];
    $so_duree_sort=$dn['so_duree_sort'];
    $so_jet_sauvegarde=$dn['so_jet_sauvegarde'];
    $so_res_id=$dn['so_res_id'];
    $so_page=$dn['so_page'];
    $so_texte=$dn['so_texte'];
    $so_resume=$dn['so_resume'];
  endif;

  //**************************************************************************
  // Formatage des données

	// collège
	$college='<select name="mp_so_co_id" id="mp_so_co_id">'.optionList("dd_colleges", "co", "nom", $dn['so_co_id']).'</select>';

  // Niveau du sort pour chaque lanceur de sorts
  $requete_ls='SELECT * FROM dd_classes WHERE cla_ruleset_var_id="'.$_SESSION['ruleset'].'" AND cla_mag_id>0';
  $result_ls=queryPDO($requete_ls);
  $num_row_ls=$result_ls->rowCount();
  $lanceurs='';
  if ($num_row_ls>0):
    while($dnls=$result_ls->fetch(PDO::FETCH_ASSOC)):
      $requete_sc='SELECT * FROM dd_sortclasse WHERE sc_so_id='.$so_id.' AND sc_cla_id='.$dnls['cla_id'];
      $result_sc=queryPDO($requete_sc);
      $num_row_sc=$result_sc->rowCount();
      if ($num_row_sc>0):
        $dnsc=$result_sc->fetch(PDO::FETCH_ASSOC);
        $niveau=stripslashes($dnsc['sc_niveau']);
        else:
        $niveau="";
      endif;
      $lanceurs.='<div class="line-data-50fr"><input type="text" class="w40 input_niveau" id="mp_ls-'.$dnls['cla_id'].'" name="mp_ls-'.$dnls['cla_id'].'" value="'.$niveau.'"> '.$dnls['cla_nom'].'</div>';
    endwhile;
  endif;

  // composante
  $composante="";
  $cpt=0;
  $comp_v="";$comp_g="";$comp_m="";$comp_f="";$comp_fd="";
  if ($so_vocal==1) $comp_v=" checked";
  $composante .= '<input type="checkbox" name="mp_so_vocal" id="mp_so_vocal" value="'.$so_vocal.'" '.$comp_v.'> <label for="mp_so_vocal" class="mr5">Verbal</label> '; 
  if ($so_gestuel==1) $comp_g=" checked";
  $composante .= '<input type="checkbox" name="mp_so_gestuel" id="mp_so_gestuel" value="'.$so_gestuel.'" '.$comp_g.'> <label for="mp_so_vocal" class="mr5">Somatique</label>';
  if ($so_materiel==1) $comp_m=" checked";
  $composante .= '<input type="checkbox" name="mp_so_materiel" id="mp_so_materiel" value="'.$so_materiel.'" '.$comp_m.'> <label for="mp_so_materiel" class="mr5">Matériel</label>'; 
  if ($so_focalisateur==1) $comp_f=" checked";
  $composante .= '<input type="checkbox" name="mp_so_focalisateur" id="mp_so_focalisateur" value="'.$so_focalisateur.'" '.$comp_f.'> <label for="mp_so_focalisateur" class="mr5">Focalisateur</label>';
  if ($so_focalisateur_divin==1) $comp_fd=" checked";
  $composante .= '<input type="checkbox" name="mp_so_focalisateur_divin" id="mp_so_focalisateur_divin" value="'.$so_focalisateur_divin.'" '.$comp_fd.'> <label for="mp_so_focalisateur_divin" class="mr5">Focalisateur Divin</label>'; 


  //**********************************************************************
  // mise en forme du contenu
  $result='<div id="sort" class="affichage formulaire">';
  $result.='<input  type="hidden" id="mp_so_id" value="'.$q.'">';
  $result.='<div class="ligne"><input id="mp_so_nom" class="input_nom" value="'.stripslashes($so_nom).'"></div>';

  $result.='<div class="ligne">DD2024</div>';

  $result.='<div class="line-data2"><div class="label">Collège</div>'.$college.'</div>';
  $result.='<div class="line-data2"><div class="label">Branche</div><input class="input_data" id="mp_so_branche" value="'.stripslashes($so_branche).'"></div>';
  
  $result.='<div class="gras mr10" onCLick="togglePlus(\'lanceurs\')">Niveau <i class="fa-solid fa-bars"></i></div>';
  $result.='<div id="lanceurs" class="box-data accordion-content">'.$lanceurs.'</div>';

  // temps d'incatation
  $result.='<div class="ligne"><div class="label">Incantation</div><input id="mp_so_duree_incantation" class="input_data" value="'.stripslashes($so_duree_incantation).'"></div>';

  // Portée
  $result.='<div class="ligne"><div class="label">Portée</div><input id="mp_so_portee" class="input_data" value="'.stripslashes($so_portee).'"></div>';

  // Composantes
  $result.='<div class="ligne"><div class="label">Composantes</div><div>'.$composante.'</div></div>';

  // Durée
  $result.='<div class="ligne"><div class="label">Durée</div><input id="mp_so_duree_sort" class="input_data" value="'.stripslashes($so_duree_sort).'"></div>';

  $result.='<div class="label">Source</div><select id="mp_so_res_id" class="input_source">'.OptionList("dd_ressources","res","nom",$so_res_id,'res_ruleset_var_id="'.$_SESSION['ruleset'].'"').'</select>';

  $result.='<div id="texte_sort">';
  $result.='<div class="gras">Description du sort</div><div><textarea id="mp_so_texte" name="mp_so_texte">'.stripslashes($so_texte).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_so_texte');</script>";
  $result.='</div>'; // #textes_sort

  if ($q=='n'):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
  endif;
  $result.='<div class="ligneBouton">';
  $result.='  <button class="btNoir" type="button" name="validModifSort" id="validModifSort" onClick="validerModifSort()">'.$libelle.'</button>';
  $result.='  <button class="btNoir" type="button" name="annuleModifSort" id="annuleModifSort" onClick="annulerPageModif()">Annuler</button>';
  $result.='</div>'; 

  $result.='</div>'; // formulaire
?>
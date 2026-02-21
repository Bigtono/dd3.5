<?
  // règles - DD3.5

  if (strlen($critere_sql)>0):
    //**********************************************************************************************************************
    // recherche d'un sort par son nom
    echo '<div class="titre-niveausort">Recherche d\'un Sort</div>';
    // Sélection des classes de lanceurs de sorts
    $requete="SELECT so_id, so_nom, so_co_id, so_resume, so_res_id, res_selection, res_nom, res_abreviation FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id WHERE so_res_id IN ".$selection.$critere_sql." ORDER BY so_nom"; 
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      // formatage du tableau de données
      if ($isDebug && $isAdmin) echo '<div class="action">DD2024 : '.$requete.'</div>';
      echo '<div class="item entete">';
      if ($_SESSION['mj']>0) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']>0) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      if ($_SESSION['onglet_sort']==1) echo '	<div class="icone_onglet"><i class="fa-solid fa-up-right-from-square"></i></div>';
      echo '  <div class="nom_sort">Sort</div>';
      echo '  <div class="ecole_sort">Ecole</div>';
      echo '  <div class="domaine_sort">Domaine</div>';
      echo '  <div class="description_courte_sort">R&eacute;sum&eacute;</div>';
      echo '  <div class="ressource_sort">Source</div>';
      echo '</div>';      
      while($sort = $result->fetch(PDO::FETCH_ASSOC)):        
        include('include/insert/'.$_SESSION['rulesetRep'].'/ligneSort.php');
      endwhile;
      else:
      echo utf8_encode('<div class="alerte">Aucun sort ne correspond à ce critère</div>');
    endif;
    echo '</div>'; // fin du bloc affichant la recherche de sorts
    else:
    //********************************************************************************************************************** 
    // Affichage des sorts par classe  
    // menu onglets pour choisir le niveau de sort
    if ($_SESSION['critere_ls']>0):
      // on recherche si le LS est un lanceur profane ou divin
      $magie=libelle("dd_classes","cla","mag_id",$_GET['critere_ls']);
      if ($isDebug && $isAdmin) echo '<div> Type magie : '.$magie.'</div>';

      // menu onglets niveaux de sort
      if ($magie==3):
        $min=1;
        else:
        $min=0;
      endif;
      // menu onglets pour choisir le niveau de sort
      echo '<div class="menu_main contenu">'; // au moins deux monstres détectés, traitement de l'onglet
      for($i=$min;$i<10;$i++):
        echo '  <div id="menu_fiche" class="btMain" onClick="afficherContenu(\'n'.$i.'\')">';
        echo '    <span class="titre_menu gras">'.$i.'</span>';
        echo '  </div>';
      endfor;
      echo '</div>'; // #menu_main

      // boucle de création des listes de sorts par niveau
      for($i=$min;$i<10;$i++):
        if ($i==1):
          $classe_grimoire=" contenuMainV"; // ongler du niveau 0, affiché par défaut
          else:
          $classe_grimoire=" contenuMain"; // onglet des niveaux supérieurs à 0, caché par défaut
        endif;
        echo '<div id="n'.$i.'" class="contenu'.$classe_grimoire.'">';
        echo '  <div class="titreAction">';
        echo '    <div class="titre">Sorts de niveau '.$i.'</div>';
        echo '    <div></div>';
        echo '  </div>'; // titreAction

        if ($magie==2): // gestion des sorts de domaines par l'union de deux requêtes (la 1ère pour les sorts de classe, la 2ème pour les sorts de domaines)
          $requete='(SELECT so_id, so_nom, so_co_id, so_resume, so_res_id, res_selection, sc_niveau, res_abreviation FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id LEFT JOIN dd_sortclasse ON so_id=sc_so_id WHERE so_res_id IN '.$selection.' AND sc_cla_id='.$_SESSION['critere_ls'].' AND sc_niveau='.$i.')
          UNION
          (SELECT so_id, so_nom, so_co_id, so_resume, so_res_id, res_selection, sd_niveau as sc_niveau, res_abreviation FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id JOIN dd_sortdomaine ON (so_id=sd_so_id AND sd_niveau='.$i.') WHERE so_res_id IN '.$selection.')
          ORDER BY so_nom';
          else:
          $requete='SELECT so_id, so_nom, so_co_id, so_resume, so_res_id, res_selection, sc_niveau, res_abreviation FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id LEFT JOIN dd_sortclasse ON so_id=sc_so_id WHERE so_res_id IN '.$selection.' AND sc_cla_id='.$_SESSION['critere_ls'].' AND sc_niveau='.$i.' ORDER BY so_nom';
        endif;
        $result=queryPDO($requete);
        $num_rows=$result->rowCount();
        if ($num_rows > 0):
          if ($isDebug && $isAdmin) echo '<div class="action">'.$requete.'</div>';
          echo '<div id="entete_sort" class="item entete">';
          if ($_SESSION['mj']>0) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
          if ($_SESSION['mj']>0) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
          if ($_SESSION['onglet_sort']==1) echo '	<div class="icone_onglet"><i class="fa-solid fa-up-right-from-square"></i></div>';
          echo '  <div class="nom_sort">Sort</div>';
          echo '  <div class="ecole_sort">Ecole</div>';
          echo '  <div class="domaine_sort">Domaine</div>';
          echo '  <div class="description_courte_sort">R&eacute;sum&eacute;</div>';
          echo '  <div class="source">Source</div>';
          echo '</div>';
          while($sort = $result->fetch(PDO::FETCH_ASSOC)):
            include('include/insert/'.$_SESSION['rulesetRep'].'/ligneSort.php');
          endwhile;
        endif;
        echo '</div>'; // fin du bloc affichant les sorts
      endfor;
      else:

    endif;
  endif;
?>
<?
session_start();
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// réception du critère (nom de l'équipement)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND so_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere='';
	$critere_sql='';
endif;

// réception du critère Lanceur de sort
if (strlen($_GET['critere_ls'])>0):
	$critere_ls=$_GET['critere_ls'];
	if ($_GET['critere_ls']=="Tout"):
		$critere_ls_sql='';
		else:
		$critere_ls_sql=' AND '.$critere_ls.'=';
	endif;
	else:
	$critere_ls='';
	$critere_ls_sql='';
endif;

?>
<!doctype html>
<HEAD>
<? include("include/header.php"); ?>
<script type='text/javascript' src='js/moncode-sorts.js'></script>
</HEAD>

<BODY>
<? include("include/affichageSelectionSources.php"); ?>

<div id="page">
	<? include("include/head.php"); ?>
	<? include("include/menu.php"); ?>
  <div id="contenu">

  <div id="listesort">
    
		<div class="ligne">
			<form action="listesorts.php" method="get" name="search-sort" id="search-sort" class="mr30">
				<input type="text" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom du sort"/>
				<input type="submit" id="search" name="search" value="Rechercher" class="form_bouton"/>
			</form>
			<form action="listesorts.php" method="get" class="mr30">
				<select name="critere_ls"><? echo OptionListeClassesLS($critere_ls,""); ?></select>
				<input type="submit" id="search_ls" name="search_ls" value="Filtrer Classe" class="form_bouton"/>
			</form>
      <div class="ml20 lien" onClick="modifierSort('n')">[+]</div>
		</div>    
<?
	// création du formulaire
	echo '<form name="select_sort" action="'.$prm_url.'livre.php" method="get">';
	echo '<input type="hidden" name="actionflag" value="1">';
	
  if (strlen($critere_sql)>0):
    // recherche d'un sort par son nom
    echo '<div class="titre-niveausort">recherche d\'un Sort</div>';
    // Sélection des classes de lanceurs de sorts
    $requete="SELECT so_id, so_nom, so_college, so_resume, so_source, res_selection FROM sorts LEFT JOIN ressources ON so_res_id=res_id AND so_res_id IN ".$selection.$critere_sql.$critere_ls_sql.$critere_type_sql." ORDER BY so_nom";    
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    $nbl=0; // nb de sorts dans la ligne
    if ($num_rows > 0):
      // formatage du tableau de données
      if ($_SESSION['debug']==1) echo '<div class="action">'.$requete.'</div>';
      echo '<div id="liste-sorts">';;
      while($sort = $result->fetch(PDO::FETCH_ASSOC)):        
        // préparation des données
        if (strlen($sort['so_resume'])>0):
          $description=$sort['so_resume'];
          else:
          $description="&nbsp";
        endif;
        // vérification de la sélection. on contrôle si le sort est déja dans le caddie du personnage sélectionné
        if (strlen($nomperso)>0):
          $sql='SELECT id_sort FROM caddie_impression WHERE id_perso="'.$idperso.'" AND id_sort="'.$sort['id_sort'].'"';
          $result_caddie= getRowSpec($sql);
          $num_rows_caddie = mysql_num_rows( $result_caddie );
          if ($num_rows_caddie > 0):
            $check=" checked";
            $value="c";
            else:
            $check="";
            $value="";
          endif;
        endif;
        // création de la ligne de sort
        echo '<div id="sort'.$sort['so_id'].'" class="sort" onClick="afficherSort('.$sort['so_id'].')">';
        echo '<div class="sort-selection"><input type="checkbox" name="s['.$sort['so_id'].']" value="'.$value.'"'.$check.'></div>';
        echo '<div class="sort-nom">'.$sort['so_nom'].'</div>';
        echo '<div class="sort-categorie">'.$sort['so_college'].'</div>';
        echo '<div class="sort-description-courte">'.$description.'</div>';
        echo '</div>';
      endwhile;
      echo '<div><input type="submit" name="Submit" value="Selectionner"></div>';
      echo '</div>';
    endif;
    echo '</div>'; // fin du bloc affichant la recherche de sorts
    
    else:
    
    // menu onglets pour choisir le niveau de sort
    if (strlen($critere_ls_sql)>0):
      echo '<div id="tabsort">';
        for($i=0;$i<10;$i++):
          if ($i==0):
             echo '<div id="niveau'.$i.'" class="tab red" onclick="changeOnglet('.$i.')">Niveau '.$i.'</div>';
             else:
             echo '<div id="niveau'.$i.'" class="tab" onclick="changeOnglet('.$i.')">Niveau '.$i.'</div>';
          endif;
        endfor;
      echo '</div>';

      // boucle de création des niveaux de sorts
      for($i=0;$i<10;$i++):

        if ($i==0):
          echo '<div id="contenuOnglet'.$i.'" class="niveausort" style="display:block;">';
          else:
          echo '<div id="contenuOnglet'.$i.'" class="niveausort" style="display:none;">';
        endif;

        echo '<div class="titre-niveausort">Sort de niveau '.$i.'</div>';
        $requete='SELECT so_id, so_nom, so_college, so_resume, so_res_id, res_selection FROM sorts LEFT JOIN ressources ON so_res_id=res_id WHERE so_res_id IN '.$selection.$critere_ls_sql.'"'.$i.'" ORDER BY so_nom';

        $result=queryPDO($requete);
        $num_rows=$result->rowCount();
        $nbl=0; // nb de sorts dans la ligne
        if ($num_rows > 0):
          // formatage du tableau de données
          if ($_SESSION['debug']==1) echo '<div class="action">'.$requete.'</div>';
          echo '<div id="liste-sorts">';;
          while($sort = $result->fetch(PDO::FETCH_ASSOC)):        
            // préparation des données
            if (strlen($sort['so_resume'])>0):
              $description=$sort['so_resume'];
              else:
              $description="&nbsp";
            endif;
            // vérification de la sélection. on contrôle si le sort est déjr dans le caddie du personnage sélectionné
            if (strlen($nomperso)>0):
              $sql='SELECT id_sort FROM caddie_impression WHERE id_perso="'.$idperso.'" AND id_sort="'.$sort['id_sort'].'"';
              $result_caddie= getRowSpec($sql);
              $num_rows_caddie = mysql_num_rows( $result_caddie );
              if ($num_rows_caddie > 0):
                $check=" checked";
                $value="c";
                else:
                $check="";
                $value="";
              endif;
            endif;
            // création de la ligne de sort
            echo '<div id="sort'.$sort['so_id'].'" class="sort" onClick="afficherSort('.$sort['so_id'].')">';
            echo '<div class="sort-selection"><input type="checkbox" name="s['.$sort['so_id'].']" value="'.$value.'"'.$check.'></div>';
            echo '<div class="sort-nom">'.$sort['so_nom'].'</div>';
            echo '<div class="sort-categorie">'.$sort['so_college'].'</div>';
            echo '<div class="sort-description-courte">'.$description.'</div>';
            echo '</div>';

          endwhile;
          echo '<div class="mt10"><input type="submit" name="Submit" value="Selectionner"></div>';
          echo '</div>'; // fin liste-sort
        endif;
        echo '</div>'; // fin du bloc affichant les sorts
      endfor;
      else:
      echo '<div class="message">S&eacute;lectionnez une classe de lanceur de sorts</div>';
    endif;
	endif;
	// fin du formulaire
	echo '</form>';

?>	
	</div>
</div>
  <div id="modification"></div>
  <!--
  <div id="parametres">
    <div id="resolution" class="perso"></div>
    <div class="perso"><? echo $selectionAffichage; ?></div>
  </div>
  -->
  <div id="detail"></div>
</body>
</html>
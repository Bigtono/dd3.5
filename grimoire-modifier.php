<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$g=$_GET['grimoire'];
//if ($_GET['idperso']) $p=$_GET['idperso'];
?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript'>
	$(document).ready(function(){
		$('#resolution').html("Resolution : "+screen.width+'x'+screen.height);
	});
</script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<div id="page">
    <?
    include("include/header.php");
		include("include/menu.php");
    
    if(!empty($g) && $g!="n"): // il s'agit d'une modification
      $requete="SELECT * FROM dd_grimoires WHERE gr_id='".$g."'";
      $result=queryPDO($requete);	
      $num_rows=$result->rowCount();
      $dn = $result->fetch(PDO::FETCH_ASSOC);
      $libelle="Modifier";
      else: // il s'agit d'un ajout
      $num_rows=1;
      $a="n"; 
      $libelle="Créer";
    endif; 
    $p=$dn['gr_pe_id'];
    $nomperso=libelle("dd_personnages", "pe", "nom", $p);
    $nomgrimoire=$dn['gr_nom'];
    $c=$dn['gr_cla_id']; 
    
    // Mise en forme du contenu
    $perso='<select id="mp_gr_pe_id" name="mp_gr_pe_id">'.optionList("dd_personnages", "pe", "nom", $dn['gr_pe_id']).'</select>';
    $format='<select id="mp_gr_grf_id" name="mp_gr_grf_id">'.optionList("dd_grimoires_format", "grf","nom", $dn['gr_grf_id']).'</select>';
    $classe='<select id="mp_gr_cla_id" name="mp_gr_cla_id">'.optionList("dd_classes", "cla","nom", $dn['gr_cla_id']).'</select>';
    $defaut='<select id="mp_gr_defaut" name="mp_gr_defaut">'.optionListOuiNon($dn['gr_defaut']).'</select>';
    ?>      
	  <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA"><? echo $dn['gr_nom']; ?></div>
        <div></div>
      </div>
      <? //if ($_GET['msg']==1) echo '<div class="contenu"><div class="confirmation">Grimoire mis &agrave; jour</div></div>'; ?>
      
      <form action="grimoire-enregistrement.php" method="post" name="grimoire" id="grimoire">
        <input type="hidden" name="idgrimoire" value="<? echo $g; ?>" />
        <input type="hidden" name="retour" value="<? echo $_GET['retour']; ?>" />
        <!-- Présentation du grimoire -->
        <div>
          <div class="ligne"><div class="label w200">Nom</div><input type="text" class="input_nom" id="mp_gr_nom" name="mp_gr_nom" value="<? echo $dn['gr_nom']; ?>"></div>        
          <div class="ligne"><div class="label w200">Classe</div><? echo $classe; ?></div>
          <div class="ligne mt5"><div class="label w200">Format</div><? echo $format; ?></div>
          <div class="ligne mt5"><div class="label w200">Personnage</div><? echo $perso; ?></div>
          <div class="ligne mt5"><div class="label w200">Par défaut</div><? echo $defaut; ?></div>
        </div>
        <!--- Sorts du grimoires --->
        <div id="grimoire">
          <?
          if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="action">'.$selection.'</div>';
          // menu onglets pour choisir le niveau de sort
          echo '<div class="menu_main menu-chiffres">';
          for($i=0;$i<10;$i++):
            echo '  <div class="item" onClick="afficherContenu(\'n'.$i.'\')">';
            echo $i;
            echo '  </div>';
          endfor;
          echo '</div>'; // #menu_main
          // boucle de création des niveaux de sorts
          for($i=0;$i<10;$i++):
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
            $requete='SELECT so_id, so_nom, so_co_id, so_resume, so_res_id, res_selection, sc_niveau FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id LEFT JOIN dd_sortclasse ON so_id=sc_so_id WHERE so_res_id IN '.$selection.' AND sc_cla_id='.$c.' AND sc_niveau='.$i.' ORDER BY so_nom';
            if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="action">'.$requete.'</div>';
            $result=queryPDO($requete);
            $num_rows=$result->rowCount();
            if ($num_rows > 0):
              // formatage du tableau de données
              echo '  <div class="item entete">';
              if (basename($_SERVER['PHP_SELF'])=="grimoire-modifier.php"):
                echo '  <div class="icone_select"><input type="checkbox" value=""></div>';
                elseif ($_SESSION['mj']==1):
                  echo '	<div class="icone_suppr"><i class="fa fa-trash"></i></div>';
                  echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
              endif;
              echo '    <div class="nom_sort">Sort</div>';
              echo '    <div class="ecole_sort">Ecole</div>';
              echo '    <div class="domaine_sort">Domaine</div>';
              echo '    <div class="description_courte_sort">R&eacute;sum&eacute;</div>';
              echo '    <div class="ressource_sort">Source</div>';
              echo '  </div>'; // item entete
              echo '  <div class="item data">';
              while($sort = $result->fetch(PDO::FETCH_ASSOC)):
                include("include/insert/ligneSort.php");
              endwhile;
              echo '  </div>'; // item data
            endif;
            echo '</div>'; // contenu $classe_grimoire
          endfor;
          //echo '<div class="mt10"><input class="btNoir" type="submit" name="Submit" value="Selectionner"></div>';
          ?>
          <!-- affichage des boutons --->
          <div class="ligneBouton">
            <button type="submit" class="btNoir" name="ok">Enregistrer</button>
            <button type="submit" class="btNoir" name="nok">Annuler</button>
          </div>          

        </div> <!-- liste-sort --->        
      </form>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div> <!-- wrapper --->
    <div id="modification"></div>
    <div id="detail-pp"></div>
  </div><!-- page --->
</body>
</html>
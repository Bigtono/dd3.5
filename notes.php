<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// critère de recherche
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' WHERE no_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;

// filtre
if(isset($_GET["type"]) && $_GET["type"]!="Tout"):
  $filtre=' WHERE no_tyno_id='.$_GET["type"];
  $filtre_url="?type=".$_GET["type"];
  else:
  $filtre='';
  $filtre_url='';
endif;

// Préparation de la pagination
$page_source=$_SESSION['page_notes'];
include('include/pagination/prepa_pagination.php');

?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-personnages.js'></script>
</head>

<body>
	<DIV id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Notes</div>
      <div><? if ($_SESSION['mj']==1) echo '<i class="icon lien fa-solid fa-pen-to-square" onClick="modifierNote(\'n\', \''.$_GET["type"].'\')"></i>'; ?></div>
    </div>
    
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="notes.php" method="get" name="search-no" id="search-no" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Titre de la note" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>      
      <form action="notes.php" method="get" class="search-form">
        <select name="type"  class="search-select">
          <? echo OptionList("dd_types_notes", "tyno", "nom",$_GET['type'],"",0,"Tout"); ?>
        </select>
        <button type="submit" class="search-button" id="search_note"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>
    </div>         
    <?
    //******************************************************************************************************************************
    // gestion de la pagination
    if ($critere_sql==''): // liste de objets magiques globale ou par catégorie
      $requete='SELECT * FROM dd_notes'.$filtre.' ORDER BY no_nom'.$limit;
      else: // recherche d'un OM par son nom
      $requete='SELECT * FROM dd_notes'.$critere_sql.' ORDER BY no_nom'.$limit;
    endif;
    include('include/pagination/pagination.php');
    //******************************************************************************************************************************

    if ($critere_sql==''): // liste des notes globale ou par catégorie
      $requete='SELECT * FROM dd_notes'.$filtre.' ORDER BY no_nom'.$limit;
      else: // recherche d'une note par son nom
      $requete='SELECT * FROM dd_notes'.$critere_sql.' ORDER BY no_nom'.$limit;
    endif;
    if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="mt10 mb20">(2) '.$requete.'</div>';  
    $result_no=queryPDO($requete);
    $num_rows_no=$result_no->rowCount();
    if ($num_rows_no > 0):
      if ($_SESSION['debug']==1) echo '<div>'.$debug.'</div>';
      echo $pagination;
      echo '<div class="item entete">';      
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_note">Nom</div>';
      echo '  <div class="categorie_note">Type</div>';
      echo '  <div class="niveau_note">Niveau</div>';
      echo '</div>';
      echo '<div class="liste-items">';
      while($dnno = $result_no->fetch(PDO::FETCH_ASSOC)):
        echo '<div id ="no'.$dnno['no_id'].'" class="item data">';
        include('include/insert/'.$_SESSION['rulesetRep'].'/ligneNote.php');
        echo $ligne;
        echo '</div>';
      endwhile;
      echo '</div>'; // liste-items
      else:
      if(isset($_GET["type"])):
        echo '<div class="nodata">Aucune note dans la cat&eacute;gorie '.libelle("dd_types_notes","tyno","nom",$_GET["type"]).' !</div>';
        else:
        echo '<div class="nodata">Aucune note disponible !</div>';
      endif;
    endif;
    ?>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
  </div> <!-- wrapper --->
</div>
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
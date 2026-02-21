<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du grimoire)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' WHERE gr_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;

// filtre
if(isset($_GET["perso"])):
  $filtre=' WHERE gr_pe_id='.$_GET["perso"];
  else:
  $filtre="";
endif;

// Préparation de la pagination
$page_source=$_SESSION['page_grimoires'];
include('include/pagination/prepa_pagination.php');

?>
<!doctype html>
<html>
<head>
<? include("include/header.php"); ?>
<script type='text/javascript' src='js/moncode-personnages.js'></script>
</head>

<body>
	<div id="page">
	<? include("include/head.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Grimoires</div>
      <div><? if ($_SESSION['mj']==1) echo '<i class="icon fa fa-plus-square-o" onClick="ajouterGrimoire()"></i>'; ?></div>
    </div> 
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="grimoires.php" method="get" name="search-don" id="search-don" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom du grimoire" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <form action="grimoires.php" method="get" class="search-form">
        <select name="perso" class="search-select">
          <? echo OptionList("dd_personnages", "pe", "nom",$_GET['perso'],"",0,"Tout"); ?>
        </select>
        <button type="submit" class="search-button" id="search_perso" name="search_perso"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>      
    </div>
    <?
    if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div>Page #'.$page.'</div>';
    //******************************************************************************************************************************
    // gestion de la pagination
    if ($critere_sql==''): // liste des grimoires d'un personnage
      $requete='SELECT * FROM dd_grimoires'.$filtre.' ORDER BY gr_nom'.$limit;
      else: // recherche par le nom
      $requete='SELECT * FROM dd_grimoires'.$critere_sql.' ORDER BY gr_nom'.$limit;
    endif;
    if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div>Requete : '.$requete.'</div>';
    include('include/pagination/pagination.php');
    //******************************************************************************************************************************    
    if ($critere_sql==''): // liste des grimoires d'un personnage
      $requete='SELECT * FROM dd_grimoires'.$filtre.' ORDER BY gr_nom'.$limit;
      else: // recherche par le nom
      $requete='SELECT * FROM dd_grimoires'.$critere_sql.' ORDER BY gr_nom'.$limit;
    endif;
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      echo $pagination;
      echo '<div class="item entete">';      
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa fa-pencil"></i></i></div>';
      echo '  <div class="nom_grimoire">Nom</div>';
      echo '  <div class="format_grimoire">Format</div>';    
      echo '  <div class="classe_perso">Classe</div>';
      echo '</div><!-- sort entête --->';
      while($gr = $result->fetch(PDO::FETCH_ASSOC)):
        //if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idgr=' ('.$gr['gr_id'].')';
        echo '<div class="item data">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_grimoires\',\'gr\','.$gr['gr_id'].')"><i class="fa fa-trash"></i></span></div>';
        if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><a class="lien" href="grimoire-modifier.php?grimoire='.$gr['gr_id'].'&retour=grimoires"><i class="fa fa-pencil"></i></a></i></div>';    
        echo '  <div class="nom_grimoire"><a class="lien" href="grimoire.php?grimoire='.$gr['gr_id'].'&retour=grimoires">'.f_nom($gr['gr_nom']).'</a></div>';
        echo '  <div class="format_grimoire">'.libelle("dd_grimoires_format", "grf","nom", $gr['gr_grf_id']).'</div>';
        echo '  <div class="classe_perso">'.libelle("dd_classes","cla","nom",$gr['gr_cla_id']).'</div>';
        echo '</div>';
      endwhile;
      else:
      if(isset($_GET["type"])):
        echo '<div class="nodata">Aucun grimoire disponible pour le personnage '.libelle("dd_personnages","pe","nom",$_GET["perso"]).' !</div>';
        else:
        echo '<div class="nodata">Aucun grimoire disponible !</div>';
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
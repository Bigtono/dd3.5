<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du sort)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND mo_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;

// Préparation de la pagination
$page_source=$_SESSION['page_monstres'];
include('include/pagination/prepa_pagination.php');

?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-dons.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Monstres</div>
      <div class="TitreA">
        <? if ($_SESSION['mj']==1) echo '<a href="monstre-modifier.php?mo=n&retour=monstre"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?>
      </div>
    </div>
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="monstres.php" method="get" name="search-monstres" id="search-monstres" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom du monstre" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
    </div>
    <?
    //if ($isDebug && $isAdmin) echo '<div>Page #'.$page.'</div>';
    //******************************************************************************************************************************
    // gestion de la pagination
    $requete='SELECT * FROM dd_monstres WHERE mo_ruleset_var_id="'.$_SESSION['ruleset'].'"'.$critere_sql.' ORDER BY mo_nom'.$limit;
    debug($requete);
    include('include/pagination/pagination.php');
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      echo $pagination;
      echo '<div class="item entete">';      
      echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_monstre">Nom</div>';
	  echo '  <div class="fp">FP</div>';
	  echo '  <div class="monstre_type">Type</div>';
      echo '</div><!-- item entête --->';
      while($monstre = $result->fetch(PDO::FETCH_ASSOC)):
        echo '<div class="item data">';
        echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_monstres\',\'mo\','.$monstre['mo_id'].')"><i class="fa fa-trash"></i></span></div>';
        echo '  <div class="icone_modif"><span><a href="monstre-modifier.php?mo='.$monstre['mo_id'].'&retour=monstre"><i class="fa-solid fa-pen-to-square"></i></span></div>'; 
        echo '  <div class="nom_monstre"><a href="monstre.php?mo='.$monstre['mo_id'].'&re=0">'.$monstre['mo_nom'].'</a></div>';
		echo '  <div class="fp"><a href="monstre.php?mo='.$monstre['mo_id'].'&re=0">'.libelle("dd_fp", "fp", "nom", $monstre['mo_fp_id']).'</a></div>';
		echo '  <div class="monstre_type"><a href="monstre.php?mo='.$monstre['mo_id'].'&re=0">'.libelle("dd_monstres_categories", "mocat", "nom", $monstre['mo_mocat_id']).'</a></div>';
        echo '</div>';
      endwhile;
      else:
      echo '<div class="nodata">Aucun monstre disponible !</div>';
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
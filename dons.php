<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du sort)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND do_nom LIKE "%'.trim($_GET['critere']).'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;

// critère de sélection des objets dont la description est nulle
if ($_GET['incomplet']==1):
  $complement=" AND (do_texte IS NULL OR do_texte='')";
  $descriptionCheck=" CHECKED";
  else:
  $descriptionCheck="";
  $complement='';
endif;

// filtre
if(!empty($_GET["type"])):
  $filtre=' do_dado_id='.trim($_GET["type"]).' AND';
  else:
  $filtre="";
endif;

// Préparation de la pagination
$page_source=$_SESSION['page_dons'];
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
      <div class="titreA">
        Dons
        <? if ($_SESSION['mj']==1) echo '<i class="fa-solid fa-pen-to-square ml15" onClick="modifierDon(\'n\')"></i>'; ?>
      </div>
      <div></div>
    </div>
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="dons.php" method="get" name="search-don" id="search-don" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom du don" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <form action="dons.php" method="get" class="search-form">
        <select name="type" class="search-select">
          <? echo OptionList("dd_data_don", "dado", "nom",$_GET['type'],"",0,""); ?>
        </select>
        <button type="submit" class="search-button" id="search_cdom" name="search_cdon"/><i class="fa-solid fa-magnifying-glass"></i></button> 
        <div class="incomplet"><input type="checkbox" class="search-input ml30" id="incomplet" name="incomplet" value="1"<? echo $descriptionCheck; ?>/><label for="incomplet" class="ml10"><? echo utf8_encode('Description à compléter'); ?></label></div>
      </form>      
    </div>
    <?
    //******************************************************************************************************************************
    // gestion de la pagination
    if ($critere_sql==''): // liste de dons globale ou par catégorie
      $requete='SELECT * FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_ruleset_var_id="'.$_SESSION['ruleset'].'" AND '.$filtre.' do_res_id IN '.$selection.' ORDER BY do_nom'.$limit;
      else: // recherche d'un don par son nom
      $requete='SELECT * FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_ruleset_var_id="'.$_SESSION['ruleset'].'" AND do_res_id IN '.$selection.$critere_sql.' ORDER BY do_nom'.$limit;
    endif;
    debug('pagination : '.$requete);
    include('include/pagination/pagination.php');
    //******************************************************************************************************************************    
    
    if ($critere_sql==''): // liste de dons globale ou par catégorie
      $requete='SELECT * FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_ruleset_var_id="'.$_SESSION['ruleset'].'" AND '.$filtre.' do_res_id IN '.$selection.' ORDER BY do_nom'.$limit;
      else: // recherche d'un don par son nom
      $requete='SELECT * FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_ruleset_var_id="'.$_SESSION['ruleset'].'" AND do_res_id IN '.$selection.$critere_sql.' ORDER BY do_nom'.$limit;
    endif;
    debug('Sélection : '.$requete);
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      echo $pagination;
      echo '<div class="item entete">';      
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_don">Nom</div>';
      echo '  <div class="categorie_don">Type</div>';
      echo '  <div class="description_courte">R&eacute;sum&eacute;</div>';
      echo '  <div class="source">Source</div>';
      echo '</div><!-- item entête --->';
      while($don = $result->fetch(PDO::FETCH_ASSOC)):
        $click='afficherDon('.$don['do_id'].')';
        if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $iddon=' ('.$don['do_id'].')';
        echo '<div class="item data">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_dons\',\'do\','.$don['do_id'].')"><i class="fa fa-trash"></i></span></div>';
        if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierDon('.$don['do_id'].')"><i class="fa-solid fa-pen-to-square"></i></span></div>';    
        echo '  <div class="nom_don" onclick="'.$click.'">'.stripslashes(ucfirst($don['do_nom'])).$iddon.'</div>';
        echo '  <div class="categorie_don" onclick="'.$click.'">'.libelle("dd_data_don","dado","nom",$don['do_dado_id']).'</div>';
        echo '  <div class="description_courte" onclick="'.$click.'">'.stripslashes($don['do_resume']).'</div>';
        echo '  <div class="source" title="'.$don['res_nom'].'" onclick="'.$click.'">'.stripslashes($don['res_abreviation']).'</div>';
        echo '</div>';
      endwhile;
      else:
      if(isset($_GET["type"])):
        echo '<div class="nodata">Aucun don disponible dans la cat&eacute;gorie '.libelle("dd_data_don","dado","nom",$_GET["type"]).' !</div>';
        else:
        echo '<div class="nodata">Aucun don disponible !</div>';
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
<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if (isset($_GET['search_ls'])):
  if (isset($_GET['critere_ls'])):
    if ($_GET['critere_ls'] === "all"):
      $_SESSION['critere_ls'] = "all";
    elseif ($_GET['critere_ls'] != ""):
      $_SESSION['critere_ls'] = intval($_GET['critere_ls']);
    else:
      $_SESSION['critere_ls'] = "all";
    endif;
  endif;
endif;

if ($_GET['critere_ls']): // saisie d'une classe de lanceur
  if ($_GET['critere_ls']===""):
    unset($_SESSION["critere_ls"]);
    else:
    $_SESSION["critere_ls"]=$_GET['critere_ls'];
  endif;
  elseif(strlen($_GET['critere'])>0): // saisie d'un nom de sort
    $critere=$_GET['critere'];
    $critere_sql=' AND so_nom LIKE "%'.trim($_GET['critere']).'%"';
    $cas=1;
    elseif(strlen($_GET['critere_res'])>0): // saisie d'une ressource
      $critere='';
      $critere_sql=' AND so_res_id="'.trim($_GET['critere_res']).'"';
      $cas=2;
      else:
      $critere='';
      $critere_sql='';
endif;

?>
<!doctype html>
<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
</HEAD>

<BODY>
<? include("include/affichageSelectionSources.php"); ?>

<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Sorts</div>
      <div class="titreA"><? if ($_SESSION['mj']>0) echo '<span onClick="modifierSort(\'n\')" class="lien"><i class="icon fa-solid fa-circle-plus"></i></span>'; ?></div>
    </div>
    <!--- Menu secondaire --->
    <div class="search-container">      
      <form action="sorts.php" method="get" name="search-sort" id="search-sort" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" placeholder="Nom du sort" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>
      <form action="sorts.php" method="get" class="search-form">
        <select name="critere_ls" class="search-select">
          <option value="" disabled hidden selected>Lanceur de sort</option>
          <? echo OptionListeClassesLS($_SESSION['critere_ls'],"Toutes"); ?>
        </select>
        <button type="submit" class="search-button" id="search_ls" name="search_ls"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>
      <form action="sorts.php" method="get" class="search-form">
        <select name="critere_res" class="search-select">
          <option value="" disabled hidden selected>Source</option>
          <?
          echo OptionListeRessources($_GET['critere_res']);
          ?>
        </select>
        <button type="submit" class="search-button" id="search_res" name="search_res"/><i class="fa-solid fa-magnifying-glass"></i></button>         
      </form>      
    </div>  
    <?
      if ($isDebug && $isAdmin) echo '<div>ID Lanceur '.$_GET['critere_ls'].'</div>';
      if ($isDebug && $isAdmin) echo '<div>Sources : '.$selection.'</div>';
      if ($isDebug && $isAdmin) echo '<div>MJ : '.$_SESSION['mj'].'</div>';

      include('include/insert/'.$_SESSION['rulesetRep'].'/sorts.php');
    ?>	
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>  
<div id="modification"></div>
</body>
</html>
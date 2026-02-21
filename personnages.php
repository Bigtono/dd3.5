<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/pagination.php");
include("include/list_helpers.inc.php");

// 1) CONFIG SPÉCIFIQUE À LA PAGE
// Exemple : liste des campagnes
$listId  = 'personnages';

include('include/sql/listePersonnages.php');

?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>  
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Personnages</div>
      <div><? echo '<a href="personnage-modifier.php?personnage=n" class="ajout_perso lien"><i class="icon fa-solid fa-pen-to-square"></i></a>'; ?></div>
    </div>
    
    <?
      debug($sqlData);
      include('include/insert/'.$_SESSION['rulesetRep'].'/listePersonnages.php');
      renderPagination($currentPage, $totalItems, $itemsPerPage, $extraParams);
    ?>

    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>        
	</div> <!-- wrapper --->
  <div id="modification"></div>
  <div id="detail-pp"></div>    
  <? include('include/footer.php'); ?>
</div><!-- page --->
</body>
</html>
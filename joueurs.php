<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

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
      <div class="titreA">Liste des	joueurs </div>
      <div><? if ($_SESSION['mj']==1) echo '<a href="joueur-modifier.php?joueur=n"><i class="icon fa-solid fa-pen-to-square"></i></a>'; ?></div>
    </div>    
		<?
      include('include/insert/'.$_SESSION['rulesetRep'].'/listeJoueurs.php');
      echo $listeJoueurs;
    ?>	
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>        
	</div> <!-- wrapper --->
</div><!-- page --->
<div id="detail-pp"></div>  
<div id="modification"></div>
</body>
</html>
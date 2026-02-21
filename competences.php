<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-competences.js'></script>  
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Comp&eacute;tences</div>
      <div><? if ($_SESSION['mj']==1) echo '<i class="icon fa-solid fa-pen-to-square" onClick="modifierCompetences(\'n\')"></i>'; ?></div>
    </div>    
    <? include('include/insert/'.$_SESSION['rulesetRep'].'/competences.php'); ?>	
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>  
</body>
</html>
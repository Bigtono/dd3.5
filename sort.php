<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
</head>
<body>
	<div id="page">
	  <div id="sort_pp">
      <?
      $_POST['sort']=$_GET['sort'];
      $nomFichier = basename($_SERVER['PHP_SELF']);
      include('ajax/ajax-affichageSort.php');
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div><!-- #sort --->
	</div><!-- page --->
</body>
</html>
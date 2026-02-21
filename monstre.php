<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if (isset($_GET['mo'])):
  $mo = $_GET['mo'];
  $_SESSION['monstre']=$mo;
  else:
  $mo="";
  $_SESSION['monstre']="";
endif;
?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-classes.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>      
</HEAD>
  
<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>  
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <?
  if($mo!=""):
    $requete = "SELECT * FROM dd_monstres WHERE mo_id='".$mo."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    ?> 
    <div class="titreAction">
      <div class="titreA">
        <? echo f_nom($dn['mo_nom']); ?>
        <? echo '<a href="monstre-modifier.php?mo='.$dn['mo_id'].'&retour=monstre"><i class="fa-solid fa-pen-to-square ml15"></i></a>';?>
      </div>
      <div></div>
    </div>
  
    <div id="monstre">
      <div class="texte description"><? echo stripslashes($dn['mo_stats']); ?></div>
    </div>
    <?    
    else:
      echo '<div class="nodata">Aucune monstre disponible !</div>';
    endif;
  ?>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
	</div> <!-- wrapper --->
</div><!-- page --->
<div id="detail-pp"></div> 
<div id="modification"></div>
</body>
</html>
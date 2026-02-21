<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_GET['race'])):
		$r = $_GET['race'];
		$_SESSION['race']=$r;
		else:
		$r="";
		$_SESSION['race']="";
endif;
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
    <?
  if($r!=""):
    $requete = "SELECT * FROM dd_races WHERE ra_id='".$r."'";
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    ?> 
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">
        <? echo stripslashes($dn['ra_nom']); ?>
        <? echo '<a href="race-modifier.php?race='.$dn['ra_id'].'"><i class="fa-solid fa-pen-to-square ml15"></i></a>';?>
      </div>
      <div></div>
    </div>
      
    <div id="race">
      <div class="texte description"><? echo stripslashes($dn['ra_description']); ?></div>
      <? include('include/insert/'.$_SESSION['rulesetRep'].'/bloc_race.php') ?>
    </div>
    <?    
    else:
      echo '<div class="nodata">Aucune race disponible !</div>';
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
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
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Races</div>
      <div class="titreA"><? if ($_SESSION['mj']==1) echo '<a href="race-modifier.php?race=n" class="ajout_race lien"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?></div>
    </div>
    <?
    echo '<div class="item entete">';
    echo '	<div class="icone_suppr"><i class="fa fa-trash"></i></div>';
    echo '	<div class="icone_modif mr30"><i class="fa-solid fa-pen-to-square"></i></div>';
    echo '  <div class="nom_race">Race</div>';
    echo '  <div class="categorie_race">Type de race</div>';
    echo '</div>';
    ?>
    <div id="item data"> 
    <?
      if ($_GET['msg'] && $_SESSION['debug']==1) echo '<div>'.$_GET['msg'].'</div>';
      $requete="SELECT * FROM dd_races WHERE ra_ruleset_var_id='".$_SESSION['ruleset']."' ORDER BY ra_rat_id, ra_nom"; 
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      if ($num_rows > 0):
      
      
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          $lien='<a href="race.php?race='.$dn['ra_id'].'&tri='.$_GET['tri'].'">';
          echo '<div class="item data">';
					echo '	<div class="icone_suppr"><span onClick="suppression(\'races\',\'p\','.$dn['ra_id'].')"><i class="fa fa-trash"></i></span></div>';
					echo '	<div class="icone_modif mr30"><a href="race-modifier.php?race='.$dn['ra_id'].'&tri='.$_GET['tri'].'"><i class="fa-solid fa-pen-to-square"></i></a></div>';
          echo '  <div class="nom_race">'.$lien.$dn['ra_nom'].'</a></div>';
          echo '  <div class="categorie_race">'.$lien.libelle("dd_race_type","rat","nom",$dn['ra_rat_id']).'</a></div>';
          echo '</div>';
        endwhile;
        else:
        echo '<div class="alerte">Aucune race dans la base de données</div>';
      endif;
    ?>	
    </div> <!-- #liste-races --->
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>
</body>
</html>
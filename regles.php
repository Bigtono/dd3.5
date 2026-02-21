<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du sort)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND re_nom LIKE "%'.$_GET['critere'].'%"';
	else:
  $critere=''; 
  $critere_sql='';
endif;
?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-regles.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">R&egrave;gles du jeu</div>
        <div>
          <? if ($_SESSION['mj']==1) echo '<a href="regle-modifier.php?regle=n"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?>
        </div>
      </div>
      <!--- Menu secondaire --->
      <div class="search-container">
        <form action="regles-recherche.php" method="get" name="search-regle" id="search-regle" class="search-form">
          <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" placeholder="Nom de la r&egrave;gle" onClick="myFocus(this)"/>
          <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>     
      </div>    
      
      <div class="regle entete">
        <div class="nom">Nom</div>
      </div><!-- regle entête --->

      <div class="liste-regles">
        <?
        $requete='SELECT * FROM dd_regles WHERE re_ruleset_var_id="'.$_SESSION['ruleset'].'"'.$critere_sql.' ORDER BY re_ordre, re_nom';
        debug($requete);
        $result=queryPDO($requete);
        $num_rows=$result->rowCount();
        if ($num_rows > 0):
          $iteration=0;
          regles($critere_parent,$iteration);
          else:
          if(isset($_GET["type"])):
            echo '<div class="nodata">Aucune r&egrave;gle disponible dans la cat&eacute;gorie '.libelle("dd_categorie_regle","cr","nom",$_GET["type"]).' !</div>';
            else:
            echo '<div class="nodata">Aucun r&egrave;gle disponible !</div>';
          endif;
        endif;
        ?>
      </div>
    </div><!-- wrapper -->
  </div> <!-- page --->
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
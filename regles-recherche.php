<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du sort)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' WHERE re_nom LIKE "%'.htmlentities($_GET['critere']).'%" OR re_texte LIKE "%'.htmlentities($_GET['critere']).'%"';
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
      <H1><a href="regles.php" class="mr20" title="Retour"><i class="icon fa fa-rotate-left"></i></a> R&eacute;sultat de la recherche : <? echo $critere; ?></H1>    
      <?
      $requete='SELECT * FROM dd_regles'.$critere_sql.' ORDER BY re_nom';
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      if ($num_rows > 0):
        echo '<div class="regle entete">';
        echo '  <div class="nom">Nom</div>';
        echo '</div><!-- regle entête --->';
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          echo '<div class="regle">';
          echo '<a href="regle.php?regle='.$dn['re_id'].'&retour=regles-recherche&critere='.$critere.'">'.$dn['re_nom'].'</a>';
          echo '</div>';          
        endwhile;
        else:
        echo '<div class="nodata">Aucun r&egrave;gle disponible !</div>';
      endif;
      ?>
    </div><!-- wrapper -->
  </div> <!-- page --->
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
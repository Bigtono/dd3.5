<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


if(isset($_GET['competence'])):
  $q=$_GET['competence'];
endif;
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
  else:
  $critere='';
endif;
?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-competence.js'></script>
</head>
<body>
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper">
      <?
      $requete="SELECT * FROM dd_competences WHERE comp_id='".$q."'";
      $result = queryPDO($requete);
      $num_rows = $result->rowCount();
      if ($num_rows>0):
        $dn = $result->fetch(PDO::FETCH_ASSOC);
        if ($critere!=''):
          $retour=$_GET['retour'].".php?critere=".$critere;
          $avant=array(htmlentities($critere));
          $apres=array('<span class="resultat_recherche">'.$critere.'</span>');
          $texte=str_replace($avant, $apres, $dn['comp_description']);
          else:
          $retour="competences.php";
          $texte=$dn['comp_description'];
        endif;
        ?>
        <div class="ligne sousmenu">
          <a href="<? echo $retour; ?>" class="mr20" title="Retour"><i class="icon fa fa-rotate-left"></i></a>
          <a href="competence-modifier.php?competence=<? echo $q; ?>" class="modifier mr20" title="Modifier"><i class="icon fa-solid fa-pen-to-square-square"></i></a>
          <a href="competence-modifier.php?competence=n&sup=<? echo $q; ?>" class="modifier" title="Ajouter"><i class="icon fa-solid fa-pen-to-square"></i></a>
        </div>
        <div id="competence_pp">
          <div class="nom"><? echo stripslashes($dn['comp_nom']);?></div>
          <?
          echo '    <div class="texte">'.stripslashes($texte).'</div>';    
          ?>
        </div>
        <?
        else:
        echo '<div class=""><a href="competences.php">Retour</a></div>';      
        echo '<div class="nodata">Pas de comp</div>';
      endif;
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div><!-- wrapper --->
	</div><!-- page --->
</body>
</html>
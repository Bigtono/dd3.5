<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


if(isset($_GET['regle'])):
  $re=$_GET['regle'];
  else:
  $re=0;
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
  <script type='text/javascript' src='js/moncode-regles.js'></script> 
</head>
<body>
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">
          <? echo libelle("dd_regles", "re", "nom", $re); ?>
          <? if ($_SESSION['mj']==1): ?>
          <a href="regle-modifier.php?regle=<? echo $re; ?>" class="modifier mr20" title="Modifier"><i class="icon fa-solid fa-pen-to-square-square"></i></a> <a href="regle-modifier.php?regle=<? echo $re; ?>" class="modifier" title="Modifier"><i class="icon fa-solid fa-pen-to-square"></i></a>
          <? endif; ?>
        </div>
        <div class="titreA">
          <? if ($_SESSION['mj']==1): ?>
          <a href="regle-modifier.php?regle=n&sup=<? echo $re; ?>" class="modifier" title="Ajouter"><i class="fa-solid fa-circle-plus"></i></a>
          <? endif; ?>
        </div>
      </div>
  
      <?
      $requete="SELECT * FROM dd_regles WHERE re_id='".$re."'";
      $result = queryPDO($requete);
      $num_rows = $result->rowCount();
      if ($num_rows>0):
        $dn = $result->fetch(PDO::FETCH_ASSOC);
        if ($critere!=''):
          $retour=$_GET['retour'].".php?critere=".$critere;
          $avant=array(htmlentities($critere));
          $apres=array('<span class="resultat_recherche">'.$critere.'</span>');
          $texte=str_replace($avant, $apres, $dn['re_texte']);
          else:
          if (isset($dn['re_re_id']) && $dn['re_re_id']>0):
            $retour="regle.php?regle=".$dn['re_re_id'];
            else:
            $retour="regles.php";
          endif;
          $texte=$dn['re_texte'];
        endif;
        ?>  
        <div id="regle">
          <?
          $requete2="SELECT * FROM dd_regles WHERE re_re_id='".$re."'";
          $result2 = queryPDO($requete2);
          $num_rows2 = $result2->rowCount();
          if ($num_rows2>0):
            ?>
          <fieldset class="sommaire">
            <legend>Sommaire</legend>
            <? regles($re,0,$re); ?>
          </fieldset>
            <?
          endif;
          ?>
          <div class="texte"><? echo $texte; ?></div>
          <?
          else:
          echo '<div class=""><a href="regles.php">Retour</a></div>';      
          echo '<div class="mt30">Pas de r&egrave;gle</div>';
        endif;
        ?>
      </div>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div><!-- wrapper --->
	</div><!-- page --->
</body>
</html>
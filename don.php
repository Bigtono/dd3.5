<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


if(isset($_GET['don'])):
  $q=$_GET['don'];
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
</head>
<body>
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper">
    <?
    //************************************************************************************************************************
    // Recherche et affichage du détail d'un don
    //************************************************************************************************************************
      // recherche du sort
    $requete="SELECT * FROM dd_dons WHERE do_id='".$q."'";
    $result = queryPDO($requete);
    $num_rows = $result->rowCount();
    if ($num_rows>0):
      $dn = $result->fetch(PDO::FETCH_ASSOC);
      if ($critere!=''):
        $retour=$_GET['retour'].".php?critere=".$critere;
        $avant=array(htmlentities($critere));
        $apres=array('<span class="resultat_recherche">'.$critere.'</span>');
        $texte=str_replace($avant, $apres, $dn['do_texte']);
        else:
        $retour="dons.php";
        $texte=$dn['do_texte'];
      endif;      
    ?>
      <div class="ligne sousmenu">
        <a href="<? echo $retour; ?>" class="mr20" title="Retour"><i class="icon fa fa-rotate-left"></i></a>
        <a href="don-modifier.php?don=<? echo $q; ?>" class="modifier mr20" title="Modifier"><i class="icon fa-solid fa-pen-to-square-square"></i></a>
        <a href="don-modifier.php?don=n&sup=<? echo $q; ?>" class="modifier" title="Ajouter"><i class="icon fa-solid fa-pen-to-square"></i></a>
      </div>      
  		<div id="don_pp">
  			<div class="nom"><? echo stripslashes($dn['do_nom']);?></div>
        <div class="texte"><? echo stripslashes($texte); ?></div>
  	  </div>
    <?
    endif;
    ?>
    </div><!-- wrapper --->
	</div><!-- page --->
</body>
</html>
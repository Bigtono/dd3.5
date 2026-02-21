<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// paramétrage du filtrage
$displayChapitres=' class="noDisplay"';
$listeChapitres='';

// réception du critère (nom de la rencontre)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND re_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;

if (isset($_GET['critere_sc']) && $_GET['critere_sc']!=''):
  unset($_SESSION['chapitre']); // on filtre via le scénario, on efface donc la session chapitre
  $filtre=' AND re_sc_id="'.$_GET["critere_sc"].'"';
  $_SESSION['scenario']=$_GET["critere_sc"];
  $displayChapitres=""; // on affiche la liste des chapitres 
  $listeChapitres='<select name="critere_ch" id="critere_ch" class="search-select">'.optionList("dd_scenarios_chapitres", "scc", "nom", 0, 'scc_sc_id="'.$_SESSION['scenario'].'"', 0,'','scc_ordre').'</select><button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>';
  elseif(isset($_SESSION['scenario']) && $_SESSION['scenario']>0):
    $filtre=' AND re_sc_id="'.$_SESSION['scenario'].'"';
    $displayChapitres=""; // on affiche la liste des chapitres 
    $listeChapitres='<select name="critere_ch" id="critere_ch" class="search-select">'.optionList("dd_scenarios_chapitres", "scc", "nom", 0, 'scc_sc_id="'.$_SESSION['scenario'].'"', 0,'','scc_ordre').'</select><button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>';
    else:
    $filtre='';
endif;

// si un chapitre est sélectionné, la valeur de filtre du scénario est remplacée par celle du chapitre
if (isset($_GET["critere_ch"]) && $_GET["critere_ch"]!=''):
  $filtre=' AND re_scc_id='.$_GET["critere_ch"];
  $_SESSION['chapitre']=$_GET["critere_ch"];
  $_SESSION['scenario']=libelle("dd_scenarios_chapitres","scc","sc_id", $_SESSION['chapitre']); // recherche du scénario correspondant
  $listeChapitres='<select name="critere_ch" id="critere_ch" class="search-select">'.optionList("dd_scenarios_chapitres", "scc", "nom", $_SESSION['chapitre'], 'scc_sc_id="'.$_SESSION['scenario'].'"', 0,'','scc_ordre').'</select><button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>';
  $displayChapitres="";
  elseif(isset($_SESSION['chapitre']) && $_SESSION['chapitre']>0):
    $_SESSION['scenario']=libelle("dd_scenarios_chapitres", "scc", "sc_id", $_SESSION['chapitre'], '', 0, '', 'scc_ordre'); // recherche du scénario correspondant
    $filtre=' AND re_scc_id='.$_SESSION['chapitre'];
    $listeChapitres='<select name="critere_ch" id="critere_ch" class="search-select">'.optionList("dd_scenarios_chapitres", "scc", "nom",$_SESSION['chapitre'],'scc_sc_id="'.$_SESSION['scenario'].'"', 0, '', 'scc_ordre').'</select><button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>';
    $displayChapitres="";
endif;
?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-rencontres.js'></script>
  <script type='text/javascript' src='js/moncode-regles.js'></script>  
</head>
<body>
	<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper_rencontre">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Rencontres</div>
      <div class="titreA"><a href="rencontre-modifier.php?re=n"><i class="icon fa-solid fa-circle-plus"></i></a></div>
    </div>  
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="rencontres.php" method="get" name="search-rencontre" id="search-rencontre" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $_GET['critere']; ?>" placeholder="Nom de la rencontre" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <form action="rencontres.php" method="get" name="search-scenario" id="search-scenario" class="search-form">
        <select name="critere_sc"  class="search-input" onChange="majChapitre(this.value)">
          <? echo OptionList("dd_scenarios","sc","nom",$_SESSION['scenario']); ?>
        </select>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <form id="chapitres" action="rencontres.php" method="get" class="search-form">
        <span id="listeChapitres"<? echo $displayChapitres; ?>><? echo $listeChapitres; ?></span>
      </form>
    </div>
    <?
    if ($critere_sql==''): // liste des rencontres globale ou par catégorie
      $requete='SELECT * FROM dd_rencontres WHERE re_ruleset_var_id="'.$_SESSION['ruleset'].'" '.$filtre.' ORDER BY re_scc_id, re_abreviation';
      $verif=1;
      else: // recherche d'une rencontre par son nom
      $requete='SELECT * FROM dd_rencontres WHERE re_ruleset_var_id="'.$_SESSION['ruleset'].'" '.$critere_sql;
      $verif=2;
    endif;
    if ($_SESSION['debug']==1) echo '<div>'.$verif.' / '.$requete.' / '.$filtre.'</div>';
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      echo '<div class="item entete">';      
      echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_rencontre">Nom</div>';
      echo '  <div class="scenario">Sc&eacute;nario</div>';
      echo '  <div class="chapitre">Chap.</div>';
      echo '  <div class="adversaires">Adversaires</div>';
      echo '</div><!-- rencontre entête --->';
      echo '<div class="liste-items">';
      while($dn = $result->fetch(PDO::FETCH_ASSOC)):
        // calcul du nombre de monstres par rencontre
        $requete='SELECT * FROM dd_rencontres_monstres WHERE rem_re_id="'.$dn['re_id'].'"';
        $result_m=queryPDO($requete);
        $num_rows_m=$result_m->rowCount();
        echo '<div class="item data">';
        echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_rencontres\',\'re\','.$dn['re_id'].')"><i class="fa fa-trash"></i></span></div>';
        echo '  <div class="icone_modif"><a href="rencontre-modifier.php?rencontre='.$dn['re_id'].'"&retour=rencontres><i class="fa-solid fa-pen-to-square"></i></a></div>';    
        echo '  <div class="nom_rencontre">';
        echo '    <a href="rencontre.php?re='.$dn['re_id'].'"&critere_sc='.$_GET["critere_sc"].'&critere='.$_GET["critere"].'>';
        echo        f_nom($dn['re_nom']);
        echo '    </a>';
        echo '  </div>';
        echo '  <div class="scenario">'.libelle("dd_scenarios","sc", "nom",$dn['re_sc_id']).'</div>';
        echo '  <div class="chapitre">'.f_nom($dn['re_abreviation']).'</div>';
        echo '  <div class="adversaires">'.$num_rows_m.'</div>';
        echo '</div>';
      endwhile;
      echo '</div>'; // liste-items
      else:
      if(isset($_GET["type"])):
        echo '<div class="nodata">Aucune rencotre disponible dans le sc&eacute;nario '.libelle("dd_scenarios","sc","nom",$_GET["type"]).' !</div>';
        else:
        echo '<div class="nodata">Aucune rencontre disponible !</div>';
      endif;
    endif;
    ?>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
  </div> <!-- wrapper centre--->
  <div class="ecran">
    <? include('include/insert/'.$_SESSION['rulesetRep'].'/ecran.php'); ?>
  </div>    
</div>
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
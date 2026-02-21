<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// critère de recherche
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND om_nom LIKE "%'.trim($_GET['critere']).'%"';
  $filtre_url="?critere=".$_GET["critere"];
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
  $filtre_url="";
endif;
// critère de sélection des objets dont la description est nulle
if ($_GET['incomplet']==1):
  $complement=" AND (om_description IS NULL OR om_description='')";
  $descriptionCheck=" CHECKED";
  else:
  $descriptionCheck="";
  $complement='';
endif;
// filtre
if(!empty($_GET["type"])):
  $filtre=' om_com_id='.trim($_GET["type"]).' AND';
  else:
  $filtre="";
endif;



// Préparation de la pagination
$page_source=$_SESSION['page_om'];
include('include/pagination/prepa_pagination.php');

?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-sort.js'></script>
<script type='text/javascript' src='js/moncode-om.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<DIV id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <H1>Objets magiques <? if ($_SESSION['mj']==1) echo '<i class="icon lien fa-solid fa-pen-to-square" onClick="modifierOM(\'n\', \''.$_GET["type"].'\')"></i>'; ?></H1>
      
    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="objets-magiques.php" method="get" name="search-om" id="search-om" class="search-form">
        <input type="text" class="search-input" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom de l'objet magique" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search" name="search"/><i class="fa-solid fa-magnifying-glass"></i></button> 
      </form>      
      <form action="objets-magiques.php" method="get" class="search-form">
        <select name="type" class="search-select">
          <? echo OptionList("dd_categorie_objet_magique", "com", "nom",$_GET['type'],"",0,"Tout"); ?>
        </select>
        <button type="submit" class="search-button" id="search_com" name="search_ls"/><i class="fa-solid fa-magnifying-glass"></i></button> 
        <div id="description_om">
          <input type="checkbox" class="ml30" id="incomplet" name="incomplet" value="1"<? echo $descriptionCheck; ?>/>
          <label class="ml10"><? echo utf8_encode('Description à compléter'); ?></label>
        </div>
      </form>
    </div> 

    <?
    //******************************************************************************************************************************
    // gestion de la visibilité
    if ($_SESSION['mj']!=1):
      $visibilite=' AND om_visible=1';
      else:
      $visibilite='';
    endif;
    // gestion de la pagination
    if ($critere_sql==''): // liste de objets magiques globale ou par catégorie
      $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_ruleset_var_id="'.$_SESSION['ruleset'].'" AND '.$filtre.' om_res_id IN '.$selection. $complement.$visibilite.' ORDER BY om_nom'.$limit;
      else: // recherche d'un OM par son nom
      $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_ruleset_var_id="'.$_SESSION['ruleset'].'" AND om_res_id IN '.$selection.$complement.$critere_sql.$visibilite.' ORDER BY om_nom'.$limit;
    endif;  
    debug('Pagination : '.$requete);
    include('include/pagination/pagination.php');
    //******************************************************************************************************************************

    if ($critere_sql==''): // liste de objets magiques globale ou par catégorie
      $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_ruleset_var_id="'.$_SESSION['ruleset'].'" AND '.$filtre.' om_res_id IN '.$selection.$complement.$visibilite.' ORDER BY om_nom'.$limit;
      else: // recherche d'un don par son nom
      $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_ruleset_var_id="'.$_SESSION['ruleset'].'" AND om_res_id IN '.$selection.$complement.$critere_sql.$visibilite.' ORDER BY om_nom'.$limit;
    endif;
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();

    if ($num_rows > 0):
      debug($debug);
      echo $pagination;
      debug('Sélection : '.$requete);
      echo '<div class="item entete">';
      if ($_SESSION['mj']>0) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']>0) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_om">Nom</div>';
      echo '  <div class="categorie_om">Type</div>';
      echo '  <div class="source">Source</div>';
      echo '</div><!-- entête --->';
      echo '<div class="liste-items">';
      while($dn = $result->fetch(PDO::FETCH_ASSOC)):
        // Préparation du contenu
        $nom=stripslashes(ucfirst($dn['om_nom']));
        if ($dn['om_so_niveau']>0) $nom.=' (niveau '.$dn['om_so_niveau'].')';
        $click='afficherOM('.$dn['om_id'].')';
        // affichage
        $vide=' <i class="fa fa-fw fa-star-half"></i>'; 
        if (strlen($dn['om_description'])>0 || ($dn['om_fom_id']==1 && $dn['om_so_id']>0)):
          $vide="";
        endif;
        if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idom=' ('.$dn['om_id'].')';
        echo '<div id ="om'.$dn['om_id'].'" class="item data">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_objets_magiques\',\'om\','.$dn['om_id'].')"><i class="fa fa-trash"></i></span></div>';
        if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierOM('.$dn['om_id'].')"><i class="fa-solid fa-pen-to-square"></i></span></div>';    
        echo '  <div id="nomOM'.$dn['om_id'].'" class="nom_om" onclick="'.$click.'">'.$nom.$vide.$idom.'</div>';
        echo '  <div id="catOM'.$dn['om_id'].'" class="categorie_om" onclick="'.$click.'">'.libelle("dd_categorie_objet_magique","com","nom",$dn['om_com_id']).'</div>';
        echo '  <div id="sourceOM'.$dn['om_id'].'" class="source" title="'.$dn['res_nom'].'" onclick="'.$click.'">'.stripslashes($dn['res_abreviation']).'</div>';
        echo '</div>';
      endwhile;
      echo '</div>'; // liste-items
      else:
      if(isset($_GET["type"])):
        echo '<div class="nodata">Aucun objet magique disponible dans la cat&eacute;gorie '.libelle("dd_categorie_objet_magique","com","nom",$_GET["type"]).' !</div>';
        else:
        echo '<div class="nodata">Aucun objet magique disponible !</div>';
      endif;
    endif;
    ?>    
  </div> <!-- wrapper --->
</div>
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
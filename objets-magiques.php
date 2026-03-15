<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$typeOm = isset($_GET['type']) ? trim((string)$_GET['type']) : 'Tout';
$incomplet = isset($_GET['incomplet']) && (int)$_GET['incomplet'] === 1;

$critere_sql = '';
if ($critere !== ''):
  $critere_sql = ' AND om_nom LIKE "%' . addslashes($critere) . '%"';
endif;

$filtre = '';
if ($typeOm !== '' && $typeOm !== 'Tout' && ctype_digit($typeOm)):
  $filtre = ' AND om_com_id=' . (int)$typeOm;
endif;

$descriptionCheck = $incomplet ? ' CHECKED' : '';
$complement = $incomplet ? " AND (om_description IS NULL OR om_description='')" : '';

// Preparation de la pagination
$page_source = $_SESSION['page_om'];
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
    <H1>Objets magiques <? if ($_SESSION['mj']==1) echo '<i class="icon lien fa-solid fa-pen-to-square" onClick="modifierOM(\'n\', \'0\')"></i>'; ?></H1>

    <div class="search-container">
      <form action="objets-magiques.php" method="get" name="search-om" id="search-om" class="notes-filter-form">
        <div class="notes-filters-row">
          <div class="notes-filter-group">
            <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom de l'objet magique" onClick="myFocus(this)"/>
          </div>
          <div class="notes-filter-group">
            <select name="type" class="search-select">
              <? echo OptionList("dd_categorie_objet_magique", "com", "nom", ($typeOm === 'Tout' ? 'Tout' : (int)$typeOm), "", 0, "Tout"); ?>
            </select>
          </div>
          <div class="notes-filter-group" style="min-width:auto;">
            <input type="checkbox" id="incomplet" name="incomplet" value="1"<? echo $descriptionCheck; ?> />
            <label for="incomplet" class="ml10">Description a completer</label>
          </div>
          <div class="notes-filter-group" style="min-width:auto;">
            <button type="submit" class="search-button" id="search" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
          </div>
        </div>
      </form>
    </div>

    <?
    if ($_SESSION['mj']!=1):
      $visibilite=' AND om_visible=1';
    else:
      $visibilite='';
    endif;

    $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_ruleset_var_id="'.$_SESSION['ruleset'].'" AND om_res_id IN '.$selection.$critere_sql.$filtre.$complement.$visibilite.' ORDER BY om_nom'.$limit;
    debug('Pagination : '.$requete);
    include('include/pagination/pagination.php');

    $result=queryPDO($requete);
    $num_rows=$result->rowCount();

    if ($num_rows > 0):
      echo $pagination;
      debug('Selection : '.$requete);
      echo '<div class="item entete">';
      if ($_SESSION['mj']>0) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']>0) echo '\t<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_om">Nom</div>';
      echo '  <div class="categorie_om">Type</div>';
      echo '  <div class="source">Source</div>';
      echo '</div><!-- entete --->';
      echo '<div class="liste-items">';
      while($dn = $result->fetch(PDO::FETCH_ASSOC)):
        $nom=stripslashes(ucfirst($dn['om_nom']));
        if ($dn['om_so_niveau']>0) $nom.=' (niveau '.$dn['om_so_niveau'].')';
        $click='afficherOM('.$dn['om_id'].')';
        $vide=' <i class="fa fa-fw fa-star-half"></i>';
        if (strlen($dn['om_description'])>0 || ($dn['om_fom_id']==1 && $dn['om_so_id']>0)) $vide="";
        if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idom=' ('.$dn['om_id'].')'; else $idom='';
        echo '<div id ="om'.$dn['om_id'].'" class="item data">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_objets_magiques\',\'om\','.$dn['om_id'].')"><i class="fa fa-trash"></i></span></div>';
        if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierOM('.$dn['om_id'].')"><i class="fa-solid fa-pen-to-square"></i></span></div>';
        echo '  <div id="nomOM'.$dn['om_id'].'" class="nom_om" onclick="'.$click.'">'.$nom.$vide.$idom.'</div>';
        echo '  <div id="catOM'.$dn['om_id'].'" class="categorie_om" onclick="'.$click.'">'.libelle("dd_categorie_objet_magique","com","nom",$dn['om_com_id']).'</div>';
        echo '  <div id="sourceOM'.$dn['om_id'].'" class="source" title="'.$dn['res_nom'].'" onclick="'.$click.'">'.stripslashes($dn['res_abreviation']).'</div>';
        echo '</div>';
      endwhile;
      echo '</div>';
    else:
      if($typeOm !== '' && $typeOm !== 'Tout' && ctype_digit($typeOm)):
        echo '<div class="nodata">Aucun objet magique disponible dans la categorie '.libelle("dd_categorie_objet_magique","com","nom",$typeOm).' !</div>';
      else:
        echo '<div class="nodata">Aucun objet magique disponible !</div>';
      endif;
    endif;
    ?>
  </div>
</div>
</body>
<div id="detail-pp"></div>
<div id="modification"></div>
</html>

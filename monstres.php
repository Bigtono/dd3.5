<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// reception des criteres de recherche
$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$typeMonstre = isset($_GET['type']) ? trim((string)$_GET['type']) : 'Tout';
$fpMonstre = isset($_GET['fp']) ? trim((string)$_GET['fp']) : 'Tout';

$critere_sql = '';
if ($critere !== ''):
  $critere_sql = ' AND mo_nom LIKE "%' . addslashes($critere) . '%"';
endif;

$type_sql = '';
if ($typeMonstre !== '' && $typeMonstre !== 'Tout' && ctype_digit($typeMonstre)):
  $type_sql = ' AND mo_mocat_id="' . (int)$typeMonstre . '"';
endif;

$fp_sql = '';
if ($fpMonstre !== '' && $fpMonstre !== 'Tout' && ctype_digit($fpMonstre)):
  $fp_sql = ' AND mo_fp_id="' . (int)$fpMonstre . '"';
endif;

// Preparation de la pagination
$page_source = $_SESSION['page_monstres'];
include('include/pagination/prepa_pagination.php');

?>
<!doctype html>
<html>

<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">
          Monstres
          <? if ($_SESSION['mj'] == 1) echo '<a href="monstre-modifier.php?mo=n&retour=monstre"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?>
        </div>
        <div class="TitreA"></div>
      </div>

      <div class="search-container">
        <form action="monstres.php" method="get" name="search-monstres" id="search-monstres" class="notes-filter-form">
          <div class="notes-filters-row">
            <div class="notes-filter-group">
              <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom du monstre" onClick="myFocus(this)" />
            </div>
            <div class="notes-filter-group">
              <select name="type" class="search-select">
                <? echo OptionList("dd_monstres_categories", "mocat", "nom", ($typeMonstre === 'Tout' ? 'Tout' : (int)$typeMonstre), "", 0, "Tout"); ?>
              </select>
            </div>
            <div class="notes-filter-group">
              <select name="fp" class="search-select">
                <? echo OptionList("dd_fp", "fp", "nom", ($fpMonstre === 'Tout' ? 'Tout' : (int)$fpMonstre), "", 0, "Tout"); ?>
              </select>
            </div>
            <div class="notes-filter-group" style="min-width:auto;">
              <button type="submit" class="search-button" id="search" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
          </div>
        </form>
      </div>

      <?
      $requete = 'SELECT * FROM dd_monstres WHERE mo_ruleset_var_id="' . $_SESSION['ruleset'] . '"' . $critere_sql . $type_sql . $fp_sql . ' ORDER BY mo_nom' . $limit;
      debug($requete);
      include('include/pagination/pagination.php');
      $result = queryPDO($requete);
      $num_rows = $result->rowCount();
      if ($num_rows > 0):
        echo $pagination;
        echo '<div class="item entete">';
        echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
        echo '\t<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
        echo '  <div class="nom_monstre">Nom</div>';
        echo '  <div class="fp">FP</div>';
        echo '  <div class="monstre_type">Type</div>';
        echo '</div><!-- item entete --->';
        while ($monstre = $result->fetch(PDO::FETCH_ASSOC)):
          echo '<div class="item data">';
          echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_monstres\',\'mo\',' . $monstre['mo_id'] . ')"><i class="fa fa-trash"></i></span></div>';
          echo '  <div class="icone_modif"><span><a href="monstre-modifier.php?mo=' . $monstre['mo_id'] . '&retour=monstre"><i class="fa-solid fa-pen-to-square"></i></span></div>';
          echo '  <div class="nom_monstre"><a href="monstre.php?mo=' . $monstre['mo_id'] . '&re=0">' . $monstre['mo_nom'] . '</a></div>';
          echo '  <div class="fp"><a href="monstre.php?mo=' . $monstre['mo_id'] . '&re=0">' . libelle("dd_fp", "fp", "nom", $monstre['mo_fp_id']) . '</a></div>';
          echo '  <div class="monstre_type"><a href="monstre.php?mo=' . $monstre['mo_id'] . '&re=0">' . libelle("dd_monstres_categories", "mocat", "nom", $monstre['mo_mocat_id']) . '</a></div>';
          echo '</div>';
        endwhile;
      else:
        echo '<div class="nodata">Aucun monstre disponible !</div>';
      endif;
      ?>
      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
  </div>
</body>
<div id="detail-pp"></div>
<div id="modification"></div>

</html>

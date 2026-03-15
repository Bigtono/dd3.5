<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$typeDon = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
$incomplet = isset($_GET['incomplet']) && (int)$_GET['incomplet'] === 1;

$critere_sql = '';
if ($critere !== ''):
  $critere_sql = ' AND do_nom LIKE "%' . addslashes($critere) . '%"';
endif;

$filtre = '';
if ($typeDon !== '' && ctype_digit($typeDon)):
  $filtre = ' AND do_dado_id=' . (int)$typeDon;
endif;

$descriptionCheck = $incomplet ? ' CHECKED' : '';
$complement = $incomplet ? " AND (do_texte IS NULL OR do_texte='')" : '';

// Preparation de la pagination
$page_source = $_SESSION['page_dons'];
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
        Dons
        <? if ($_SESSION['mj']==1) echo '<i class="fa-solid fa-pen-to-square ml15" onClick="modifierDon(\'n\')"></i>'; ?>
      </div>
      <div></div>
    </div>

    <div class="search-container">
      <form action="dons.php" method="get" name="search-don" id="search-don" class="notes-filter-form">
        <div class="notes-filters-row">
          <div class="notes-filter-group">
            <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom du don" onClick="myFocus(this)"/>
          </div>
          <div class="notes-filter-group">
            <select name="type" class="search-select">
              <? echo OptionList("dd_data_don", "dado", "nom", ($typeDon !== '' ? (int)$typeDon : ''), "", 0, "Tout"); ?>
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
    $requete='SELECT * FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_ruleset_var_id="'.$_SESSION['ruleset'].'" AND do_res_id IN '.$selection.$critere_sql.$filtre.$complement.' ORDER BY do_nom'.$limit;
    debug('pagination : '.$requete);
    include('include/pagination/pagination.php');

    debug('Selection : '.$requete);
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
    if ($num_rows > 0):
      echo $pagination;
      echo '<div class="item entete">';
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']==1) echo '\t<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_don">Nom</div>';
      echo '  <div class="categorie_don">Type</div>';
      echo '  <div class="description_courte">Resume</div>';
      echo '  <div class="source">Source</div>';
      echo '</div><!-- item entete --->';
      while($don = $result->fetch(PDO::FETCH_ASSOC)):
        $click='afficherDon('.$don['do_id'].')';
        if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $iddon=' ('.$don['do_id'].')'; else $iddon='';
        echo '<div class="item data">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_dons\',\'do\','.$don['do_id'].')"><i class="fa fa-trash"></i></span></div>';
        if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierDon('.$don['do_id'].')"><i class="fa-solid fa-pen-to-square"></i></span></div>';
        echo '  <div class="nom_don" onclick="'.$click.'">'.stripslashes(ucfirst($don['do_nom'])).$iddon.'</div>';
        echo '  <div class="categorie_don" onclick="'.$click.'">'.libelle("dd_data_don","dado","nom",$don['do_dado_id']).'</div>';
        echo '  <div class="description_courte" onclick="'.$click.'">'.stripslashes($don['do_resume']).'</div>';
        echo '  <div class="source" title="'.$don['res_nom'].'" onclick="'.$click.'">'.stripslashes($don['res_abreviation']).'</div>';
        echo '</div>';
      endwhile;
    else:
      if($typeDon !== '' && ctype_digit($typeDon)):
        echo '<div class="nodata">Aucun don disponible dans la categorie '.libelle("dd_data_don","dado","nom",$typeDon).' !</div>';
      else:
        echo '<div class="nodata">Aucun don disponible !</div>';
      endif;
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

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$critereRes = isset($_GET['critere_res']) ? trim((string)$_GET['critere_res']) : '';
$critereLsGet = isset($_GET['critere_ls']) ? trim((string)$_GET['critere_ls']) : null;

if ($critereLsGet !== null):
  if ($critereLsGet === '' || $critereLsGet === 'all'):
    $_SESSION['critere_ls'] = 'all';
  elseif (ctype_digit($critereLsGet)):
    $_SESSION['critere_ls'] = (int)$critereLsGet;
  else:
    $_SESSION['critere_ls'] = 'all';
  endif;
elseif (!isset($_SESSION['critere_ls']) || $_SESSION['critere_ls'] === ''):
  $_SESSION['critere_ls'] = 'all';
endif;

$critere_sql = '';
if ($critere !== ''):
  $critere_sql .= ' AND so_nom LIKE "%' . addslashes($critere) . '%"';
endif;
if ($critereRes !== '' && ctype_digit($critereRes)):
  $critere_sql .= ' AND so_res_id="' . (int)$critereRes . '"';
endif;

// Compatibilite avec les include de rendu des sorts
$_GET['critere_ls'] = (string)$_SESSION['critere_ls'];
$_GET['critere_res'] = $critereRes;

?>
<!doctype html>
<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
</HEAD>

<BODY>
<? include("include/affichageSelectionSources.php"); ?>

<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Sorts</div>
      <div class="titreA"><? if ($_SESSION['mj']>0) echo '<span onClick="modifierSort(\'n\')" class="lien"><i class="icon fa-solid fa-circle-plus"></i></span>'; ?></div>
    </div>

    <div class="search-container">
      <form action="sorts.php" method="get" name="search-sort" id="search-sort" class="notes-filter-form">
        <div class="notes-filters-row">
          <div class="notes-filter-group">
            <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nom du sort" onClick="myFocus(this)"/>
          </div>
          <div class="notes-filter-group">
            <select name="critere_ls" class="search-select">
              <? echo OptionListeClassesLS($_SESSION['critere_ls'], "Toutes"); ?>
            </select>
          </div>
          <div class="notes-filter-group">
            <select name="critere_res" class="search-select">
              <option value="">Toutes les sources</option>
              <? echo OptionListeRessources($critereRes); ?>
            </select>
          </div>
          <div class="notes-filter-group" style="min-width:auto;">
            <button type="submit" class="search-button" id="search" name="search"><i class="fa-solid fa-magnifying-glass"></i></button>
          </div>
        </div>
      </form>
    </div>

    <?
      if ($isDebug && $isAdmin) echo '<div>ID Lanceur '.$_GET['critere_ls'].'</div>';
      if ($isDebug && $isAdmin) echo '<div>Sources : '.$selection.'</div>';
      if ($isDebug && $isAdmin) echo '<div>MJ : '.$_SESSION['mj'].'</div>';

      include('include/insert/'.$_SESSION['rulesetRep'].'/sorts.php');
    ?>	
    <p class="mb50">&nbsp;</p>
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
  </div>
</div>
<div id="detail-pp"></div>
<div id="modification"></div>
</body>
</html>

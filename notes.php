<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$campagne = isset($_GET['campagne']) ? (int)$_GET['campagne'] : 0;
$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$typeRaw = isset($_GET['type']) ? trim((string)$_GET['type']) : 'Tout';

$type = 'Tout';
if ($typeRaw !== '' && $typeRaw !== 'Tout' && ctype_digit($typeRaw)) {
  $type = (int)$typeRaw;
}

$where = array();
$params = array();
$join = '';

// detection du champ auteur de note selon le schema
$champAuteur = 'no_j_id';
$testChampNoJ = queryPDO("SHOW COLUMNS FROM dd_notes LIKE 'no_j_id'");
if ($testChampNoJ->rowCount() === 0) $champAuteur = 'no_redacteur';

// Cas 1 : filtre campagne depuis campagne.php
if ($campagne > 0):
  $join = ' INNER JOIN dd_campagnes_notes cpno ON cpno.cpno_no_id = no.no_id';
  $where[] = 'cpno.cpno_camp_id = :campagne';
  $params[':campagne'] = $campagne;
  $titreNotes = 'Notes - Campagne : ' . htmlspecialchars(libelle("dd_campagnes", "camp", "nom", $campagne));
else:
  // Cas 2 : acces direct depuis le menu principal
  $where[] = 'no.' . $champAuteur . ' = :user_id';
  $params[':user_id'] = (int)$_SESSION['user_id'];
  $titreNotes = 'Notes (mes notes)';
endif;

// Filtre type (commun aux 2 cas)
if ($type !== 'Tout'):
  $where[] = 'no.no_tyno_id = :type_id';
  $params[':type_id'] = (int)$type;
endif;

// Recherche titre (complementaire)
if ($critere !== ''):
  $where[] = 'no.no_nom LIKE :critere';
  $params[':critere'] = '%' . $critere . '%';
endif;

$whereSql = '';
if (!empty($where)) $whereSql = ' WHERE ' . implode(' AND ', $where);

// Preparation pagination
$page_source = $_SESSION['page_notes'];
include('include/pagination/prepa_pagination.php');

$totalSql = 'SELECT COUNT(DISTINCT no.no_id) FROM dd_notes no' . $join . $whereSql;
$stmtTotal = $db->prepare($totalSql);
foreach ($params as $k => $v) {
  $stmtTotal->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmtTotal->execute();
$nb = (int)$stmtTotal->fetchColumn();

$pm = ($nbp > 0) ? (int)ceil($nb / $nbp) : 1;
if ($pm < 1) $pm = 1;
if ($page < 1) $page = 1;
if ($page > $pm) $page = $pm;

$offset = $nbp * ($page - 1);

$paramsUrl = array();
if ($campagne > 0) $paramsUrl['campagne'] = $campagne;
$paramsUrl['type'] = ($type === 'Tout') ? 'Tout' : (string)$type;
if ($critere !== '') $paramsUrl['critere'] = $critere;

$pagination = '';
if ($pm > 1):
  $inferieur = '';
  $superieur = '';

  if ($page > 1):
    $prevParams = $paramsUrl;
    $prevParams['page'] = $page - 1;
    $inferieur = '<a href="' . $_SERVER['PHP_SELF'] . '?' . http_build_query($prevParams) . '">< page pr&eacute;c&eacute;dente</a>';
  endif;

  if ($page < $pm):
    $nextParams = $paramsUrl;
    $nextParams['page'] = $page + 1;
    $superieur = '<a href="' . $_SERVER['PHP_SELF'] . '?' . http_build_query($nextParams) . '">page suivante ></a>';
  endif;

  $pagination = '<div class="pagination"><div class="gauche agauche">' . $inferieur . '</div><div class="droite adroite">' . $superieur . '</div></div>';
endif;

$dataSql = 'SELECT DISTINCT no.* FROM dd_notes no' . $join . $whereSql . ' ORDER BY no.no_nom LIMIT :limit OFFSET :offset';
$stmtNo = $db->prepare($dataSql);
foreach ($params as $k => $v) {
  $stmtNo->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmtNo->bindValue(':limit', (int)$nbp, PDO::PARAM_INT);
$stmtNo->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmtNo->execute();
$rowsNo = $stmtNo->fetchAll(PDO::FETCH_ASSOC);
$num_rows_no = count($rowsNo);

$typeFormValue = ($type === 'Tout') ? 'Tout' : (string)$type;
?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-personnages.js'></script>
</head>

<body>
  <DIV id="page">
  <? include("include/header.php"); ?>
  <? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA"><? echo $titreNotes; ?></div>
      <div><? if ($_SESSION['mj']==1) echo '<i class="icon lien fa-solid fa-pen-to-square" onClick="modifierNote(\'n\', \''.$typeFormValue.'\')"></i>'; ?></div>
    </div>

    <!--- Menu secondaire --->
    <div class="search-container">
      <form action="notes.php" method="get" name="search-no" id="search-no" class="search-form">
        <? if ($campagne > 0) echo '<input type="hidden" name="campagne" value="'.$campagne.'">'; ?>
        <input type="hidden" name="type" value="<? echo htmlspecialchars($typeFormValue); ?>">
        <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere); ?>" size="20" placeholder="Titre de la note" onClick="myFocus(this)"/>
        <button type="submit" class="search-button" id="search"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
      <form action="notes.php" method="get" class="search-form">
        <? if ($campagne > 0) echo '<input type="hidden" name="campagne" value="'.$campagne.'">'; ?>
        <input type="hidden" name="critere" value="<? echo htmlspecialchars($critere); ?>">
        <select name="type"  class="search-select">
          <? echo OptionList("dd_types_notes", "tyno", "nom", $typeFormValue, "", 0, "Tout"); ?>
        </select>
        <button type="submit" class="search-button" id="search_note"><i class="fa-solid fa-magnifying-glass"></i></button>
      </form>
    </div>

    <?
    if ($_SESSION['debug']==1 && $_SESSION['mj']==1):
      echo '<div class="mt10 mb20">(count) '.htmlspecialchars($totalSql).'</div>';
      echo '<div class="mt10 mb20">(data) '.htmlspecialchars($dataSql).'</div>';
    endif;

    if ($num_rows_no > 0):
      echo $pagination;
      echo '<div class="item entete">';
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
      if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
      echo '  <div class="nom_note">Nom</div>';
      echo '  <div class="categorie_note">Type</div>';
      echo '  <div class="niveau_note">Niveau</div>';
      echo '</div>';
      echo '<div class="liste-items">';
      foreach ($rowsNo as $dnno):
        echo '<div id ="no'.$dnno['no_id'].'" class="item data">';
        include('include/insert/'.$_SESSION['rulesetRep'].'/ligneNote.php');
        echo $ligne;
        echo '</div>';
      endforeach;
      echo '</div>';
    else:
      if ($type !== "Tout"):
        echo '<div class="nodata">Aucune note dans la cat&eacute;gorie '.libelle("dd_types_notes","tyno","nom",$type).' !</div>';
      else:
        echo '<div class="nodata">Aucune note disponible !</div>';
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
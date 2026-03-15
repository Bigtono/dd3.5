<?php
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include_once("include/noteslib.inc.php");

$userId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$critere = isset($_GET['critere']) ? trim((string)$_GET['critere']) : '';
$typeRaw = isset($_GET['type']) ? trim((string)$_GET['type']) : 'Tout';
$campagneRaw = isset($_GET['campagne']) ? trim((string)$_GET['campagne']) : 'Tout';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$type = 'Tout';
if ($typeRaw !== '' && $typeRaw !== 'Tout' && ctype_digit($typeRaw)) $type = (int)$typeRaw;

$campagneFilter = 'Tout';
if ($campagneRaw !== '' && $campagneRaw !== 'Tout' && ctype_digit($campagneRaw)) $campagneFilter = (int)$campagneRaw;

$hasTagsTable = notes_table_exists('dd_tags');
$hasNotesTagsTable = notes_table_exists('dd_notes_tags');
$hasTagsTables = $hasTagsTable && $hasNotesTagsTable;
$selectedTags = [];
if ($hasTagsTables && isset($_GET['tags']) && is_array($_GET['tags'])):
  foreach ($_GET['tags'] as $tagRaw):
    $tagId = (int)$tagRaw;
    if ($tagId > 0) $selectedTags[$tagId] = $tagId;
  endforeach;
endif;
$selectedTags = array_values($selectedTags);

$campagnes = [];
$stmtCampagnes = $db->prepare('SELECT camp_id, camp_nom FROM dd_campagnes ORDER BY camp_nom ASC');
$stmtCampagnes->execute();
$campagnes = $stmtCampagnes->fetchAll(PDO::FETCH_ASSOC);

$tagsDisponibles = [];
if ($hasTagsTable):
  $stmtTags = $db->prepare('SELECT tag_id, tag_nom FROM dd_tags WHERE tag_j_id=:user_id ORDER BY tag_nom ASC');
  $stmtTags->execute([':user_id' => $userId]);
  $tagsDisponibles = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
endif;

$where = [];
$params = [];
$where[] = 'no.no_j_id = :user_id';
$params[':user_id'] = $userId;

if ($type !== 'Tout'):
  $where[] = 'no.no_tyno_id = :type_id';
  $params[':type_id'] = (int)$type;
endif;

if ($campagneFilter !== 'Tout'):
  $where[] = 'EXISTS (SELECT 1 FROM dd_campagnes_notes cpno WHERE cpno.cpno_no_id = no.no_id AND cpno.cpno_camp_id = :camp_id)';
  $params[':camp_id'] = (int)$campagneFilter;
endif;

if ($critere !== ''):
  $mots = preg_split('/\s+/', $critere);
  $iMot = 0;
  foreach ($mots as $mot):
    $mot = trim((string)$mot);
    if ($mot === '') continue;
    $iMot++;
    $pMot = ':mot' . $iMot;
    $where[] = '(no.no_nom LIKE ' . $pMot . ' OR EXISTS (SELECT 1 FROM dd_notes_contenus nc WHERE nc.noc_no_id = no.no_id AND nc.noc_texte LIKE ' . $pMot . '))';
    $params[$pMot] = '%' . $mot . '%';
  endforeach;
endif;

if ($hasTagsTables && !empty($selectedTags)):
  $iTag = 0;
  foreach ($selectedTags as $tagId):
    $iTag++;
    $pTag = ':tag' . $iTag;
    $where[] = 'EXISTS (SELECT 1 FROM dd_notes_tags nt WHERE nt.notag_no_id = no.no_id AND nt.notag_tag_id = ' . $pTag . ')';
    $params[$pTag] = (int)$tagId;
  endforeach;
endif;

$whereSql = '';
if (!empty($where)) $whereSql = ' WHERE ' . implode(' AND ', $where);

$nbp = 25;
$stmtTotal = $db->prepare('SELECT COUNT(*) FROM dd_notes no' . $whereSql);
foreach ($params as $k => $v) {
  $stmtTotal->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmtTotal->execute();
$nb = (int)$stmtTotal->fetchColumn();
$pm = ($nbp > 0) ? (int)ceil($nb / $nbp) : 1;
if ($pm < 1) $pm = 1;
if ($page > $pm) $page = $pm;
$offset = $nbp * ($page - 1);

$sqlData = 'SELECT no.no_id, no.no_nom, no.no_tyno_id FROM dd_notes no' . $whereSql . ' ORDER BY no.no_nom ASC LIMIT :limit OFFSET :offset';
$stmtData = $db->prepare($sqlData);
foreach ($params as $k => $v) {
  $stmtData->bindValue($k, $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmtData->bindValue(':limit', (int)$nbp, PDO::PARAM_INT);
$stmtData->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmtData->execute();
$rowsNo = $stmtData->fetchAll(PDO::FETCH_ASSOC);

$tagsByNote = [];
if ($hasTagsTables && !empty($rowsNo)):
  $noteIds = [];
  foreach ($rowsNo as $rowNo) $noteIds[] = (int)$rowNo['no_id'];
  $inParts = [];
  foreach ($noteIds as $idx => $noteId):
    $inParts[] = ':nid' . $idx;
  endforeach;
  $sqlTags = 'SELECT nt.notag_no_id, t.tag_nom
              FROM dd_notes_tags nt
              INNER JOIN dd_tags t ON t.tag_id = nt.notag_tag_id
              WHERE nt.notag_no_id IN (' . implode(',', $inParts) . ')
              ORDER BY t.tag_nom ASC';
  $stmtTagsByNote = $db->prepare($sqlTags);
  foreach ($noteIds as $idx => $noteId):
    $stmtTagsByNote->bindValue(':nid' . $idx, (int)$noteId, PDO::PARAM_INT);
  endforeach;
  $stmtTagsByNote->execute();
  while ($rowTag = $stmtTagsByNote->fetch(PDO::FETCH_ASSOC)):
    $noteId = (int)$rowTag['notag_no_id'];
    if (!isset($tagsByNote[$noteId])) $tagsByNote[$noteId] = [];
    $tagsByNote[$noteId][] = (string)$rowTag['tag_nom'];
  endwhile;
endif;

$queryBase = [
  'critere' => $critere,
  'type' => ($type === 'Tout' ? 'Tout' : (string)$type),
  'campagne' => ($campagneFilter === 'Tout' ? 'Tout' : (string)$campagneFilter),
];
if ($hasTagsTables && !empty($selectedTags)) $queryBase['tags'] = $selectedTags;

$pagination = '';
if ($pm > 1):
  $prevLink = '';
  $nextLink = '';
  if ($page > 1):
    $qPrev = $queryBase;
    $qPrev['page'] = $page - 1;
    $prevLink = '<a href="notes.php?' . http_build_query($qPrev) . '">< page pr&eacute;c&eacute;dente</a>';
  endif;
  if ($page < $pm):
    $qNext = $queryBase;
    $qNext['page'] = $page + 1;
    $nextLink = '<a href="notes.php?' . http_build_query($qNext) . '">page suivante ></a>';
  endif;
  $pagination = '<div class="pagination"><div class="gauche agauche">' . $prevLink . '</div><div class="droite adroite">' . $nextLink . '</div></div>';
endif;
?>
<!doctype html>
<html>

<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>
  <style>
    .notes-filter-form {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: stretch;
      margin-bottom: 10px;
    }

    .notes-filters-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .notes-filter-group {
      display: flex;
      align-items: center;
      min-width: 220px;
    }

    .notes-filter-group .search-input,
    .notes-filter-group .search-select {
      min-width: 180px;
      flex: 1 1 auto;
      border-right: 2px solid #ccc;
      border-radius: 999px;
    }

    .notes-tags-panel {
      width: 100%;
      margin-top: 5px;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #fafafa;
    }

    .notes-tags-panel label {
      display: inline-block;
      margin-right: 12px;
      margin-bottom: 6px;
    }
  </style>
</head>

<body>
  <DIV id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">Notes (mes notes)</div>
        <div><i class="icon lien fa-solid fa-pen-to-square" onClick="modifierNote('n', 0)"></i></div>
      </div>

      <div class="search-container">
        <form action="notes.php" method="get" id="search-no" class="notes-filter-form">
          <div class="notes-filters-row">
            <div class="notes-filter-group">
              <input type="text" class="search-input" name="critere" value="<? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Mots dans titre ou contenus" />
            </div>

            <div class="notes-filter-group">
              <select name="type" class="search-select">
                <? echo OptionList("dd_types_notes", "tyno", "nom", ($type === 'Tout' ? 'Tout' : (string)$type), "", 0, "Tout"); ?>
              </select>
            </div>

            <div class="notes-filter-group">
              <select name="campagne" class="search-select">
                <option value="Tout"<? if ($campagneFilter === 'Tout') echo ' selected="selected"'; ?>>Toutes les campagnes</option>
                <?
                foreach ($campagnes as $camp):
                  $cid = (int)$camp['camp_id'];
                  $sel = ($campagneFilter !== 'Tout' && (int)$campagneFilter === $cid) ? ' selected="selected"' : '';
                  echo '<option value="' . $cid . '"' . $sel . '>' . htmlspecialchars((string)$camp['camp_nom'], ENT_QUOTES, 'UTF-8') . '</option>';
                endforeach;
                ?>
              </select>
            </div>

            <div class="notes-filter-group" style="min-width:auto;">
              <button type="submit" class="search-button" id="search" title="Appliquer les filtres"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
          </div>

          <? if ($hasTagsTable): ?>
            <div class="gras mr10 mt5 lien" onCLick="toggle('filtre-tags')">Tags <span id="toggle-filtre-tags"><i class="fa-solid fa-bars"></i></span></div>
            <div id="filtre-tags" class="notes-tags-panel<? if (empty($selectedTags)) echo ' noDisplay'; ?>">
              <?
              if (!empty($tagsDisponibles)):
                foreach ($tagsDisponibles as $tag):
                  $tid = (int)$tag['tag_id'];
                  $checked = in_array($tid, $selectedTags, true) ? ' checked="checked"' : '';
                  echo '<label><input type="checkbox" name="tags[]" value="' . $tid . '"' . $checked . ' onchange="document.getElementById(\'search-no\').submit();"> ' . htmlspecialchars((string)$tag['tag_nom'], ENT_QUOTES, 'UTF-8') . '</label>';
                endforeach;
              else:
                echo '<div class="nodata">Aucun tag disponible.</div>';
              endif;
              ?>
            </div>
          <? endif; ?>
        </form>
      </div>

      <?
      if (!empty($rowsNo)):
        echo $pagination;
        echo '<div class="item entete">';
        echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
        echo '  <div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';
        echo '  <div class="nom_note">Nom</div>';
        echo '  <div class="categorie_note">Type</div>';
        echo '  <div class="niveau_note">Tags</div>';
        echo '</div>';

        echo '<div class="liste-items">';
        foreach ($rowsNo as $dnno):
          $noteId = (int)$dnno['no_id'];
          $click = 'afficherNote(' . $noteId . ',999)';
          $nom = stripslashes(ucfirst((string)$dnno['no_nom']));

          $tagsBubbles = '<span class="small">-</span>';
          if (isset($tagsByNote[$noteId]) && !empty($tagsByNote[$noteId])):
            $tmp = [];
            foreach ($tagsByNote[$noteId] as $tagNom):
              $tmp[] = '<span class="mr5" style="display:inline-block;padding:2px 8px;border:1px solid #999;border-radius:12px;font-size:12px;line-height:1.4;">' . htmlspecialchars((string)$tagNom, ENT_QUOTES, 'UTF-8') . '</span>';
            endforeach;
            $tagsBubbles = implode('', $tmp);
          endif;

          echo '<div id="no' . $noteId . '" class="item data">';
          echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_notes\',\'no\',' . $noteId . ')"><i class="fa fa-trash"></i></span></div>';
          echo '  <div class="icone_modif"><span onclick="modifierNote(' . $noteId . ',0)"><i class="fa fa-pencil"></i></span></div>';
          echo '  <div class="nom_note" onclick="' . $click . '">' . htmlspecialchars($nom, ENT_QUOTES, 'UTF-8') . '</div>';
          echo '  <div class="categorie_note" onclick="' . $click . '">' . libelle("dd_types_notes", "tyno", "nom", $dnno['no_tyno_id']) . '</div>';
          echo '  <div class="niveau_note" onclick="' . $click . '">' . $tagsBubbles . '</div>';
          echo '</div>';
        endforeach;
        echo '</div>';
      else:
        echo '<div class="nodata">Aucune note disponible !</div>';
      endif;
      ?>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
  </DIV>
</body>
<div id="detail-pp"></div>
<div id="modification"></div>

</html>

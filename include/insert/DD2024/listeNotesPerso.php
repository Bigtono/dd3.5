<?
$filtreTypeSql = '';
if (isset($_GET['type']) && ctype_digit((string)$_GET['type']) && (int)$_GET['type'] > 0):
  $filtreTypeSql = ' AND no_tyno_id="' . (int)$_GET['type'] . '"';
endif;

$requete_no = 'SELECT * FROM dd_notes JOIN dd_types_notes ON no_tyno_id=tyno_id LEFT JOIN dd_personnages_notes ON no_id=pno_no_id WHERE pno_pe_id="' . $p . '" AND pno_dd > 0' . $filtreTypeSql;
$result_no = queryPDO($requete_no);
$num_rows_no = $result_no->rowCount();
$rowsNo = $result_no->fetchAll(PDO::FETCH_ASSOC);

$tagsByNote = [];
if (!empty($rowsNo)):
  $hasTagsTables = false;
  try {
    $testTags = queryPDO("SHOW TABLES LIKE 'dd_tags'");
    $testNotesTags = queryPDO("SHOW TABLES LIKE 'dd_notes_tags'");
    $hasTagsTables = ($testTags && $testTags->rowCount() > 0 && $testNotesTags && $testNotesTags->rowCount() > 0);
  } catch (Throwable $e) {
    $hasTagsTables = false;
  }

  if ($hasTagsTables):
    $noteIds = [];
    foreach ($rowsNo as $rowNo):
      $noteIds[] = (int)$rowNo['no_id'];
    endforeach;
    $noteIds = array_values(array_unique($noteIds));

    if (!empty($noteIds)):
      $sqlTags = 'SELECT nt.notag_no_id, t.tag_nom
                  FROM dd_notes_tags nt
                  INNER JOIN dd_tags t ON t.tag_id = nt.notag_tag_id
                  WHERE nt.notag_no_id IN (' . implode(',', $noteIds) . ')
                  ORDER BY t.tag_nom ASC';
      $resultTags = queryPDO($sqlTags);
      while ($rowTag = $resultTags->fetch(PDO::FETCH_ASSOC)):
        $noteIdTag = (int)$rowTag['notag_no_id'];
        if (!isset($tagsByNote[$noteIdTag])) $tagsByNote[$noteIdTag] = [];
        $tagsByNote[$noteIdTag][] = (string)$rowTag['tag_nom'];
      endwhile;
    endif;
  endif;
endif;

if ($num_rows_no > 0):
  if ($_SESSION['debug'] == 1) echo '<div>' . $debug . '</div>';
  echo $pagination;
  echo '<div class="item entete">';
  if ($_SESSION['mj'] == 1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
  if ($_SESSION['mj'] == 1) echo '  <div class="icone_modif"><i class="fa fa-pencil"></i></div>';
  echo '  <div class="nom_note">Nom</div>';
  echo '  <div class="categorie_note">Type</div>';
  echo '  <div class="niveau_note">Tags</div>';
  echo '</div>';

  foreach ($rowsNo as $dnno):
    echo '<div class="item data notes-row">';
    $accreditation = isset($dnno['pno_dd']) ? (int)$dnno['pno_dd'] : (isset($dnno['pno_niveau']) ? (int)$dnno['pno_niveau'] : 0);
    $click = 'afficherNote(' . $dnno['no_id'] . ',' . $accreditation . ')';
    $nom = stripslashes(ucfirst($dnno['no_nom']));
    $idno = '';
    if ($_SESSION['debug'] == 1 && $_SESSION['mj'] == 1) $idno = ' (' . $dnno['no_id'] . ')';

    $tagsBubbles = '<span class="small">-</span>';
    $noteId = (int)$dnno['no_id'];
    if (isset($tagsByNote[$noteId]) && !empty($tagsByNote[$noteId])):
      $tmp = [];
      foreach ($tagsByNote[$noteId] as $tagNom):
        $tmp[] = '<span class="notes-tag-pill">' . htmlspecialchars((string)$tagNom, ENT_QUOTES, 'UTF-8') . '</span>';
      endforeach;
      $tagsBubbles = '<div class="notes-tags-wrap">' . implode('', $tmp) . '</div>';
    endif;

    if ($_SESSION['mj'] == 1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_notes\',\'no\',' . $dnno['no_id'] . ')"><i class="fa fa-trash"></i></span></div>';
    if ($_SESSION['mj'] == 1) echo '  <div class="icone_modif"><span onclick="modifierNote(' . $dnno['no_id'] . ',' . $p . ')"><i class="fa fa-pencil"></i></span></div>';
    echo '  <div id="nomNo' . $dnno['no_id'] . '" class="nom_note" onclick="' . $click . '">' . $nom . $idno . '</div>';
    echo '  <div id="catNo' . $dnno['no_id'] . '" class="categorie_note" onclick="' . $click . '">' . libelle("dd_types_notes", "tyno", "nom", $dnno['no_tyno_id']) . '</div>';
    echo '  <div class="niveau_note notes-tags-column" onclick="' . $click . '">' . $tagsBubbles . '</div>';
    echo '</div>';
  endforeach;
else:
  if (isset($_GET["type"]) && ctype_digit((string)$_GET["type"]) && (int)$_GET["type"] > 0):
    echo '<div class="nodata">Aucune note dans la cat&eacute;gorie ' . libelle("dd_types_notes", "tyno", "nom", $_GET["type"]) . ' !</div>';
  else:
    echo '<div class="nodata">Aucune note disponible !</div>';
  endif;
endif;
?>

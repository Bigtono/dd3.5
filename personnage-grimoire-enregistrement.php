<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include_once("include/insert/common/personnage_grimoire_helper.php");

function pg_magic_build_insert_sql($tableName, array $columns)
{
  $placeholders = [];
  foreach ($columns as $col) {
    $placeholders[] = ':' . $col;
  }
  return 'INSERT INTO ' . $tableName . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
}

function pg_magic_redirect_grimoire($personnageId, $campagneId, $msg)
{
  $url = 'personnage-grimoire.php?personnage=' . (int)$personnageId;
  if ((int)$campagneId > 0) $url .= '&campagne=' . (int)$campagneId;
  $url .= '&msg=' . (int)$msg;
  header('Location: ' . $url);
  exit;
}

$personnageId = isset($_POST['mp_magic_personnage_id']) ? (int)$_POST['mp_magic_personnage_id'] : 0;
$campagneId = isset($_POST['campagne']) ? (int)$_POST['campagne'] : (isset($_GET['campagne']) ? (int)$_GET['campagne'] : 0);

if (!isset($_POST['ok']) || $personnageId <= 0) {
  pg_magic_redirect_grimoire($personnageId, $campagneId, 0);
}

$stmtPerso = $db->prepare("SELECT pe_j_id, pe_camp_id FROM dd_personnages WHERE pe_id = :pid LIMIT 1");
$stmtPerso->execute([':pid' => $personnageId]);
$currentPerso = $stmtPerso->fetch(PDO::FETCH_ASSOC);
if (!$currentPerso) {
  pg_magic_redirect_grimoire($personnageId, $campagneId, 0);
}

$isPersonnageOwner = isset($_SESSION['user_id']) && (int)$currentPerso['pe_j_id'] === (int)$_SESSION['user_id'];
$campaignOwnerId = 0;
if ((int)$currentPerso['pe_camp_id'] > 0):
  $stmtCamp = $db->prepare("SELECT camp_j_id FROM dd_campagnes WHERE camp_id = :cid LIMIT 1");
  $stmtCamp->execute([':cid' => (int)$currentPerso['pe_camp_id']]);
  $camp = $stmtCamp->fetch(PDO::FETCH_ASSOC);
  if ($camp) $campaignOwnerId = (int)$camp['camp_j_id'];
endif;

$isCampaignOwner = (int)$currentPerso['pe_camp_id'] > 0
  && isset($_SESSION['user_id'])
  && $campaignOwnerId === (int)$_SESSION['user_id'];
$canEditPersonnage = $isPersonnageOwner || $isCampaignOwner;
if (!$canEditPersonnage) {
  pg_magic_redirect_grimoire($personnageId, $campagneId, 0);
}

$selectedFilter = isset($_POST['mp_magic_filter']) ? (int)$_POST['mp_magic_filter'] : 0;
pg_magic_set_session_filter($personnageId, $selectedFilter);

include("include/affichageSelectionSources.php");
$resourceIds = pg_magic_parse_resource_ids(isset($selection) ? $selection : '');
$context = pg_magic_load_context($db, $personnageId, (string)$_SESSION['rulesetRep'], $resourceIds);

$payloadRaw = isset($_POST['mp_magic_state']) ? (string)$_POST['mp_magic_state'] : '';
$payload = json_decode($payloadRaw, true);
if (!is_array($payload)) {
  $payload = [];
}
$payloadClasses = (isset($payload['classes']) && is_array($payload['classes'])) ? $payload['classes'] : [];

$touched = [];
if (isset($payload['touched_class_ids']) && is_array($payload['touched_class_ids'])) {
  foreach ($payload['touched_class_ids'] as $rawPcId) {
    $pcId = (int)$rawPcId;
    if ($pcId > 0 && isset($context['classes'][(string)$pcId])) $touched[$pcId] = true;
    if ($pcId > 0 && isset($context['classes'][$pcId])) $touched[$pcId] = true;
  }
}
if (empty($touched)) {
  foreach ($payloadClasses as $pcIdRaw => $classPayload) {
    $pcId = (int)$pcIdRaw;
    if ($pcId > 0 && (isset($context['classes'][(string)$pcId]) || isset($context['classes'][$pcId]))) {
      $touched[$pcId] = true;
    }
  }
}

if (empty($touched)) {
  pg_magic_redirect_grimoire($personnageId, $campagneId, 1);
}

$sortColumns = pg_magic_table_columns($db, 'dd_personnages_sorts');
$prepareColumns = pg_magic_table_columns($db, 'dd_personnages_sorts_prepares');

if (empty($sortColumns) || !in_array('pes_so_id', $sortColumns, true)) {
  pg_magic_redirect_grimoire($personnageId, $campagneId, 0);
}

$sortInsertCols = [];
foreach (['pes_pc_id', 'pes_so_id', 'pes_connu', 'pes_compris'] as $col) {
  if (in_array($col, $sortColumns, true)) $sortInsertCols[] = $col;
}
$sortInsertStmt = $db->prepare(pg_magic_build_insert_sql('dd_personnages_sorts', $sortInsertCols));

try {
  $db->beginTransaction();

  foreach (array_keys($touched) as $pcIdTouched) {
    $pcIdTouched = (int)$pcIdTouched;
    $classData = isset($context['classes'][$pcIdTouched]) ? $context['classes'][$pcIdTouched] : (isset($context['classes'][(string)$pcIdTouched]) ? $context['classes'][(string)$pcIdTouched] : null);
    if (!$classData) continue;

    $claId = (int)$classData['cla_id'];
    $payloadClass = isset($payloadClasses[$pcIdTouched]) ? $payloadClasses[$pcIdTouched] : (isset($payloadClasses[(string)$pcIdTouched]) ? $payloadClasses[(string)$pcIdTouched] : []);
    $payloadSpells = (is_array($payloadClass) && isset($payloadClass['spells']) && is_array($payloadClass['spells'])) ? $payloadClass['spells'] : [];

    $allowedSpellIds = [];
    foreach ($classData['spells'] as $soId => $spellData) {
      $allowedSpellIds[(int)$soId] = true;
    }
    if (empty($allowedSpellIds)) continue;

// Nettoyage dd_personnages_sorts pour cette classe touchee (schéma PC strict)
if (in_array('pes_pc_id', $sortColumns, true)) {
  $stmtDeleteSort = $db->prepare('DELETE FROM dd_personnages_sorts WHERE pes_pc_id = :pcid');
  $stmtDeleteSort->execute([':pcid' => $pcIdTouched]);
}

    foreach ($classData['spells'] as $soId => $spellData) {
      $soId = (int)$soId;
      $rawState = isset($payloadSpells[$soId]) ? $payloadSpells[$soId] : (isset($payloadSpells[(string)$soId]) ? $payloadSpells[(string)$soId] : []);

      $known = is_array($rawState) ? pg_magic_normalize_state_flag(isset($rawState['known']) ? $rawState['known'] : 0) : 0;
      $understood = is_array($rawState) ? pg_magic_normalize_state_flag(isset($rawState['understood']) ? $rawState['understood'] : 0) : 0;
      $prepared = is_array($rawState) ? pg_magic_normalize_state_flag(isset($rawState['prepared']) ? $rawState['prepared'] : 0) : 0;

      if ((int)$classData['sort_known_all'] === 1) {
        $known = 1;
      }
      if ($known === 0) {
        $prepared = 0;
        if ((int)$classData['sort_auto_understood'] !== 1) {
          $understood = 0;
        }
      }
      if ((int)$classData['sort_auto_understood'] === 1 && $known === 1) {
        $understood = 1;
      }

      $persistKnownOnly = ($known === 1 && (int)$classData['sort_known_all'] !== 1);
      $persistInSortTable = $persistKnownOnly || $understood === 1 || $prepared === 1;

      if ($persistInSortTable) {
        $rowSort = [];
        foreach ($sortInsertCols as $col) {
          if ($col === 'pes_pc_id') $rowSort[$col] = $pcIdTouched;
          elseif ($col === 'pes_so_id') $rowSort[$col] = $soId;
          elseif ($col === 'pes_connu') $rowSort[$col] = $known;
          elseif ($col === 'pes_compris') $rowSort[$col] = $understood;
        }
        $sortInsertStmt->execute($rowSort);
      }

    }
  }

  $db->commit();
  pg_magic_redirect_grimoire($personnageId, $campagneId, 1);
} catch (Exception $e) {
  if ($db->inTransaction()) $db->rollBack();
  pg_magic_redirect_grimoire($personnageId, $campagneId, 0);
}

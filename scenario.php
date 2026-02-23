<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/pagination.php");
include("include/list_helpers.inc.php");

$scenario_id = isset($_GET['scenario']) ? (int)$_GET['scenario'] : 0;

$stmt = $db->prepare("
  SELECT sc.sc_id, sc.sc_nom, sc.sc_description,
         c.camp_id, c.camp_nom
  FROM dd_scenarios sc
  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id
  WHERE sc.sc_id = ?
");
$stmt->execute([$scenario_id]);

$scenario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$scenario):
    die('Scénario introuvable');
endif;

// chapitres
$stmtChap = $db->prepare("
  SELECT scc_id, scc_nom
  FROM dd_scenarios_chapitres
  WHERE scc_sc_id = ?
  ORDER BY scc_nom
");
$stmtChap->execute([$scenario_id]);
$chapitres = $stmtChap->fetchAll(PDO::FETCH_ASSOC);

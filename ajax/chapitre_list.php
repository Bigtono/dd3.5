<?
include('../include/dblib.inc.php');

$scenario_id = isset($_GET['scenario']) ? (int)$_GET['scenario'] : 0;

$stmt = $db->prepare("
  SELECT scc_id, scc_nom
  FROM dd_scenarios_chapitres
  WHERE scc_sc_id = ?
  ORDER BY scc_nom
");
$stmt->execute([$scenario_id]);

$chapitres = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($chapitres as $ch):
?>

    <div class="list-row">

        <div class="col action-col action-delete">
            <i class="fa fa-trash btn-delete-chapitre"
                data-ch-id="<?= $ch['scc_id'] ?>"></i>
        </div>

        <div class="col action-col action-edit">
            <a href="chapitre.php?chapitre=<?= $ch['scc_id'] ?>">
                <i class="fa fa-edit"></i>
            </a>
        </div>

        <div class="col">
            <?= htmlspecialchars($ch['scc_nom']) ?>
        </div>

    </div>

<? endforeach ?>
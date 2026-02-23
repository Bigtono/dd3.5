<?
include('../include/dblib.inc.php');

$camp_id = (int)$_GET['camp_id'];

$stmt = $db->prepare("
  SELECT sc_id, sc_nom
  FROM dd_scenarios
  WHERE sc_camp_id = ?
  ORDER BY sc_nom
");
$stmt->execute([$camp_id]);
$scenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($scenarios as $sc):
?>

    <div class="list-row">

        <div class="col action-col action-delete">
            <i class="fa fa-trash btn-delete-scenario"
                data-sc-id="<?= $sc['sc_id'] ?>"></i>
        </div>

        <div class="col action-col action-edit">
            <a href="scenario.php?sc_id=<?= $sc['sc_id'] ?>">
                <i class="fa fa-edit"></i>
            </a>
        </div>

        <div class="col">
            <?= htmlspecialchars($sc['sc_nom']) ?>
        </div>

    </div>

<? endforeach ?>
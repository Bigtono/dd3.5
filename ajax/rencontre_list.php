<?
include('../include/dblib.inc.php');

$chapitre_id = isset($_GET['chapitre']) ? (int)$_GET['chapitre'] : 0;

$stmt = $db->prepare("
  SELECT re_id, re_nom
  FROM dd_rencontres
  WHERE re_scc_id = ?
  ORDER BY re_nom
");
$stmt->execute([$chapitre_id]);

$rencontres = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rencontres as $re):
?>

    <div class="list-row">

        <div class="col action-col action-delete">
            <i class="fa fa-trash btn-delete-rencontre"
                data-re-id="<?= $re['re_id'] ?>"></i>
        </div>

        <div class="col action-col action-edit">
            <a href="rencontre.php?rencontre=<?= $re['re_id'] ?>">
                <i class="fa fa-edit"></i>
            </a>
        </div>

        <div class="col">
            <?= htmlspecialchars($re['re_nom']) ?>
        </div>

    </div>

<? endforeach ?>
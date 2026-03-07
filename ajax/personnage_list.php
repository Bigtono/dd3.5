<?

include("../include/session.php");
include('../include/dblib.inc.php');
include('../include/diverslib.inc.php');

$camp_id = isset($_GET['camp_id']) ? (int)$_GET['camp_id'] : 0;

if ($camp_id <= 0) exit;

$sql = "
  SELECT
    p.pe_id,
    p.pe_nom,
    p.pe_ra_id,
    p.pe_j_id,
    j.j_nom AS joueur_nom
  FROM dd_personnages p
  LEFT JOIN dd_joueurs j ON j.j_id = p.pe_j_id
  WHERE p.pe_camp_id = :campaign
  ORDER BY p.pe_nom
";
$stmt = $db->prepare($sql);
$stmt->execute([
  ':campaign' => $camp_id,
]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $dn):
  $complement = "&campagne=" . $camp_id;
?>
  <div class="list-row">
    <div class="col action-col">
      <a href="campagnes.php?action=supprimer&id=<?= (int)$camp_id ?>"
        class="action-delete"
        title="Supprimer">
        <i class="fa-solid fa-trash"></i>
      </a>
    </div>

    <div class="col action-col">
      <a href="personnage-modifier.php?personnage=<?= (int)$dn['pe_id'] . $complement ?>"
        class="action-edit"
        title="Modifier">
        <i class="fa-solid fa-pen-to-square"></i>
      </a>
    </div>

    <div class="col">
      <a href="personnage.php?personnage=<?= (int)$dn['pe_id']; ?>&campagne=<?= (int)$camp_id; ?>"><?= htmlspecialchars($dn['pe_nom']) ?></a>
    </div>

    <div class="col">
      <?= htmlspecialchars($dn['joueur_nom']) ?>
    </div>

    <div class="col">
      <?= libelle("dd_races", "ra", "nom", $dn['pe_ra_id']) ?>
    </div>

    <div class="col3">
      <?= classesPersonnage($dn['pe_id']) ?>
    </div>
  </div>
<? endforeach; ?>


<?

include("../include/session.php");
include('../include/dblib.inc.php');

$pe_id = isset($_GET['personnage']) ? (int)$_GET['personnage'] : 0;
if ($pe_id <= 0) exit;

$sqlPerso = "
  SELECT pe_id, pe_camp_id
  FROM dd_personnages
  WHERE pe_id = :pe_id
  LIMIT 1
";
$stmtPerso = $db->prepare($sqlPerso);
$stmtPerso->execute([':pe_id' => $pe_id]);
$perso = $stmtPerso->fetch(PDO::FETCH_ASSOC);

if (!$perso):
  echo '<div class="personnage-campagne-empty">Personnage introuvable.</div>';
  exit;
endif;

$campagne = null;
if ((int)$perso['pe_camp_id'] > 0):
  $sqlCamp = "
    SELECT camp_id, camp_nom
    FROM dd_campagnes
    WHERE camp_id = :camp_id
    LIMIT 1
  ";
  $stmtCamp = $db->prepare($sqlCamp);
  $stmtCamp->execute([':camp_id' => (int)$perso['pe_camp_id']]);
  $campagne = $stmtCamp->fetch(PDO::FETCH_ASSOC);
endif;

if ($campagne):
?>
  <div class="personnage-campagne">
    <div class="personnage-campagne-label">Campagne :</div>
    <div class="personnage-campagne-value">
      <a href="campagne.php?campagne=<?= (int)$campagne['camp_id'] ?>"><?= htmlspecialchars($campagne['camp_nom'], ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <? if (isset($_SESSION['mj']) && (int)$_SESSION['mj'] === 1): ?>
      <button
        type="button"
        class="btRouge"
        id="btn-detach-personnage"
        data-personnage="<?= (int)$pe_id ?>">
        Detacher de la campagne
      </button>
    <? endif; ?>
  </div>
<?
else:
  echo '<div class="personnage-campagne-empty">Aucune campagne en cours pour ce personnage.</div>';
endif;

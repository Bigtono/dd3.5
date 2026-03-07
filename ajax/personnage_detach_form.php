<?

include("../include/session.php");
include('../include/dblib.inc.php');

$pe_id = isset($_GET['personnage']) ? (int)$_GET['personnage'] : 0;
$isMj = isset($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;

$perso = null;
if ($isMj && $pe_id > 0):
  $sql = "
    SELECT p.pe_id, p.pe_nom, p.pe_camp_id, c.camp_nom
    FROM dd_personnages p
    LEFT JOIN dd_campagnes c ON c.camp_id = p.pe_camp_id
    WHERE p.pe_id = :pe_id
    LIMIT 1
  ";
  $stmt = $db->prepare($sql);
  $stmt->execute([':pe_id' => $pe_id]);
  $perso = $stmt->fetch(PDO::FETCH_ASSOC);
endif;

?>

<div id="personnage-detach" class="affichage">
  <div class="detail">
    <div class="nom_objet">Detacher le personnage</div>

    <? if (!$isMj): ?>
      <div class="nodata">Acces refuse.</div>
      <div class="ligneBouton">
        <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Fermer</button>
      </div>
    <? elseif (!$perso): ?>
      <div class="nodata">Personnage introuvable.</div>
      <div class="ligneBouton">
        <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Fermer</button>
      </div>
    <? elseif ((int)$perso['pe_camp_id'] <= 0): ?>
      <div class="nodata">Ce personnage n'est rattache a aucune campagne.</div>
      <div class="ligneBouton">
        <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Fermer</button>
      </div>
    <? else: ?>
      <form id="form-detach-personnage">
        <input type="hidden" name="personnage" value="<?= (int)$perso['pe_id'] ?>">

        <div class="form-group">
          <label>Personnage</label>
          <div><?= htmlspecialchars($perso['pe_nom'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div class="form-group">
          <label>Campagne</label>
          <div><?= htmlspecialchars((string)$perso['camp_nom'], ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div class="ligneBouton">
          <button type="submit" class="btRouge">Detacher</button>
          <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Annuler</button>
        </div>
      </form>
    <? endif; ?>
  </div>
</div>

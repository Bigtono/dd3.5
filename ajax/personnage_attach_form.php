<?

include("../include/session.php");
include('../include/dblib.inc.php');

$camp_id = isset($_GET['camp_id']) ? (int)$_GET['camp_id'] : 0;
$ruleset = isset($_SESSION['ruleset']) ? (int)$_SESSION['ruleset'] : 0;

$personnages = [];
if ($camp_id > 0 && $ruleset > 0):
  $sql = "
    SELECT p.pe_id, p.pe_nom, j.j_pseudo
    FROM dd_personnages p
    LEFT JOIN dd_joueurs j ON j.j_id = p.pe_j_id
    WHERE p.pe_ruleset_var_id = :ruleset
      AND p.pe_camp_id = 0
    ORDER BY p.pe_nom
  ";
  $stmt = $db->prepare($sql);
  $stmt->execute([
    ':ruleset' => $ruleset,
  ]);
  $personnages = $stmt->fetchAll(PDO::FETCH_ASSOC);
endif;

?>

<div id="personnage" class="affichage">
  <div class="detail">
    <div class="nom_objet">Ajouter un personnage</div>

    <? if (!empty($personnages)): ?>
      <form id="form-attach-personnage">
        <input type="hidden" name="camp_id" value="<?= $camp_id ?>">

        <div class="form-group">
          <label>Personnage</label>
          <select name="pe_id" required>
            <option value="">Choisir...</option>
            <? foreach ($personnages as $perso):
              $libelle = $perso['pe_nom'];
              if (!empty($perso['j_pseudo'])) $libelle .= ' (' . $perso['j_pseudo'] . ')';
            ?>
              <option value="<?= (int)$perso['pe_id'] ?>"><?= htmlspecialchars($libelle) ?></option>
            <? endforeach; ?>
          </select>
        </div>

        <div class="ligneBouton">
          <button type="submit" class="btNoir">Valider</button>
          <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Annuler</button>
        </div>
      </form>
    <? else: ?>
      <div class="nodata">Aucun personnage disponible.</div>
      <div class="ligneBouton">
        <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Fermer</button>
      </div>
    <? endif; ?>
  </div>
</div>


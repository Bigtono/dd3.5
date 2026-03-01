<?
include('../include/dblib.inc.php');

$scenario_id = isset($_GET['scenario']) ? (int)$_GET['scenario'] : 0;
?>

<div class="detail">

    <div class="nom_objet">Créer un chapitre</div>

    <form id="form-create-chapitre">

        <input type="hidden" name="scenario_id" value="<?= $scenario_id ?>">

        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="scc_nom" required>
        </div>

        <div class="ligneBouton">
            <button type="submit" class="btNoir mr10">
                Créer
            </button>

            <button type="button" class="btDisabled btn-cancel-detail">
                Annuler
            </button>
        </div>

    </form>

</div>
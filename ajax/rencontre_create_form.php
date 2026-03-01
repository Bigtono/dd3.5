<?
include('../include/dblib.inc.php');

$chapitre_id = isset($_GET['chapitre']) ? (int)$_GET['chapitre'] : 0;
?>

<div class="detail">

    <div class="nom_objet">Créer une rencontre</div>

    <form id="form-create-rencontre">

        <input type="hidden" name="chapitre_id" value="<?= $chapitre_id ?>">

        <div class="form-group">
            <label>Nom</label>
            <input type="text" name="re_nom" required>
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
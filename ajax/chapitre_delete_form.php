<?
include('../include/dblib.inc.php');

$chapitre_id = isset($_GET['chapitre']) ? (int)$_GET['chapitre'] : 0;
?>

<div class="detail">

    <div class="nom_objet">Supprimer le chapitre</div>

    <form id="form-delete-chapitre">

        <input type="hidden" name="chapitre_id" value="<?= $chapitre_id ?>">

        <div class="form-group">
            <label>
                <input type="checkbox" name="delete_rencontres" value="1">
                Supprimer également les rencontres associées
            </label>
        </div>

        <div class="ligneBouton">
            <button type="submit" class="btRouge mr10">
                Confirmer
            </button>

            <button type="button" class="btDisabled btn-cancel-detail">
                Annuler
            </button>
        </div>

    </form>

</div>
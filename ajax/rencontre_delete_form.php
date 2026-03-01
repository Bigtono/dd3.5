<?
include('../include/dblib.inc.php');

$rencontre_id = isset($_GET['rencontre']) ? (int)$_GET['rencontre'] : 0;
?>

<div class="detail">

    <div class="nom_objet">Supprimer la rencontre</div>

    <form id="form-delete-rencontre">

        <input type="hidden" name="rencontre_id" value="<?= $rencontre_id ?>">

        <div class="form-group">
            <label>
                Cette action supprimera définitivement la rencontre.
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
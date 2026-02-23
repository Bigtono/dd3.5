<?
include('../include/dblib.inc.php');
$sc_id = (int)$_GET['sc_id'];
?>

<div class="detail">

    <div class="nom_objet">Supprimer le scénario</div>

    <form id="form-delete-scenario">

        <input type="hidden" name="sc_id" value="<?= $sc_id ?>">

        <div class="form-group">
            <label>
                <input type="checkbox" name="delete_rencontres" value="1">
                Supprimer aussi les rencontres
            </label>
        </div>

        <div class="ligneBouton">
            <button type="submit" class="btRouge">
                Confirmer
            </button>
        </div>

    </form>

</div>
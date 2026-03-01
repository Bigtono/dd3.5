<?
include("../include/session.php");
include('../include/dblib.inc.php');
$camp_id = (int)$_GET['camp_id'];
?>

<div id="sort" class="affichage">
    <div class="detail">

        <div class="nom_objet">Créer un scénario</div>

        <form id="form-create-scenario">

            <input type="hidden" name="camp_id" value="<?= $camp_id ?>">

            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="sc_nom" required>
            </div>

            <div class="ligneBouton">
                <button type="submit" class="btNoir">Créer</button>
                <button type="button" class="btDisabled btn-cancel-detail" onClick="fermerDetail()">Annuler</button>
            </div>
        </form>

    </div>

</div>
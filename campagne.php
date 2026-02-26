<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/pagination.php");
include("include/list_helpers.inc.php");

$c = "";
$campagne = null;
// recherche de la campagne
if (isset($_GET['campagne'])):
  $c = (int)$_GET['campagne'];
  $_SESSION['campagne'] = $c;

  // On récupère la campagne + le MJ + le ruleset
  $sql = "
    SELECT c.*,
           v.var_valeur AS ruleset_nom,
           j.j_pseudo,
           j.j_prenom,
           j.j_nom
    FROM dd_campagnes c
    LEFT JOIN dd_variables v ON v.var_id = c.camp_ruleset_var_id
    LEFT JOIN dd_joueurs j ON j.j_id = c.camp_j_id
    WHERE c.camp_id = :id
    AND c.camp_j_id='" . $_SESSION['user_id'] . "'
  ";
  $stmt = $db->prepare($sql);
  $stmt->execute([':id' => $c]);
  $campagne = $stmt->fetch(PDO::FETCH_ASSOC);
else:
  $_SESSION['campagne'] = "";
endif;
?>
<!doctype html>
<html lang="fr">

<head>
  <? include("include/head.php"); ?>
</head>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>

    <div class="wrapper">
      <?
      include('include/ariane.php');

      // recherche des personnages
      $listId  = 'campagne';
      include('include/sql/listePersonnages.php');
      debug('total : ' . $totalItems . " | listeId : " . $listId);

      if (!$campagne):

        echo '<div class="nodata">Aucune campagne...</div>';
      else:
        // Droits : MJ ou propriétaire de la campagne
        $isOwner = isset($_SESSION['user_id']) && ($campagne['camp_j_id'] == $_SESSION['user_id']);
        $canEdit = (!empty($_SESSION['mj']) && $_SESSION['mj'] == 1) || $isOwner;
      ?>

        <div class="titreAction">
          <div class="titreA">
            <? echo htmlspecialchars($campagne['camp_nom']); ?>
            <a href="campagne-modifier.php?campagne=<? echo (int)$campagne['camp_id']; ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>
          </div>
          <div>
          </div>
        </div>

        <div id="campagne">
          <div class="campagne-meta">
            <? if (!empty($campagne['ruleset_nom'])): ?>
              <p><strong>Règles :</strong> <? echo htmlspecialchars($campagne['ruleset_nom']); ?></p>
            <? endif; ?>

            <? if (!empty($campagne['j_pseudo'])): ?>
              <p>
                <strong>MJ / propriétaire :</strong>
                <? echo htmlspecialchars($campagne['j_pseudo']); ?>
                <? if (!empty($campagne['j_prenom']) || !empty($campagne['j_nom'])): ?>
                  (<? echo htmlspecialchars(trim($campagne['j_prenom'] . ' ' . $campagne['j_nom'])); ?>)
                <? endif; ?>
              </p>
            <? endif; ?>
          </div>

          <div class="campagne-description">
            <? echo nl2br(stripslashes($campagne['camp_resume'])); ?>
          </div>

          <? if (!empty($campagne['camp_description'])): ?>
            <div class="campagne-description">
              <? echo nl2br(stripslashes($campagne['camp_description'])); ?>
            </div>
          <? else: ?>
            <p><em>Aucune description n'a encore été saisie pour cette campagne.</em></p>
          <? endif; ?>
        </div> <!-- #campagne --->

        <?
        debug($sqlData);

        // Gestion des personnages
        ?>
        <div class="titreAction">
          <div class="titreB">Personnages</div>
          <div>
            <button
              class="btNoir"
              id="btn-add-perso"
              data-camp-id="<?= $camp_id ?>">
              Nouveau personnage
            </button>
          </div>
        </div>
        <?
        include('include/insert/' . $_SESSION['rulesetRep'] . '/listePersonnages.php');
        renderPagination($currentPage, $totalItems, $itemsPerPage, $extraParams);

        // Gestion des scénarios
        $stmtSc = $db->prepare("
        SELECT sc_id, sc_nom
        FROM dd_scenarios
        WHERE sc_camp_id = ?
        ORDER BY sc_nom
      ");
        $stmtSc->execute([$c]);
        $scenarios = $stmtSc->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="titreAction">
          <div class="titreB">Scénarios</div>
          <div>
            <button
              class="btNoir"
              id="btn-add-scenario"
              data-camp-id="<?= $c; ?>">
              Nouveau scénario
            </button>
          </div>
        </div>

        <div id="liste-scenarios" class="sortable-list">

          <div class="list-header">
            <div class="col action-col"></div>
            <div class="col action-col"></div>
            <div class="col">Nom du scénario</div>
          </div>

          <div class="list-body">

            <? if (empty($scenarios)): ?>
              <div class="list-row">
                <div class="col" colspan="3">Aucun scénario</div>
              </div>
            <? else: ?>

              <? foreach ($scenarios as $sc): ?>
                <div class="list-row">

                  <div class="col action-col action-delete">
                    <i class="fa fa-trash btn-delete-scenario"
                      data-sc-id="<?= $sc['sc_id'] ?>"></i>
                  </div>

                  <div class="col action-col action-edit">
                    <a href="scenario-modifier.php?scenario=<?= $sc['sc_id'] ?>">
                      <i class="fa fa-edit"></i>
                    </a>
                  </div>

                  <div class="col">
                    <a href="scenario.php?scenario=<?= $sc['sc_id'] ?>"><?= htmlspecialchars($sc['sc_nom']) ?></a>
                  </div>

                </div>
              <? endforeach ?>

            <? endif ?>

          </div>
        </div>

      <? endif; ?>
      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page">
        <i class="fas fa-chevron-up"></i>
      </button>
    </div> <!-- wrapper --->
    <div id="modification"></div>
    <div id="detail-pp"></div>
    <? include('include/footer.php'); ?>
  </div><!-- page --->
  <script>
    function refreshScenarios() {
      const campId = document.getElementById('btn-add-scenario').dataset.campId
      fetch('ajax/scenario_list.php?camp_id=' + campId)
        .then(r => r.text())
        .then(html => {
          document.querySelector('#liste-scenarios .list-body').innerHTML = html
        })
    }
    // création
    document.addEventListener('click', function(e) {
      if (e.target.id === 'btn-add-scenario') {

        const campId = e.target.dataset.campId

        fetch('ajax/scenario_create_form.php?camp_id=' + campId)
          .then(r => r.text())
          .then(html => {
            document.getElementById('detail-pp').innerHTML = html
            document.getElementById('detail-pp').style.display = 'block'
          })
      }
      if (e.target.classList.contains('btn-delete-scenario')) {

        const scId = e.target.dataset.scId

        fetch('ajax/scenario_delete_form.php?sc_id=' + scId)
          .then(r => r.text())
          .then(html => {
            document.getElementById('detail-pp').innerHTML = html
            document.getElementById('detail-pp').style.display = 'block'
          })
      }
    })
    // gestion soumission création scénario
    document.addEventListener('submit', function(e) {

      if (e.target.id === 'form-create-scenario') {

        e.preventDefault()

        fetch('ajax/scenario_create.php', {
            method: 'POST',
            body: new FormData(e.target)
          })
          .then(r => r.json())
          .then(data => {

            console.log(data)

            if (data.success) {

              document.getElementById('detail-pp').innerHTML = ''
              document.getElementById('detail-pp').style.display = 'none'

              refreshScenarios()
            }

          })

      }
    })
    // gestion suppression scénario
    document.addEventListener('submit', function(e) {

      if (e.target.id === 'form-delete-scenario') {

        e.preventDefault()

        fetch('ajax/scenario_delete.php', {
            method: 'POST',
            body: new FormData(e.target)
          })
          .then(r => r.json())
          .then(data => {

            console.log(data)

            if (data.success) {

              document.getElementById('detail-pp').innerHTML = ''
              document.getElementById('detail-pp').style.display = 'none'

              refreshScenarios()
            }

          })

      }

    })
  </script>
</body>

</html>
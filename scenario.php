<?

include("include/session.php");

include("include/dblib.inc.php");

include("connexion.php");

include("include/diverslib.inc.php");

include("include/date.inc.php");

include("include/pagination.php");

include("include/list_helpers.inc.php");



$scenario_id = isset($_GET['scenario']) ? (int)$_GET['scenario'] : 0;



$stmt = $db->prepare("

  SELECT sc.sc_id, sc.sc_nom, sc.sc_description, c.camp_id, c.camp_nom

  FROM dd_scenarios sc

  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id

  WHERE sc.sc_id = ?

");

$stmt->execute([$scenario_id]);

$scenario = $stmt->fetch(PDO::FETCH_ASSOC);



$_SESSION['scenario'] = !empty($scenario['sc_id']) ? (int)$scenario['sc_id'] : 0;

$_SESSION['campagne'] = !empty($scenario['camp_id']) ? (int)$scenario['camp_id'] : 0;



if (!$scenario):

  die('Scénario introuvable');

endif;



// chapitres

$stmtChap = $db->prepare("

  SELECT scc_id, scc_ordre, scc_nom

  FROM dd_scenarios_chapitres

  WHERE scc_sc_id = ?

  ORDER BY scc_ordre, scc_nom

");

$stmtChap->execute([$scenario_id]);

$chapitres = $stmtChap->fetchAll(PDO::FETCH_ASSOC);

?>



<head>

  <? include("include/head.php"); ?>

</head>



<body>

  <div id="page">

    <? include("include/header.php"); ?>

    <? include("include/menu.php"); ?>



    <div class="wrapper">

      <? include('include/ariane.php'); ?>



      <div class="titreAction">

        <div class="titreA">

          <?= htmlspecialchars($scenario['sc_nom']); ?>

          <a href="scenario-modifier.php?scenario=<?= (int)$scenario['sc_id']; ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>

        </div>

        <div>

        </div>

      </div>

      <? if (!empty($scenario['sc_description'])): ?>
        <div class="titreAction campagne-description-header">
          <div class="titreB">Description</div>
          <div>
            <button
              type="button"
              class="btNoir campagne-toggle-description"
              id="btn-toggle-scenario-description"
              aria-expanded="false"
              aria-controls="scenario-description-longue"
              aria-label="Afficher la description"
              title="Afficher la description">
              <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <div id="scenario-description-longue" class="campagne-description is-collapsed">
          <? echo nl2br(stripslashes($scenario['sc_description'])); ?>
        </div>
      <? else: ?>
        <div class="campagne-description-empty"><em>Aucune description n'a encore été saisie pour ce scénario.</em></div>
      <? endif; ?>


      <!-- Bloc chapitres -->

      <div class="titreAction">

        <div class="titreB">Chapitres</div>

        <div>

          <button

            class="btNoir"

            id="btn-add-chapitre"

            data-scenario-id="<?= $scenario_id ?>">

            Nouveau chapitre

          </button>

        </div>

      </div>



      <div id="liste-chapitres" class="sortable-list">



        <div class="list-header">

          <div class="col action-col"></div>

          <div class="col action-col"></div>

          <div class="col">Nom du chapitre</div>

        </div>



        <div class="list-body">



          <? if (empty($chapitres)): ?>

            <div class="list-row">

              <div class="col">Aucun chapitre</div>

            </div>

          <? else: ?>



            <? foreach ($chapitres as $ch): ?>

              <div class="list-row">



                <div class="col action-col action-delete">

                  <i class="fa fa-trash btn-delete-chapitre" data-ch-id="<?= $ch['scc_id'] ?>"></i>

                </div>



                <div class="col action-col action-edit">

                  <a href="chapitre-modifier.php?chapitre=<?= $ch['scc_id'] ?>">

                    <i class="fa fa-edit"></i>

                  </a>

                </div>



                <div class="col">

                  <a href="chapitre.php?chapitre=<?= $ch['scc_id'] ?>">

                    <?= htmlspecialchars($ch['scc_nom']) ?>

                  </a>

                </div>



              </div>

            <? endforeach ?>



          <? endif ?>



        </div>



      </div>



      <p class="mb50">&nbsp;</p>

      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>

    </div> <!-- wrapper --->

    <div id="modification"></div>

    <div id="detail-pp"></div>

    <? include('include/footer.php'); ?>

  </div><!-- page --->

</body>

<script>
  const btnToggleScenarioDescription = document.getElementById('btn-toggle-scenario-description')
  const blocScenarioDescriptionLongue = document.getElementById('scenario-description-longue')

  if (btnToggleScenarioDescription && blocScenarioDescriptionLongue) {
    btnToggleScenarioDescription.addEventListener('click', function() {
      const isCollapsed = blocScenarioDescriptionLongue.classList.toggle('is-collapsed')
      const isExpanded = !isCollapsed
      btnToggleScenarioDescription.setAttribute('aria-expanded', isExpanded ? 'true' : 'false')
      const actionLabel = isExpanded ? 'Masquer la description' : 'Afficher la description'
      btnToggleScenarioDescription.setAttribute('aria-label', actionLabel)
      btnToggleScenarioDescription.setAttribute('title', actionLabel)
    })
  }

  // refresh chapitres

  function refreshChapitres() {



    const scenarioId = document.getElementById('btn-add-chapitre').dataset.scenarioId



    fetch('ajax/chapitre_list.php?scenario=' + scenarioId)

      .then(r => r.text())

      .then(html => {

        document.querySelector('#liste-chapitres .list-body').innerHTML = html

      })

  }



  // ouverture création

  document.addEventListener('click', function(e) {



    if (e.target.id === 'btn-add-chapitre') {



      const scenarioId = e.target.dataset.scenarioId



      fetch('ajax/chapitre_create_form.php?scenario=' + scenarioId)

        .then(r => r.text())

        .then(html => {

          document.getElementById('detail-pp').innerHTML = html

          document.getElementById('detail-pp').style.display = 'block'

        })

    }



    if (e.target.classList.contains('btn-delete-chapitre')) {



      const chId = e.target.dataset.chId



      fetch('ajax/chapitre_delete_form.php?chapitre=' + chId)

        .then(r => r.text())

        .then(html => {

          document.getElementById('detail-pp').innerHTML = html

          document.getElementById('detail-pp').style.display = 'block'

        })

    }



  })



  // submit création

  document.addEventListener('submit', function(e) {



    if (e.target.id === 'form-create-chapitre') {



      e.preventDefault()



      fetch('ajax/chapitre_create.php', {

          method: 'POST',

          body: new FormData(e.target)

        })

        .then(r => r.json())

        .then(data => {

          if (data.success) {

            document.getElementById('detail-pp').style.display = 'none'

            refreshChapitres()

          }

        })



    }



    if (e.target.id === 'form-delete-chapitre') {



      e.preventDefault()



      fetch('ajax/chapitre_delete.php', {

          method: 'POST',

          body: new FormData(e.target)

        })

        .then(r => r.json())

        .then(data => {

          if (data.success) {

            document.getElementById('detail-pp').style.display = 'none'

            refreshChapitres()

          }

        })



    }



  })



  // fermeture générique

  document.addEventListener('click', function(e) {



    if (e.target.classList.contains('btn-cancel-detail')) {

      document.getElementById('detail-pp').innerHTML = ''

      document.getElementById('detail-pp').style.display = 'none'

    }



  })
</script>



</html>
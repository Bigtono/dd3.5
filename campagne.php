<?

include("include/session.php");

include("include/dblib.inc.php");

include("connexion.php");

include("include/diverslib.inc.php");

include("include/date.inc.php");

include("include/pagination.php");

include("include/list_helpers.inc.php");



$c = "";

$c = isset($_GET['campagne']) ? (int)$_GET['campagne'] : 0;

$campagne = null;



// recherche de la campagne

if ($c > 0):

  // gestion des variables de session des sélections de l'utilisateur (affichées dans le header)

  if ($c != $_SESSION['campagne']):

    $_SESSION['campagne'] = $c;

    unset($_SESSION['chapitre']);

    unset($_SESSION['scenario']);

  endif;

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

          <div class="campagne-meta-row">
            <div class="campagne-meta-left">
              <? if (!empty($campagne['ruleset_nom'])): ?>
                <div class="campagne-meta-item">
                  <i class="fa-solid fa-book campagne-meta-icon" aria-hidden="true"></i>
                  <div>
                    <div class="campagne-meta-label">Règles</div>
                    <div class="campagne-meta-value"><? echo htmlspecialchars($campagne['ruleset_nom']); ?></div>
                  </div>
                </div>
              <? endif; ?>

              <? if (!empty($campagne['camp_resume'])): ?>
                <div class="campagne-meta-item">
                  <i class="fa-solid fa-user-shield campagne-meta-icon" aria-hidden="true"></i>
                  <div>
                    <div class="campagne-meta-label">Résumé</div>
                    <div class="campagne-meta-value">
                      <? echo nl2br(stripslashes($campagne['camp_resume'])); ?>
                    </div>
                  </div>
                </div>
              <? endif; ?>
            </div>

            <div class="campagne-meta-right">
              <a class="btNoir lien" href="notes.php?campagne=<? echo (int)$c; ?>">Voir les notes</a>
            </div>
          </div>

          <? if (!empty($campagne['camp_description'])): ?>
            <div class="titreAction campagne-description-header">
              <div class="titreB">Description</div>
              <div>
                <button
                  type="button"
                  class="btNoir campagne-toggle-description"
                  id="btn-toggle-campagne-description"
                  aria-expanded="false"
                  aria-controls="campagne-description-longue">
                  Afficher
                </button>
              </div>
            </div>

            <div id="campagne-description-longue" class="campagne-description is-collapsed">
              <? echo nl2br(stripslashes($campagne['camp_description'])); ?>
            </div>
          <? else: ?>
            <div class="campagne-description-empty"><em>Aucune description n'a encore été saisie pour cette campagne.</em></div>
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

              data-camp-id="<?= $c ?>">

              Nouveau personnage

            </button>

          </div>

        </div>

        <?

        $listDomId = 'liste-personnages';
        include('include/insert/' . $_SESSION['rulesetRep'] . '/listePersonnages.php');
        unset($listDomId);

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
    function refreshPersonnages() {

      const campId = document.getElementById('btn-add-perso').dataset.campId

      fetch('ajax/personnage_list.php?camp_id=' + campId)

        .then(r => r.text())

        .then(html => {

          document.querySelector('#liste-personnages .list-body').innerHTML = html

        })

    }

    function refreshScenarios() {

      const campId = document.getElementById('btn-add-scenario').dataset.campId

      fetch('ajax/scenario_list.php?camp_id=' + campId)

        .then(r => r.text())

        .then(html => {

          document.querySelector('#liste-scenarios .list-body').innerHTML = html

        })

    }

    const btnToggleDescription = document.getElementById('btn-toggle-campagne-description')
    const blocDescriptionLongue = document.getElementById('campagne-description-longue')

    if (btnToggleDescription && blocDescriptionLongue) {
      btnToggleDescription.addEventListener('click', function() {
        const isCollapsed = blocDescriptionLongue.classList.toggle('is-collapsed')
        const isExpanded = !isCollapsed
        btnToggleDescription.setAttribute('aria-expanded', isExpanded ? 'true' : 'false')
        btnToggleDescription.textContent = isExpanded ? 'Masquer' : 'Afficher'
      })
    }

    // création

    document.addEventListener('click', function(e) {

      if (e.target.id === 'btn-add-perso') {

        const campId = e.target.dataset.campId

        fetch('ajax/personnage_attach_form.php?camp_id=' + campId)
          .then(r => r.text())
          .then(html => {
            document.getElementById('detail-pp').innerHTML = html
            document.getElementById('detail-pp').style.display = 'block'
          })

      }

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



      if (e.target.id === 'form-attach-personnage') {

        e.preventDefault()

        fetch('ajax/personnage_attach.php', {
            method: 'POST',
            body: new FormData(e.target)
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              document.getElementById('detail-pp').innerHTML = ''
              document.getElementById('detail-pp').style.display = 'none'
              refreshPersonnages()
            } else {
              alert(data.message || 'Impossible d\'affecter ce personnage � la campagne.')
            }
          })

      }

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
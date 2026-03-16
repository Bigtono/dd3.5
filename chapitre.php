<?

include("include/session.php");

include("include/dblib.inc.php");

include("connexion.php");

include("include/diverslib.inc.php");

include("include/date.inc.php");

include("include/pagination.php");

include("include/list_helpers.inc.php");



$chapitre_id = isset($_GET['chapitre']) ? (int)$_GET['chapitre'] : 0;



$stmt = $db->prepare("

  SELECT ch.scc_id, ch.scc_nom, ch.scc_description, sc.sc_id, sc.sc_nom, c.camp_id, c.camp_nom

  FROM dd_scenarios_chapitres ch

  JOIN dd_scenarios sc ON sc.sc_id = ch.scc_sc_id

  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id

  WHERE ch.scc_id = ?

");

$stmt->execute([$chapitre_id]);



$chapitre = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$chapitre):

  die('Chapitre introuvable');

endif;



// rencontres

$stmtR = $db->prepare("

  SELECT re_id, re_nom

  FROM dd_rencontres

  WHERE re_scc_id = ?

  ORDER BY re_nom

");

$stmtR->execute([$chapitre_id]);

$rencontres = $stmtR->fetchAll(PDO::FETCH_ASSOC);

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

          <?= htmlspecialchars($chapitre['scc_nom']); ?>

          <a href="chapitre-modifier.php?chapitre=<?= (int)$chapitre['scc_id']; ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>

        </div>

        <div>

        </div>

      </div>



      <? if (!empty($chapitre['scc_description'])): ?>

        <div class="titreAction campagne-description-header">
          <div class="titreB">Description</div>
          <div>
            <button
              type="button"
              class="btNoir campagne-toggle-description"
              id="btn-toggle-chapitre-description"
              aria-expanded="false"
              aria-controls="chapitre-description-longue"
              aria-label="Afficher la description"
              title="Afficher la description">
              <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <div id="chapitre-description-longue" class="campagne-description is-collapsed">
          <?= nl2br(htmlspecialchars($chapitre['scc_description'])) ?>
        </div>

      <? else: ?>

        <div class="campagne-description-empty"><em>Aucune description n'a encore été saisie pour ce chapitre.</em></div>

      <? endif ?>



      <!-- Bloc rencontres -->



      <div class="titreAction">

        <div class="titreB">Rencontres</div>

        <div>

          <button

            class="btNoir"

            id="btn-add-rencontre"

            data-chapitre-id="<?= $chapitre_id ?>">

            Nouvelle rencontre

          </button>

        </div>

      </div>



      <div id="liste-rencontres" class="sortable-list">



        <div class="list-header">

          <div class="col action-col"></div>

          <div class="col action-col"></div>

          <div class="col">Nom de la rencontre</div>

        </div>



        <div class="list-body">



          <? if (empty($rencontres)): ?>

            <div class="list-row">

              <div class="col">Aucune rencontre</div>

            </div>

          <? else: ?>



            <? foreach ($rencontres as $re): ?>

              <div class="list-row">



                <div class="col action-col action-delete">

                  <i class="fa fa-trash btn-delete-rencontre" data-re-id="<?= $re['re_id'] ?>"></i>

                </div>



                <div class="col action-col action-edit">

                  <a href="rencontre-modifier.php?rencontre=<?= $re['re_id'] ?>&retour=chapitre">

                    <i class="fa fa-edit"></i>

                  </a>

                </div>



                <div class="col">

                  <a href="rencontre.php?rencontre=<?= $re['re_id'] ?>">

                    <?= htmlspecialchars($re['re_nom']) ?>

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
  const btnToggleChapitreDescription = document.getElementById('btn-toggle-chapitre-description')
  const blocChapitreDescriptionLongue = document.getElementById('chapitre-description-longue')

  if (btnToggleChapitreDescription && blocChapitreDescriptionLongue) {
    btnToggleChapitreDescription.addEventListener('click', function() {
      const isCollapsed = blocChapitreDescriptionLongue.classList.toggle('is-collapsed')
      const isExpanded = !isCollapsed
      btnToggleChapitreDescription.setAttribute('aria-expanded', isExpanded ? 'true' : 'false')
      const actionLabel = isExpanded ? 'Masquer la description' : 'Afficher la description'
      btnToggleChapitreDescription.setAttribute('aria-label', actionLabel)
      btnToggleChapitreDescription.setAttribute('title', actionLabel)
    })
  }

  function refreshRencontres() {



    const chapitreId = document.getElementById('btn-add-rencontre').dataset.chapitreId



    fetch('ajax/rencontre_list.php?chapitre=' + chapitreId)

      .then(r => r.text())

      .then(html => {

        document.querySelector('#liste-rencontres .list-body').innerHTML = html

      })

  }



  // ouverture popup

  document.addEventListener('click', function(e) {



    if (e.target.id === 'btn-add-rencontre') {



      const chapitreId = e.target.dataset.chapitreId



      fetch('ajax/rencontre_create_form.php?chapitre=' + chapitreId)

        .then(r => r.text())

        .then(html => {

          document.getElementById('detail-pp').innerHTML = html

          document.getElementById('detail-pp').style.display = 'block'

        })

    }



    if (e.target.classList.contains('btn-delete-rencontre')) {



      const reId = e.target.dataset.reId



      fetch('ajax/rencontre_delete_form.php?rencontre=' + reId)

        .then(r => r.text())

        .then(html => {

          document.getElementById('detail-pp').innerHTML = html

          document.getElementById('detail-pp').style.display = 'block'

        })

    }



  })



  // submit

  document.addEventListener('submit', function(e) {



    if (e.target.id === 'form-create-rencontre') {



      e.preventDefault()



      fetch('ajax/rencontre_create.php', {

          method: 'POST',

          body: new FormData(e.target)

        })

        .then(r => r.json())

        .then(data => {

          if (data.success) {

            document.getElementById('detail-pp').style.display = 'none'

            refreshRencontres()

          }

        })

    }



    if (e.target.id === 'form-delete-rencontre') {



      e.preventDefault()



      fetch('ajax/rencontre_delete.php', {

          method: 'POST',

          body: new FormData(e.target)

        })

        .then(data => data.json())

        .then(data => {

          if (data.success) {

            document.getElementById('detail-pp').style.display = 'none'

            refreshRencontres()

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
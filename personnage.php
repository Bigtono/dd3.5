<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$personnagePageKey = 'fiche';
include("include/personnage_bootstrap.php");

if ($dn['pe_arc_id'] > 0):
  $archetype = ' (' . libelle("dd_races", "ra", "nom", $dn['pe_arc_id'], "ra_rat_id=2") . ')';
else:
  $archetype = '';
endif;

$editPersonnageUrl = 'personnage-modifier.php?personnage=' . $p;
if ($campagneId > 0) {
  $editPersonnageUrl .= '&campagne=' . $campagneId;
}
if (isset($_GET['tri']) && $_GET['tri'] !== '') {
  $editPersonnageUrl .= '&tri=' . urlencode((string)$_GET['tri']);
}
?>
<!doctype html>
<html>
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
        <? echo htmlspecialchars($personnageNom); ?>
        <? if ($canEditPersonnage): ?>
          <a href="<? echo htmlspecialchars($editPersonnageUrl); ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>
        <? endif; ?>
      </div>
      <div></div>
    </div>

    <? include("include/personnage_nav.php"); ?>

    <div class="contenu">
      <div class="titreAction">
        <div class="titreA">Fiche</div>
        <div></div>
      </div>
      <div>
        <div class="mb10">
          <? echo libelle("dd_races", "ra", "nom", $dn['pe_ra_id'], "ra_rat_id=1") . $archetype; ?>,
          Niveau <? echo niveauPersonnage($p) . ' (' . classesPersonnage($p) . ')'; ?>,
          <? echo libelle("dd_alignements", "al", "abreviation", $dn['pe_al_id']); ?>,
          NLS : <? echo $nls; ?>
          <?
          $organisation = libelle("dd_organisations", "org", "nom", $dn['pe_org_id']);
          if ($organisation != '') echo ', ' . $organisation;
          if ($_SESSION['mj'] == 1):
            echo '<br><span class="label">Joueur : </span>' . libelle_joueur($dn['pe_j_id']);
          endif;
          ?>
        </div>
        <div id="campagne-personnage-bloc" data-personnage-id="<? echo (int)$p; ?>">
          <? if ($campagnePerso): ?>
            <div class="personnage-campagne">
              <div class="personnage-campagne-label">Campagne :</div>
              <div class="personnage-campagne-value">
                <a href="campagne.php?campagne=<? echo (int)$campagnePerso['camp_id']; ?>"><? echo htmlspecialchars($campagnePerso['camp_nom']); ?></a>
              </div>
              <? if ($canViewNotesMj): ?>
                <button type="button" class="btRouge" id="btn-detach-personnage" data-personnage="<? echo (int)$p; ?>">Detacher de la campagne</button>
              <? endif; ?>
            </div>
          <? else: ?>
            <div class="personnage-campagne-empty">Aucune campagne en cours pour ce personnage.</div>
          <? endif; ?>
        </div>
        <div>
          <div class="titre">Caracteristiques</div>
          <div class="cellMainSort">
            <div>
              <div class="cellEntete">For</div>
              <div class="cellValue"><? echo $dn['pe_for']; ?></div>
            </div>
            <div>
              <div class="cellEntete">Dex</div>
              <div class="cellValue"><? echo $dn['pe_dex']; ?></div>
            </div>
            <div>
              <div class="cellEntete">Con</div>
              <div class="cellValue"><? echo $dn['pe_con']; ?></div>
            </div>
            <div>
              <div class="cellEntete">Int</div>
              <div class="cellValue"><? echo $dn['pe_int']; ?></div>
            </div>
            <div>
              <div class="cellEntete">Sag</div>
              <div class="cellValue"><? echo $dn['pe_sag']; ?></div>
            </div>
            <div>
              <div class="cellEntete">Cha</div>
              <div class="cellValue"><? echo $dn['pe_cha']; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p class="mb50">&nbsp;</p>
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
  </div>
  <div id="modification"></div>
  <div id="detail-pp"></div>
  <? include('include/footer.php'); ?>
</div>
<script>
(function(){
  function getPersonnageId(){
    const bloc = document.getElementById('campagne-personnage-bloc');
    if (bloc && bloc.dataset.personnageId) return bloc.dataset.personnageId;
    const params = new URLSearchParams(window.location.search);
    return params.get('personnage');
  }

  function closeDetailPanel(){
    const detail = document.getElementById('detail-pp');
    if (!detail) return;
    detail.innerHTML = '';
    detail.style.display = 'none';
  }

  window.refreshCampagnePersonnage = function(){
    const bloc = document.getElementById('campagne-personnage-bloc');
    const personnageId = getPersonnageId();
    if (!bloc || !personnageId) return;
    fetch('ajax/personnage_campaign_status.php?personnage=' + encodeURIComponent(personnageId))
      .then(res => res.text())
      .then(html => {
        bloc.innerHTML = html;
      })
      .catch(() => {
        bloc.innerHTML = '<div class="personnage-campagne-empty">Erreur lors du rafraichissement de la campagne.</div>';
      });
  };

  document.addEventListener('click', function(e){
    const btnDetach = e.target.closest('#btn-detach-personnage');
    if (!btnDetach) return;
    const personnageId = btnDetach.dataset.personnage || getPersonnageId();
    if (!personnageId) return;
    fetch('ajax/personnage_detach_form.php?personnage=' + encodeURIComponent(personnageId))
      .then(res => res.text())
      .then(html => {
        const detail = document.getElementById('detail-pp');
        if (!detail) return;
        detail.innerHTML = html;
        detail.style.display = 'block';
      });
  });

  document.addEventListener('submit', function(e){
    if (e.target.id !== 'form-detach-personnage') return;
    e.preventDefault();
    const form = e.target;
    fetch('ajax/personnage_detach.php', {
      method: 'POST',
      body: new FormData(form)
    })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          closeDetailPanel();
          window.refreshCampagnePersonnage();
          return;
        }
        alert((data && data.message) ? data.message : 'Une erreur est survenue.');
      })
      .catch(() => {
        alert('Erreur reseau.');
      });
  });
})();
</script>
</body>
</html>

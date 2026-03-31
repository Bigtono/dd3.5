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

$nlsPrestigeContext = ['has_section' => false];
if (isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] === 'DD3.5') {
  include_once("include/insert/DD3.5/personnage_nls_helper.php");
  if (dd35_ruleset_active()) {
    $nlsPrestigeContext = dd35_load_personnage_nls_context($db, (int)$p);
  }
}

$caracMap = [
  'for' => ['label' => 'For', 'value' => (int)$dn['pe_for']],
  'dex' => ['label' => 'Dex', 'value' => (int)$dn['pe_dex']],
  'con' => ['label' => 'Con', 'value' => (int)$dn['pe_con']],
  'int' => ['label' => 'Int', 'value' => (int)$dn['pe_int']],
  'sag' => ['label' => 'Sag', 'value' => (int)$dn['pe_sag']],
  'cha' => ['label' => 'Cha', 'value' => (int)$dn['pe_cha']],
];
$modsByScore = [];
$stmtMods = $db->query("SELECT mod_carac, mod_modificateur FROM dd_modificateurs");
while ($rowMod = $stmtMods->fetch(PDO::FETCH_ASSOC)) {
  $modsByScore[(int)$rowMod['mod_carac']] = (int)$rowMod['mod_modificateur'];
}
$personnageCaracs = [];
foreach ($caracMap as $caracData) {
  $score = (int)$caracData['value'];
  $mod = isset($modsByScore[$score]) ? (int)$modsByScore[$score] : 0;
  $personnageCaracs[] = [
    'label' => $caracData['label'],
    'score' => $score,
    'mod' => $mod,
    'mod_label' => ($mod > 0 ? '+' : '') . (string)$mod,
  ];
}

$personnageCompetences = [];
if (isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] === 'DD3.5') {
  $stmtCompPerso = $db->prepare("
    SELECT c.comp_id, c.comp_nom, pc.pec_maitrise
    FROM dd_personnages_competences pc
    JOIN dd_competences c ON c.comp_id = pc.pec_comp_id
    WHERE pc.pec_pe_id = :pid
      AND c.comp_ruleset_var_id = :ruleset
    ORDER BY c.comp_nom
  ");
  $stmtCompPerso->execute([
    ':pid' => (int)$p,
    ':ruleset' => (int)$_SESSION['ruleset'],
  ]);
  while ($rowComp = $stmtCompPerso->fetch(PDO::FETCH_ASSOC)) {
    $personnageCompetences[] = [
      'comp_id' => (int)$rowComp['comp_id'],
      'comp_nom' => (string)$rowComp['comp_nom'],
      'maitrise' => (int)$rowComp['pec_maitrise'],
    ];
  }
}
?>
<!doctype html>
<html>

<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
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
        <?php
        $personnageFicheTemplate = 'include/insert/' . $_SESSION['rulesetRep'] . '/personnage_fiche_sections.php';
        if (file_exists($personnageFicheTemplate)):
          include($personnageFicheTemplate);
        else:
          echo '<div class="nodata">Template fiche personnage introuvable pour ce ruleset.</div>';
        endif;
        ?>
      </div>

      <div class="contenu">
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
      </div>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
    <div id="modification"></div>
    <div id="detail-pp"></div>
    <? include('include/footer.php'); ?>
  </div>
  <script>
    (function() {
      function getPersonnageId() {
        const bloc = document.getElementById('campagne-personnage-bloc');
        if (bloc && bloc.dataset.personnageId) return bloc.dataset.personnageId;
        const params = new URLSearchParams(window.location.search);
        return params.get('personnage');
      }

      function closeDetailPanel() {
        const detail = document.getElementById('detail-pp');
        if (!detail) return;
        detail.innerHTML = '';
        detail.style.display = 'none';
      }

      window.refreshCampagnePersonnage = function() {
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

      document.addEventListener('click', function(e) {
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

      document.addEventListener('submit', function(e) {
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

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$personnagePageKey = 'magie';
include("include/personnage_bootstrap.php");
include_once("include/insert/common/personnage_grimoire_helper.php");
include("include/affichageSelectionSources.php");

$resourceIds = pg_magic_parse_resource_ids(isset($selection) ? $selection : '');
$grimoireContext = pg_magic_load_context($db, (int)$p, (string)$_SESSION['rulesetRep'], $resourceIds);
$saveOk = isset($_GET['msg']) && (int)$_GET['msg'] === 1;
$saveErr = isset($_GET['msg']) && (int)$_GET['msg'] === 0;
$jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
  $jsonFlags = $jsonFlags | JSON_INVALID_UTF8_SUBSTITUTE;
}
$grimoireContextJson = json_encode($grimoireContext, $jsonFlags);
if ($grimoireContextJson === false) {
  $grimoireContextJson = '{}';
}

$templatePath = 'include/insert/' . $_SESSION['rulesetRep'] . '/personnage_grimoire_sections.php';
?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <link rel="stylesheet" href="include/personnage-grimoire.css">
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-personnage-grimoire.js'></script>
</head>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA"><? echo htmlspecialchars($personnageNom); ?></div>
        <div><a class="personnage-retour lien" href="<? echo htmlspecialchars($retourFicheUrl); ?>" title="Retour a la fiche"><i class="fa-solid fa-arrow-left"></i></a></div>
      </div>

      <? include("include/personnage_nav.php"); ?>

      <? if ($saveOk): ?>
        <div class="contenu">
          <div class="confirmation">Gestion des sorts mise a jour.</div>
        </div>
      <? elseif ($saveErr): ?>
        <div class="contenu">
          <div class="alerte">Erreur lors de l'enregistrement de la gestion des sorts.</div>
        </div>
      <? endif; ?>

      <form action="personnage-grimoire-enregistrement.php?personnage=<? echo (int)$p; ?>&campagne=<? echo (int)$campagneId; ?>" method="post" class="formulaire" id="personnage-grimoire-form">
        <input type="hidden" name="mp_magic_payload_ready" id="mp_magic_payload_ready" value="1">
        <input type="hidden" name="mp_magic_personnage_id" id="mp_magic_personnage_id" value="<? echo (int)$p; ?>">
        <input type="hidden" name="mp_magic_filter" id="mp_magic_filter" value="<? echo (int)$grimoireContext['session_filter']; ?>">
        <input type="hidden" name="mp_magic_state" id="mp_magic_state" value="">
        <input type="hidden" name="campagne" value="<? echo (int)$campagneId; ?>">
        <div class="contenu">
          <? if (file_exists($templatePath)): ?>
            <? include($templatePath); ?>
          <? else: ?>
            <div class="nodata">Template grimoire introuvable pour ce ruleset.</div>
          <? endif; ?>

          <div class="ligneBouton">
            <? if ($canEditPersonnage): ?>
              <button type="submit" class="btNoir" name="ok">Enregistrer</button>
            <? endif; ?>
            <button type="button" class="btNoir" onClick="window.location.href='<? echo addslashes($personnageUrls['magie']); ?>'">Annuler</button>
          </div>

          <? if (!empty($selectionAffichage)): ?>
            <div class="personnage-section personnage-grimoire-resources mt10">
              <div class="titre personnage-section-toggle">
                <div class="gras mr10 lien" onClick="togglePlus('pg-selected-resources')">
                  <span class="personnage-section-title">
                    <i class="fa-solid fa-book-open personnage-section-icon"></i>
                    <span>R&eacute;f&eacute;rences choisies</span>
                  </span>
                  <span id="toggle-pg-selected-resources"><i class="fa-solid fa-bars"></i></span>
                </div>
              </div>
              <div id="pg-selected-resources" class="accordion-content noDisplay pg-box-data">
                <? echo $selectionAffichage; ?>
              </div>
            </div>
          <? endif; ?>
        </div>
      </form>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
    <div id="modification"></div>
    <div id="detail-pp"></div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      initPersonnageGrimoireEditor({
        contexte: <? echo $grimoireContextJson; ?>,
        canEdit: <? echo $canEditPersonnage ? 'true' : 'false'; ?>,
        filterSyncUrl: 'ajax/ajax-personnage-grimoire-filtre.php'
      });
    });
  </script>
</body>
</html>

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$sc = isset($_GET['scenario']) ? (int)$_GET['scenario'] : 0;

$stmt = $db->prepare("
  SELECT sc.sc_id, sc.sc_nom, sc.sc_description, sc.sc_camp_id,
         c.camp_id, c.camp_nom, c.camp_ruleset_var_id
  FROM dd_scenarios sc
  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id
  WHERE sc.sc_id = :sc_id
");
$stmt->execute([':sc_id' => $sc]);
$scenario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$scenario):
  die("Scénario introuvable.");
endif;

$_SESSION['scenario'] = (int)$scenario['sc_id'];
$_SESSION['campagne'] = (int)$scenario['camp_id'];

$stmtCampagnes = $db->prepare("
  SELECT camp_id, camp_nom
  FROM dd_campagnes
  WHERE camp_j_id = :user_id
    AND camp_ruleset_var_id = :ruleset_id
  ORDER BY camp_nom
");
$stmtCampagnes->execute([
  ':user_id' => (int)$_SESSION['user_id'],
  ':ruleset_id' => (int)$scenario['camp_ruleset_var_id'],
]);
$campagnes = $stmtCampagnes->fetchAll(PDO::FETCH_ASSOC);
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
      <? include('include/ariane.php'); ?>

      <div class="titreAction">
        <div class="titreA">Modifier le scénario</div>
        <div></div>
      </div>

      <form action="scenario-enregistrement.php" method="post" class="formulaire">
        <input type="hidden" name="scenario" value="<?= (int)$scenario['sc_id']; ?>" />

        <div class="contenu_profil">
          <div class="ligne">
            <div class="label w75">Nom</div>
            <input
              type="text"
              id="mp_sc_nom"
              name="mp_sc_nom"
              class="w300 input_left"
              value="<?= htmlspecialchars($scenario['sc_nom']); ?>">
          </div>

          <div class="ligne">
            <div class="label w75">Campagne</div>
            <select name="mp_sc_camp_id" id="mp_sc_camp_id">
              <? foreach ($campagnes as $camp): ?>
                <option
                  value="<?= (int)$camp['camp_id']; ?>"
                  <? if ((int)$camp['camp_id'] === (int)$scenario['sc_camp_id']) echo 'selected'; ?>>
                  <?= htmlspecialchars($camp['camp_nom']); ?>
                </option>
              <? endforeach; ?>
            </select>
          </div>

          <div class="label">Description</div>
          <textarea id="mp_sc_description" name="mp_sc_description" class="wp100"><?= htmlspecialchars($scenario['sc_description']); ?></textarea>
          <script>
            CKEDITOR.replace('mp_sc_description', {
              allowedContent: true,
              extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
              contentsCss: 'include/_styles_.css'
            });
          </script>
        </div>

        <div class="ligneBouton">
          <button type="submit" name="action" value="save" class="btNoir">Enregistrer</button>
          <button type="submit" name="action" value="cancel" class="btGris">Annuler</button>
        </div>
      </form>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
  </div>
</body>

</html>
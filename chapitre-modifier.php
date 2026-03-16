<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$ch = isset($_GET['chapitre']) ? (int)$_GET['chapitre'] : 0;

$stmt = $db->prepare("
  SELECT ch.scc_id, ch.scc_nom, ch.scc_abreviation, ch.scc_description,
         sc.sc_id, sc.sc_nom,
         c.camp_id, c.camp_nom
  FROM dd_scenarios_chapitres ch
  JOIN dd_scenarios sc ON sc.sc_id = ch.scc_sc_id
  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id
  WHERE ch.scc_id = :ch_id
    AND c.camp_j_id = :user_id
");
$stmt->execute([
  ':ch_id' => $ch,
  ':user_id' => (int)$_SESSION['user_id'],
]);
$chapitre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chapitre):
  die("Chapitre introuvable.");
endif;

$_SESSION['chapitre'] = (int)$chapitre['scc_id'];
$_SESSION['scenario'] = (int)$chapitre['sc_id'];
$_SESSION['campagne'] = (int)$chapitre['camp_id'];
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
        <div class="titreA">Modifier le chapitre</div>
        <div></div>
      </div>

      <form action="chapitre-enregistrement.php" method="post" class="formulaire">
        <input type="hidden" name="chapitre" value="<?= (int)$chapitre['scc_id']; ?>" />

        <div class="contenu_profil">
          <div class="ligne">
            <div class="label w100">Nom</div>
            <input
              type="text"
              id="mp_scc_nom"
              name="mp_scc_nom"
              class="w300 input_left"
              value="<?= htmlspecialchars($chapitre['scc_nom']); ?>">
          </div>

          <div class="ligne">
            <div class="label w100">Abreviation</div>
            <input
              type="text"
              id="mp_scc_abreviation"
              name="mp_scc_abreviation"
              class="w150 input_left"
              maxlength="20"
              value="<?= htmlspecialchars((string)$chapitre['scc_abreviation']); ?>">
          </div>

          <div class="label">Description</div>
          <textarea id="mp_scc_description" name="mp_scc_description" class="wp100"><?= htmlspecialchars((string)$chapitre['scc_description']); ?></textarea>
          <script>
            CKEDITOR.replace('mp_scc_description', {
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

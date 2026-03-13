<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$personnagePageKey = 'notes-mj';
include("include/personnage_bootstrap.php");

if (!$canViewNotesMj) {
  header('Location: ' . $retourFicheUrl);
  exit;
}

if (strlen((string)$dn['pe_notes_mj']) > 0):
  $notes_mj = stripslashes((string)$dn['pe_notes_mj']);
else:
  $notes_mj = 'Aucune note';
endif;
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
        <div class="titreA"><? echo htmlspecialchars($personnageNom); ?></div>
        <div><a class="personnage-retour lien" href="<? echo htmlspecialchars($retourFicheUrl); ?>" title="Retour a la fiche"><i class="fa-solid fa-arrow-left"></i></a></div>
      </div>

      <? include("include/personnage_nav.php"); ?>
      <? include('include/insert/' . $_SESSION['rulesetRep'] . '/personnage_notes_mj.php'); ?>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
    <div id="modification"></div>
    <div id="detail-pp"></div>
  </div>
</body>

</html>
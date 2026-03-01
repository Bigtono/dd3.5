<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if (isset($_GET['classe'])):
  // appel Include
  $c = $_GET['classe'];
  $_SESSION['classe'] = $c;
else:
  $c = "";
  $_SESSION['classe'] = "";
endif;
if (strlen($_GET['critere']) > 0):
  $critere = $_GET['critere'];
else:
  $critere = '';
endif;

/* -------------------------------------------------
   Recherche des données pour la classe
-------------------------------------------------- */

$requete = "SELECT * FROM dd_classes WHERE cla_id='" . $c . "'";
$resultat = queryPDO($requete);
$dn = $resultat->fetch(PDO::FETCH_ASSOC);
if ($critere != ''):
  $retour = $_GET['retour'] . ".php?critere=" . $critere;
  $texte = tag($critere, $dn['cla_description']);
else:
  $retour = "classes.php";
  $texte = $dn['cla_description'];
endif;

$isLanceurSorts = ($dn['cla_mag_id'] != 0);

?>
<!doctype html>

<HEAD>
  <? include("include/head.php"); ?>
  <link href="include/classes.css" rel="stylesheet" type="text/css">
  <script type='text/javascript' src='js/moncode-classes.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
</HEAD>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <?
      if ($c != ""):
      ?>
        <div id="classe" class="affichage">
          <div class="titreAction">
            <div class="titreA"><? echo stripslashes($dn['cla_nom']); ?></div>
            <div>
              <? if ($_SESSION['mj'] == 1): ?>
                <a href="classe-modifier.php?classe=<? echo $c; ?>&tri=<? echo $_GET['tri']; ?>"><i class="fa-solid fa-pen-to-square"></i></a>
              <? endif; ?>
            </div>
          </div>

          <? include('include/insert/' . $_SESSION['rulesetRep'] . '/bloc_classe.php'); ?>

          <div class="contenu">
            <div class="label mt10">Description :</div>
            <div class="description"><? echo stripslashes($texte); ?></div>
          </div>

          <div class="ligne">
            <div class="label">Source : </div>
            <div><? echo libelle("dd_ressources", "res", "nom", $dn['cla_res_id']); ?></div>
          </div>
        </div> <!-- #classe -->
      <?
      else:
        echo '<div class="nodata">Aucune classe disponible !</div>';
      endif;
      ?>
    </div> <!-- wrapper --->
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
  </div><!-- page --->
  <div id="detail-pp"></div>
  <div id="modification"></div>
</body>
<script>
  function switchVueClasse() {
    var container = document.querySelector('.classe-niveaux');
    if (container) {
      container.classList.toggle('vue-sorts');
    }
  }
</script>

</html>
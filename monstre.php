<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$mo = isset($_GET['mo']) ? (int)$_GET['mo'] : 0;
$re = isset($_GET['rencontre']) ? (int)$_GET['rencontre'] : 0;

if ($mo > 0):
  $sql = "SELECT * FROM dd_monstres WHERE mo_id= :id";
  $stmt = $db->prepare($sql);
  $stmt->execute([':id' => $mo]);
  $monstre = $stmt->fetch(PDO::FETCH_ASSOC);
  $libelle_bouton = "Modifier";
else:
  $libelle_bouton = "Cr&eacute;er";
endif;

// On récupère la rencontre
$sql = "
  SELECT r.*,
         ch.scc_id,
         ch.scc_nom,
         s.sc_id,
         s.sc_nom,
         c.camp_id,
         c.camp_nom
  FROM dd_rencontres r
  LEFT JOIN dd_scenarios_chapitres ch ON r.re_scc_id = ch.scc_id
  LEFT JOIN dd_scenarios s ON ch.scc_sc_id = s.sc_id
  LEFT JOIN dd_campagnes c ON s.sc_camp_id=c.camp_id
  WHERE r.re_id = :id
";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $re]);
$rencontre = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<!doctype html>

<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-classes.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
</HEAD>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <?
      if ($mo != ""):
        $requete = "SELECT * FROM dd_monstres WHERE mo_id='" . $mo . "'";
        $resultat = queryPDO($requete);
        $dn = $resultat->fetch(PDO::FETCH_ASSOC);
      ?>
        <div class="titreAction">
          <div class="titreA">
            <? echo f_nom($monstre['mo_nom']); ?>
            <? echo '<a href="monstre-modifier.php?mo=' . $monstre['mo_id'] . '&retour=monstre"><i class="fa-solid fa-pen-to-square ml15"></i></a>'; ?>
          </div>
          <div></div>
        </div>

        <div id="monstre">
          <div class="texte description"><? echo stripslashes($monstre['mo_stats']); ?></div>
        </div>
      <?
      else:
        echo '<div class="nodata">Aucune monstre disponible !</div>';
      endif;
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div> <!-- wrapper --->
  </div><!-- page --->
  <div id="detail-pp"></div>
  <div id="modification"></div>
</body>

</html>
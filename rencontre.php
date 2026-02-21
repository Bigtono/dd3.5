<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if(isset($_GET['re'])):
  $re=$_GET['re'];
endif;
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
  else:
  $critere='';
endif;
if (strlen($_GET['critere_sc'])>0):
	$critere_sc=$_GET['critere_sc'];
  else:
  $critere_sc='';
endif;
$requete = "SELECT * FROM dd_rencontres WHERE re_id='".$re."'";
$resultat = queryPDO($requete);
$dn=$resultat->fetch(PDO::FETCH_ASSOC);
// On récupère la rencontre
$sql = "
  SELECT r.*,
         s.sc_id,
         s.sc_nom,
         ch.scc_id,
         ch.scc_nom
  FROM dd_rencontres r
  LEFT JOIN dd_scenarios s ON r.re_sc_id = s.sc_id
  LEFT JOIN dd_scenarios_chapitres ch ON r.re_scc_id = ch.scc_id
  WHERE r.re_id = :id
";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $re]);
$rencontre = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-classes.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
  <script type='text/javascript' src='js/moncode-regles.js'></script>
  <script type='text/javascript' src='js/moncode-rencontres.js'></script>
</head>
<body>
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper_rencontre">
      <? include('include/ariane.php'); ?>
      <div class="titreAction ml5 mr5">
        <div class="titreA">
          <? echo $rencontre['re_nom']; ?>
          <a href="rencontre-modifier.php?rencontre=<? echo (int)$rencontre['re_id']; ?>"><i class="fa-solid fa-pen-to-square ml15"></i></a>
        </div>
        <div></div>
      </div>    
      <div id="rencontre">
        <? include('include/insert/'.$_SESSION['rulesetRep'].'/detailRencontre.php'); ?>
        <div id="detailRencontre"><? echo $renc; ?></div>
        <div class="ajout_monstre">
          <div class="gras mr10 lien" onCLick="togglePlus('ajout-monstre')"><span id="toggle-domaines"><i class="fa-solid fa-bars"></i> Ajouter un monstre</span></div>
          <div id="ajout-monstre" class="box-data accordion-content noDisplay">
            <div>
              <select class="mr10" name="mp_nouveau_monstre" id="mp_nouveau_monstre"><? echo optionList("dd_monstres", "mo", "nom",0,'mo_ruleset_var_id="'.$_SESSION['ruleset'].'"'); ?></select>
              <select class="mr10" name="mp_nb_monstre" id="mp_nb_monstre"><? echo optionListInt(1, 20); ?></select>
              <button onclick="ajoutMonstreRencontre('<? echo $re; ?>')" id="ajoutMonstre"><i class="fas fa-plus"></i></button>
            </div>
          </div>
        </div>
      </div> <!-- rencontre --->
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>      
    </div><!-- wrapper --->
    <div class="ecran">
      <? include('include/insert/'.$_SESSION['rulesetRep'].'/ecran.php'); ?>
    </div>
	</div><!-- page --->
  <script>
    // Initialisation au chargement des onglets
    document.addEventListener('DOMContentLoaded', initDetailRencontre);
  </script>

</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
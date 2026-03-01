<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
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
    <?


    $categories = '<select id="mp_mo_mocat_id" name="mp_mo_mocat_id">' . OptionList("dd_monstres_categories", "mocat", "nom", $monstre['mo_mocat_id'], "mocat_ruleset_var_id='" . $_SESSION['ruleset'] . "'") . '</select>';
    $fp = '<select id="mp_mo_fp_id" name="mp_mo_fp_id">' . OptionList("dd_fp", "fp", "nom", $monstre['mo_fp_id']) . '</select>';
    ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA"><?= $titre; // récupéré dnas le fil d'ariane
                            ?></div>
        <div></div>
      </div>
      <form action="monstre-enregistrement.php" method="post" name="modif-monstre" id="modif-monstre">
        <input type="hidden" name="mo" value="<? echo $mo; ?>" />
        <input type="hidden" name="re" value="<? echo $re; ?>" />
        <div id="rencontre">
          <div class="contenu_profil">
            <div class="ligne">
              <div class="label w75">Nom</div><input type="text" id="mp_mo_nom" name="mp_mo_nom" class="monstre_nom" value="<? echo $monstre['mo_nom']; ?>">
            </div>
            <div class="ligne">
              <div class="label w75">Catégorie</div><? echo $categories; ?>
            </div>
            <div class="ligne">
              <div class="label w75">FP</div><? echo $fp; ?>
            </div>
            <div class="label">Bloc de stats</div><textarea id="mp_mo_stats" name="mp_mo_stats" class="monstre_description"><? echo stripslashes($monstre['mo_stats']); ?></textarea>
            <? if ($mo != "n"): // On ne charge pas CKeditor sur un ajout de monstre afin d'éviter que le code Ckeditor ne perturbe la routine de formatage 
            ?>
              <script>
                CKEDITOR.replace('mp_mo_stats', {
                  allowedContent: true, // désactive le filtre de contenu
                  // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
                  extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
                  contentsCss: 'include/_styles_.css',
                  height: 600 // en pixels
                });
              </script>
            <? endif; ?>
          </div>
        </div>
        <!-- affichage des boutons --->
        <div class="ligneBouton">
          <button type="submit" name="action" value="save" class="btNoir">Enregistrer *</button>
          <button type="submit" name="action" value="cancel" class="btGris">Annuler</button>
        </div>
      </form>
      </section>
    </div><!-- page --->
    <div id="detail-pp"></div>
    <div id="modification"></div>
</body>

</html>
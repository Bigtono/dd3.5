<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/affichageSelectionSources.php");

$mo = isset($_POST['mo']) && $_POST['mo'] != "n" ? (int)$_POST['mo'] : 0;
$re = isset($_POST['re']) ? (int)$_POST['re'] : 0;

if ($action == 'cancel'):
  if ($re > 0):
    header("location: rencontre.php?rencontre='.$re.'");
  else:
    header("location: monstres.php&rencontre='.$re.'");
  endif;
  exit;
endif;

if ($mo > 0):
  // Modification 
  $sql = "UPDATE dd_monstres
          SET mo_nom = :mo_nom,
              mo_mocat_id = :mo_mocat_id,
              mo_stats = :mo_stats,
              mo_fp_id = :mo_fp_id
          WHERE mo_id = :mo_id";
  $stmt = $db->prepare($sql);
  $resultat = $stmt->execute([
    ':mo_nom' => $_POST['mp_mo_nom'],
    ':mo_mocat_id' => $_POST["mp_mo_mocat_id"],
    ':mo_stats' => $_POST['mp_mo_stats'],
    ':mo_fp_id' => $_POST["mp_mo_fp_id"],
    ':mo_id' => $mo,
  ]);
else:
  // Vérifie si un textarea a été envoyé
  if (!empty($_POST['mp_mo_stats'])):
    // Récupère les données brutes
    $contenu = trim($_POST['mp_mo_stats']);
    // Normaliser les apostrophes « Word » en apostrophes simples
    $recherche = [
      "’",  // U+2019 typographique
      "‘",  // U+2018 ouverture
      "\xC2\x92",    // variante possible CP1252 ? UTF-8
    ];
    $remplace = ["'", "'", "'"];
    $contenu = str_replace($recherche, $remplace, $contenu);
    $contenu = str_replace(["“", "”"], '"', $contenu);
    // Transforme en tableau de lignes comme pour un fichier
    $lignes = explode("\n", $contenu);
    // Variables utilisées pendant le traitement
    $debug = $_SESSION['debug'];
    $sortie = '';
    $pouvoirs = 0;
    $etape = 0;
    $monstre = "";
    $enregistrement = 1;
    // variable pour la gestion du bloc Caractéristiques
    $mode_caracteristiques = false;
    $buffer_caracteristiques = [];
    //***
    $i = 1;
    static $mode_pouvoirs = false; // Mode "Pouvoirs" activé après une ligne "..."
    // Traitement des lignes
    $resultat_trt = '';
    foreach ($lignes as $ligne):
      $info_trt = '';
      include('include/insert/' . $_SESSION['rulesetRep'] . '/trt-insertion-monstre-2.php');
      if (!empty($info_trt)) $resultat_trt .= $info_trt;
      $i++;
    endforeach;
    // insertion du monstre dans la base
    $requete = "INSERT INTO dd_monstres (mo_nom, mo_mocat_id, mo_stats, mo_ruleset_var_id, mo_fp_id)
                VALUES (:mo_nom, :mo_mocat_id, :mo_stats, :mo_ruleset_var_id, :mo_fp_id)";
    $stmt = $db->prepare($requete);
    $resultat = $stmt->execute([
      ':mo_nom' => $_POST["mp_mo_nom"],
      ':mo_mocat_id' => $_POST["mp_mo_mocat_id"],
      ':mo_stats' => $monstre,
      ':mo_ruleset_var_id' => $_SESSION['ruleset'],
      ':mo_fp_id' => $_POST["mp_mo_fp_id"],
    ]);
    $mo = lastID("dd_monstres", "mo");
    $info_trt .= '<div class="ml20 gras">Insertion du monstre "' . $_POST["mp_mo_nom"] . '"</div>';
  else:
    // Si textarea vide mais que l'action d'import est demandée
    if (isset($_POST['import']) && $_POST['import'] == 1):
      if ($re > 0):
        header("location: rencontre.php?rencontre='.$re.'&msg=nodata");
      else:
        header("location: monstres.php&msg=nodata");
      endif;
      exit;
    endif;
  endif;
endif;
header("location: monstre.php?mo=" . $mo . "&rencontre=" . $re);

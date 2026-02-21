<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

include("include/affichageSelectionSources.php");

$mo = $_POST['mp_mo_id'];

if (isset($_POST['validModifMonstre'])):
  if ($mo=="n"):
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
      $contenu = str_replace(["“","”"], '"', $contenu);
      // Transforme en tableau de lignes comme pour un fichier
      $lignes = explode("\n", $contenu);
      // Variables utilisées pendant le traitement
      $debug = $_SESSION['debug'];
      $sortie = '';
      $pouvoirs = 0;
      $etape=0;
      $monstre = "";
      $enregistrement = 1;
      // variable pour la gestion du bloc Caractéristiques
      $mode_caracteristiques = false;
      $buffer_caracteristiques = [];
      //***
      $i = 1;
      static $mode_pouvoirs = false; // Mode "Pouvoirs" activé après une ligne "..."
      // Traitement des lignes
      $resultat_trt='';
      foreach ($lignes as $ligne):
        $info_trt = '';
        include('include/insert/'.$_SESSION['rulesetRep'].'/trt-insertion-monstre-2.php');
        if (!empty($info_trt)) $resultat_trt.=$info_trt;
        $i++;
      endforeach;
      // insertion du monstre dans la base
      $requete='INSERT INTO dd_monstres (mo_nom, mo_mocat_id, mo_stats, mo_ruleset_var_id, mo_fp_id) VALUES ("'.addslashes($_POST["mp_mo_nom"]).'","'.$_POST["mp_mo_mocat_id"].'","'.addslashes($monstre).'","'.$_SESSION['ruleset'].'","'.$_POST["mp_mo_fp_id"].'")';
      $resultat=execPDO($requete); 
      $mo=lastID("dd_monstres","mo");
      $info_trt.='<div class="ml20 gras">Insertion du monstre "'.$_POST["mp_mo_nom"].'"</div>';
      else:
      // Si textarea vide mais que l'action d'import est demandée
      if (isset($_POST['import']) && $_POST['import'] == 1):
          $msg=1;
      endif;
    endif;
    else:
    // Modification 
    $sql="UPDATE dd_monstres SET mo_nom='".addslashes($_POST['mp_mo_nom'])."', mo_mocat_id='".$_POST["mp_mo_mocat_id"]."', mo_stats='".addslashes($_POST['mp_mo_stats'])."', mo_fp_id='".$_POST["mp_mo_fp_id"]."' WHERE mo_id='".$mo."'";
    $resultat = execPDO($sql);
  endif;
  header("location: monstre.php?mo=".$mo."&msg=1");
  else: // l'action est annuler
  header("location: monstres.php");
endif;
?>
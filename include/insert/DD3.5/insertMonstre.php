<?
  /* Insertion Monstre DD3.5 */

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
  $i = 1;
  static $mode_pouvoirs = false; // Mode "Pouvoirs" activé après une ligne "..."
  // Traitement du nom et de la catégorie du monstre
  $resultat_trt='';
  foreach ($lignes as $ligne):
    $info_trt = '';
    include('include/insert/'.$_SESSION['rulesetRep'].'/trt-insertion-monstre-2.php');
    if (!empty($info_trt)) $resultat_trt.=$info_trt;
    $i++;
  endforeach;

?>
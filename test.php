<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<html>

<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
</HEAD>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
  <div class="titreAction">
    <div class="titreA">Test de code</div>
  </div>

  <?
  $p = 14;
  $liste = '';
  // gestion des classes
  $requete_cl = 'SELECT pc_id, pc_niveau, cla_niveauMax, cla_nom FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_pe_id="' . $p . '" ORDER BY pc_niveau DESC;';
  $result_cl = queryPDO($requete_cl);
  $num_rows_cl = $result_cl->rowCount();
  if ($num_rows_cl > 0):
    while ($dncl = $result_cl->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<div id="pc' . $dncl['pc_id'] . '" class="classe">';
      $liste .= '  <div onClick="supprimerClassePerso(' . $p . ',' . $dncl['pc_id'] . ')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
      $liste .= '  <div class="libelle_classe">' . $dncl['cla_nom'] . '</div>';
      $liste .= '  <select class="niveau_classe" id="pcn' . $dncl['pc_id'] . '" name="pcn' . $dncl['pc_id'] . '" onChange="majNiveauClassePerso(\'pcn' . $dncl['pc_id'] . '\')">';
      $liste .= optionListInt(1, $dncl['cla_niveauMax'], $dncl['pc_niveau'], "T");
      $liste .= '  </select>';
      $liste .= '</div>';
    endwhile;
  endif;
  echo "Liste (" . $dn['pc_niveau'] . ")<br>" . $liste;

  ?>

</body>
<div id="detail-pp"></div>
<div id="modification"></div>

</html>
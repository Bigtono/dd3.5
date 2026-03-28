<?
$requete_filtre = "SELECT tyno_id, tyno_nom, tyno_icone FROM dd_types_notes ORDER BY tyno_nom";
$result_fn = queryPDO($requete_filtre);
$num_rows_fn = $result_fn->rowCount();
$selectedType = isset($_GET['type']) ? (string)$_GET['type'] : 'tout';
$filtre_notes = '';
if ($num_rows_fn > 0):
  $filtre_notes = '<select id="filtre_notes" name="type" class="search-select">';
  $filtre_notes .= '<option value="tout"' . (($selectedType === 'tout' || $selectedType === 'Tout' || $selectedType === '') ? ' selected="selected"' : '') . '>tout</option>';
  while ($dnfn = $result_fn->fetch(PDO::FETCH_ASSOC)):
    $typeId = (string)$dnfn['tyno_id'];
    $filtre_notes .= '<option value="' . $typeId . '"' . (($selectedType === $typeId) ? ' selected="selected"' : '') . '>' . htmlspecialchars($dnfn['tyno_nom']) . '</option>';
  endwhile;
  $filtre_notes .= '</select>';
endif;
?>
<div class="contenu">
  <div class="titreAction">
    <div class="titreA">Connaissances</div>
    <div></div>
  </div>
  <div>
    <?
    if ($filtre_notes != ''):
      echo '<form action="personnage-connaissances.php" method="get" class="line-data-fr100">';
      echo '<input type="hidden" name="personnage" value="' . (int)$p . '">';
      if (isset($campagneId) && (int)$campagneId > 0):
        echo '<input type="hidden" name="campagne" value="' . (int)$campagneId . '">';
      endif;
      echo $filtre_notes;
      echo '<button type="submit" class="search-button" id="search_res" name="search_res"><i class="fa-solid fa-magnifying-glass"></i></button>';
      echo '</form>';
    endif;
    ?>
  </div>
  <div id="listeNotesPerso">
    <? include('include/insert/' . $_SESSION['rulesetRep'] . '/listeNotesPerso.php'); ?>
  </div>
</div>

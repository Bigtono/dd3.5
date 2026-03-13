<?
$requete_filtre = "SELECT tyno_id, tyno_nom, tyno_icone FROM dd_types_notes ORDER BY tyno_nom";
$result_fn = queryPDO($requete_filtre);
$num_rows_fn = $result_fn->rowCount();
$filtre_notes = '';
if ($num_rows_fn > 0):
  $filtre_notes = '<select id="filtre_notes" name="filtre_notes" class="search-select">';
  $filtre_notes .= '<option value="tout">tout</option>';
  while ($dnfn = $result_fn->fetch(PDO::FETCH_ASSOC)):
    $filtre_notes .= '<option value="' . $dnfn['tyno_id'] . '">' . htmlspecialchars($dnfn['tyno_nom']) . '</option>';
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
      echo $filtre_notes;
      echo '<button type="submit" class="search-button" id="search_res" name="search_res"/><i class="fa-solid fa-magnifying-glass"></i></button>';
    endif;
    ?>
  </div>
  <div id="listeNotesPerso">
    <? include('include/insert/' . $_SESSION['rulesetRep'] . '/listeNotesPerso.php'); ?>
  </div>
</div>

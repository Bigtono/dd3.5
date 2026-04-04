<?
include_once(__DIR__ . '/personnage_grimoire_helper.php');

// Contexte slots aligné sur la page grimoire (inclut bonus carac/domain/NLS prestige)
$pgContext = pg_magic_load_context($db, (int)$p, (string)$_SESSION['rulesetRep'], []);
?>
<div class="contenu">
  <div class="titreAction">
    <div class="titreA">
      Magie
      <span class="ml15"><a class="lien_cbt" href="personnage-grimoire.php?personnage=<? echo $p; ?>&campagne=<? echo $campagneId; ?>"><i class="fa-solid fa-book"></i></a></span>
    </div>
    <div><a class="lien_cbt" href="grimoire-modifier.php?personnage=<? echo $p; ?>&campagne=<? echo $campagneId; ?>"><i class="fa-solid fa-pen-to-square"></i></a></div>
  </div>
  <div>
    <?php
    if ((string)$_SESSION['rulesetRep'] === 'DD3.5' && !empty($pgContext['has_spellcasting'])):
      foreach ($pgContext['ordered_class_ids'] as $pcId):
        $classData = $pgContext['classes'][$pcId];
        ?>
        <div class="mb10">
          <div class="gras mb5">Nombre de sorts par jour &mdash; <? echo htmlspecialchars($classData['cla_nom']); ?> (NLS <? echo (int)$classData['nls']; ?>)</div>
          <div class="tabMain mb10">
            <? for ($lvl = 0; $lvl <= 9; $lvl++):
              $compCss = ($lvl == 1) ? ' cellLeft' : '';
              $value = (isset($classData['slots'][$lvl]) ? $classData['slots'][$lvl] : null);
              $display = ($value === null || $value === '') ? '-' : (int)$value;
            ?>
              <div class="cellMainSort">
                <div>
                  <div class="cellEntete<? echo $compCss; ?>"><? echo $lvl; ?></div>
                  <div class="cellValue<? echo $compCss; ?>"><? echo $display; ?></div>
                </div>
              </div>
            <? endfor; ?>
          </div>
        </div>
      <? endforeach;
    else: ?>
      <div class="nodata">Ce personnage ne poss&egrave;de aucune classe de lanceur de sorts.</div>
    <? endif; ?>
  </div>

  <div>
    <?
    $requete = 'SELECT gr_cla_id, count(grc_so_id) as nbsorts FROM dd_grimoires JOIN dd_grimoires_contenu ON gr_id=grc_gr_id WHERE gr_pe_id="' . $p . '" AND gr_defaut="1" GROUP BY gr_cla_id ORDER BY nbsorts DESC';
    if ($_SESSION['mj'] == 1 && $_SESSION['debug'] == 1) echo '<div>' . $requete . '</div>';
    $result_gr = queryPDO($requete);
    $num_rows_gr = $result_gr->rowCount();
    if ($num_rows_gr > 0):
      $dngr = $result_gr->fetch(PDO::FETCH_ASSOC);
      if ($num_rows_gr > 1) echo '<div class="titre">' . libelle("dd_classes", "cla", "nom", $dngr['gr_cla_id']) . '</div>';
      echo '<div id="grimoire' . $dngr['gr_cla_id'] . '">';
      $requete = 'SELECT gr_id, grc_so_id, so_nom, sc_niveau FROM dd_grimoires JOIN dd_grimoires_contenu ON gr_id=grc_gr_id LEFT JOIN dd_sorts ON grc_so_id=so_id LEFT JOIN dd_sortclasse ON so_id=sc_so_id WHERE gr_cla_id=' . $dngr['gr_cla_id'] . ' AND sc_cla_id=' . $dngr['gr_cla_id'] . ' AND gr_pe_id=' . $p . ' ORDER BY sc_niveau, so_nom';
      $result = queryPDO($requete);
      $num_rows = $result->rowCount();
      if ($num_rows > 0):
        $niveau = '';
        $i = 0;
        if ($_SESSION['debug'] == 1 && $_SESSION['mj'] == 1) echo '<div class="action">' . $requete . '</div>';
        while ($sort = $result->fetch(PDO::FETCH_ASSOC)):
          if ($sort['sc_niveau'] != $niveau):
            $i = 0;
            if ($niveau != '') echo '</div>';
            echo '<div class="gras gros mb10"><i class="fa-solid fa-wand-magic-sparkles mr10"></i> Niveau ' . $sort['sc_niveau'] . '</div>';
          endif;
          if ($i == 0) echo '<div class="lignePastille2">';
          if ($_SESSION['onglet_sort'] == 1):
            echo '  <div class="icone_onglet mr10">';
            echo '    <a href="sort.php?sort=' . $sort['grc_so_id'] . '" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>';
            echo '  </div>';
          endif;
          echo '<div onClick="afficherSort(' . $sort['grc_so_id'] . ')" class="pastille2 lien">' . $sort['so_nom'];
          if ($isAdmin && $isDebug):
            echo ' (' . $sort['grc_so_id'] . ')';
          endif;
          echo '</div>';
          $i += 1;
          $niveau = $sort['sc_niveau'];
        endwhile;
        echo '</div>';
      endif;
      echo '</div><!--  grimoire' . $dngr['gr_cla_id'] . ' -->';
    else:
      echo 'Aucun grimoire';
    endif;
    ?>
  </div>
</div>

<?
// recherche du grimoire par defaut
$requete = 'SELECT gr_id FROM dd_grimoires WHERE gr_pe_id="' . $p . '" AND gr_defaut="1"';
$result_grd = queryPDO($requete);
$num_rows_grd = $result_grd->rowCount();
if ($num_rows_grd > 0):
  $dngrd = $result_grd->fetch(PDO::FETCH_ASSOC);
  $grimoire = $dngrd['gr_id'];
else:
  $grimoire = 0;
endif;
?>
<div class="contenu">
  <div class="titreAction">
    <div class="titreA">
      Magie
      <span class="ml15"><a class="lien_cbt" href="grimoire.php?personnage=<? echo $p; ?>&campagne=<? echo $campagneId; ?>"><i class="fa-solid fa-book"></i></a></span>
    </div>
    <div><a class="lien_cbt" href="grimoire-modifier.php?personnage=<? echo $p; ?>&campagne=<? echo $campagneId; ?>"><i class="fa-solid fa-pen-to-square"></i></a></div>
  </div>
  <div>
    <?
    // recherche de la classe de LS
    $requete = 'SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_caracteristiques ON cla_car_id=car_id WHERE cla_mag_id>0 AND pc_pe_id="' . $p . '" ORDER BY cla_mag_id';
    $result_ls = queryPDO($requete);
    $num_rows_ls = $result_ls->rowCount();
    if ($num_rows_ls > 0):
      $dnls = $result_ls->fetch(PDO::FETCH_ASSOC);
      echo '<div>Nombre de sorts pas jour</div>';
      $requete = 'SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_classe_niveau ON cn_cla_id=cla_id WHERE cla_mag_id>0 AND pc_pe_id="' . $p . '" AND cla_id="' . $dnls['cla_id'] . '" AND cn_niveau="' . $nls . '"';
      if ($_SESSION['mj'] == 1 && $_SESSION['debug'] == 1) echo '<div>' . $requete . '</div>';
      $result_nls = queryPDO($requete);
      $num_rows_nls = $result_nls->rowCount();
      if ($num_rows_nls > 0):
        $dnnls = $result_nls->fetch(PDO::FETCH_ASSOC);
        $result = '  <div class="tabMain mb10">';
        for ($i = 0; $i < 10; $i++):
          $compCss = '';
          if ($i == 1) $compCss = " cellLeft";
          if (strlen($dnnls['cn_sort_n' . $i]) > 0):
            $nbs = $dnnls['cn_sort_n' . $i];
          else:
            $nbs = '-';
          endif;
          $result .= '    <div class="cellMainSort">';
          $result .= '      <div>';
          $result .= '        <div class="cellEntete' . $compCss . '">' . $i . '</div>';
          $result .= '        <div class="cellValue' . $compCss . '">' . $nbs . '</div>';
          $result .= '      </div>';
          $result .= '    </div>';
        endfor;
        $result .= '  </div>';
        echo '<div>' . $result . '</div>';
      endif;
    endif;
    ?>
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

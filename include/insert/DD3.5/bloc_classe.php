<!--  Bloc Classe DD3.5 --->

<?
$activePouvoirs = [];
for ($i = 1; $i <= 4; $i++) {
  if (trim((string)$dn['cla_pouvoir' . $i]) !== '') {
    $activePouvoirs[] = $i;
  }
}

$gridTemplate = '60px repeat(4, minmax(55px, 70px)) minmax(220px, 1fr)';
if (!empty($activePouvoirs)) {
  $gridTemplate .= ' repeat(' . count($activePouvoirs) . ', minmax(90px, 120px))';
}
if ($isLanceurSorts) {
  $gridTemplate .= ' repeat(10, minmax(30px, 40px))';
}
?>
<style>
  .classe-table {
    overflow-x: auto;
  }

  .classe-ligne {
    display: grid;
    grid-template-columns: <?= $gridTemplate ?>;
    column-gap: 8px;
    align-items: center;
    font-size: 14px;
  }

  .classe-lignes {
    display: block;
    gap: 0;
  }

  .classe-ligne.classe-entete {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    opacity: 0.78;
    margin-bottom: 4px;
  }

  .classe-niveaux .list-body.classe-lignes {
    border: none !important;
    border-radius: 0 !important;
  }

  .classe-ligne .cell {
    text-align: center;
    white-space: nowrap;
    padding: 5px 6px;
    box-sizing: border-box;
  }

  .classe-ligne .cell.aptitudes {
    text-align: left;
    white-space: normal;
  }

  .classe-ligne .cell.niveau {
    font-weight: 700;
  }

  .classe-lignes .classe-ligne.contenu {
    padding: 0;
    border: none !important;
    border-radius: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
  }

  .classe-lignes .classe-ligne.contenu:nth-child(odd) {
    background: #f5efe6;
  }

  .classe-lignes .classe-ligne.contenu:nth-child(even) {
    background: #ffffff;
  }

  @media (max-width: 991px) {
    .classe-ligne {
      min-width: 620px;
    }

    .classe-ligne.classe-entete .cell[data-groupe="sorts"] {
      display: none;
    }

    .classe-ligne.contenu .cell[data-groupe="sorts"] {
      display: none;
    }

    .classe-niveaux.vue-sorts .classe-ligne.classe-entete .cell[data-groupe="stats"],
    .classe-niveaux.vue-sorts .classe-ligne.classe-entete .cell[data-groupe="aptitudes"],
    .classe-niveaux.vue-sorts .classe-ligne.classe-entete .cell[data-groupe="pouvoirs"] {
      display: none;
    }

    .classe-niveaux.vue-sorts .classe-ligne.contenu .cell[data-groupe="stats"],
    .classe-niveaux.vue-sorts .classe-ligne.contenu .cell[data-groupe="aptitudes"],
    .classe-niveaux.vue-sorts .classe-ligne.contenu .cell[data-groupe="pouvoirs"] {
      display: none;
    }

    .classe-niveaux.vue-sorts .classe-ligne.classe-entete .cell[data-groupe="sorts"] {
      display: block;
    }

    .classe-niveaux.vue-sorts .classe-ligne.contenu .cell[data-groupe="sorts"] {
      display: block;
    }
  }
</style>


<div class="entete">
  <div class="gauche">
    <div class="ligne"><span class="label">D&eacute; de vie : </span><? echo stripslashes($dn['cla_dV']); ?></div>
    <div class="ligne"><span class="label">Niveau max : </span><? echo stripslashes($dn['cla_niveauMax']); ?></div>
    <div class="ligne"><span class="label">Points de comp&eacute;tences : </span><? echo stripslashes($dn['cla_pointsCompetences']); ?></div>
  </div>
  <div class="droite">
    <div class="ligne"><span class="label">Type de magie : </span><? echo libelle("dd_typeMagie", "mag", "nom", $dn['cla_mag_id']); ?></div>
    <div class="ligne"><span class="label">Caract&eacute;ristique de Lanceur de sort : </span><? echo libelle("dd_caracteristiques", "car", "nom", $dn['cla_car_id']); ?></div>
    <div class="ligne"><span class="label">Alignement : </span><? echo stripslashes($dn['cla_alignement']); ?></div>
  </div>
</div>

<?
$competences = '';
$requete = 'SELECT comp_id, comp_nom, cc_precision FROM `dd_classe_competence` JOIN dd_classes ON cc_cla_id=cla_id JOIN dd_competences ON cc_comp_id=comp_id WHERE cc_cla_id="' . $c . '" ORDER BY cla_nom, comp_nom';
$resultat_cc = queryPDO($requete);
$num_rows_cc = $resultat_cc->rowCount();
if ($num_rows_cc > 0):
  while ($dncc = $resultat_cc->fetch(PDO::FETCH_ASSOC)):
    if ($dncc['cc_precision'] != ''):
      $precision = ' (' . $dncc['cc_precision'] . ')';
    else:
      $precision = '';
    endif;
    if ($critere != ''):
      $competence = tag($critere, $dncc['comp_nom']);
    else:
      $competence = $dncc['comp_nom'];
    endif;
    if ($competences != '') $competences .= ', ';
    $competences .= '<span id="comp' . $dncc['comp_id'] . '" class="lien" onClick="afficherComp(' . $dncc['comp_id'] . ')">' . $competence . $precision . '</span>';
  endwhile;
else:
  $competences = "aucune";
endif;
?>
<div class="competences"><span class="label"> Comp&eacute;tences de classe : </span><? echo $competences; ?></div>

<? if ($dn['cla_clt_id'] == 2): ?>
  <div class="label_long mt10">Conditions (classe de prestige uniquement) :</div>
  <div class="conditions"><? echo stripslashes($dn['cla_conditions']); ?></div>
<? endif; ?>

<!--- Tableau de la classe --->
<?
$sql = "
  SELECT
    cn.cn_niveau,
    cn.cn_bba,
    cn.cn_reflexes,
    cn.cn_vigueur,
    cn.cn_volonte,
    cn.cn_pouvoir1,
    cn.cn_pouvoir2,
    cn.cn_pouvoir3,
    cn.cn_pouvoir4,
    cn.cn_sort_n0,
    cn.cn_sort_n1,
    cn.cn_sort_n2,
    cn.cn_sort_n3,
    cn.cn_sort_n4,
    cn.cn_sort_n5,
    cn.cn_sort_n6,
    cn.cn_sort_n7,
    cn.cn_sort_n8,
    cn.cn_sortPrepare,
    GROUP_CONCAT(
      CONCAT(
        '<span class=\"cap\" onclick=\"afficherCapacite(',
        cap.cap_id,
        ')\">',
        cap.cap_nom,
        '</span>'
      )
      SEPARATOR ', '
    ) AS capacites
  FROM dd_classe_niveau cn
  LEFT JOIN dd_classe_capacite cc
    ON cc.cc_cla_id = cn.cn_cla_id
    AND cc.cc_niveau = cn.cn_niveau
  LEFT JOIN dd_capacites_speciales cap
    ON cap.cap_id = cc.cc_cap_id
  WHERE cn.cn_cla_id = ?
  GROUP BY cn.cn_niveau
  ORDER BY cn.cn_niveau
  ";

$stmt = $db->prepare($sql);
$stmt->execute([$c]);
$niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="classe-niveaux <?= !$isLanceurSorts ? 'no-sorts' : '' ?>">

  <div class="classe-header">
    <div class="classe-title">
      Table : <?= htmlspecialchars($dn['cla_nom']) ?>
    </div>

    <? if ($isLanceurSorts): ?>
      <button
        class="btn-switch"
        onclick="switchVueClasse()"
        title="Afficher les sorts / les caractéristiques"
        aria-label="Basculer affichage">
        <i class="fas fa-exchange-alt"></i>
      </button>
    <? endif; ?>
  </div>

  <div class="classe-table">
    <div class="classe-ligne classe-entete">
      <div class="cell niveau" data-groupe="niveau">Niveau</div>
      <div class="cell" data-groupe="stats">BBA</div>
      <div class="cell" data-groupe="stats">R&eacute;f.</div>
      <div class="cell" data-groupe="stats">Vig.</div>
      <div class="cell" data-groupe="stats">Vol.</div>
      <div class="cell aptitudes" data-groupe="aptitudes">Sp&eacute;cial</div>
      <? foreach ($activePouvoirs as $pow): ?>
        <div class="cell" data-groupe="pouvoirs"><?= htmlspecialchars((string)$dn['cla_pouvoir' . $pow]) ?></div>
      <? endforeach; ?>
      <? if ($isLanceurSorts): ?>
        <div class="cell" data-groupe="sorts">0</div>
        <div class="cell" data-groupe="sorts">1</div>
        <div class="cell" data-groupe="sorts">2</div>
        <div class="cell" data-groupe="sorts">3</div>
        <div class="cell" data-groupe="sorts">4</div>
        <div class="cell" data-groupe="sorts">5</div>
        <div class="cell" data-groupe="sorts">6</div>
        <div class="cell" data-groupe="sorts">7</div>
        <div class="cell" data-groupe="sorts">8</div>
        <div class="cell" data-groupe="sorts">9</div>
      <? endif; ?>
    </div>

    <div class="list-body classe-lignes">
      <? foreach ($niveaux as $n): ?>
        <div class="classe-ligne contenu">
          <div class="cell niveau" data-groupe="niveau"><?= $n['cn_niveau'] ?></div>
          <div class="cell" data-groupe="stats"><?= $n['cn_bba'] ?></div>
          <div class="cell" data-groupe="stats">+<?= $n['cn_reflexes'] ?></div>
          <div class="cell" data-groupe="stats">+<?= $n['cn_vigueur'] ?></div>
          <div class="cell" data-groupe="stats">+<?= $n['cn_volonte'] ?></div>
          <div class="cell aptitudes" data-groupe="aptitudes"><?= $n['capacites'] ?: '&mdash;' ?></div>
          <? foreach ($activePouvoirs as $pow): ?>
            <div class="cell" data-groupe="pouvoirs"><?= ($n['cn_pouvoir' . $pow] !== '' ? htmlspecialchars((string)$n['cn_pouvoir' . $pow]) : '&mdash;') ?></div>
          <? endforeach; ?>
          <? if ($isLanceurSorts): ?>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n0'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n1'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n2'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n3'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n4'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n5'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n6'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n7'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n8'] ?: '&mdash;' ?></div>
            <div class="cell" data-groupe="sorts"><?= $n['cn_sort_n9'] ?: '&mdash;' ?></div>
          <? endif; ?>
        </div>
      <? endforeach; ?>
    </div>
  </div>
</div>

<div class=" capacites mt10">
  <?
  // Armes et armures
  echo '<div class="capacite"><div>Armes et amures</div>' . $dn['cla_armes_armures'] . '</div>';

  // Sorts
  if ($dn['cla_mag_id'] > 0 && $dn['cla_sorts'] != ""):
    echo '<div class="capacite"><div>Sorts</div>' . $dn['cla_sorts'] . '</div>';
  endif;

  // autres capacités
  $requete = 'SELECT DISTINCT cap_id, cap_nom, cap_description FROM `dd_classe_capacite` JOIN dd_capacites_speciales ON cc_cap_id=cap_id WHERE cc_cla_id="' . $c . '" ORDER BY cc_niveau';
  $resultat_cap = queryPDO($requete);
  $num_rows_cap = $resultat_cap->rowCount();
  if ($num_rows_cap > 0):
    while ($dncap = $resultat_cap->fetch(PDO::FETCH_ASSOC)):
      echo '<div class="capacite"><div><span id="capNom' . $dncap['cap_id'] . '">' . $dncap['cap_nom'] . '</span> <span onclick="modifierCapacite(' . $dncap['cap_id'] . ')"><i class="fa fa-pencil"></i></span></div><span id="capDesc' . $dncap['cap_id'] . '">' . $dncap['cap_description'] . '</span></div>';
    endwhile;
  endif;
  ?>
</div>
<!--  Bloc Classe DD3.5 --->

<?

/* -------------------------------------------------
   Détermination des colonnes actives pour la classe
-------------------------------------------------- */
/* spécial toujours présent */
$colonnes = ['niveau', 'stats', 'aptitudes'];


/* blocs liés aux sorts */
if ($isLanceurSorts) {
  $colonnes[] = 'sorts';
}

/* -------------------------------------------------
   Construction dynamique du grid-template-columns
-------------------------------------------------- */

$gridColumns = [];

foreach ($colonnes as $col) {

  switch ($col) {

    case 'niveau':
      $gridColumns[] = '45px';
      break;

    case 'stats':
      $gridColumns[] = '210px';
      break;

    case 'sorts':
      $gridColumns[] = '320px';
      break;

    case 'aptitudes':
      $gridColumns[] = '1fr';
      break;

    default:
      //
  }
}

$gridTemplate = implode(' ', $gridColumns);

?>
<style>
  .classe-ligne {
    display: grid;
    grid-template-columns: <?= $gridTemplate ?>;
    gap: 8px;
    align-items: start;
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
  <div class="label mt10">Conditions (classe de prestige uniquement) :</div>
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

  <div class="list-body classe-lignes">

    <? foreach ($niveaux as $n): ?>
      <div class="classe-ligne contenu">

        <div class="bloc niveau" data-groupe="niveau">
          <span class="label">Niveau</span>
          <span class="valeur"><?= $n['cn_niveau'] ?></span>
        </div>

        <div class="bloc stats" data-groupe="stats">
          <div><span>BBA</span><strong><?= $n['cn_bba'] ?></strong></div>
          <div><span>Réflexes</span><strong>+<?= $n['cn_reflexes'] ?></strong></div>
          <div><span>Vigueur</span><strong>+<?= $n['cn_vigueur'] ?></strong></div>
          <div><span>Volonté</span><strong>+<?= $n['cn_volonte'] ?></strong></div>
        </div>

        <div class="bloc aptitudes" data-groupe="aptitudes">
          <span class="label">Special</span>
          <span class="valeur"><?= $n['capacites'] ?: '—' ?></span>
        </div>

        <? if ($isLanceurSorts): ?>
          <div class="bloc sorts" data-groupe="sorts">
            <div><span>0</span><strong><?= $n['cn_sort_n0'] ?: '—' ?></strong></div>
            <div><span>1</span><strong><?= $n['cn_sort_n1'] ?: '—' ?></strong></div>
            <div><span>2</span><strong><?= $n['cn_sort_n2'] ?: '—' ?></strong></div>
            <div><span>3</span><strong><?= $n['cn_sort_n3'] ?: '—' ?></strong></div>
            <div><span>4</span><strong><?= $n['cn_sort_n4'] ?: '—' ?></strong></div>
            <div><span>5</span><strong><?= $n['cn_sort_n5'] ?: '—' ?></strong></div>
            <div><span>6</span><strong><?= $n['cn_sort_n6'] ?: '—' ?></strong></div>
            <div><span>7</span><strong><?= $n['cn_sort_n7'] ?: '—' ?></strong></div>
            <div><span>8</span><strong><?= $n['cn_sort_n8'] ?: '—' ?></strong></div>
            <div><span>9</span><strong><?= $n['cn_sort_n9'] ?: '—' ?></strong></div>
          </div>
        <? endif; ?>
      </div>
    <? endforeach; ?>

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
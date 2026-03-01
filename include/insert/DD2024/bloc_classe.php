<!--  Bloc Classe version DD2024 --->

<?

/* -------------------------------------------------
   Détermination des colonnes actives pour la classe
-------------------------------------------------- */
/* spécial toujours présent */
$colonnes = ['niveau', 'stats', 'aptitudes'];

/* pouvoirs spécifiques à la classe */
for ($i = 1; $i <= 4; $i++) {
  if (!empty($dn['cla_pouvoir' . $i])) {
    $colonnes[] = 'pouvoir' . $i;
  }
}

/* blocs liés aux sorts */
if ($isLanceurSorts) {
  $colonnes[] = 'sorts_mineurs';
  $colonnes[] = 'sorts_prepares';
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
      $gridColumns[] = '70px';
      break;

    case 'pouvoir1':
    case 'pouvoir2':
    case 'pouvoir3':
    case 'pouvoir4':
      $gridColumns[] = '100px';
      break;

    case 'sorts_mineurs':
    case 'sorts_prepares':
      $gridColumns[] = '80px';
      break;

    case 'sorts':
      $gridColumns[] = '280px';
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


<div class="contenu">
  <div class="ligne_classe_dd5">
    <div class="label">Caractéristique(s) principale(s)</div>
    <div><? echo stripslashes($dn['cla_caracteristiques']); ?></div>
  </div>
  <div class="ligne_classe_dd5">
    <div class="label">D&eacute; de vie : </div>
    <div><? echo stripslashes($dn['cla_dV']); ?></div>
  </div>
  <div class="ligne_classe_dd5">
    <div class="label">Maîtrise des jets de sauvegarde</div>
    <div><? echo stripslashes($dn['cla_sauvegardes']); ?></div>
  </div>
  <div class="ligne_classe_dd5">
    <div class="label">Maîtrise des compétences</div>
    <div><? echo stripslashes($dn['cla_competences']); ?></div>
  </div>
  <div class="ligne_classe_dd5">
    <div class="label">Maîtrise d'armes</div>
    <div><? echo stripslashes($dn['cla_armes']); ?></div>
  </div>
  <div class="ligne_classe_dd5">
    <div class="label">Formation aux armures</div>
    <div><? echo stripslashes($dn['cla_armures']); ?></div>
  </div>
  <div class="ligne_classe_dd5"></div>
  <div class="label">Équipement de départ</div>
  <div><? echo stripslashes($dn['cla_equipement']); ?></div>
</div>



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
    cn.cn_pouvoir1,
    cn.cn_pouvoir2,
    cn.cn_pouvoir3,
    cn.cn_pouvoir4,
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
          <div>
            <span class="label">Bonus de maîtrise</span>
            <span class="valeur"><?= $n['cn_bba'] ?></span>
          </div>
        </div>

        <div class="bloc aptitudes" data-groupe="aptitudes">
          <span class="label">Aptitude de classe</span>
          <span class="valeur"><?= $n['capacites'] ?: '—' ?></span>
        </div>

        <!-- Pouvoirs spécifiques -->
        <? for ($i = 1; $i <= 4; $i++): ?>
          <? if (!empty($dn['cla_pouvoir' . $i])): ?>
            <div class="bloc pouvoir" id="pouvoir<?= $i ?>" data-groupe="aptitudes">
              <span class="label"><?= $dn['cla_pouvoir' . $i] ?></span>
              <span class="valeur"><?= $n['cn_pouvoir' . $i] ?></span>
            </div>
          <? endif; ?>
        <? endfor; ?>

        <? if ($isLanceurSorts): ?>
          <div class="bloc sort_mineurs" data-groupe="sorts">
            <span class="label">Sorts mineurs</span>
            <span class="valeur"><?= $n['cn_sort_n0'] ?: '—' ?></span>
          </div>

          <div class="bloc sorts_prepares" data-groupe="sorts">
            <span class="label">Sorts préparés</span>
            <span class="valeur"><?= $n['cn_sortPrepare'] ?: '—' ?></span>
          </div>

          <div class="bloc sorts" data-groupe="sorts">
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
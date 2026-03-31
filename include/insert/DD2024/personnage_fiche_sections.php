<?php
$organisation = libelle("dd_organisations", "org", "nom", $dn['pe_org_id']);
?>
<div class="personnage-section mb10">
  <div>
    <? echo libelle("dd_races", "ra", "nom", $dn['pe_ra_id'], "ra_rat_id=1") . $archetype; ?>,
    Niveau <? echo niveauPersonnage($p) . ' (' . classesPersonnage($p) . ')'; ?>,
    <? echo libelle("dd_alignements", "al", "nom", $dn['pe_al_id']); ?>
  </div>
  <? if ($organisation != ''): ?>
    <div><span class="label">Organisation : </span> <? echo htmlspecialchars($organisation); ?></div>
  <? endif; ?>
  <? if ($_SESSION['mj'] == 1): ?>
    <div><span class="label">Joueur : </span><? echo htmlspecialchars(libelle_joueur($dn['pe_j_id'])); ?></div>
  <? endif; ?>
</div>

<div class="personnage-section">
  <div class="titre">
    <span class="personnage-section-title">
      <i class="fa-solid fa-chart-column personnage-section-icon"></i>
      <span>Caracteristiques</span>
    </span>
  </div>
  <div class="personnage-carac-block">
    <table class="personnage-carac-table">
      <thead>
        <tr>
          <? foreach ($personnageCaracs as $caracData): ?>
            <th><? echo htmlspecialchars($caracData['label']); ?></th>
          <? endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <? foreach ($personnageCaracs as $caracData): ?>
            <td><? echo (int)$caracData['score']; ?> (<? echo htmlspecialchars($caracData['mod_label']); ?>)</td>
          <? endforeach; ?>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="personnage-section">
  <div class="titre">
    <span class="personnage-section-title">
      <i class="fa-solid fa-shield-halved personnage-section-icon"></i>
      <span>Combat</span>
    </span>
  </div>
  <div class="personnage-combat-values">
    <div class="personnage-combat-item"><span class="label">Classe d'armure :</span> <? echo (int)$dn['pe_ca']; ?></div>
    <div class="personnage-combat-item"><span class="label">Points de vie :</span> <? echo (int)$dn['pe_pv']; ?></div>
  </div>
</div>

<div class="personnage-section">
  <div class="titre personnage-section-toggle">
    <div class="gras mr10 lien" onClick="togglePlus('personnage-competences')">
      <span class="personnage-section-title">
        <i class="fa-solid fa-book-open personnage-section-icon"></i>
        <span>Competences</span>
      </span>
      <span id="toggle-personnage-competences"><i class="fa-solid fa-bars"></i></span>
    </div>
  </div>
  <div id="personnage-competences" class="accordion-content noDisplay box-data personnage-accordion-box">
    <div>Section disponible prochainement pour ce ruleset.</div>
  </div>
</div>

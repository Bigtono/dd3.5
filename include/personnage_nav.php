<?
if (!isset($personnagePageKey)) {
  $personnagePageKey = 'fiche';
}
?>
<div id="personnage">
  <div class="menu_main contenu personnage-nav">
    <a class="btMain<?= $personnagePageKey === 'fiche' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['fiche']); ?>">
      <span class="logo_menu"><i class="fa-solid fa-person"></i></span>
      <span class="titre_menu">Fiche</span>
    </a>
    <a class="btMain<?= $personnagePageKey === 'background' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['background']); ?>">
      <span class="logo_menu"><i class="fa-solid fa-feather"></i></span>
      <span class="titre_menu">Background</span>
    </a>
    <a class="btMain<?= $personnagePageKey === 'possessions' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['possessions']); ?>">
      <span class="logo_menu"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
      <span class="titre_menu">Objets magiques</span>
    </a>
    <a class="btMain<?= $personnagePageKey === 'magie' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['magie']); ?>">
      <span class="logo_menu"><i class="fa-solid fa-hat-wizard"></i></span>
      <span class="titre_menu">Magie</span>
    </a>
    <a class="btMain<?= $personnagePageKey === 'connaissances' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['connaissances']); ?>">
      <span class="logo_menu"><i class="fa-solid fa-note-sticky"></i></span>
      <span class="titre_menu">Connaissances</span>
    </a>
    <? if ($personnageCampagneId > 0 && $canViewNotesMj): ?>
      <a class="btMain<?= $personnagePageKey === 'notes-mj' ? ' is-active' : ''; ?>" href="<?= htmlspecialchars($personnageUrls['notes-mj']); ?>">
        <span class="logo_menu"><i class="fa-solid fa-user-shield"></i></span>
        <span class="titre_menu">Notes MJ</span>
      </a>
    <? else: ?>
      <span class="btMain is-disabled" aria-disabled="true" title="Accessible uniquement au proprietaire de la campagne">
        <span class="logo_menu"><i class="fa-solid fa-user-shield"></i></span>
        <span class="titre_menu">Notes MJ</span>
      </span>
    <? endif; ?>
  </div>
</div>

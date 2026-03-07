<?
$crumbs = [];

$addCrumb = function ($label, $href = null) use (&$crumbs) {
  $crumbs[] = [
    'label' => (string)$label,
    'href' => $href ? (string)$href : null,
    'current' => false
  ];
};

$addCrumb('Accueil', 'index.php');

$campagneId = !empty($_GET['campagne']) ? (int)$_GET['campagne'] : 0;
switch ($_SERVER['PHP_SELF']):
  case ("/dd3.5/campagnes.php"):
    $addCrumb('campagnes', 'campagnes.php');
    break;
  case ("/dd3.5/campagne.php"):
  case ("/dd3.5/campagne-modifier.php"):
    $addCrumb('campagnes', 'campagnes.php');
    if (!empty($campagne['camp_id'])):
      $addCrumb($campagne['camp_nom'], 'campagne.php?campagne=' . (int)$campagne['camp_id']);
    endif;
    break;
  case ("/dd3.5/scenario.php"):
  case ("/dd3.5/scenario-modifier.php"):
    $addCrumb('campagnes', 'campagnes.php');
    if (!empty($scenario['camp_id'])):
      $addCrumb($scenario['camp_nom'], 'campagne.php?campagne=' . (int)$scenario['camp_id']);
    endif;
    if (!empty($scenario['sc_id'])):
      $addCrumb($scenario['sc_nom'], 'scenario.php?scenario=' . (int)$scenario['sc_id']);
    endif;
    break;
  case ("/dd3.5/chapitre.php"):
  case ("/dd3.5/chapitre-modifier.php"):
    $addCrumb('campagnes', 'campagnes.php');
    if (!empty($chapitre['camp_id'])):
      $addCrumb($chapitre['camp_nom'], 'campagne.php?campagne=' . (int)$chapitre['camp_id']);
    endif;
    if (!empty($chapitre['sc_id'])):
      $addCrumb($chapitre['sc_nom'], 'scenario.php?scenario=' . (int)$chapitre['sc_id']);
    endif;
    if (!empty($chapitre['scc_id'])):
      $addCrumb($chapitre['scc_nom'], 'chapitre.php?chapitre=' . (int)$chapitre['scc_id']);
    endif;
    break;
  case ("/dd3.5/rencontre.php"):
  case ("/dd3.5/rencontre-modifier.php"):
    $addCrumb('campagnes', 'campagnes.php');
    if (!empty($rencontre['camp_id'])):
      $addCrumb($rencontre['camp_nom'], 'campagne.php?campagne=' . (int)$rencontre['camp_id']);
    endif;
    if (!empty($rencontre['sc_id'])):
      $addCrumb($rencontre['sc_nom'], 'scenario.php?scenario=' . (int)$rencontre['sc_id']);
    endif;
    if (!empty($rencontre['scc_id'])):
      $addCrumb($rencontre['scc_nom'], 'chapitre.php?chapitre=' . (int)$rencontre['scc_id']);
    endif;
    if (!empty($re)):
      $addCrumb($rencontre['re_nom'], 'rencontre.php?rencontre=' . (int)$re);
    endif;
    break;
  case ("/dd3.5/personnages.php"):
    $addCrumb('personnages', 'personnages.php');
    break;
  case ("/dd3.5/personnage.php"):
  case ("/dd3.5/personnage-modifier.php"):
    if ($campagneId > 0):
      $addCrumb('campagnes', 'campagnes.php');
      $addCrumb(libelle('dd_campagnes', 'camp', 'nom', $campagneId), 'campagne.php?campagne=' . $campagneId);
    else:
      $addCrumb('personnages', 'personnages.php');
    endif;
    if (!empty($p)):
      $addCrumb($dn['pe_nom'], 'personnage.php?personnage=' . (int)$p . '&campagne=' . $campagneId);
    endif;
    break;
  case ("/dd3.5/grimoire.php"):
  case ("/dd3.5/grimoire_gestion.php"):
    if ($campagneId > 0):
      $addCrumb('campagnes', 'campagnes.php');
      $addCrumb(libelle('dd_campagnes', 'camp', 'nom', $campagneId), 'campagne.php?campagne=' . $campagneId);
    else:
      $addCrumb('personnages', 'personnages.php');
    endif;
    if (!empty($perso_id)):
      $addCrumb(libelle('dd_personnages', 'pe', 'nom', $perso_id), 'personnage.php?personnage=' . (int)$perso_id . '&campagne=' . $campagneId);
      $addCrumb('grimoire', 'grimoire.php?personnage=' . (int)$perso_id . '&campagne=' . $campagneId);
    endif;
    break;
  case ("/dd3.5/regles.php"):
    $addCrumb('regles', 'regles.php');
    break;
  case ("/dd3.5/regle.php"):
    $addCrumb('regles', 'regles.php');
    if (!empty($re)):
      foreach (getBreadcrumb($re) as $ruleCrumb):
        $addCrumb($ruleCrumb['label'], $ruleCrumb['href']);
      endforeach;
    endif;
    break;
  case ("/dd3.5/classes.php"):
    $addCrumb('classes', 'classes.php');
    break;
  case ("/dd3.5/classe.php"):
  case ("/dd3.5/classe-modifier.php"):
    $addCrumb('classes', 'classes.php');
    if (!empty($c)):
      $addCrumb(libelle("dd_classes", "cla", "nom", $c), 'classe.php?classe=' . (int)$c);
    endif;
    break;
  case ("/dd3.5/races.php"):
    $addCrumb('races', 'races.php');
    break;
  case ("/dd3.5/race.php"):
  case ("/dd3.5/race-modifier.php"):
    if (isset($r) && $r === "n"):
      $libelle = "Ajouter une race";
    else:
      $libelle = libelle("dd_races", "ra", "nom", $r);
    endif;
    $addCrumb('races', 'races.php');
    $addCrumb($libelle, 'race.php?race=' . urlencode((string)$r));
    break;
  case ("/dd3.5/dons.php"):
    $addCrumb('dons', 'dons.php');
    break;
  case ("/dd3.5/joueurs.php"):
    $addCrumb('joueurs', 'joueurs.php');
    break;
  case ("/dd3.5/joueur.php"):
  case ("/dd3.5/joueur-modifier.php"):
    $addCrumb('joueurs', 'joueurs.php');
    if (!empty($j)):
      $addCrumb(libelle("dd_joueurs", "j", "prenom", $j) . ' ' . libelle("joueurs", "j", "nom", $j), 'joueur.php?joueur=' . (int)$j);
    endif;
    break;
  case ("/dd3.5/notes.php"):
    if ($campagneId > 0):
      $addCrumb('campagnes', 'campagnes.php');
      $addCrumb(libelle("dd_campagnes", "camp", "nom", $campagneId), 'campagne.php?campagne=' . $campagneId);
    endif;
    $addCrumb('notes de campagne', 'notes.php' . ($campagneId > 0 ? '?campagne=' . $campagneId : ''));
    break;
  case ("/dd3.5/sorts.php"):
    $addCrumb('sorts', 'sorts.php');
    break;
  case ("/dd3.5/competences.php"):
    $addCrumb('competences', 'competences.php');
    break;
  case ("/dd3.5/recherche.php"):
    $addCrumb('recherche', 'recherche.php' . (!empty($critere_recherche) ? '?critere_recherche=' . urlencode((string)$critere_recherche) : ''));
    break;
  case ("/dd3.5/grimoires.php"):
    $addCrumb('grimoires', 'grimoires.php');
    break;
  case ("/dd3.5/monstres.php"):
    $addCrumb('monstres', 'monstres.php');
    break;
  case ("/dd3.5/insertion-monstres.php"):
    $addCrumb('Insertion de monstres', 'insertion-monstres.php');
    break;
  case ("/dd3.5/monstre.php"):
  case ("/dd3.5/monstre-modifier.php"):
    if (!empty($re) && $re > 0):
      $addCrumb('campagnes', 'campagnes.php');
      if (!empty($rencontre['camp_id'])):
        $addCrumb($rencontre['camp_nom'], 'campagne.php?campagne=' . (int)$rencontre['camp_id']);
      endif;
      if (!empty($rencontre['sc_id'])):
        $addCrumb($rencontre['sc_nom'], 'scenario.php?scenario=' . (int)$rencontre['sc_id']);
      endif;
      if (!empty($rencontre['scc_id'])):
        $addCrumb($rencontre['scc_nom'], 'chapitre.php?chapitre=' . (int)$rencontre['scc_id']);
      endif;
      $addCrumb($rencontre['re_nom'], 'rencontre.php?rencontre=' . (int)$re);
    else:
      $addCrumb('monstres', 'monstres.php');
    endif;
    if (!empty($mo) && $mo > 0):
      $addCrumb($monstre['mo_nom'], 'monstre.php?mo=' . (int)$mo);
    else:
      $addCrumb('Ajouter un monstre');
    endif;
    break;
  case ("/dd3.5/profil.php"):
  case ("/dd3.5/profil-modifier.php"):
    $addCrumb('Mon profil', 'profil.php');
    break;
  case ("/dd3.5/variables.php"):
    $addCrumb('Variables', 'variables.php');
    break;
  default:
endswitch;

$lastIndex = count($crumbs) - 1;
if ($lastIndex >= 0):
  $crumbs[$lastIndex]['current'] = true;
  if (count($crumbs) === 1):
    $crumbs[$lastIndex]['href'] = null;
  endif;
endif;
?>
<nav id="ariane" aria-label="Fil d'Ariane">
  <?
  $currentRequestUri = !empty($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : basename($_SERVER['PHP_SELF']);
  ?>
  <select id="ariane-mobile-select" class="search-select" aria-label="Navigation rapide du fil d'Ariane">
    <? foreach ($crumbs as $crumb): ?>
      <?
      $optionValue = !empty($crumb['href']) ? (string)$crumb['href'] : $currentRequestUri;
      ?>
      <option value="<?= htmlspecialchars($optionValue, ENT_QUOTES, 'UTF-8'); ?>"<?= $crumb['current'] ? ' selected' : ''; ?>>
        <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
      </option>
    <? endforeach; ?>
  </select>
  <ol id="ariane-list">
    <? foreach ($crumbs as $index => $crumb): ?>
      <? if ($index === 1): ?>
        <li class="ariane-item ariane-ellipsis" aria-hidden="true">...</li>
      <? endif; ?>
      <li class="ariane-item<?= $crumb['current'] ? ' is-current' : ''; ?>" <?= $crumb['current'] ? ' aria-current="page"' : ''; ?>>
        <? if (!$crumb['current'] && !empty($crumb['href'])): ?>
          <a href="<?= htmlspecialchars($crumb['href'], ENT_QUOTES, 'UTF-8') ?>">
            <? if ($index === 0): ?>
              <i class="icon fa fa-home" aria-hidden="true"></i>
              <span class="sr-only">Accueil</span>
            <? else: ?>
              <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
            <? endif; ?>
          </a>
        <? else: ?>
          <? if ($index === 0): ?>
            <i class="icon fa fa-home" aria-hidden="true"></i>
            <span class="sr-only">Accueil</span>
          <? else: ?>
            <?= htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8') ?>
          <? endif; ?>
        <? endif; ?>
      </li>
    <? endforeach; ?>
  </ol>
</nav>
<script>
  (function() {
    const nav = document.getElementById('ariane');
    if (!nav || nav.dataset.initialized === '1') return;
    nav.dataset.initialized = '1';

    const mobileSelect = document.getElementById('ariane-mobile-select');
    const list = document.getElementById('ariane-list');
    if (!list) return;

    const ellipsis = list.querySelector('.ariane-ellipsis');
    const allItems = Array.from(list.querySelectorAll('.ariane-item:not(.ariane-ellipsis)'));

    function applyDesktopCompact(width) {
      const isCompact = width >= 992 && width < 1200 && allItems.length >= 4;
      nav.classList.toggle('is-desktop-compact', isCompact);

      allItems.forEach((item, idx) => {
        const isIntermediate = idx > 0 && idx < allItems.length - 2;
        item.classList.toggle('is-hidden-compact', isCompact && isIntermediate);
      });

      if (ellipsis) {
        ellipsis.classList.toggle('is-visible', isCompact);
      }
    }

    function syncMode() {
      const width = window.innerWidth;
      applyDesktopCompact(width);
    }

    let resizeTimer = null;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(syncMode, 120);
    });

    if (mobileSelect) {
      mobileSelect.addEventListener('change', function() {
        const target = this.value;
        if (target) window.location.href = target;
      });
    }

    syncMode();
  })();
</script>

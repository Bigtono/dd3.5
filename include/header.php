  <header>
    <div class="dflex clearfix">
      <button type="button" id="toggleMenu" class="toggle_menu">
        <i class="fa fa-bars txt-white"></i>
      </button>
      <h1><a href="http://<? echo $_SESSION['url_site']; ?>" class="lien txt-white"><? echo $_SESSION['titre_site']; ?></a></h1>

      <!-- formulaire de recherche desktop -->
      <form action="recherche.php" method="get" name="recherche" id="recherche">
        <input type="text" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)" />
        <button id="fa-search"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>

      <!-- bouton de recherche mobile -->
      <div id="bouton_recherche">
        <div onClick="toggle('menu_recherche2')"><i class="fa fa-search" aria-hidden="true"></i></div>
      </div>

      <!-- ruleset actif -->
      <div><span id="ruleset" class="ruleset"><? echo libvar($_SESSION['ruleset']); ?></span></div>

      <?
      // memorisation du contexte campagne/scenario/chapitre
      if (isset($_GET['campagne']) && (int)$_GET['campagne'] > 0):
        $_SESSION['campagne'] = (int)$_GET['campagne'];
      endif;
      if (isset($_GET['scenario']) && (int)$_GET['scenario'] > 0):
        $_SESSION['scenario'] = (int)$_GET['scenario'];
      endif;
      if (isset($_GET['chapitre']) && (int)$_GET['chapitre'] > 0):
        $_SESSION['chapitre'] = (int)$_GET['chapitre'];
      endif;

      $campagne_id = isset($_SESSION['campagne']) ? (int)$_SESSION['campagne'] : 0;
      $scenario_id = isset($_SESSION['scenario']) ? (int)$_SESSION['scenario'] : 0;
      $chapitre_id = isset($_SESSION['chapitre']) ? (int)$_SESSION['chapitre'] : 0;

      $header_context_items = [];
      if ($campagne_id > 0):
        $campagne_nom = libelle("dd_campagnes", "camp", "nom", $campagne_id);
        if ($campagne_nom != ''):
          $header_context_items[] = [
            'type' => 'Campagne',
            'label' => $campagne_nom,
            'url' => 'campagne.php?campagne=' . $campagne_id
          ];
        endif;
      endif;
      if ($scenario_id > 0):
        $scenario_nom = libelle("dd_scenarios", "sc", "nom", $scenario_id);
        if ($scenario_nom != ''):
          $header_context_items[] = [
            'type' => 'Scenario',
            'label' => $scenario_nom,
            'url' => 'scenario.php?scenario=' . $scenario_id
          ];
        endif;
      endif;
      if ($chapitre_id > 0):
        $chapitre_nom = libelle("dd_scenarios_chapitres", "scc", "nom", $chapitre_id);
        if ($chapitre_nom != ''):
          $header_context_items[] = [
            'type' => 'Chapitre',
            'label' => $chapitre_nom,
            'url' => 'chapitre.php?chapitre=' . $chapitre_id
          ];
        endif;
      endif;
      ?>

      <div id="header-context">
        <div id="header-context-links">
          <? foreach ($header_context_items as $ctx): ?>
            <div><a class="ruleset lien" href="<?= htmlspecialchars($ctx['url']); ?>"><?= htmlspecialchars($ctx['label']); ?></a></div>
          <? endforeach; ?>
        </div>
        <? if (count($header_context_items) > 0): ?>
          <select id="header-context-select" class="search-select" aria-label="Navigation contextuelle" onChange="if(this.value){window.location.href=this.value;}">
            <? foreach ($header_context_items as $idx => $ctx): ?>
              <option value="<?= htmlspecialchars($ctx['url']); ?>"<?= $idx === count($header_context_items) - 1 ? ' selected' : ''; ?>>
                <?= htmlspecialchars($ctx['type'] . ' : ' . $ctx['label']); ?>
              </option>
            <? endforeach; ?>
          </select>
        <? endif; ?>
      </div>
    </div>

    <!-- formulaire de recherche mobile -->
    <div id="menu_recherche2" class="search-container">
      <form action="recherche.php" method="get" name="recherche2" id="recherche2" class="search-form">
        <input type="text" class="search-input" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)" />
        <button id="fa-search2" class="search-button"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
    </div>
  </header>

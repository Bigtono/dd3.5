  <header>
    <div class="dflex clearfix">
      <button type="button" id="toggleMenu" class="toggle_menu">
        <i class="fa fa-bars txt-white"></i>
      </button>
      <h1><a href="http://<? echo $_SESSION['url_site']; ?>" class="lien txt-white"><? echo $_SESSION['titre_site']; ?></a></h1>
      <form action="recherche.php" method="get" name="recherche" id="recherche">
        <input type="text" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)" />
        <button id="fa-search"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
      <div id="bouton_recherche">
        <div onClick="toggle('menu_recherche2')"><i class="fa fa-search" aria-hidden="true"></i></div>
      </div>
      <div><span class="ruleset"><? echo libvar($_SESSION['ruleset']); ?></span></div>
      <? // gestion des données sélectionnées par l'utilisateur
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

      if ($campagne_id > 0):
        $campagne_nom = libelle("dd_campagnes", "camp", "nom", $campagne_id);
      ?>
        <div><a class="ruleset lien" href="campagne.php?campagne=<?= $campagne_id; ?>"><?= htmlspecialchars($campagne_nom); ?></a></div>
      <? endif; ?>

      <? if ($scenario_id > 0):
        $scenario_nom = libelle("dd_scenarios", "sc", "nom", $scenario_id);
      ?>
        <div><a class="ruleset lien" href="scenario.php?scenario=<?= $scenario_id; ?>"><?= htmlspecialchars($scenario_nom); ?></a></div>
      <? endif; ?>

      <? if ($chapitre_id > 0):
        $chapitre_nom = libelle("dd_scenarios_chapitres", "scc", "nom", $chapitre_id);
      ?>
        <div><a class="ruleset lien" href="chapitre.php?chapitre=<?= $chapitre_id; ?>"><?= htmlspecialchars($chapitre_nom); ?></a></div>
      <? endif; ?>
      <? // Fin de la la gestion
      ?>
    </div>
    <div id="menu_recherche2" class="search-container">
      <form action="recherche.php" method="get" name="recherche2" id="recherche2" class="search-form">
        <input type="text" class="search-input" name="critere_recherche" value="<? echo $critere_recherche; ?>" size="20" onClick="myFocus(this)" />
        <button id="fa-search2" class="search-button"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
    </div>
  </header>
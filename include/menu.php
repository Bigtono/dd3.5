<nav class="vertical_nav">
  <? if ($_SESSION['pseudo'] != ""): ?>
    <ul id="js-menu" class="menu">
      <? if (!isset($_SESSION['mode_campagne']) || (int)$_SESSION['mode_campagne'] === 1): ?>
        <li class="menu--item  menu--item__has_sub_menu">
          <label class="menu--link" title="Item 1">
            <i class="menu--icon  fa fa-fw fa-user"></i>
            <span class="menu--label">Ressources de jeu</span>
          </label>
          <ul class="sub_menu">
            <li class="sub_menu--item">
              <a href="personnages.php" class="sub_menu--link">Personnages</a>
            </li>
            <li class="sub_menu--item">
              <a href="campagnes.php" class="sub_menu--link">Campagnes</a>
            </li>
            <?
            if ($_SESSION['mj'] == 1):
              echo '<li class="sub_menu--item"><a href="grimoires.php" class="sub_menu--link">Grimoires</a></li>';
            endif;
            ?>
          </ul>
        </li>
      <? else: ?>
        <li class="menu--item">
          <a href="personnages.php" class="menu--link" title="Personnages">
            <i class="menu--icon  fa fa-fw fa-user"></i>
            <span class="menu--label">Personnages</span>
          </a>
        </li>
      <? endif; ?>
      <li class="menu--item  menu--item__has_sub_menu">
        <label class="menu--link" title="Univers">
          <i class="menu--icon fa fa-fw fa-globe"></i>
          <span class="menu--label">Univers</span>
        </label>
        <ul class="sub_menu">
          <li class="sub_menu--item">
            <a href="notes.php" class="sub_menu--link">Notes</a>
          </li>
        </ul>
      </li>
      <li class="menu--item">
        <a href="regles.php" class="menu--link" title="R&egrave;gles">
          <i class="menu--icon fa fa-fw fa-bookmark"></i>
          <span class="menu--label">R&egrave;gles</span>
        </a>
      </li>
      <li class="menu--item  menu--item__has_sub_menu">
        <label class="menu--link" title="Regles">
          <i class="menu--icon fa fa-fw fa-book"></i>
          <span class="menu--label">Compendium</span>
        </label>
        <ul class="sub_menu">
          <li class="sub_menu--item">
            <a href="races.php" class="sub_menu--link">Races</a>
          </li>
          <li class="sub_menu--item">
            <a href="classes.php" class="sub_menu--link">Classes</a>
          </li>
          <li class="sub_menu--item">
            <a href="competences.php" class="sub_menu--link">Comp&eacute;tences</a>
          </li>
          <li class="sub_menu--item">
            <a href="dons.php" class="sub_menu--link">Dons</a>
          </li>
          <li class="sub_menu--item">
            <a href="sorts.php" class="sub_menu--link">Sorts</a>
          </li>
          <li class="sub_menu--item">
            <a href="objets-magiques.php" class="sub_menu--link">Objets magiques</a>
          </li>
          <? if ($_SESSION['mj'] == 1): ?>
            <li class="sub_menu--item">
              <a href="monstres.php" class="sub_menu--link">Monstres</a>
            </li>
          <? endif; ?>
        </ul>
      </li>
      <? if ($_SESSION['mj'] == 1): ?>
        <li class="menu--item  menu--item__has_sub_menu">
          <label class="menu--link" title="Divers">
            <i class="menu--icon  fa fa-fw fa-wrench"></i>
            <span class="menu--label">Outils</span>
          </label>
          <ul class="sub_menu">
            <li class="sub_menu--item">
              <a href="selection-grimoires.php" class="sub_menu--link sub_menu--link__active">Sources</a>
            </li>
            <li class="sub_menu--item">
              <a href="joueurs.php" class="sub_menu--link">Joueurs</a>
            </li>
            <li class="sub_menu--item">
              <a href="controle.php" class="sub_menu--link">Contr&ocirc;le</a>
            </li>
            <li class="sub_menu--item">
              <a href="test.php" class="sub_menu--link">Test</a>
            </li>
            <li class="sub_menu--item">
              <a href="session.php" class="sub_menu--link">Session</a>
            </li>
            <li class="sub_menu--item">
              <a href="insertion-monstres.php" class="sub_menu--link">Insertion Monstres</a>
            </li>
            <li class="sub_menu--item">
              <a href="variables.php" class="sub_menu--link">Variables</a>
            </li>
          </ul>
        </li>
      <? endif; ?>
      <li class="menu--item">
        <a href="profil.php" class="menu--link" title="Profil">
          <i class="menu--icon fa-solid fa-fw fa-circle-user"></i>
          <span class="menu--label">Mon profil</span>
        </a>
      </li>
      <li class="menu--item">
        <a href="deconnexion.php" class="menu--link" title="Deconnexion">
          <i class="menu--icon fa fa-fw fa-cog"></i>
          <span class="menu--label">D&eacute;connexion</span>
        </a>
      </li>
      <?
      if ($_SESSION['mj'] == 1):
        echo '<li class="mt20 ml10">';
        if ($_SESSION["debug"] == 0):
          echo '<div>DEBUG : <span id="debug"><i class="fa-solid fa-toggle-off" onClick="onOff(\'debug\')"></i><span></div>';
        else:
          echo '<div>DEBUG : <span id="debug"><i class="fa-solid fa-toggle-on" onClick="onOff(\'debug\')"></i><span></div>';
        endif;
        echo '</li>';
      endif;
      ?>
    </ul>
    <button id="collapse_menu" class="collapse_menu">
      <i class="collapse_menu--icon fa fa-fw"></i>
      <span class="collapse_menu--label">menu</span>
    </button>
  <? endif; ?>


</nav>

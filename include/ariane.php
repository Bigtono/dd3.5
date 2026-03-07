<div id="ariane">
  <a href="index.php" class="home-link"><i class="icon fa fa-home"></i></a>
  <span class="ariane-rest">
    <?
    echo '<span>/</span>';
    switch ($_SERVER['PHP_SELF']):
      case ("/dd3.5/campagnes.php"):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        break;
      case ("/dd3.5/campagne.php"):
      case ("/dd3.5/campagne-modifier.php"):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="campagne.php?campagne=' . $campagne['camp_id'] . '">' . $campagne['camp_nom'] . '</a></span>';
        break;
      case ("/dd3.5/scenario.php"):
      case ("/dd3.5/scenario-modifier.php"):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="campagne.php?campagne=' . $scenario['camp_id'] . '">' . $scenario['camp_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="scenario.php?scenario=' . $scenario['sc_id'] . '">' . $scenario['sc_nom'] . '</a></span>';
        break;
      case ("/dd3.5/chapitre.php"):
      case ("/dd3.5/chapitre-modifier.php"):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="campagne.php?campagne=' . $chapitre['camp_id'] . '">' . $chapitre['camp_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="scenario.php?scenario=' . $chapitre['sc_id'] . '">' . $chapitre['sc_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="chapitre.php?chapitre=' . $chapitre['scc_id'] . '">' . $chapitre['scc_nom'] . '</a></span>';
        break;
      case ("/dd3.5/rencontre.php"):
      case ("/dd3.5/rencontre-modifier.php"):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="campagne.php?campagne=' . $rencontre['camp_id'] . '">' . $rencontre['camp_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="scenario.php?scenario=' . $rencontre['sc_id'] . '">' . $rencontre['sc_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="chapitre.php?chapitre=' . $rencontre['scc_id'] . '">' . $rencontre['scc_nom'] . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="rencontre.php?rencontre=' . $re . '">' .  $rencontre['re_nom'] . '</a></span>';
        break;
      case ("/dd3.5/personnages.php"):
        echo '<span><a href="personnages.php">personnages</a></span>';
        break;
      case ("/dd3.5/personnage.php"):
      case ("/dd3.5/personnage-modifier.php"):
        if ($_GET['campagne'] && $_GET['campagne'] > 0):
          echo '<span><a href="campagnes.php">campagnes</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="campagne.php?campagne=' . $_GET['campagne'] . '">' . libelle('dd_campagnes', 'camp', 'nom', $_GET['campagne']) . '</a></span>';
        else:
          echo '<span><a href="personnages.php">personnages</a></span>';
        endif;
        echo '<span>/</span>';
        echo '<span><a href="personnage.php?personnage=' . $p . '&campagne=' . $_GET['campagne'] . '">' . $dn['pe_nom'] . '</a></span>';
        break;
      case ("/dd3.5/grimoire.php"):
      case ("/dd3.5/grimoire_gestion.php"):
        if ($_GET['campagne'] > 0):
          echo '<span><a href="campagnes.php">campagnes</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="campagne.php?campagne=' . $_GET['campagne'] . '">' . libelle('dd_campagnes', 'camp', 'nom', $_GET['campagne']) . '</a></span>';
        else:
          echo '<span><a href="personnages.php">personnages</a></span>';
        endif;
        echo '<span>/</span>';
        echo '<span><a href="personnage.php?personnage=' . $perso_id . '&campagne=' . $_GET['campagne'] . '">' . libelle('dd_personnages', 'pe', 'nom', $perso_id) . '</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="grimoire.php?personnage=' . $perso_id . '&campagne=' . $_GET['campagne'] . '">grimoire</a></span>';
        break;
      case ("/dd3.5/regles.php"):
        echo '<span><a href="regles.php">règles</a></span>';
        break;
      case ("/dd3.5/regle.php"):
        echo '<span><a href="regles.php">règles</a></span>';
        echo '<span>/</span>';
        if ($re):
          echo getBreadcrumb($re);
        endif;
        break;
      case ("/dd3.5/classes.php"):
        echo '<span><a href="classes.php">classes</a></span>';
        break;
      case ("/dd3.5/classe.php"):
      case ("/dd3.5/classe-modifier.php"):
        echo '<span><a href="classes.php">classes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="classe.php?classe=' . $c . '">' . libelle("dd_classes", "cla", "nom", $c) . '</a></span>';
        break;
      case ("/dd3.5/races.php"):
        echo '<span><a href="races.php">races</a></span>';
        break;
      case ("/dd3.5/race.php"):
      case ("/dd3.5/race-modifier.php"):
        if ($r === "n"):
          $libelle = "Ajouter une race";
          $titre = "Ajouter une race";
        else:
          $libelle = libelle("dd_races", "ra", "nom", $r);
          $titre = "Modifier la race";
        endif;
        echo '<span><a href="races.php">races</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="race.php?race=' . $r . '">' . $libelle . '</a></span>';
        break;
      case ("/dd3.5/dons.php"):
        echo '<span><a href="dons.php">dons</a></span>';
        break;
      case ("/dd3.5/joueurs.php"):
        echo '<span><a href="joueurs.php">joueurs</a></span>';
        break;
      case ("/dd3.5/joueur.php"):
      case ("/dd3.5/joueur-modifier.php"):
        echo '<span><a href="joueurs.php">joueurs</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="joueur.php?joueur=' . $j . '">' . libelle("dd_joueurs", "j", "prenom", $j) . ' ' . libelle("joueurs", "j", "nom", $j) . '</a></span>';
        break;
      case ("/dd3.5/notes.php"):
        if ($_GET['campagne'] > 0):
          echo '<span><a href="campagnes.php">campagnes</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="campagne.php?campagne=' . $_GET['campagne'] . '">' . libelle("dd_campagnes", "camp", "nom", $_GET['campagne']) . '</a></span>';
          echo '<span>/</span>';
        endif;
        echo '<span><a href="notes.php">notes de campagne</a></span>';
        break;
      case ("/dd3.5/sorts.php"):
        echo '<span><a href="sorts.php">sorts</a></span>';
        break;
      case ("/dd3.5/competences.php"):
        echo '<span><a href="competences.php">Compétences</a></span>';
        break;
      case ("/dd3.5/race.php"):
        echo '<span><a href="races.php">races</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="race.php?race=' . $r . '">' . libelle("dd_races", "ra", "nom", $r) . '</a></span>';
        break;
      case ("/dd3.5/recherche.php"):
        echo '<span><a href="recherche.php?critere_recherche=' . $critere_recherche . '">recherche</a></span>';
        break;
      case ("/dd3.5/grimoires.php"):
        echo '<span><a href="grimoires.php">Grimoires</a></span>';
        break;

      case ("/dd3.5/monstres.php"):
        echo '<span><a href="monstres.php">monstres</a></span>';
        break;

      case ("/dd3.5/insertion-monstres.php"):
        echo '<span><a href="insertion-monstres.php">Insertion de monstres</a></span>';
        break;

      case ("/dd3.5/monstre.php"):
      case ("/dd3.5/monstre-modifier.php"):
        if ($mo > 0):
          $libelle = $monstre['mo_nom'];
          $titre = "Modifier le monstre";
        else:
          $libelle = "Ajouter un monstre";
          $titre = "Ajouter un monstre";
        endif;
        if ($re > 0):
          echo '<span><a href="campagnes.php">campagnes</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="campagne.php?campagne=' . $rencontre['camp_id'] . '">' . $rencontre['camp_nom'] . '</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="scenario.php?scenario=' . $rencontre['sc_id'] . '">' . $rencontre['sc_nom'] . '</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="chapitre.php?chapitre=' . $rencontre['scc_id'] . '">' . $rencontre['scc_nom'] . '</a></span>';
          echo '<span>/</span>';
          echo '<span><a href="rencontre.php?rencontre=' . $re . '">' .  $rencontre['re_nom'] . '</a></span>';
        else:
          echo '<span><a href="monstres.php">monstres</a></span>';
        endif;
        echo '<span>/</span>';
        echo '<span><a href="monstre.php?mo="' . $mo . '">' . $libelle . '</a></span>';
        break;

      case ("/dd3.5/profil.php"):
      case ("/dd3.5/profil-modifier.php"):
        echo '<span><a href="profil.php">Mon profil</a></span>';
        break;
      case ("/dd3.5/variables.php"):
        echo '<span><a href="variables.php">Variables</a></span>';
        break;
      default;
    endswitch;
    ?>
  </span>
</div>
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
      echo '<span><a href="campagne.php?campagne='.$campagne['camp_id'].'">'.$campagne['camp_nom'].'</a></span>';
      break;    
    case ("/dd3.5/personnages.php"):
      echo '<span><a href="personnages.php">personnages</a></span>';
      break;
    case ("/dd3.5/personnage.php"):
    case ("/dd3.5/personnage-modifier.php"):
      if ($_GET['campagne'] && $_GET['campagne']>0):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';    
        echo '<span><a href="campagne.php?campagne='.$_GET['campagne'].'">'.libelle('dd_campagnes','camp','nom',$_GET['campagne']).'</a></span>';
        else:
        echo '<span><a href="personnages.php">personnages</a></span>';
      endif;
      echo '<span>/</span>';
      echo '<span><a href="personnage.php?personnage='.$p.'&campagne='.$_GET['campagne'].'">'.$dn['pe_nom'].'</a></span>';
      break;
    case ("/dd3.5/grimoire.php"):
    case ("/dd3.5/grimoire_gestion.php"):
      if ($_GET['campagne']>0):
        echo '<span><a href="campagnes.php">campagnes</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="campagne.php?campagne='.$_GET['campagne'].'">'.libelle('dd_campagnes','camp','nom',$_GET['campagne']).'</a></span>';
        else:
        echo '<span><a href="personnages.php">personnages</a></span>';    
      endif;
      echo '<span>/</span>';    
      echo '<span><a href="personnage.php?personnage='.$perso_id.'&campagne='.$_GET['campagne'].'">'.libelle('dd_personnages','pe','nom',$perso_id).'</a></span>';
      echo '<span>/</span>';
      echo '<span><a href="grimoire.php?personnage='.$perso_id.'&campagne='.$_GET['campagne'].'">grimoire</a></span>';        
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
      echo '<span><a href="classe.php?classe='.$c.'">'.libelle("dd_classes", "cla", "nom", $c).'</a></span>';
      break;
    case ("/dd3.5/races.php"):
      echo '<span><a href="races.php">races</a></span>';
      break;
    case ("/dd3.5/race.php"):
    case ("/dd3.5/race-modifier.php"):
      if ($r==="n"):
        $libelle="Ajouter une race";
        $titre="Ajouter une race";        
        else:
        $libelle=libelle("dd_races","ra","nom",$r);
        $titre="Modifier la race";
      endif;    
      echo '<span><a href="races.php">races</a></span>';
      echo '<span>/</span>';
      echo '<span><a href="race.php?race='.$r.'">'.$libelle.'</a></span>';
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
      echo '<span><a href="joueur.php?joueur='.$j.'">'.libelle("joueurs", "j", "prenom", $j).' '.libelle("joueurs", "j", "nom", $j).'</a></span>';
      break;
    case ("/dd3.5/notes.php"):
      echo '<span><a href="notes.php">notes</a></span>';
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
      echo '<span><a href="race.php?race='.$r.'">'.libelle("dd_races", "ra", "nom", $r).'</a></span>';
      break;
    case ("/dd3.5/recherche.php"):
      echo '<span><a href="recherche.php?critere_recherche='.$critere_recherche.'">recherche</a></span>';
      break;
    case ("/dd3.5/rencontres.php"):
      echo '<span><a href="rencontres.php">rencontres</a></span>';
      break;
    case ("/dd3.5/grimoires.php"):
      echo '<span><a href="grimoires.php">Grimoires</a></span>';
      break;    
    case ("/dd3.5/rencontre.php"):
    case ("/dd3.5/rencontre-modifier.php"):
      echo '<span><a href="rencontres.php">rencontres</a></span>';
      echo '<span>/</span>';
      $abrev=libelle("dd_rencontres","re","abreviation",$re);
      if ($abrev!="") $abrev.=$abrev." : ";
      echo '<span><a href="rencontre.php?re='.$re.'">'.$abrev.libelle("dd_rencontres","re","nom",$re).'</a></span>';
      break;
    case ("/dd3.5/monstres.php"):
      echo '<span><a href="monstres.php">monstres</a></span>';
      break;
    case ("/dd3.5/insertion-monstres.php"):
      echo '<span><a href="insertion-monstres.php">Insertion de monstres</a></span>';
      break;
    case ("/dd3.5/monstre.php"):
    case ("/dd3.5/monstre-modifier.php"):
      if ($mo==="n"):
        $libelle="Ajouter un monstre";
        $titre="Ajouter un monstre";
        else:
        $libelle=libelle("dd_monstres","mo","nom",$mo);
        $titre="Modifier le monstre";
      endif;
      if ($re==0):
        echo '<span><a href="monstres.php">monstres</a></span>';
        else:
        echo '<span><a href="rencontres.php">rencontres</a></span>';
        echo '<span>/</span>';
        echo '<span><a href="rencontre.php?re='.$re.'">'.libelle("dd_rencontres","re","abreviation",$re).' : '.libelle("dd_rencontres","re","nom",$re).'</a></span>';  
      endif;
      echo '<span>/</span>';
      echo '<span><a href="monstre.php?mo="'.$mo.'">'.$libelle.'</a></span>';
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
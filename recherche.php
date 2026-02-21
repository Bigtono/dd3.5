<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère
if (strlen($_GET['critere_recherche'])>0):
	$critere=$_GET['critere_recherche'];
	else:
  $critere=''; 
endif;
?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-regles.js'></script>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>  
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">Recherche : <? echo $critere; ?></div>
        <div></div>
      </div>
      <?
      if ($critere!=''):
        $requete='
(SELECT re_id as id, re_nom as nom, "regles" as source, "regle.php?regle" as lapage FROM `dd_regles` WHERE re_nom LIKE "%'.$critere.'%" OR re_texte LIKE "%'.$critere.'%")
UNION
(SELECT om_id as id, om_nom as nom, "objets magiques" as source, "objet_magique.php?om" as lapage FROM `dd_objets_magiques` WHERE om_nom LIKE "%'.$critere.'%" OR `om_description` LIKE "%'.$critere.'%")
UNION
(SELECT comp_id as id, comp_nom as nom, "competences" as source, "competence.php?competence" as matable FROM `dd_competences` WHERE comp_nom LIKE "%'.$critere.'%" OR `comp_description` LIKE "%'.$critere.'%" OR `comp_test` LIKE "%'.$critere.'%" OR `comp_action` LIKE "%'.$critere.'%" OR `comp_nouvelleTentative` LIKE "%'.$critere.'%" OR `comp_special` LIKE "%'.$critere.'%")
UNION
(SELECT so_id as id, so_nom as nom,"sorts" as source, "sort.php?sort" as matable FROM `dd_sorts` WHERE so_res_id IN '.$selection.' AND (so_nom LIKE "%'.$critere.'%" OR `so_texte` LIKE "%'.$critere.'%"))
UNION
(SELECT do_id as id, do_nom as nom,"dons" as source, "don.php?don" as matable FROM `dd_dons` WHERE do_res_id IN '.$selection.' AND (do_nom LIKE "%'.$critere.'%" OR `do_texte` LIKE "%'.$critere.'%" OR `do_resume` LIKE "%'.$critere.'%"))
UNION
(SELECT cla_id as id, cla_nom as nom, "classes" as source, "classe.php?classe" as matable FROM `dd_classe_competence` JOIN dd_classes ON cc_cla_id=cla_id JOIN dd_competences ON cc_comp_id=comp_id WHERE cla_nom LIKE "%'.$critere.'%" OR `cla_description` LIKE "%'.$critere.'%" OR comp_nom LIKE "%'.$critere.'%")
UNION
(SELECT ra_id as id, ra_nom as nom, "races" as source, "race.php?race" as matable FROM `dd_races` WHERE ra_nom LIKE "%'.$critere.'%" OR `ra_description` LIKE "%'.$critere.'%")
UNION
(SELECT no_id as id, no_nom as nom, "notes" as source, "note.php?note" as lapage FROM `dd_notes` WHERE no_nom LIKE "%'.$critere.'%" OR no_texte_basique LIKE "%'.$critere.'%" OR no_texte_intermediaire LIKE "%'.$critere.'%" OR no_texte_avance LIKE "%'.$critere.'%" OR no_texte_expert LIKE "%'.$critere.'%")
ORDER BY nom';
        $result=queryPDO($requete);
        $num_rows=$result->rowCount();
        if ($num_rows > 0):
          echo '<div class="item entete">';
          echo '  <div class="nom_recherche">Nom</div>';
          echo '  <div class="categorie_recherche">Nom</div>';
          echo '</div><!-- item entête --->';
          while($dn = $result->fetch(PDO::FETCH_ASSOC)):
            echo '<div class="item data">';
            //echo '<a href="'.$dn['lapage'].'='.$dn['id'].'&retour=recherche&critere='.$critere.'">'.$dn['nom'].'</a>';
            switch ($dn['source']):
              case "objets magiques":
                $click='afficherOM('.$dn['id'].')';
                $categorie="Objet magique";
                break;      
              case "dons":
                $click='afficherDon('.$dn['id'].')';
                $categorie="Don";
                break;
              case "competences":
                $click='afficherCompetence('.$dn['id'].')';
                $categorie="Comp&eacute;tence";
                break;    
              case "sorts":
                $click='afficherSort('.$dn['id'].')';
                $categorie="Sort";      
                break;
              case "regles":
                $click='afficherRegle('.$dn['id'].')';
                $categorie="R&egrave;gle";  
                break;    
              case "classes":
                $click='afficherClasse('.$dn['id'].')';
                $categorie="Classe";
                break;      
              case "races":
                $click='afficherRace('.$dn['id'].')';
                $categorie="Race";
                break;
              case "notes":
                $click='afficherNote('.$dn['id'].')';
                $categorie="Note";
                break;      
              default:
            endswitch;
            echo '<div onClick="'.$click.'" class="nom_recherche">'.$dn['nom'].'</div>';
            echo '<div onClick="'.$click.'" class="categorie_recherche">'.$categorie.'</div>';
            echo '</div>';          
          endwhile;
          else:
          echo '<div class="nodata">Aucun r&eacute;sultat disponible !</div>';
        endif; 
        else:
        echo '<div class="nodata">Aucun crit&egrave;re de recherche !</div>';
      endif;
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>      
    </div><!-- wrapper -->
  </div> <!-- page --->
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
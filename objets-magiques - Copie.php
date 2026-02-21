<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


// réception du critère (nom du sort)
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' AND om_nom LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	else:
	$critere=''; 
	$critere_sql='';
endif;
if(isset($_GET["type"])):
  $filtre=' om_com_id='.$_GET["type"].' AND';
  $filtre_url="?type=".$_GET["type"];
  else:
  $filtre='';
  $filtre_url='';
endif;
if (isset($_SESSION['page_om'])):
  if ($_SESSION['page_om']>0):
    $nbp=$_SESSION['page_om'];
    else:
    $nbp=9;
  endif;
  else:
  $nbp=11;
endif;
if (isset($_GET['page'])):
  if ($_GET['page']>0):
    $page=$_GET['page'];
    else:
    $page=1;
  endif;
  else:
  $page=1;
endif;

?>
<!doctype html>
<html>
<head>
<? include("include/header.php"); ?>
<script type='text/javascript' src='js/moncode-sort.js'></script>
<script type='text/javascript' src='js/moncode-om.js'></script>
<script type="text/javascript">
	var nombreOnglets = 26;
  function changeOnglet(numero)
  	{
    	// On commence par tout masquer
      for (var i = 1; i < nombreOnglets+1; i++) {
      	document.getElementById("contenuOnglet" + i).style.display = "none";
				document.getElementById(i).style.color = "black";
			}
        // Puis on affiche celui qui a été sélectionné
        document.getElementById("contenuOnglet" + numero).style.display = "block";
				document.getElementById(numero).style.color = "red";
    }
</script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<DIV id="page">
	<? include("include/head.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <H1>Objets magiques <? if ($_SESSION['mj']==1) echo '<i class="icon lien fa fa-plus-square-o" onClick="modifierOM(\'n\')"></i>'; ?></H1>
      <!--- Menu secondaire --->
      <div class="ligne">
        <form action="objets-magiques.php" method="get" name="search-om" id="search-om" class="mr30">
          <input type="text" name="critere" value="<? echo $critere; ?>" size="20" placeholder="Nom de l'objet magique" onClick="myFocus(this)"/>
          <input type="submit" id="search" name="search" value="Rechercher" class="form_bouton"/>
        </form>
      </div>
      <div class="filtre">
        <a href="<? echo $_SERVER['PHP_SELF']; ?>" class="bouton2">Tous</a>
        <?
        $requete="SELECT * FROM dd_categorie_objet_magique ORDER BY com_nom";
        $resultat_cat=queryPDO($requete);
        $num_rows_cat=$resultat_cat->rowCount();
        if ($num_rows_cat>0):
          while($dnc=$resultat_cat->fetch(PDO::FETCH_ASSOC)):
            echo '<a href="'.$_SERVER['PHP_SELF'].'?type='.$dnc['com_id'].'" class="bouton2 ml5">'.$dnc['com_nom'].'</a>';
          endwhile;
        endif;
        ?>
      </div>
      <?
      // gestion de la pagination
      if ($critere_sql==''): // liste de objets magiques globale ou par catégorie
        $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE '.$filtre.' om_res_id IN '.$selection.' ORDER BY om_nom'.$limit;
        else: // recherche d'un don par son nom
        $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_res_id IN '.$selection.$critere_sql.' ORDER BY om_nom'.$limit;
      endif;
      $result=queryPDO($requete);
      $nb=$result->rowCount();    
      $pm=(int)($nb/$nbp); // nb pages maximum
      $limit='';
      $pagination='';
      $inferieur='';  
      $superieur='';
      if ($nb<=$nbp): // moins d'enregistrements dans la base que d'enregistrements à afficher dans une page
        $pagination='';
        else: // plus d'enregistrements dans la base que d'enregistrements à afficher dans une page
        if ($page==1):
          $debug='1 - nb om : '.$nb.', nb par page : '.$nbp.', nb page : '.$pm.', page : '.$page;
          if ($filtre_url!=""):
            $superieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page+1).'">page suivante ></a>';
            else:
            $superieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page+1).'">page suivante ></a>';
          endif;
          $limit=' LIMIT '.$nbp; 
          else:
          if ($page>$pm) $page=$pm; // la page demandée excède la dernière page
          if ($pm>$page):
            if ($filtre_url!=""):
              $superieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page+1).'">page suivante ></a>';
              else:
              $superieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page+1).'">page suivante ></a>';
            endif;
          endif;
          if ($filtre_url!=""):
            $inferieur='<a href="'.$_SERVER['PHP_SELF'].$filtre_url."&page=".($page-1).'">< page pr&eacute;c&eacute;dente</a>';
            else:
            $inferieur='<a href="'.$_SERVER['PHP_SELF']."?page=".($page-1).'">< page pr&eacute;c&eacute;dente</a>';
          endif;
          
          $limit=' LIMIT '.$nbp.' OFFSET '.$nbp*($page-1); 
          $debug='1 - nb om : '.$nb.', nb par page : '.$nbp.', nb page : '.$pm.', page : '.$page;
        endif;
      endif;
      $pagination='<div class="dflex mt10 mb20"><div class="gauche agauche">'.$inferieur.'</div><div class="droite adroite">'.$superieur.'</div></div>';
    
      if ($critere_sql==''): // liste de objets magiques globale ou par catégorie
        $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE '.$filtre.' om_res_id IN '.$selection.' ORDER BY om_nom'.$limit;
        else: // recherche d'un don par son nom
        $requete='SELECT * FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_res_id IN '.$selection.$critere_sql.' ORDER BY om_nom'.$limit;
      endif;
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();

      if ($num_rows > 0):    
        echo $pagination;
        if ($_SESSION['debug']==1) echo '<div class="mt10 mb20">'.$requete.'</div>';
        echo '<div class="don entete">';
        if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><img src="images/suppression.png" class="icone16" title="Supprimer"></div>';
        if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><img src="images/modif.png" class="icone16" title="modifier"></div>';
        echo '  <div class="nom_om">Nom</div>';
        echo '  <div class="categorie_om">Type</div>';
        echo '  <div class="source">Source</div>';
        echo '</div><!-- entête --->';
        echo '<div class="liste-dons">';
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          $vide=' <i class="fa fa-fw fa-star-half"></i>'; if (strlen($dn['om_description'])>0) $vide="";
          if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idom=' ('.$dn['om_id'].')';
          echo '<div id ="om'.$dn['om_id'].'" class="don">';
          if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_objets_magiques\',\'om\','.$dn['om_id'].')"><img src="images/suppression.png" class="icone16"></span></div>';
          if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierOM('.$dn['om_id'].')"><img src="images/modif.png" class="icone16"></span></div>';    
          echo '  <div id="nomOM'.$dn['om_id'].'" class="nom_om" onclick="afficherOM('.$dn['om_id'].')">'.stripslashes(ucfirst($dn['om_nom'])).$vide.$idom.'</div>';
          echo '  <div id="catOM'.$dn['om_id'].'" class="categorie_om" onclick="afficherOM('.$dn['om_id'].')">'.libelle("dd_categorie_objet_magique","com","nom",$dn['om_com_id']).'</div>';
          echo '  <div id="sourceOM'.$dn['om_id'].'" class="source" title="'.$dn['res_nom'].'" onclick="afficherOM('.$dn['om_id'].')">'.stripslashes($dn['res_abreviation']).'</div>';
          echo '</div>';
        endwhile;
        echo '</div>'; // liste-dons
        else:
        if(isset($_GET["type"])):
          echo '<div class="nodata">Aucun objet magique disponible dans la cat&eacute;gorie '.libelle("dd_categorie_objet_magique","com","nom",$_GET["type"]).' !</div>';
          else:
          echo '<div class="nodata">Aucun objet magique disponible !</div>';
        endif;
      endif;
      ?>
    
  </div> <!-- wrapper --->
</div>
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// réception du critère 
if (strlen($_GET['critere'])>0):
	$critere=$_GET['critere'];
	$critere_sql=' var_valeur LIKE "%'.$_GET['critere'].'%"';
	$cas=1;
	elseif(strlen($_GET['critere_var'])>0):
    $critere_var=$_GET['critere_var'];
    $critere_sql=' var_cat="'.$_GET['critere_var'].'"';
    $cas=2;
    else:
    $critere='';
    $critere_var='';
    $critere_sql='';
    $cas=3;
endif;

?>
<!doctype html>
<HEAD>
  <? include("include/head.php"); ?>
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <H1>Variables <? if ($_SESSION['mj']>0) echo '<span class="lien"><i class="icon fa-solid fa-pen-to-square" onClick="modifierVariable(\'n\',\''.$critere_var.'\')"></i></span>'; ?></H1>
    <!--- Menu secondaire --->
    <div class="ligne mb5"><? echo descVariable("aide_recherche_variables"); ?></div>
    <div class="ligne">
      <form action="variables.php" method="get" name="search-var" id="search-var" class="mr30 mb10">
        <input type="text" class="filtre_sort" name="critere" value="<? echo $critere; ?>" placeholder="Nom de la variable" onClick="myFocus(this)"/>
        <input type="submit" id="search" name="search" value="Rechercher" class="form_bouton"/>
      </form>
      <form action="variables.php" method="get" class="mr30">
        <select name="critere_var">
          <option value="" disabled selected>Cat&eacute;gorie de variable</option>--->
          <?
          $requete='SELECT * FROM dd_variables_categories ORDER BY varcat_nom';
          $result_cat=queryPDO($requete);
          $num_rows_cat=$result_cat->rowCount();
          $liste='<option value="">Toutes</option>';
          if ($num_rows_cat > 0):
            while($cat = $result_cat->fetch(PDO::FETCH_ASSOC)):
              $liste.='<option value="'.$cat['varcat_abreviation'].'"';
              if ($cat['varcat_abreviation']==$critere_var) $liste.=' selected="SELECTED"';
              $liste.='>'.stripslashes($cat['varcat_nom']).'</option>';	
            endwhile;
          endif;
          echo $liste;
          ?>
        </select>
        <input type="submit" id="search_ls" name="search_ls" value="Filtrer " class="form_bouton"/>
      </form>
    </div>
    <div class="item entete">
      <div class="icone_suppr"><i class="fa fa-trash"></i></div>
      <div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>
      <div class="nom_variable">Valeur</div>
      <div class="categorie_variable">Cat&eacute;gorie</div>
      <div class="variable_variable">Variable</div>
      <div class="description_variable">Description</div>
    </div>
    <div id="variables">
    <?
      include('include/insert/'.$_SESSION['rulesetRep'].'/listeVariables.php');
      echo $liste_var;
    ?>	
    </div>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>        
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>  
<div id="modification"></div>
</body>
</html>
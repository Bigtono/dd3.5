<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_GET['race'])):
  // appel Include
  $r = $_GET['race'];
  else:
  $r="";
endif;
?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
	<?
  if(!empty($r) && $r!="n"): // il s'agit d'une modification
    $requete="SELECT * FROM dd_races WHERE ra_id='".$r."'";
    $result=queryPDO($requete);	
    $num_rows=$result->rowCount();
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $libelle="Modifier";
    else: // il s'agit d'un ajout
    $num_rows=1;
    $a="n"; 
    $libelle="Créer";
  endif;  
	?>  
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA"><? echo $titre; // obtenu via ariane.php ?></div>
      <div></div>
    </div>          
  	<?	
      /* 21	ra_mod_niveau */
      // mise en forme du contenu
      $type_race='<select id="mp_ra_rat_id" name="mp_ra_rat_id">'.optionList("dd_race_type", "rat","nom", $dn['ra_rat_id']).'</select>';
		?>
    <div id="formulaire">
      <form action="race-enregistrement.php?race=<? echo $r; ?>&tri=<? echo $_GET['tri']; ?>" class="formulaire" method="post">
        <input type="hidden" name="actionflag" value="modif" />
        <input type="hidden" name="mp_ra_id" value="<? echo $r; ?>" />

        <div class="ligne">
          <div class="label w200">Nom</div><input type="text" class="input_nom" id="mp_ra_nom" name="mp_ra_nom" value="<? echo $dn['ra_nom']; ?>">
        </div> 
        <? include('include/insert/'.$_SESSION['rulesetRep'].'/bloc_race_modif.php'); ?>
        <!-- affichage des boutons --->
        <div class="ligneBouton">
          <button type="submit" class="btNoir" name="validModifDon" id="validModifDon" onClick="validerModifOM()"><? echo $libelle; ?></button>
          <button type="submit" class="btNoir" name="annuleModifDon" id="annuleModifDon" onClick="annulerPageModif()">Annuler</button>
        </div>

      </form>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div> <!--- #race--->
	</div> <!-- #wrapper --->
</div><!-- #page --->
</body>
</html>
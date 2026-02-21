<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");
/* p est la variable accueillant l'id de l'objet à modifier */
if	(isset($_GET['classe'])):
		// appel Include
		$c = $_GET['classe'];
		else:
		$c="";
endif;
?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
</HEAD>

<body>
<div id="page">
  <? include_once("include/affichageSelectionSources.php"); ?>
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
	  <?	
		if(isset($c) && $c!="n"): // il s'agit d'une modification
			$requete="SELECT * FROM dd_classes WHERE cla_id='".$c."'";
			$result=queryPDO($requete);	
			$num_rows=$result->rowCount();
			$dn = $result->fetch(PDO::FETCH_ASSOC);
			else: // il s'agit d'un ajout
			$num_rows=1;
			$a="n"; 
		endif;
		if ($num_rows>0):
      // mise en forme du contenu
      $source='<select id="mp_cla_res_id" name="mp_cla_res_id">'.optionList("dd_ressources", "res","nom", $dn['cla_res_id'],"res_id IN ".$selection).'</select>';  
      $type_magie='<select id="mp_cla_mag_id" name="mp_cla_mag_id">'.optionList("dd_typeMagie", "mag","nom", $dn['cla_mag_id'], "mag_ruleset_var_id='".$_SESSION['ruleset']."'").'</select>';
			?>
      <div id="classe" class="formulaire">
        <div class="titreAction">
          <div class="titreA"><? echo stripslashes($dn['cla_nom']); ?></div>
          <div></div>
        </div>        
				<form action="classe-enregistrement.php?classe=<? echo $c; ?>&tri=<? echo $_GET['tri']; ?>" method="post">
					<input type="hidden" name="actionflag" value="modif" />
					<input type="hidden" name="mp_cla_id" value="<? echo $c; ?>" />
					
					<div class="ligne">
            <div class="label w100">Nom</div><input type="text" class="input_nom" id="mp_cla_nom" name="mp_cla_nom" value="<? echo $dn['cla_nom']; ?>">
            <div class="label w100 ml25">Abreviation</div><input type="text" class="input_abreviation" id="mp_cla_abreviation" name="mp_cla_abreviation" value="<? echo stripslashes($dn['cla_abreviation']); ?>">            
          </div>
          
          <div class="ligne"><span class="label w200">Type de magie</span><? echo $type_magie; ?></div>
          
          <? include('include/insert/'.$_SESSION['rulesetRep'].'/bloc_classe_modif.php'); ?>
          
          <div class="ligne mt10"><div class="label">Source</div><? echo $source; ?></div>

          <!-- affichage des boutons --->
          <div class="mt25 mb10">
            <input type="submit" id="ok" name="ok" value="Modifier" class="btNoir"/>
            <input type="submit" id="nok" name="nok" value="Annuler"  class="btNoir"/>							
          </div>
																																													 
				</form>																																													 
		<?
			else:
			echo '<div class="nodata">Aucune classe selectionn&eacute;e ('.$c.')!</div>';
		endif;
		?>
	</div> <!-- #contenu --->
</div><!-- #page --->
</body>
</html>
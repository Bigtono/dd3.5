<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");
if	(isset($_GET['comp'])):
  $comp=$_GET['comp'];
  else:
  $comp="";
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
	if($comp!="n"):
		$requete = "SELECT * FROM dd_competences WHERE comp_id='". $comp ."'";
		$resultat = queryPDO($requete);
		$dn=$resultat->fetch(PDO::FETCH_ASSOC);
		$action="modif";
		else:
		$action="insert";
	endif;						
	?>
  <div class="wrapper">
    <div class="menu2">
      <div class="ga"></div>
      <div class="ce"><? echo stripslashes(ucfirst($dn['comp_nom'])); ?></div>
      <div class="dr"><a href="competences.php">Retour</a></div>
    </div>	
		<form action="competence-enregistrement.php" class="formulaire" method="post" name="modif-competence" id="modif-competence">
			<input type="hidden" name="actionflag" value="<? echo $action; ?>" />
			<input type="hidden" name="comp" value="<? echo $comp; ?>" />		

			<div id="detail_compteence">
				<!-- affichage des boutons --->
				<div class="mb15"> 	 	
					<input type="submit" id="ok" name="ok" class="form_bouton" value="Valider" />
					<input type="submit" id ="nok" name="nok" class="form_bouton" value="Annuler" />
				</div>
				<div class="contenu_profil">
          <div class="ligne"><div class="label w75">Nom</div><input type="text" id="mp_comp_nom" name="mp_comp_nom" class=" w300 input_left" value="<? echo $dn['comp_nom']; ?>"></div></div>
        
          <div class="label">Description</div><textarea id="mp_comp_description" name="mp_comp_description" class="input_notes"><? echo $dn['comp_description']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_description' );</script>
          <!---  
          <div class="label">Test</div><textarea id="mp_comp_test" name="mp_comp_test" class="input_notes"><? echo $dn['comp_test']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_test' );</script>
        
          <div class="label">Action</div><textarea id="mp_comp_action" name="mp_comp_action" class="input_notes"><? echo $dn['comp_action']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_action' );</script>
        
          <div class="label">Nouvelle Tentative</div><textarea id="mp_comp_nouvelleTentative" name="mp_comp_nouvelleTentative" class="input_notes"><? echo $dn['comp_nouvelleTentative']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_nouvelleTentative' );</script>

          <div class="label">Spécial</div><textarea id="mp_comp_special" name="mp_comp_special" class="input_notes"><? echo $dn['comp_special']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_special' );</script>

          <div class="label">Synergie</div><textarea id="mp_comp_synergie" name="mp_comp_synergie" class="input_notes"><? echo $dn['comp_synergie']; ?></textarea>
          <script>CKEDITOR.replace( 'mp_comp_synergie' );</script>
        --->
        </div>
			</div> <!--- detail_joueur --->
		</form>
	</section>
</div><!-- page --->
</body>
</html>
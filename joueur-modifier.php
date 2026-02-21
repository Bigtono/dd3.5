<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");
if	(isset($_GET['joueur'])):
		$j = $_GET['joueur'];
		else:
		$j="";
endif;
?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
	<link href="include/_styles-joueurs_.css" rel="stylesheet" type="text/css">	
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
	<?
	if($j!=""):
		$requete = "SELECT * FROM dd_joueurs WHERE j_id='". $j ."'";
		$resultat = queryPDO($requete);
		$dn=$resultat->fetch(PDO::FETCH_ASSOC);
		// on enregistre l'id du joueur au cas ou...
		$_SESSION['joueur']=$dn['j_id'];
		if (strlen($dn['j_notes'])>0):
			$notes=$dn['j_notes'];
			else:
			$notes="Aucune note";
		endif;
		$action="modif";
		else:
		$action="insert";
	endif;						
	?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Modification de la fiche du joueur <? echo stripslashes($dn['j_nom']); ?></div>
      <div></div>
    </div>

    <div class="contenu">
      <form action="joueur-enregistrement.php?joueur=<? echo $j; ?>" method="post" name="modif-joueur" id="modif-joueur">
        <input type="hidden" name="actionflag" value="<? echo $action; ?>" />
        <input type="hidden" name="mp_j_id" value="<? echo $j; ?>" />		
        <div class="contenu_profil">
          <div class="ligne">
            <div class="label w250">Nom</div>
            <div class="data"><input type="text" id="mp_j_nom" name="mp_j_nom" class="input_left" value="<? echo $dn['j_nom']; ?>" size="30"></div>
          </div>
          <div class="ligne">
            <div class="label w250">Pr&eacute;nom</div>
            <div class="data"><input type="text" id="mp_j_prenom" name="mp_j_prenom" class="input_left" value="<? echo $dn['j_prenom']; ?>" size="30"></div>
          </div>
          <div class="ligne">
            <div class="label w250">Pseudo</div>
            <div class="data"><input type="text" id="mp_j_pseudo" name="mp_j_pseudo" class="input_left" value="<? echo $dn['j_pseudo']; ?>" size="30"></div>
          </div>
          <div class="ligne">
            <div class="label w250">Email</div>
            <div class="data"><input type="text" id="mp_j_email" name="mp_j_email" class="input_left" value="<? echo $dn['j_email']; ?>" size="50"></div>
          </div>
          <div class="ligne">
            <div class="label w250">Notes</div>
            <div class="data"><textarea id="mp_j_notes" name="mp_j_notes" class="input_notes"><? echo $dn['j_notes']; ?></textarea></div>
            <script>CKEDITOR.replace( 'mp_j_notes' );</script>
          </div>
          <div class="ligne">
            <div class="label w250">Set de règles par défaut</div>
            <div class="data">
              <select id="mp_j_default_ruleset_var_id" name="mp_j_default_ruleset_var_id">
                <? echo optionList('dd_variables', 'var', 'valeur', $dn['j_default_ruleset_var_id'], 'var_cat="rule"'); ?>  
              </select>
            </div>
          </div>
          
        <!--Onglet Sorts -->
        <input type="hidden" name="mp_j_dd_onglet_sort" id="mp_j_dd_onglet_sort" value="<?= $dn['j_dd_onglet_sort'] ?>">
        <div class="ligne">
          <div class="label w250">Afficher un sort dans un onglet</div>
          <label class="switch">
            <input type="checkbox" id="toggleDDOngletSort" <?= $dn['j_dd_onglet_sort'] == 1 ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div> 
        <script>
          document.getElementById('toggleDDOngletSort').addEventListener('change', function() {
            document.getElementById('mp_j_dd_onglet_sort').value = this.checked ? 1 : 0;
          });
        </script>        
        <!--Onglet don -->
        <input type="hidden" name="mp_j_dd_onglet_don" id="mp_j_dd_onglet_don" value="<?= $dn['j_dd_onglet_don'] ?>">
        <div class="ligne">
          <div class="label w250">Afficher un don dans un onglet</div>
          <label class="switch">
            <input type="checkbox" id="toggleDDOngletDon" <?= $dn['j_dd_onglet_don'] == 1 ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div> 
        <script>
          document.getElementById('toggleDDOngletDon').addEventListener('change', function() {
            document.getElementById('mp_j_dd_onglet_don').value = this.checked ? 1 : 0;
          });
        </script>        
        <!--Onglet OM -->
        <input type="hidden" name="mp_j_dd_onglet_om" id="mp_j_dd_onglet_om" value="<?= $dn['j_dd_onglet_om'] ?>">
        <div class="ligne">
          <div class="label w250">Afficher un objet magique dans un onglet</div>
          <label class="switch">
            <input type="checkbox" id="toggleDDOngletOM" <?= $dn['j_dd_onglet_om'] == 1 ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div>
        <script>
          document.getElementById('toggleDDOngletOM').addEventListener('change', function() {
            document.getElementById('mp_j_dd_onglet_om').value = this.checked ? 1 : 0;
          });
        </script>
          
          
          
        <!-- affichage des boutons --->
        <div class="mt10">
          <button id="ok" name="ok" class="btNoir mr10">Valider</button>
          <button id ="nok" name="nok" class="btNoir">Annuler</button>      
        </div>          
      </form>
    </div> <!-- contenu -->
  </div> <!-- #wrapper -->  
</div><!-- #page ---
</body>
</html>
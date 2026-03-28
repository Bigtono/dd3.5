<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
if	(isset($_SESSION['user_id'])):
	$j = $_SESSION['user_id'];
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
    // recherche du personnage joué
    // pour Donjon
    // à faire
    // Pour Shadowrun
    // à faire
		else:
		$action="insert";
	endif;						
	?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Modifier mon profil</div>
      <div></div>
    </div>

    <form action="profil-enregistrement.php" method="post" name="modif-joueur" id="modif-joueur">
      <input type="hidden" name="mp_j_id" value="<? echo $j; ?>" />		

      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-user"></i></div><div>Mon compte</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <div class="ligne">
          <div class="label w150">Pseudo</div>
          <div class="data"><span class="gras mr15"><? echo $dn['j_pseudo']; ?></span><span>(<? echo $_SESSION['no_modif_pseudo']; ?>)</span></div>
        </div>          
        <div class="ligne">
          <div class="label w150">Nom</div>
          <div class="data"><input type="text" id="mp_j_nom" name="mp_j_nom" class="input_left" value="<? echo $dn['j_nom']; ?>" size="30"></div>
        </div>
        <div class="ligne">
          <div class="label w150">Pr&eacute;nom</div>
          <div class="data"><input type="text" id="mp_j_prenom" name="mp_j_prenom" class="input_left" value="<? echo $dn['j_prenom']; ?>" size="30"></div>
        </div>
        <div class="ligne">
          <div class="label w150">Email</div>
          <div class="data"><input type="text" id="mp_j_email" name="mp_j_email" class="input_left" value="<? echo $dn['j_email']; ?>" size="50"></div>
        </div>
         <div class="sm-text mt5"><sup>*</sup> identifiant de connexion</div>
      </div>
     
      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-blog"></i></div><div>Ma biographie</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <textarea id="mp_j_bio" name="mp_j_bio" class="ckeditor input_notes" rows="10" cols="100"><? echo $dn['j_bio']; ?></textarea>
        <script>
          CKEDITOR.replace('mp_j_bio', {
            allowedContent: true, // désactive le filtre de contenu
            // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
            extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
            contentsCss: 'include/_styles_.css'
          });
        </script>
      </div>

      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-gear"></i></div><div>Mes Options</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <div class="ligne">
          <div class="label w300">Set de règles par défaut</div>
          <div class="data">
            <select id="mp_j_default_ruleset_var_id" name="mp_j_default_ruleset_var_id">
              <? echo optionList('dd_variables', 'var', 'valeur', $dn['j_default_ruleset_var_id'], 'var_cat="rule"'); ?>  
            </select>
          </div>
        </div>        
        <!--Onglet Sorts -->
        <input type="hidden" name="mp_j_dd_onglet_sort" id="mp_j_dd_onglet_sort" value="<?= $dn['j_dd_onglet_sort'] ?>">
        <div class="ligne">
          <div class="label w300">Afficher un sort dans un onglet</div>
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
          <div class="label w300">Afficher un don dans un onglet</div>
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
          <div class="label w300">Afficher un objet magique dans un onglet</div>
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

        <input type="hidden" name="mp_j_mode_campagne" id="mp_j_mode_campagne" value="<?= (!isset($dn['j_mode_campagne']) || (int)$dn['j_mode_campagne'] === 1) ? 1 : 0 ?>">
        <div class="ligne">
          <div class="label w300">Mode campagne (afficher les fonctionnalites campagne)</div>
          <label class="switch">
            <input type="checkbox" id="toggleModeCampagne" <?= (!isset($dn['j_mode_campagne']) || (int)$dn['j_mode_campagne'] === 1) ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div>
        <script>
          document.getElementById('toggleModeCampagne').addEventListener('change', function() {
            document.getElementById('mp_j_mode_campagne').value = this.checked ? 1 : 0;
          });
        </script>
        <input type="hidden" name="mp_j_affichage_ruleset" id="mp_j_affichage_ruleset" value="<?= (isset($dn['j_affichage_ruleset']) && (int)$dn['j_affichage_ruleset'] === 1) ? 1 : 0 ?>">
        <div class="ligne">
          <div class="label w300">Afficher le ruleset dans le header</div>
          <label class="switch">
            <input type="checkbox" id="toggleAffichageRuleset" <?= (isset($dn['j_affichage_ruleset']) && (int)$dn['j_affichage_ruleset'] === 1) ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
        </div>
        <script>
          document.getElementById('toggleAffichageRuleset').addEventListener('change', function() {
            document.getElementById('mp_j_affichage_ruleset').value = this.checked ? 1 : 0;
          });
        </script>
      </div>

      <!-- affichage des boutons --->
      <div class="mt10">
        <button id="ok" name="ok" class="btNoir mr10">Valider</button>
        <button id ="nok" name="nok" class="btNoir">Annuler</button>      
      </div>          
    </form>
  </div> <!-- #wrapper -->  
</div><!-- #page ---
</body>
</html>

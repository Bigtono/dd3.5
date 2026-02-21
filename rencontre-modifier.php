<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

if	(isset($_GET['rencontre'])):
  $re=$_GET['rencontre'];
  else:
  $re="";
endif;

// paramétrage du filtrage
$displayChapitres=' class="noDisplay"';
$listeChapitres='';

?>
<!doctype html>
<HEAD>
	<? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-rencontres.js'></script>
  <script type='text/javascript' src='js/moncode-regles.js'></script>    
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
	<?
	if($re!="n"):
		$requete = "SELECT * FROM dd_rencontres WHERE re_id='".$re."'";
		$resultat = queryPDO($requete);
		$dn=$resultat->fetch(PDO::FETCH_ASSOC);
		$libelle="Modifier";
    $titre="Modifier la rencontre";
    $scenario=OptionList("dd_scenarios","sc","nom",$dn['re_sc_id'],'sc_ruleset_var_id="'.$_SESSION['ruleset'].'"');
    if ($dn['re_sc_id']>0):  
      $displayChapitres=""; // on affiche la liste des chapitres 
      $listeChapitres='<select name="mp_re_scc_id" id="mp_re_scc_id">'.optionList("dd_scenarios_chapitres", "scc", "nom", $dn['re_scc_id'], 'scc_sc_id="'.$dn['re_sc_id'].'"', 0,'','scc_ordre').'</select>';
    endif;  
    else:
    $libelle="Créer";
    $titre="Nouvelle rencontre";
    $scenario=OptionList("dd_scenarios","sc","nom",$_SESSION['scenario'],'sc_ruleset_var_id="'.$_SESSION['ruleset'].'"');
    if ($_SESSION['scenario']>0):
      $displayChapitres=""; // on affiche la liste des chapitres 
      $listeChapitres='<select name="mp_re_scc_id" id="mp_re_scc_id">'.optionList("dd_scenarios_chapitres", "scc", "nom", 0, 'scc_sc_id="'.$_SESSION['scenario'].'"', 0,'','scc_ordre').'</select>';  
    endif;    
  endif;
	?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA"><?= $titre; ?></div>
      <div></div>
    </div>    
          
		<form id="detail_rencontre" action="rencontre-enregistrement.php" class="formulaire" method="post" name="modif-rencontre" id="modif-rencontre">
			<input type="hidden" name="mp_re_id" value="<?= $re; ?>" />
      <div class="contenu_profil">
        
        <div class="ligne">
          <div class="label w75">Nom</div>
          <input type="text" id="mp_re_nom" name="mp_re_nom" class="w300 input_left" value="<?= $dn['re_nom']; ?>">
        </div>
        
        <div class="ligne">
          <div class="label w75">Scénario</div>
          <select name="mp_re_sc_id" id="mp_re_sc_id" onChange="majChapitre(this.value)">
            <?= $scenario; ?>
          </select>
        </div>        
        <div class="ligne">
          <div class="label w75">Chapitre</div>
          <span id="listeChapitres"<?= $displayChapitres; ?>><?= $listeChapitres; ?></span>
        </div>
        <div class="label">Description</div>
        <textarea id="mp_re_description" name="mp_re_description" class="wp100"><?= $dn['re_description']; ?></textarea>
        <script>
          CKEDITOR.replace('mp_re_description', {
            allowedContent: true, // désactive le filtre de contenu
            // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
            extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
            contentsCss: 'include/_styles_.css'
          });
        </script>
      </div>
      <!-- affichage des boutons --->
      <div class="ligneBouton">
        <button type="submit" class="btNoir" name="validModifRencontre" id="validModifRencontre"><? echo $libelle; ?></button>
        <button type="submit" class="btNoir" name="annuleModifRencontre" id="annuleModifRencontre">Annuler</button>
      </div>
		</form>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>  
	</div> <!-- wrapper -->
</div><!-- page --->
</body>
</html>
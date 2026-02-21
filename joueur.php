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
<?
include("include/header.php");
include("include/menu.php");
if($j!=""):
  // Requête SQL
  $requete = "SELECT * FROM dd_joueurs WHERE j_id='". $j ."'";
  $resultat = queryPDO($requete);
  $dn=$resultat->fetch(PDO::FETCH_ASSOC);
  ?>  
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA"><? echo stripslashes(ucfirst($dn['j_prenom']))." ".stripslashes(ucfirst($dn['j_nom'])); ?></div>
      <div><? if ($_SESSION['mj']==1) echo '<a href="joueur-modifier.php?joueur='.$j.'&retour=joueur&idretour='.$j.'"><i class="icon fa-solid fa-pen-to-square"></i></a>'; ?></div>
    </div>
		<div id="detail_joueur">		
			<fieldset>
				<legend>Profil</legend>		
				<div class="contenu_profil">
				  <div class="detail_joueur_ligne"><div class="label">Nom</div><div><? echo stripslashes($dn['j_nom']); ?></div></div>
					<div class="detail_joueur_ligne"><div class="label">Pr&eacute;nom</div><div><? echo stripslashes($dn['j_prenom']); ?></div></div>
					<div class="detail_joueur_ligne"><div class="label">Pseudo</div><div><? echo stripslashes($dn['j_pseudo']); ?></div></div>
					<div class="detail_joueur_ligne"><div class="label">Email</div><div><? echo stripslashes($dn['j_email']); ?></div></div>
          <div class="detail_joueur_ligne"><div class="label">Notes</div><div><? echo stripslashes($dn['j_notes']); ?></div></div>
          <div class="detail_joueur_ligne"><div class="label">Set de règles par défaut</div><div><? echo libelle("dd_variables","var","valeur",$dn['j_default_ruleset_var_id']); ?></div></div>
				</div> 
			</fieldset>
			<fieldset>
        <legend>Options</legend>
        
      </fieldset>
      
      <?    
      if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="mt15">msgEnr : '.$_GET['msgEnr'].'</div>';
      if ($_SESSION['debug']==1 && $_SESSION['mj']==1) echo '<div class="mt15">message : '.$_GET['message'].'</div>';
      ?>
		</div> <!--- detail_joueur --->
  <?
  else:
  echo '<div class="nodata">Aucun joueur selectionné !</div>';
endif;
?>
  </div> <!-- #wrapper --->
</div><!-- page --->
</body>
</html>
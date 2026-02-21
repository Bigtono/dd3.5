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
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Mon profil</div>
      <div><? echo '<a href="profil-modifier.php"><button class="btNoir">Modifier</button></a>'; ?></div>
    </div>    
		<?
		if($j!=""):
			// Requête SQL
			$requete = "SELECT * FROM dd_joueurs WHERE j_id='". $j ."'";
			$resultat = queryPDO($requete);
			$dn=$resultat->fetch(PDO::FETCH_ASSOC);						
		?>
      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-user"></i></div><div>Mon compte</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <div class="detail_joueur_ligne"><div class="label">Pseudo</div><div class="gras"><? echo stripslashes($dn['j_pseudo']); ?></div></div>
        <div class="detail_joueur_ligne"><div class="label">Nom</div><div><? echo stripslashes($dn['j_nom']); ?></div></div>
        <div class="detail_joueur_ligne"><div class="label">Pr&eacute;nom</div><div><? echo stripslashes($dn['j_prenom']); ?></div></div>
        <div class="detail_joueur_ligne"><div class="label">Email<sup>*</sup></div><div><? echo stripslashes($dn['j_email']); ?></div></div>
        <div class="sm-text mt5"><sup>*</sup> identifiant de connexion</div>
			</div>
      
    
      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-blog"></i></div><div>Ma biographie</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <? 
        if (!empty($dn['j_bio'])):
          echo stripslashes($dn['j_bio']);
          else:
          echo "Aucune bio...";
        endif;
        ?>
			</div>
    
      <div class="titreAction">
        <div class="titre2A"><div class="mr15"><i class="fa-solid fa-gear"></i></div><div>Mes Options</div></div>
        <div></div>
      </div>
      <div class="contenu_profil contenu">
        <div class="detail_joueur_ligne"><div class="label">Set de règles par défaut</div><div><? echo libelle("dd_variables","var","valeur",$dn['j_default_ruleset_var_id']); ?></div></div>
        <!--Onglet Sorts -->
        <div class="ligne">
          <div class="switch-display <?= $dn['j_dd_onglet_sort'] ? 'on' : 'off' ?>">
            <div class="slider"></div>
          </div>          
          <div class="label ml10">Afficher un sort dans un onglet</div>    
        </div>
        <!--Onglet Don -->
        <div class="ligne">
          <div class="switch-display <?= $dn['j_dd_onglet_don'] ? 'on' : 'off' ?>">
            <div class="slider"></div>
          </div>          
          <div class="label ml10">Afficher un don dans un onglet</div>              
        </div> 
        <!--Onglet OM -->
        <div class="ligne">
          <div class="switch-display <?= $dn['j_dd_onglet_om'] ? 'on' : 'off' ?>">
            <div class="slider"></div>
          </div>          
          <div class="label ml10">Afficher un objet magique dans un onglet</div>    
        </div> 
        
      </div> 
      
		</div> <!--- detail_joueur --->
		<? else:
			echo '<div class="nodata">Aucune info disponible ! Veuillez contacter l\'administrateur.</div>';
		endif;
		?>
		</div> <!-- detail_joueur --->
	</div> <!-- wrapper --->
</div><!-- page --->
</body>
</html>
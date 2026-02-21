<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

?>
<!doctype html>
<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-classes.js'></script>
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Classes de personnages</div>
      <div class="titreA"><? if ($_SESSION['mj']==1) echo '<a href="classe-modifier.php?classe=n" class="ajout_classe lien"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?></div>
    </div>   
 
      <div class="item entete">
        <?  if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>'; ?>
        <?  if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>'; ?>
        <div class="nom_classe">Classe</div>
        <div class="type_classe gras">Type</div>
        <div class="ls gras">Magie</div>
      </div>
      <?
      if ($_GET['msg'] && $_SESSION['debug']==1) echo '<div>'.$_GET['msg'].'</div>';
      $requete='SELECT * FROM dd_classes WHERE cla_ruleset_var_id="'.$_SESSION['ruleset'].'" ORDER BY cla_clt_id, cla_nom'; 
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      if ($num_rows > 0):
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          $lien='<a href="classe.php?classe='.$dn['cla_id'].'&tri='.$_GET['tri'].'">';
          echo '<div class="item data">';
          if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_dons\',\'do\','.$don['do_id'].')"><i class="fa fa-trash"></i></span></div>';
          if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><a href="classe-modifier.php?classe='.$dn['cla_id'].'&tri='.$_GET['tri'].'"><i class="fa-solid fa-pen-to-square"></i></a></div>';      
          echo '  <div class="nom_classe">'.$lien.$dn['cla_nom'].'</a></div>';
          echo '  <div class="type_classe">'.$lien.libelle("dd_classe_type","clt","nom",$dn['cla_clt_id']).'</a></div>';
          echo '  <div class="ls">'.$lien.libelle("dd_typeMagie","mag","nom",$dn['cla_mag_id']).'</a></div>';
          echo '</div>';
        endwhile;
        else:
        echo '<div class="alerte">Aucun classe dans la base de données</div>';
      endif;
      ?>

  </div> <!-- #wrapper --->
  <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
  <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
</div> <!-- #page --->
<div id="detail-pp"></div>
</body>
</html>
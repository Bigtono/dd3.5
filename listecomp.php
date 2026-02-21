<?
include("include/session.php");
include("connexion.php");
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<HEAD>
<? include("include/header.php"); ?>
<script type='text/javascript' src='js/moncode-competences.js'></script>  
</HEAD>

<BODY>
<div id="page">
	<? include("include/head.php"); ?>
	<? include("include/menu.php"); ?>
  <div id="contenu">
    <div class="liste-competences"> 
    <?
      if ($_GET['msg'] && $_SESSION['debug']==1) echo '<div>'.$_GET['msg'].'</div>';
      $requete="SELECT * FROM dd_competences ORDER BY comp_nom"; 
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      if ($num_rows > 0):
        echo '<div class="competence-entete">';
        echo '  <div class="comp-nom">Nom</div>';
        echo '  <div class="comp-cacac">Carac</div>';
        echo '  <div class="comp-formation">Formation</div>';
        echo '  <div class="comp-armure">Malus Armure</div>';
        echo '</div>';
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          echo '<div id="comp'.$dn['comp_id'].'" class="competence" onClick="afficherComp('.$dn['comp_id'].')">';
          echo '  <div id="nomComp'.$dn['comp_id'].'" class="competence-nom">'.$dn['comp_nom'].' </div>';
          echo '  <div id="caracComp'.$dn['comp_id'].'" class="competence-cacac">'.$dn['comp_caracteristique'].'</div>';
          echo '  <div id=formationComp'.$dn['comp_id'].'" class="competence-formation">'.ouiNon($dn['comp_formationNecessaire']).'</div>';
          echo '  <div id="armureComp'.$dn['comp_id'].'" class="competence-armure">'.ouiNon($dn['comp_malusArmure']).'</div>';
          echo '</div>';
        endwhile;
        else:
        echo utf8_encode('<div class="alerte">Aucun compétence dans la base de données</div>');
      endif;
    ?>	
    </div> <!-- #liste-contenu --->
  </div> <!-- #contenu --->
</div> <!-- #page --->
<div id="detail-pp"></div>  
</body>
</html>
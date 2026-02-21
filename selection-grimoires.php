<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

if ($_GET["actionflag"]==1):
	razRessourceLivre();
	foreach($_GET["livre"] as $key=>$value):
		updateRessourceLivre($key);
	endforeach;
endif;

?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <H1>Sources</H1>
    <?
    //********************************************************************************************************
    // sélection des livres
    //********************************************************************************************************

    if ($_GET["actionflag"]<1):
    ?>

    <?
      // Sélection des références de livres
      $requete="SELECT res_id, res_nom, res_editeur, res_selection FROM dd_ressources ORDER BY res_nom";
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      $nbl=0; // nb de ressources dans la ligne
      if ($num_rows > 0):
        ?>
        <form action="<? echo $prm_url.$_SERVER['PHP_SELF']; ?>" method="get" name="selections" id="selections">
          <input type="hidden" name="actionflag" value="1" />
          <!---<input type="submit" name="Submit" value="Selectionner" />-->
          <div class="action">S&eacute;lectionner les sources de donn&eacute;es</div>
          <?
          echo '<div class="ressource entete">';
          echo '  <div class="icone_select"></div>';
          echo '  <div class="icone_select">ID</div>';      
          echo '  <div class="nom">nom</div>';
          echo '  <div class="sorts">Nb Sorts</div>';
          echo '  <div class="dons">Nb Dons</div>';
          echo '  <div class="classes">Nb Classes</div>';      
          echo '  <div class="detail"></div>';
          echo '</div>';
          echo "<div class='liste-ressources'>";
          while($dn = $result->fetch(PDO::FETCH_ASSOC)):
            if ($dn['res_selection']==1):
              $check=" checked";
              else:
              $check="";
            endif;
            // Nb sorts
            $requete='SELECT count(so_id) as total FROM dd_sorts WHERE so_res_id="'.$dn['res_id'].'"';
            $resultat_so=queryPDO($requete);
            $dnso=$resultat_so->fetch(PDO::FETCH_ASSOC);
            // Nb dons
            $requete='SELECT count(do_id) as total FROM dd_dons WHERE do_res_id="'.$dn['res_id'].'"';
            $resultat_do=queryPDO($requete);
            $dndo=$resultat_do->fetch(PDO::FETCH_ASSOC);
            // Nb Classes
            $requete='SELECT count(cla_id) as total FROM dd_classes WHERE cla_res_id="'.$dn['res_id'].'"';
            $resultat_cl=queryPDO($requete);
            $dncl=$resultat_cl->fetch(PDO::FETCH_ASSOC);       
            echo '<div class="ressource">';
            echo '  <div class="icone_select"><input type="checkbox" name="livre['.$dn['res_id'].']" value="'.$check.'"'.$check.'></div>';
            echo '  <div class="id">'.$dn['res_id'].'</div>';      
            echo '  <div class="nom">'.$dn['res_nom'].'</div>';
            echo '  <div class="sorts">'.$dnso['total'].'</div>';
            echo '  <div class="dons">'.$dndo['total'].'</div>';
            echo '  <div class="classes">'.$dncl['total'].'</div>';      
            echo '  <div class="detail"></div>';
            echo '</div>';
          endwhile;
          echo "</div>";
        endif;
        ?>
        <input type="submit" name="Submit" value="Selectionner" />
      </form>  
      <?	
      else:
      ?>
      <div class="action">Liste mise à jour !</div>
      <div class="selection">
      <?
      // affichage des références de livres sélectionnées
      $requete='SELECT res_id, res_nom, res_editeur, res_selection FROM dd_ressources WHERE res_selection="1" ORDER BY res_nom';
      $result=queryPDO($requete);
      $num_rows=$result->rowCount();
      $nbl=0; // nb de ressources dans la ligne
      if ($num_rows > 0):
        while($dn = $result ->fetch(PDO::FETCH_ASSOC)):
          echo $dn['res_nom'].'</br>';
        endwhile;
      endif;	
      ?>
      </div>
      <?
    endif;
    ?>
	</div>
</div>  
</body>
</html>
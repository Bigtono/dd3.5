<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$perso_id = isset($_GET['personnage']) ? (int)$_GET['personnage'] : 0;
if ($perso_id <= 0):
  exit('Personnage invalide');
endif;

/* récupération des sorts possédés */
$sql = "
SELECT
  s.so_id,
  s.so_nom,
  sc.sc_niveau,
  ps.pes_connu,
  ps.pes_memorise
FROM dd_personnages_sorts ps
JOIN dd_sorts s ON s.so_id = ps.pes_so_id
JOIN dd_sortclasse sc ON sc.sc_so_id = s.so_id
JOIN dd_classes c ON c.cla_id = sc.sc_cla_id
WHERE ps.pes_pe_id = :pid
  AND c.cla_mag_id > 0
GROUP BY s.so_id
ORDER BY sc.sc_niveau, s.so_nom
";
$stmt = $db->prepare($sql);
$stmt->execute([':pid'=>$perso_id]);
$sorts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sorts_par_niveau = [];
foreach ($sorts as $s):
  $niv = (int)$s['sc_niveau'];
  if (!isset($sorts_par_niveau[$niv])):
    $sorts_par_niveau[$niv] = [];
  endif;
  $sorts_par_niveau[$niv][] = $s;
endforeach;
?>
<!doctype html>
<html>
<HEAD>
  <? include("include/head.php"); ?>
  <link rel="stylesheet" href="include/grimoire.css">
  <script src="js/grimoire_gestion.js" defer></script>
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">Gestion des sorts connus et mémorisés</div>
      <div><a href="grimoire.php?personnage=<? echo $perso_id; ?>&campagne=<? echo $_GET['campagne']; ?>" class="lien"><i class="icon fa-solid fa-pen-to-square"></i></a></div>
    </div>

    <div class="contenu">
      <?
      // recherche de la classe de LS
      $requete='SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_caracteristiques ON cla_car_id=car_id WHERE cla_mag_id>0 AND pc_pe_id="'.$perso_id.'" ORDER BY cla_mag_id';
      $result_ls=queryPDO($requete);
      $num_rows_ls=$result_ls->rowCount();
      echo '<div class="debug">'.$num_rows_ls.'</div>';
      if ($num_rows_ls>0):
        $dnls=$result_ls->fetch(PDO::FETCH_ASSOC);
        echo '<div>Nombre de sorts pas jour</div>';
        $requete='SELECT * FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id JOIN dd_classe_niveau ON cn_cla_id=cla_id WHERE cla_mag_id>0 AND pc_pe_id="'.$perso_id.'" AND cla_id="'.$dnls['cla_id'].'" AND cn_niveau="'.$nls.'"';
        if ($_SESSION['mj']==1 && $_SESSION['debug']==1) echo '<div>'.$requete.'</div>';
        $result_nls=queryPDO($requete);
        $num_rows_nls=$result_nls->rowCount();
        if ($num_rows_nls>0):
          $dnnls=$result_nls->fetch(PDO::FETCH_ASSOC);
          $result='  <div class="tabMain mb10">';
          for ($i=0;$i<10;$i++):
            $compCss='';
            if ($i==1) $compCss=" cellLeft";
            if (strlen($dnnls['cn_sort_n'.$i])>0):
              $nbs=$dnnls['cn_sort_n'.$i];
              else:
              $nbs='-';
            endif;
            $result.='    <div class="cellMainSort">';
            $result.='      <div>';
            $result.='        <div class="cellEntete'.$compCss.'">'.$i.'</div>';
            $result.='        <div class="cellValue'.$compCss.'">'.$nbs.'</div>';
            $result.='      </div>';
            $result.='    </div>';
          endfor;
          $result.='  </div>';
          echo '<div>'.$result.'</div>';
        endif;
      endif;            
      ?>
    </div>
    
    
    <div class="page-content" data-perso-id="<?= $perso_id ?>">

    <? for ($niv=0; $niv<=9; $niv++):
      if (!isset($sorts_par_niveau[$niv])) continue;
    ?>
      <h3 class="titre-niveau">Niveau <?= $niv ?></h3>
      <div class="ligne-sorts">

      <? foreach ($sorts_par_niveau[$niv] as $s):
        $classes = ['sort-bulle'];
        if ($s['pes_connu'] && $s['pes_memorise'] > 0):
          $classes[] = 'memorise';
        elseif ($s['pes_connu']):
          $classes[] = 'connu';
        else:
          $classes[] = 'inconnu';
        endif;
      ?>
        <div class="<?= implode(' ', $classes) ?>"
          data-sort-id="<?= $s['so_id'] ?>"
          data-connu="<?= $s['pes_connu'] ?>"
          data-memo="<?= $s['pes_memorise'] ?>">

          <span class="nom"><?= htmlspecialchars($s['so_nom']) ?></span>
          <span class="compteur">
            <?= $s['pes_memorise'] > 0 ? (int)$s['pes_memorise'] : '' ?>
          </span>

        </div>
      <? endforeach; ?>

      </div>
    <? endfor; ?>

    </div><!-- page-content -->

    <!-- MENU CONTEXTUEL -->
    <div id="menu-sort" class="menu-contextuel hidden">
      <ul></ul>
    </div>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>
</body>
</html>
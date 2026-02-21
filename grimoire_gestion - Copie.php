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
    
    <div class="page-content" data-perso-id="<?= $perso_id ?>">
    <!---<div class="sort_dispo" data-perso-id="<?= $perso_id ?>">--->
    <? for ($niv = 0; $niv <= 9; $niv++):
      if (!isset($sorts_par_niveau[$niv])) continue;
    ?>

      <h3 class="titre-niveau">Niveau <?= $niv ?></h3>

      <div class="ligne-sorts">

      <? foreach ($sorts_par_niveau[$niv] as $s):
        $classes = ['sort-bulle'];
        if ($s['pes_connu'] == 1 && $s['pes_memorise'] > 0):
          $classes[] = 'memorise';
        elseif ($s['pes_connu'] == 1):
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

    </div>

    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>
</body>
</html>
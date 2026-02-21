<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$perso_id = isset($_GET['personnage']) ? (int)$_GET['personnage'] : 0;
if (!$perso_id):
  exit;
endif;

/* filtre persistant */
if (isset($_POST['filtre'])):
  $_SESSION['grimoire_filtre'] = $_POST['filtre'];
endif;
$filtre = $_SESSION['grimoire_filtre'] ?? 'connus';

/* types de magie */
$sql = "
SELECT
  c.cla_mag_id,
  SUM(pc.pc_niveau) AS nls
FROM dd_personnages_classes pc
JOIN dd_classes c ON c.cla_id = pc.pc_cla_id
WHERE pc.pc_pe_id = :pid
  AND c.cla_mag_id > 0
GROUP BY c.cla_mag_id
";
$stmt = $db->prepare($sql);
$stmt->execute([':pid'=>$perso_id]);
$magies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<HEAD>
  <? include("include/head.php"); ?>
  <link rel="stylesheet" href="include/grimoire.css">
  <script src="js/grimoire.js" defer></script>  
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
</HEAD>

<body>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <? include('include/ariane.php'); ?>
    <div class="titreAction">
      <div class="titreA">
        Grimoire
        <span><a href="grimoire_gestion.php?personnage=<? echo $perso_id; ?>&campagne=<? echo $_GET['campagne']; ?>" class="lien">* <i class="fa-solid fa-pen-to-square ml15"></i></a></span>
      </div>
      <div></div>
    </div>
    <div class="page-content" data-perso-id="<?= $perso_id ?>">
      <? if (!$magies): ?>
        <p>Ce personnage ne possède aucune capacité de lancement de sorts.</p>
      <? else: ?>

      <form method="post" class="grimoire-filtre">
        <label>Afficher :</label>
        <select name="filtre" onchange="this.form.submit()">
          <option value="connus" <?= $filtre=='connus'?'selected':'' ?>>
            Sorts connus
          </option>
          <option value="memorises" <?= $filtre=='memorises'?'selected':'' ?>>
            Sorts mémorisés
          </option>
        </select>
      </form>
      
      <? if (count($magies) > 1): ?>
      <div class="tabs">
        <? foreach ($magies as $i=>$m): ?>
          <button class="tab-btn<?= $i==0?' active':'' ?>"
            data-tab="tab<?= $m['cla_mag_id'] ?>">
            <?= $m['cla_mag_id']==1?'Magie profane':'Magie divine' ?>
          </button>
        <? endforeach; ?>
      </div>
      <? endif; ?>

      <? foreach ($magies as $i=>$m): ?>
      <div class="tab-content<?= $i==0?' active':'' ?>"
        id="tab<?= $m['cla_mag_id'] ?>">

      <?
      $sql = "
      SELECT
        so_id,
        so_nom,
        sc_niveau,
        pes_id,
        pes_so_id,
        pes_memorise,
        pes_lance
      FROM dd_personnages_sorts ps
      JOIN dd_sorts s ON s.so_id = ps.pes_so_id
      JOIN dd_sortclasse sc ON sc.sc_so_id = s.so_id
      JOIN dd_classes c ON c.cla_id = sc.sc_cla_id
      WHERE pes_pe_id = :pid
        AND cla_mag_id = :mag
        AND (
          (:f='connus' AND pes_connu=1)
          OR
          (:f='memorises' AND pes_memorise>0)
        )
      GROUP BY so_id
      ORDER BY sc_niveau, so_nom
      ";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':pid'=>$perso_id,
        ':mag'=>$m['cla_mag_id'],
        ':f'=>$filtre
      ]);
      $sorts = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $par_niveau = [];
      foreach ($sorts as $s):
        $par_niveau[$s['sc_niveau']][] = $s;
      endforeach;

      for ($niv=0;$niv<=9;$niv++):
        if (!isset($par_niveau[$niv])) continue;
      ?>
      <h3 class="titre-niveau">Niveau <?= $niv ?></h3>
      <div class="ligne-sorts">
      <? foreach ($par_niveau[$niv] as $s): ?>
        <div class="sort-bulle"
          data-id="<?= $s['pes_id'] ?>"
          data-max="<?= $s['pes_memorise'] ?>">
          <span class="nom" onClick="afficherSort(<? echo $s['pes_so_id']; ?>)"><?= htmlspecialchars($s['so_nom']) ?></span>
          <span class="compteur">
            <span class="charges"><?= (int)$s['pes_lance'] ?></span>
            /
            <span class="memorises"><?= (int)$s['pes_memorise'] ?></span>
          </span>
        </div>
      <? endforeach; ?>
      </div>
      <? endfor; ?>

      </div>
      <? endforeach; ?>
    </div>
    <? endif; ?>
    <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>    
  </div> <!-- #wrapper --->
</div> <!-- #page --->
<div id="detail-pp"></div>
</body>
</html>
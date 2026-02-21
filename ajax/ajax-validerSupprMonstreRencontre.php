<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$rem=$_POST['rem'];
$re=$_POST['rencontre'];
$mo=$_POST['monstre'];

// Suppression
if (!empty($rem)): 
  $requete='DELETE FROM dd_rencontres_monstres WHERE rem_id="'.$rem.'"';
  $resultat=execPDO($requete);
endif;

// On récupère la rencontre
$sql = "
  SELECT r.*,
         s.sc_id,
         s.sc_nom,
         ch.scc_id,
         ch.scc_nom
  FROM dd_rencontres r
  LEFT JOIN dd_scenarios s ON r.re_sc_id = s.sc_id
  LEFT JOIN dd_scenarios_chapitres ch ON r.re_scc_id = ch.scc_id
  WHERE r.re_id = :id
";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $re]);
$rencontre = $stmt->fetch(PDO::FETCH_ASSOC);

// actualisation du contenu de la rencontre
include('../include/insert/'.$_SESSION['rulesetRep'].'/detailRencontre.php');

// On ajoute les donnnées dans un tableau
echo $rem."@".$mo."@".$renc."@".$sql;
?>
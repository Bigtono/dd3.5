<?php
session_start();
include_once("../include/dblib.inc.php");
include_once("../include/diverslib.inc.php");

$pcId = isset($_POST['classe']) ? (int)$_POST['classe'] : 0;
$p = isset($_POST['perso']) ? (int)$_POST['perso'] : 0;

if ($pcId > 0):
  $stmtDeleteNls = $db->prepare("DELETE FROM dd_personnages_nls WHERE penl_pc_id_base = :pcid OR penl_pc_id_prestige = :pcid");
  $stmtDeleteNls->execute([':pcid' => $pcId]);

  $stmtDeleteClasse = $db->prepare("DELETE FROM dd_personnages_classes WHERE pc_id = :pcid");
  $stmtDeleteClasse->execute([':pcid' => $pcId]);
endif;

// MAJ de l'affichage des classes
$liste = '';
$requete_cl = '';
if (isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] !== ''):
  $legacyFile = '../include/insert/' . $_SESSION['rulesetRep'] . '/listeClassesPerso.php';
  if (file_exists($legacyFile)):
    include($legacyFile);
  endif;
endif;

echo $p."@".$liste."@".$requete_cl;

?>

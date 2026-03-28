<?php
include("../include/session.php");
include_once("../include/dblib.inc.php");

$perso = isset($_POST['perso']) ? (int)$_POST['perso'] : 0;
$classe = isset($_POST['classe']) ? (int)$_POST['classe'] : 0;
$niveau = isset($_POST['niveau']) ? (int)$_POST['niveau'] : 1;
$last = 0;
$liste = '';
$requete_cl = '';

if ($perso > 0 && $classe > 0):
  if ($niveau < 1) $niveau = 1;

  // Verification doublon par personnage + classe.
  $stmtVerif = $db->prepare("SELECT pc_id FROM dd_personnages_classes WHERE pc_pe_id = :perso AND pc_cla_id = :classe LIMIT 1");
  $stmtVerif->execute([
    ':perso' => $perso,
    ':classe' => $classe,
  ]);

  if (!$stmtVerif->fetch(PDO::FETCH_ASSOC)):
    $stmtInsert = $db->prepare("INSERT INTO dd_personnages_classes (pc_pe_id, pc_cla_id, pc_niveau) VALUES (:perso, :classe, :niveau)");
    $stmtInsert->execute([
      ':perso' => $perso,
      ':classe' => $classe,
      ':niveau' => $niveau,
    ]);
    $last = (int)$db->lastInsertId();
  endif;
endif;

// Compat historique: recharge liste si le fichier existe.
if (isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] !== ''):
  $legacyFile = '../include/insert/' . $_SESSION['rulesetRep'] . '/listeClassesPerso.php';
  if (file_exists($legacyFile)):
    $p = $perso;
    include($legacyFile);
  endif;
endif;

echo $last . "@" . $liste . "@" . $requete_cl;
?>

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$ch = isset($_POST['chapitre']) ? (int)$_POST['chapitre'] : 0;
$action = $_POST['action'] ?? '';

if ($ch <= 0):
  header("Location: campagnes.php");
  exit;
endif;

if ($action == 'cancel'):
  header("Location: chapitre.php?chapitre=" . $ch);
  exit;
endif;

$nom = isset($_POST['mp_scc_nom']) ? trim((string)$_POST['mp_scc_nom']) : '';
$abreviation = isset($_POST['mp_scc_abreviation']) ? trim((string)$_POST['mp_scc_abreviation']) : '';
$description = isset($_POST['mp_scc_description']) ? (string)$_POST['mp_scc_description'] : '';

if ($nom === ''):
  header("Location: chapitre-modifier.php?chapitre=" . $ch . "&msg=nom_vide");
  exit;
endif;

$sqlCheck = "
  SELECT ch.scc_id, sc.sc_id, c.camp_id
  FROM dd_scenarios_chapitres ch
  JOIN dd_scenarios sc ON sc.sc_id = ch.scc_sc_id
  JOIN dd_campagnes c ON c.camp_id = sc.sc_camp_id
  WHERE ch.scc_id = :ch_id
    AND c.camp_j_id = :user_id
";
$stmtCheck = $db->prepare($sqlCheck);
$stmtCheck->execute([
  ':ch_id' => $ch,
  ':user_id' => (int)$_SESSION['user_id'],
]);
$row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$row):
  header("Location: campagnes.php");
  exit;
endif;

$sql = "UPDATE dd_scenarios_chapitres
        SET scc_nom = :nom,
            scc_abreviation = :abreviation,
            scc_description = :description
        WHERE scc_id = :ch_id";
$stmt = $db->prepare($sql);
$stmt->execute([
  ':nom' => $nom,
  ':abreviation' => $abreviation,
  ':description' => $description,
  ':ch_id' => $ch,
]);

$_SESSION['chapitre'] = (int)$row['scc_id'];
$_SESSION['scenario'] = (int)$row['sc_id'];
$_SESSION['campagne'] = (int)$row['camp_id'];

header("Location: chapitre.php?chapitre=" . $ch . "&msg=M");
exit;

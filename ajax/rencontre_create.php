<?
include('../include/dblib.inc.php');

header('Content-Type: application/json');

$chapitre_id = isset($_POST['chapitre_id']) ? (int)$_POST['chapitre_id'] : 0;
$re_nom      = isset($_POST['re_nom']) ? trim($_POST['re_nom']) : '';

if ($chapitre_id > 0 && $re_nom !== ''):

    $stmt = $db->prepare("
    INSERT INTO dd_rencontres (re_nom, re_scc_id)
    VALUES (?, ?)
  ");

    $ok = $stmt->execute([$re_nom, $chapitre_id]);

    echo json_encode(['success' => $ok]);

else:

    echo json_encode([
        'success' => false,
        'debug' => [
            'chapitre_id' => $chapitre_id,
            're_nom' => $re_nom
        ]
    ]);

endif;

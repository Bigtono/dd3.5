<?
header('Content-Type: application/json; charset=utf-8');
include_once("../include/dblib.inc.php");
$pdo = $db;
include_once("../include/session.php");
include_once("../include/affichageSelectionSources.php"); 

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '' || mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$pdo->exec("SET NAMES utf8mb4");

/*
 * Pour ignorer les accents et la casse :
 * - on convertit la colonne et le terme avec COLLATE utf8mb4_general_ci
 *   (CI = Case Insensitive)
 * - pour les accents, on force la comparaison à utf8mb4_unicode_ci ou utf8mb4_general_ci
 */
$sql = "
  SELECT so_id, so_nom
  FROM dd_sorts
  WHERE so_res_id IN ".$selection."
  AND so_nom COLLATE utf8mb4_general_ci LIKE :like COLLATE utf8mb4_general_ci
  ORDER BY LOCATE(:locate, so_nom), so_nom
  LIMIT 5
";

$stmt = $pdo->prepare($sql);
$like = '%' . $q . '%';
$stmt->bindParam(':like',   $like, PDO::PARAM_STR);
$stmt->bindParam(':locate', $q,    PDO::PARAM_STR);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transformer les résultats en format attendu par le JavaScript
$out = array_map(fn($r) => [
  'id'    => $r['so_id'],
  'label' => $r['so_nom'],
], $rows);

echo json_encode($out, JSON_UNESCAPED_UNICODE);
?>
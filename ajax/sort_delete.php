<?
include('../include/dblib_ajax.inc.php');
header('Content-Type: application/json; charset=utf-8');

$p = (int)($_POST['personnage'] ?? 0);
$s = (int)($_POST['sort'] ?? 0);

$db->prepare("
DELETE FROM dd_personnages_sorts
WHERE pes_pe_id = :p AND pes_so_id = :s
")->execute([':p'=>$p, ':s'=>$s]);

echo json_encode(['success'=>true]);
?>
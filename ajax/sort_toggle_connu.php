<?
include('../include/dblib_ajax.inc.php');
header('Content-Type: application/json; charset=utf-8');

$p = (int)($_POST['personnage'] ?? 0);
$s = (int)($_POST['sort'] ?? 0);

$db->prepare("
UPDATE dd_personnages_sorts
SET pes_connu = 1 - pes_connu
WHERE pes_pe_id = :p AND pes_so_id = :s
")->execute([':p'=>$p, ':s'=>$s]);

$val = $db->query("
SELECT pes_connu
FROM dd_personnages_sorts
WHERE pes_pe_id=$p AND pes_so_id=$s
")->fetchColumn();

echo json_encode(['connu'=>$val]);
?>
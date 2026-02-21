<?
include('../include/dblib_ajax.inc.php');
header('Content-Type: application/json; charset=utf-8');

$p = (int)$_POST['personnage'];
$s = (int)$_POST['sort'];

$db->prepare("
UPDATE dd_personnages_sorts
SET pes_memorise = GREATEST(0, pes_memorise - 1), pes_lance = GREATEST(0, pes_lance - 1)
WHERE pes_pe_id = :p AND pes_so_id = :s
")->execute([':p'=>$p, ':s'=>$s]);

$val = $db->query("
SELECT pes_memorise
FROM dd_personnages_sorts
WHERE pes_pe_id=$p AND pes_so_id=$s
")->fetchColumn();

echo json_encode(['memo'=>$val]);
?>
<?
file_put_contents(
  __DIR__.'/debug_ajax.log',
  "POST=".print_r($_POST,true)."\n",
  FILE_APPEND
);

include('../include/dblib_ajax.inc.php');
file_put_contents(
  __DIR__.'/debug_ajax.log',
  "DB OK\n",
  FILE_APPEND
);

header('Content-Type: application/json; charset=utf-8');

/* paramètres */
$pes_id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$delta    = isset($_POST['delta']) ? (int)$_POST['delta'] : 0;
$perso_id = isset($_POST['personnage']) ? (int)$_POST['personnage'] : 0;

if ($pes_id <= 0 || $perso_id <= 0):
  ob_clean();
  echo json_encode(['error'=>'parametres invalides']);
  file_put_contents(
    __DIR__.'/debug_ajax.log',
    "AVANT EXIT - parametres introuvables\n",
    FILE_APPEND
  );
  exit;
endif;

/* récupération sort */
$sql = "
SELECT pes_lance, pes_memorise
FROM dd_personnages_sorts
WHERE pes_id = :id
  AND pes_pe_id = :p
";
$stmt = $db->prepare($sql);
$stmt->execute([
  ':id'=>$pes_id,
  ':p'=>$perso_id
]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row):
  ob_clean();
  echo json_encode(['error'=>'sort introuvable']);
  file_put_contents(
    __DIR__.'/debug_ajax.log',
    "AVANT EXIT - sort introuvable\n",
    FILE_APPEND
  );
  exit;
endif;

/* calcul */
$new = $row['pes_lance'] + $delta;
if ($new < 0) $new = 0;
if ($new > $row['pes_memorise']) $new = $row['pes_memorise'];

/* update */
$stmt = $db->prepare("
UPDATE dd_personnages_sorts
SET pes_lance = :v
WHERE pes_id = :id
");
$stmt->execute([
  ':v'=>$new,
  ':id'=>$pes_id
]);

ob_clean(); // 🔥 supprime TOUS les warnings éventuels
echo json_encode([
  'success'=>true,
  'val'=>$new
]);
?>
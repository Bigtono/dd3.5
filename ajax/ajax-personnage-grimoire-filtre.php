<?
include("../include/session.php");
include_once("../include/insert/common/personnage_grimoire_helper.php");

header('Content-Type: application/json; charset=utf-8');

$personnageId = isset($_POST['personnage']) ? (int)$_POST['personnage'] : 0;
$filterValue = isset($_POST['filtre']) ? (int)$_POST['filtre'] : 0;

if ($personnageId <= 0):
  echo json_encode(['ok' => false]);
  exit;
endif;

pg_magic_set_session_filter($personnageId, $filterValue);
echo json_encode(['ok' => true, 'filtre' => pg_magic_normalize_filter($filterValue)]);


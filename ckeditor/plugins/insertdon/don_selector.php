<?php
header('Content-Type: application/json');
include("../../../include/dblib.inc.php");
include("../../../include/affichageSelectionSources.php");

$requete="SELECT do_id, do_nom FROM dd_dons LEFT JOIN dd_ressources ON do_res_id=res_id WHERE do_res_id IN ".$selection." ORDER BY do_nom"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
if ($num_rows > 0):
  $dons = $result->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($dons);
  else:
  echo json_encode('erreur');
endif;
?>

<?php
header('Content-Type: application/json');
include("../../../include/dblib.inc.php");
include("../../../include/affichageSelectionSources.php");

$requete="SELECT cla_id, cla_nom FROM dd_classes LEFT JOIN dd_ressources ON cla_res_id=res_id WHERE cla_res_id IN ".$selection." ORDER BY cla_clt_id, cla_nom"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
if ($num_rows > 0):
  $classes = $result->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($classes);
  else:
  echo json_encode('erreur');
endif;
?>

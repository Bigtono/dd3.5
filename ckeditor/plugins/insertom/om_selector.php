<?php
header('Content-Type: application/json');
include("../../../include/dblib.inc.php");
include("../../../include/affichageSelectionSources.php");

$requete="SELECT om_id, om_nom FROM dd_objets_magiques LEFT JOIN dd_ressources ON om_res_id=res_id WHERE om_res_id IN ".$selection." ORDER BY om_nom"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
if ($num_rows > 0):
  $om = $result->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($om);
  else:
  echo json_encode('erreur');
endif;
?>

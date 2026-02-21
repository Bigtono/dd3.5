<?php
header('Content-Type: application/json');
include("../../../include/dblib.inc.php");
include("../../../include/affichageSelectionSources.php");

$requete="SELECT cap_id, cap_nom FROM dd_capacites_speciales ORDER BY cap_nom"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
if ($num_rows > 0):
  $capacites = $result->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($capacites);
  else:
  echo json_encode('erreur');
endif;
?>

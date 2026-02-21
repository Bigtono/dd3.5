<?php
header('Content-Type: application/json');
include("../../../include/dblib.inc.php");
include("../../../include/affichageSelectionSources.php");

$requete="SELECT so_id, so_nom FROM dd_sorts LEFT JOIN dd_ressources ON so_res_id=res_id WHERE so_ruleset_var_id='".$_SESSION['ruleset']."' AND so_res_id IN ".$selection." ORDER BY so_nom"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
if ($num_rows > 0):
  //$sorts = $result->fetch(PDO::FETCH_ASSOC);  
  $sorts = $result->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($sorts);
  else:
  echo json_encode('erreur');
endif;
?>

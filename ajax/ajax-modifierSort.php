<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['sort'])):
  include_once($_SESSION['rulesetRep'].'/modifierSort.php');  	
  // On ajoute les donnnées dans un tableau
  echo $dn['so_id']."@".$result;
	else:
	echo "prout";
endif;
?>
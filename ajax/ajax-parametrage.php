<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$p=$_POST['param'];

if(!empty($p)):

// Requête SQL
  $requete = "SELECT valeur FROM dd_parametres WHERE nom='".$p."'";	
  $result=queryPDO($requete);
	$num_rows=$result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  if ($dn['valeur']==0):
    $new=1;
    $icone='<i class="fa-solid fa-toggle-on" onClick="onOff(\''.$p.'\')"></i>';
    else:
    $new=0;
    $icone='<i class="fa-solid fa-toggle-off" onClick="onOff(\''.$p.'\')"></i>';
  endif;
  // MAJ de la base et de la session
  $requete='UPDATE dd_parametres SET valeur="'.$new.'" WHERE nom="'.$p.'"';
  $resultat=execPDO($requete);
  $_SESSION[$p]=$new;
  
  // On ajoute les données dans un tableau
  echo $p."@".$icone;
	else:
	echo "prout";
endif;
?>
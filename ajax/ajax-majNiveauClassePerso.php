<?php
include_once("../include/dblib.inc.php");

if(isset($_POST['classe'])):
  $classe = (int)substr($_POST['classe'],3);
  if ($classe>0):
    $requete = 'UPDATE dd_personnages_classes SET pc_niveau='.$_POST['niveau'].' WHERE pc_id="'.$classe.'"';
    $resultat = execPDO($requete);
    echo $classe."@".$_POST['niveau'];
    else:
    echo "0@Erreur2";
  endif;
	else:
	echo "0@Erreur";
endif;
?>
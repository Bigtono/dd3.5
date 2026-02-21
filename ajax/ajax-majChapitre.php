<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

if(isset($_POST['scenario'])):
  $chapitre='<select name="critere_ch" id="critere_ch">'.optionList("dd_scenarios_chapitres", "scc", "nom", $_SESSION['chapitre'], 'scc_sc_id="'.$_POST['scenario'].'"',0, '', 'scc_ordre').'</select>';
  $_SESSION['scenario']=$_POST['scenario']; // on met le scénario en session
  echo $_POST['scenario']."@".$chapitre;
  else:
	echo "0@Erreur";
endif;
?>
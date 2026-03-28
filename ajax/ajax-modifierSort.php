<?php
include("../include/session.php");
include_once("../include/dblib.inc.php");

if (isset($_POST['sort']) && isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] !== ''):
  include_once($_SESSION['rulesetRep'] . '/modifierSort.php');
  echo $dn['so_id'] . "@" . $result;
else:
  echo "0@Erreur ajax-modifierSort";
endif;
?>

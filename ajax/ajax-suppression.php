<?php
session_start();
include_once("../include/dblib.inc.php");

$table=$_POST['table'];
$prefixe=$_POST['prefixe'];
$id=$_POST['id'];

$requete = "DELETE FROM ".$table." WHERE ".$prefixe."_id='".$id."'";
$resultat = execPDO($requete);

echo $prefixe.$id."@".$requete;

?>

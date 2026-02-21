<?
// connexion MJ
if ($_SESSION['mj']!=1):
  header("location: index.php?cnx=no");
  exit;
endif;
?>
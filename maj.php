<?
session_start();
include_once("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-dons.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
	<DIV id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">

<?
  $requete='SELECT * FROM dons ORDER BY do_nom';
  $result=queryPDO($requete);
  $num_rows=$result->rowCount();
  $nbl=0; // nb de sorts dans la ligne
  if ($num_rows > 0):
    $i=0;
    while($don = $result->fetch(PDO::FETCH_ASSOC)):
      $i++;
      $don['do_nom']=stripslashes($don['do_nom']);
      $don['do_conditions']=stripslashes($don['do_conditons']);  
      $don['do_texte']=stripslashes($don['do_texte']);
      $don['do_resume']=stripslashes($don['do_resume']);
      $requete='UPDATE dons SET do_nom="'.addslashes($don['do_nom']).'", do_conditions="'.addslashes($don['do_conditions']).'", do_texte="'.addslashes($don['do_texte']).'", do_resume="'.addslashes($don['do_resume']).'" WHERE do_id="'.$don['do_id'].'"';
      $resultat=execPDO($requete);
      echo '<div>'.$i.' - Traitement du don '.$don['do_nom'].'</div>';
    endwhile;
  endif;

?>
  </div> <!-- wrapper --->
</div>
</body>
</html>

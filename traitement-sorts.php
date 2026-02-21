<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Traitements des sorts</title>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-sorts.js'></script>
</head>
<body>
<div
<?
/*
$requete="SELECT * FROM sorts"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
$i=0;
if ($num_rows > 0):
  while($dn=$result->fetch(PDO::FETCH_ASSOC)):
    if ($dn['so_pretre']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 9, '.(int)$dn['so_pretre'].')';
      $resultat=execPDO($requete);
    endif;
    if ($dn['so_mage']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 6, '.(int)$dn['so_mage'].')';
      $resultat=execPDO($requete);
    endif;
    if ($dn['so_paladin']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 8, '.(int)$dn['so_paladin'].')';
      $resultat=execPDO($requete);
    endif;
    if ($dn['so_rodeur']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 10, '.(int)$dn['so_rodeur'].')';
      $resultat=execPDO($requete);
    endif;
    if ($dn['so_barde']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 2, '.(int)$dn['so_barde'].')';
      $resultat=execPDO($requete);
    endif;
    if ($dn['so_druide']!=''):
      $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['so_id'].', 3, '.(int)$dn['so_druide'].')';
      $resultat=execPDO($requete);
    endif;
    $requete='UPDATE sorts SET so_modif=1 WHERE so_id='.$dn['so_id'];
    $resultat=execPDO($requete);
    $i++;
  endwhile;
  echo '<div>Traitement terminé. '.$i.' sorts traités</div>';
endif;
/*  Ajout des sorts pour les ensorceleurs
$requete="SELECT * FROM sortclasse WHERE sc_cla_id=6"; 
$result=queryPDO($requete);
$num_rows=$result->rowCount();
$i=0;
if ($num_rows > 0):
  while($dn=$result->fetch(PDO::FETCH_ASSOC)):
     
    $requete='INSERT INTO sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ('.$dn['sc_so_id'].',4,'.$dn['sc_niveau'].')';
    $resultat=execPDO($requete);
    $i++;
  endwhile;
endif;
echo '<div>Traitement terminé. '.$i.' sorts traités</div>';
*/
?>
</body>
</html>
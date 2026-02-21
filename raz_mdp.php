<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");
?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
</HEAD>

<BODY>
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
    <h1>Changement de mot de passe</h1>
    <?
    if (isset($_GET['id']) && $_GET['id']>0):
      $sql='UPDATE joueurs SET j_pass="'.password_hash('Tempo11111', PASSWORD_DEFAULT).'" WHERE j_id="'.$_GET['id'].'"';
      $resultat = execPDO($sql);
      echo "<div>MAJ du mot de passe de ".libelle("joueurs","j","nom",$_GET['id'])."</div>";
      else:
      echo "<div>Probleme ave l'identifiant du joueur";
    endif;
    ?>
  </div>
</div>
</BODY>

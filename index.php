<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
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
    <div class="bienvenue">
		<?
			if ($_SESSION['pseudo']!=""):
				echo '<p class="aucentre wp100">Bonjour <b>'.$_SESSION['pseudo'].'</b> et '.$_SESSION['bienvenue'].'.</p>';
        else:
        echo '<p class="aucentre">'.ucfirst($_SESSION['bienvenue']).'. Veuillez vous <a href="login.php">connecter</a></p>';
			endif;
      
      // Vérification du choix d'un set de règle
			if (empty($_SESSION['ruleset'])):
				echo '<p class="aucentre wp100">Veuillez choisir un set de règles de référence pour bénéficier de toutes les fonctionnalités du site.</p>';
        else:
        echo '<p class="aucentre">Le set de règles actuellement choisi est '.libvar($_SESSION['ruleset']).'</p>';
			endif;
      
      if ($isAdmin==1 && $isDebug):
        echo '<h2>Debug : Cookies actifs</h2>';
        echo dump_array_readable($_COOKIE);
        echo '<h2>Debug : Variables de session</h2>';
        echo dump_array_readable($_SESSION);
      endif;      
  
		?> 
    </div>
  </div>
</div>
</BODY>

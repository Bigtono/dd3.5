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
  <script type='text/javascript' src='include/_styles-session_.css'></script>
</HEAD>
<body>
  
<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
     <?php
    // Vérifier qu'il y a bien des variables de session
    if (empty($_SESSION)) {
        echo "<p>Aucune variable de session active.</p>";
        exit;
    }
    // En-tête de page
    echo "<h2>Liste de toutes les variables de session actives</h2>";
    // Génération du tableau
    echo "<table class='session-table'>";
    echo "<tr><th>Nom de la variable</th><th>Valeur</th></tr>";
    
    $ignore = ['password', 'panneau_lateral'];
    // Parcours de toutes les variables de session
    foreach ($_SESSION as $key => $value) {
        if (in_array($key, $ignore)) continue;
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "</strong></td>";

              // Affichage adapté selon le type
        if (is_array($value) || is_object($value)) {
            echo "<td><pre>" . htmlspecialchars(print_r($value, true), ENT_QUOTES, 'UTF-8') . "</pre></td>";
        } else {
            echo "<td>" . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    ?>
  </div>
</div>
    
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
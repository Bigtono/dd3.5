<? 
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$camp_id = 0;

$campagne = [
  'camp_id'             => 0,
  'camp_nom'            => '',
  'camp_ruleset_var_id' => 0,
  'camp_j_id'           => isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0,
  'camp_resume'         => '',
  'camp_description'    => ''
];

if (isset($_GET['campagne'])):
  $camp_id = $_GET['campagne'];
  if ($camp_id > 0):
    $sql = "SELECT * FROM dd_campagnes WHERE camp_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $camp_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row):
      $campagne = $row;
    endif;
  endif;
endif;

// Droits : MJ ou propriétaire (pour l'édition)
// Pour une création, on autorise tout joueur connecté
$isOwner = isset($_SESSION['user_id']) && ($campagne['camp_j_id'] == $_SESSION['user_id']);
$canEdit = (!empty($_SESSION['mj']) && $_SESSION['mj'] == 1) || $isOwner || ($campagne['camp_id'] == 0);

if (!$canEdit):
  die("Accès refusé.");
endif;

// Récupération des rulesets
// Adapter var_cat = 'ruleset' si besoin
$sqlRulesets = "
  SELECT var_id, var_valeur
  FROM dd_variables
  WHERE var_cat = 'rule'
  ORDER BY var_valeur
";
$rulesets = $db->query($sqlRulesets)->fetchAll(PDO::FETCH_ASSOC);

// Récupération des joueurs
$sqlJoueurs = "
  SELECT j_id, j_pseudo
  FROM dd_joueurs
  WHERE j_visible = 1
  ORDER BY j_pseudo
";
$joueurs = $db->query($sqlJoueurs)->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="fr">
<head>
  <? include("include/head.php"); ?>
</head>

<body>
<div id="page">
  <? include("include/header.php"); ?>
  <? include("include/menu.php"); ?>  

  <div class="wrapper">
    <? include('include/ariane.php'); ?>

    <div class="titreAction">
      <div class="titreA">
        <? echo ($campagne['camp_id'] > 0) ? "Modifier la campagne" : "Nouvelle campagne"; ?>
      </div>
    </div>

    <? debug($camp_id.' : '.$sql); ?>
    
    <form action="campagne-enregistrement.php" method="post">
      <input type="hidden" name="mp_camp_id" value="<?= $camp_id; ?>" />

      <div class="form-group">
        <label for="camp_nom">Nom de la campagne</label>
        <input type="text" name="mp_camp_nom" id="mp_camp_nom" 
               value="<? echo htmlspecialchars($campagne['camp_nom']); ?>" 
               />
      </div>

      <div class="form-group">
        <label for="camp_ruleset_var_id">Set de règles</label>
        <select name="mp_camp_ruleset_var_id" id="mp_camp_ruleset_var_id">
          <? echo optionListVar($campagne['camp_ruleset_var_id'], "rule"); ?>  
        </select>
      </div>

      <div class="form-group">
        <label for="camp_j_id" class="w200">Maître du Jeu</label>
        <? if ($isAdmin): ?>
        <select name="mp_camp_j_id" id="mp_camp_j_id">
          <? foreach ($joueurs as $j): ?>
            <option value="<? echo (int)$j['j_id']; ?>"
              <? if ($campagne['camp_j_id'] == $j['j_id']) echo 'selected'; ?>>
              <? echo htmlspecialchars($j['j_pseudo']); ?>
            </option>
          <? endforeach; ?>
        </select>
        <? else: ?>
          <input type="text" id="mp_camp_j_id" name="mp_camp_j_id" value="<? echo (int)$campagne['camp_j_id']; ?>" />
        <? endif; ?>
      </div>

      <div class="form-group">
        <label for="camp_nom">Résumé</label>
        <input type="text" name="mp_camp_resume" id="mp_camp_resume" 
               value="<? echo htmlspecialchars($campagne['camp_resume']); ?>" />
      </div>
      
      
      <div class="form-group">
        <label for="mp_camp_description">Description</label><br>
        <textarea name="mp_camp_description" id="mp_camp_description" rows="10" cols="80"><? 
          echo htmlspecialchars($campagne['camp_description']); 
        ?></textarea>
        <script>CKEDITOR.replace( 'mp_camp_description' );</script>
      </div>

      <div class="form-group">
      <!-- affichage des boutons --->
      <div class="ligneBouton">
        <button type="submit" name="action" value="save" class="btNoir">Enregistrer</button>
        <button type="submit" name="action" value="cancel" class="btGris">Annuler</button>
      </div>  
      
    </form>

    <p class="mb50">&nbsp;</p>
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page">
      <i class="fas fa-chevron-up"></i>
    </button>    
  </div> <!-- wrapper --->
  <div id="modification"></div>
  <div id="detail-pp"></div>  
</div><!-- page --->
</body>
</html>
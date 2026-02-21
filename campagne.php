<? 
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/pagination.php");
include("include/list_helpers.inc.php");

$c = "";
$campagne = null;
// recherche de la campagne
if (isset($_GET['campagne'])):
  $c = (int)$_GET['campagne'];
  $_SESSION['campagne'] = $c;

  // On récupère la campagne + le MJ + le ruleset
  $sql = "
    SELECT c.*,
           v.var_valeur AS ruleset_nom,
           j.j_pseudo,
           j.j_prenom,
           j.j_nom
    FROM dd_campagnes c
    LEFT JOIN dd_variables v ON v.var_id = c.camp_ruleset_var_id
    LEFT JOIN dd_joueurs j ON j.j_id = c.camp_j_id
    WHERE c.camp_id = :id
  ";
  $stmt = $db->prepare($sql);
  $stmt->execute([':id' => $c]);
  $campagne = $stmt->fetch(PDO::FETCH_ASSOC);
else:
  $_SESSION['campagne'] = "";
endif;
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
    <?
    include('include/ariane.php');
    
    // recherche des personnages
    $listId  = 'campagne';
    include('include/sql/listePersonnages.php');
    debug('total : '.$totalItems." | listeId : ".$listId);

    if (!$campagne):
      
      echo '<div class="nodata">Aucune campagne...</div>';
      else: 
      // Droits : MJ ou propriétaire de la campagne
      $isOwner = isset($_SESSION['user_id']) && ($campagne['camp_j_id'] == $_SESSION['user_id']);
      $canEdit = (!empty($_SESSION['mj']) && $_SESSION['mj'] == 1) || $isOwner;
      ?>

      <div class="titreAction">
        <div class="titreA">
          <? echo htmlspecialchars($campagne['camp_nom']); ?>
          <? if ($canEdit): ?>  
            <a href="campagne-modifier.php?campagne=<? echo (int)$campagne['camp_id']; ?>">
              <i class="fa-solid fa-pen-to-square ml15"></i>
            </a>
          <? endif; ?>
          
        </div>
        <div>
        </div>
      </div>  

      <div id="campagne">
        <div class="campagne-meta">
          <? if (!empty($campagne['ruleset_nom'])): ?>
            <p><strong>Règles :</strong> <? echo htmlspecialchars($campagne['ruleset_nom']); ?></p>
          <? endif; ?>

          <? if (!empty($campagne['j_pseudo'])): ?>
            <p>
              <strong>MJ / propriétaire :</strong> 
              <? echo htmlspecialchars($campagne['j_pseudo']); ?>
              <? if (!empty($campagne['j_prenom']) || !empty($campagne['j_nom'])): ?>
                (<? echo htmlspecialchars(trim($campagne['j_prenom'].' '.$campagne['j_nom'])); ?>)
              <? endif; ?>
            </p>
          <? endif; ?>
        </div>
        
        <div class="campagne-description">
          <? echo nl2br(stripslashes($campagne['camp_resume'])); ?>
        </div>
        
        <? if (!empty($campagne['camp_description'])): ?>
          <div class="campagne-description">
            <? echo nl2br(stripslashes($campagne['camp_description'])); ?>
          </div>
        <? else: ?>
          <p><em>Aucune description n'a encore été saisie pour cette campagne.</em></p>
        <? endif; ?>
      </div> <!-- #campagne ---> 
    
      <?
      debug($sqlData);
      include('include/insert/'.$_SESSION['rulesetRep'].'/listePersonnages.php');
      renderPagination($currentPage, $totalItems, $itemsPerPage, $extraParams);
      ?>   

    <? endif; ?>
    <p class="mb50">&nbsp;</p>
    <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page">
      <i class="fas fa-chevron-up"></i>
    </button>    
  </div> <!-- wrapper --->
  <div id="modification"></div>
  <div id="detail-pp"></div>  
  <? include('include/footer.php'); ?>
</div><!-- page --->
</body>
</html>
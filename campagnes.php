<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");
include("include/pagination.php");
include("include/list_helpers.inc.php");

// 1) CONFIG SPÉCIFIQUE À LA PAGE
// Exemple : liste des campagnes
$listId  = 'campagnes';

// mapping des champs de tri => colonnes SQL
if ($isAdmin) {
  $validSortFields = [
    'campagne' => 'c.camp_nom',
    'joueur'   => 'j.j_nom',
  ];
} else {
  $validSortFields = [
    'campagne' => 'camp_nom',
  ];
}

$defaultSort = 'campagne';

// 2) TRI GLOBAL (commun à toutes les listes)
list($sort, $order, $orderDir, $orderBySql) = getSortParams($validSortFields, $defaultSort);

// 3) PAGINATION (commune à toutes les listes)
$itemsPerPage = getItemsPerPage(); // basé sur $_SESSION['items_par_page']
$currentPage  = getCurrentPage();
$offset       = ($currentPage - 1) * $itemsPerPage;

// 4) REQUÊTES SQL (spécifiques à cette liste)
if ($isAdmin) {
  // COUNT total
  $sqlCount = 'SELECT COUNT(*) 
                 FROM dd_campagnes c
                 WHERE c.camp_ruleset_var_id = :ruleset
                  AND c.camp_j_id = :user_id';
  $stmtCount = $db->prepare($sqlCount);
  $stmtCount->execute([
    ':ruleset' => $_SESSION['ruleset'],
    ':user_id' => $_SESSION['user_id']
  ]);
  $totalItems = (int)$stmtCount->fetchColumn();

  // SELECT paginé
  $requete = 'SELECT 
                    c.camp_id,
                    c.camp_nom,
                    c.camp_j_id,
                    c.camp_resume,
                    j.j_nom AS joueur_nom
                FROM dd_campagnes c
                LEFT JOIN dd_joueurs j ON j.j_id = c.camp_j_id
                WHERE c.camp_ruleset_var_id = :ruleset
                  AND c.camp_j_id = :user_id
                ORDER BY ' . $orderBySql . '
                LIMIT :limit OFFSET :offset';

  $stmt = $db->prepare($requete);
  $stmt->bindValue(':ruleset', $_SESSION['ruleset'], PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
} else {
  // COUNT total pour ce joueur
  $sqlCount = 'SELECT COUNT(*)
                 FROM dd_campagnes
                 WHERE camp_j_id = :user_id
                  AND camp_ruleset_var_id = :ruleset';

  $stmtCount = $db->prepare($sqlCount);
  $stmtCount->execute([
    ':user_id' => $_SESSION['user_id'],
    ':ruleset' => $_SESSION['ruleset'],
  ]);
  $totalItems = (int)$stmtCount->fetchColumn();

  // SELECT paginé
  $requete = 'SELECT 
                    camp_id,
                    camp_nom,
                    camp_resume
                FROM dd_campagnes
                WHERE camp_j_id = :user_id
                  AND camp_ruleset_var_id = :ruleset
                ORDER BY ' . $orderBySql . '
                LIMIT :limit OFFSET :offset';

  $stmt = $db->prepare($requete);
  $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->bindValue(':ruleset', $_SESSION['ruleset'], PDO::PARAM_INT);
  $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// paramètres à conserver dans la pagination
$extraParams = [
  'sort'  => $sort,
  'order' => $order,
];

?>
<!doctype html>

<HEAD>
  <? include("include/head.php"); ?>
</HEAD>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">
          Campagnes
        </div>
        <div><? echo '<a href="campagne-modifier.php?campagne=n" class="ajout_perso lien"><i class="icon fa-solid fa-circle-plus"></i></a>'; ?></div>
      </div>

      <? debug($requete); ?>

      <div class="sortable-list" data-list-id="<?= htmlspecialchars($listId) ?>" data-global-sort="1">
        <div class="list-header">
          <?php
          // classes pour flèches
          $campagneClass = 'col';
          if ($sort === 'campagne') {
            $campagneClass .= ' sort-' . ($orderDir === 'ASC' ? 'asc' : 'desc');
          }

          $joueurClass = 'col';
          if ($isAdmin && $sort === 'joueur') {
            $joueurClass .= ' sort-' . ($orderDir === 'ASC' ? 'asc' : 'desc');
          }
          ?>

          <!-- Icône suppression -->
          <div class="col action-col">
            <i class="fa-solid fa-trash"></i>
          </div>

          <!-- Icône modification -->
          <div class="col action-col">
            <i class="fa-solid fa-pen-to-square"></i>
          </div>

          <div class="<?= $campagneClass ?> campagne-col-nom" data-sort-field="campagne">
            Campagne
          </div>

          <div class="col3 campagne-col-description">
            Description
          </div>
        </div>

        <div class="list-body">
          <?php foreach ($rows as $camp): ?>
            <div class="list-row">

              <!-- Icône suppression -->
              <div class="col action-col">
                <a href="campagnes.php?action=supprimer&id=<?= $camp['camp_id'] ?>"
                  class="action-delete"
                  title="Supprimer">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>

              <!-- Icône modification -->
              <div class="col action-col">
                <a href="campagne-modifier.php?campagne=<?= $camp['camp_id'] ?>"
                  class="action-edit"
                  title="Modifier">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
              </div>

              <div class="col campagne-col-nom">
                <a href="campagne.php?campagne=<?= $camp['camp_id'] ?>" class="ajout_perso lien">
                  <?= htmlspecialchars($camp['camp_nom']) ?>
                </a>
                <div class="list-secondary-meta">
                  <?= htmlspecialchars($camp['camp_resume']) ?>
                </div>
              </div>


              <div class="col3 campagne-col-description">
                <?= htmlspecialchars($camp['camp_resume']) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <? renderPagination($currentPage, $totalItems, $itemsPerPage, $extraParams); ?>

      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div> <!-- wrapper --->
    <? include('include/footer.php'); ?>
  </div><!-- page --->

</body>

</html>

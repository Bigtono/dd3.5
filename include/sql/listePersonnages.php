<?

// mapping des champs de tri => colonnes SQL
if ($isAdmin || $listId==="campagne") {
    $validSortFields = [
        'personnage' => 'p.pe_nom',
        'joueur'   => 'j.j_nom',
    ];
} else {
    $validSortFields = [
        'personnage' => 'pe_nom',
    ];
}

$defaultSort = 'personnage';

// 2) TRI GLOBAL (commun à toutes les listes)
list($sort, $order, $orderDir, $orderBySql) = getSortParams($validSortFields, $defaultSort);

// 3) PAGINATION (commune à toutes les listes)
$itemsPerPage = getItemsPerPage(); // basé sur $_SESSION['items_par_page']
$currentPage  = getCurrentPage();
$offset       = ($currentPage - 1) * $itemsPerPage;

// 4) REQUÊTES SQL (spécifiques à cette liste)
if ($isAdmin || $listId==="campagne") {
    // COUNT total
    if ($listId==="campagne"): // si campagne
      $sqlCount = 'SELECT COUNT(*) 
                 FROM dd_personnages p
                 WHERE p.pe_camp_id = :campaign';
        $stmtCount = $db->prepare($sqlCount);
      $stmtCount->execute([
          ':campaign' => $c,
      ]);
      $totalItems = (int)$stmtCount->fetchColumn();
      else: // sinon c'est Admin
      $sqlCount = 'SELECT COUNT(*) 
                 FROM dd_personnages p
                 WHERE p.pe_ruleset_var_id = :ruleset';
      $stmtCount = $db->prepare($sqlCount);
      $stmtCount->execute([
          ':ruleset' => $_SESSION['ruleset'],
      ]);
      $totalItems = (int)$stmtCount->fetchColumn();
    endif;

    // SELECT paginé
    if ($listId==="campagne"): // si campagne  
      $sqlData = 'SELECT 
                      p.pe_id,
                      p.pe_nom,
                      p.pe_ra_id,
                      p.pe_j_id,
                      j.j_nom AS joueur_nom,
                      r.ra_nom AS race_nom
                  FROM dd_personnages p
                  LEFT JOIN dd_joueurs j ON j.j_id = p.pe_j_id
                  LEFT JOIN dd_races r ON p.pe_ra_id = r.ra_id
                  WHERE p.pe_camp_id = :campaign
                  ORDER BY ' . $orderBySql . '
                  LIMIT :limit OFFSET :offset';
      $stmt = $db->prepare($sqlData);
      $stmt->bindValue(':campaign',$c, PDO::PARAM_INT);
      $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();  
      else: // sinon c'est Admin
      $sqlData = 'SELECT 
                      p.pe_id,
                      p.pe_nom,
                      p.pe_ra_id,
                      p.pe_j_id,
                      j.j_nom AS joueur_nom,
                      r.ra_nom AS race_nom
                  FROM dd_personnages p
                  LEFT JOIN dd_joueurs j ON j.j_id = p.pe_j_id
                  LEFT JOIN dd_races r ON p.pe_ra_id = r.ra_id
                  WHERE p.pe_ruleset_var_id = :ruleset
                  ORDER BY ' . $orderBySql . '
                  LIMIT :limit OFFSET :offset';
      $stmt = $db->prepare($sqlData);
      $stmt->bindValue(':ruleset', $_SESSION['ruleset'], PDO::PARAM_INT);
      $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();
    endif;
} else {
  // COUNT total pour ce joueur
  $sqlCount = 'SELECT COUNT(*)
               FROM dd_personnages p
               WHERE p.pe_j_id = :user_id
                 AND p.pe_ruleset_var_id = :ruleset';

  $stmtCount = $db->prepare($sqlCount);
  $stmtCount->execute([
    ':user_id' => $_SESSION['user_id'],
    ':ruleset' => $_SESSION['ruleset'],
  ]);
  $totalItems = (int)$stmtCount->fetchColumn();

  // SELECT paginé pour ce joueur
  $sqlData = 'SELECT
                p.pe_id,
                p.pe_nom,
                p.pe_ra_id,
                p.pe_j_id,
                r.ra_nom AS race_nom
              FROM dd_personnages p
              LEFT JOIN dd_races r ON p.pe_ra_id = r.ra_id
              WHERE p.pe_ruleset_var_id = :ruleset
                AND p.pe_j_id = :user_id
              ORDER BY ' . $orderBySql . '
              LIMIT :limit OFFSET :offset';

  $stmt = $db->prepare($sqlData);
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

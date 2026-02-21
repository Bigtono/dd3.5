<?
// include/list_helpers.inc.php

/**
 * Récupère le tri à partir de $_GET et renvoie :
 * [$sort, $order, $orderDir, $orderBySql]
 *
 * @param array  $validSortFields  ex: ['nom' => 'm.mon_nom', 'type' => 't.type_nom']
 * @param string $defaultSort      ex: 'nom'
 */
function getSortParams(array $validSortFields, string $defaultSort = 'id'): array
{
    // sort = clé fonctionnelle (nom, joueur, niveau, etc.)
    $sort  = isset($_GET['sort']) ? strtolower($_GET['sort']) : $defaultSort;
    $order = isset($_GET['order']) ? strtolower($_GET['order']) : 'asc';

    if (!isset($validSortFields[$sort])) {
        $sort = $defaultSort;
    }

    $orderDir = ($order === 'desc') ? 'DESC' : 'ASC';

    // colonne SQL correspondant au sort choisi
    $orderBySql = $validSortFields[$sort] . ' ' . $orderDir;

    return [$sort, $order, $orderDir, $orderBySql];
}
?>
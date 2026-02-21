<?php
// include/pagination.php

function getItemsPerPage(): int
{
    if (!empty($_SESSION['items_par_page']) && (int)$_SESSION['items_par_page'] > 0) {
        return (int)$_SESSION['items_par_page'];
    }
    // Valeur par défaut si rien dans le profil
    return 20;
}

function getCurrentPage(): int
{
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    return ($page > 0) ? $page : 1;
}

/**
 * Affiche une pagination simple et réutilisable.
 *
 * @param int   $currentPage   Page actuelle
 * @param int   $totalItems    Nombre total d'enregistrements
 * @param int   $itemsPerPage  Nombre d'items par page
 * @param array $extraParams   Paramètres GET à conserver (tri, filtres, etc.)
 */
function renderPagination(int $currentPage, int $totalItems, int $itemsPerPage, array $extraParams = []): void
{
    $totalPages = (int)ceil($totalItems / $itemsPerPage);
    if ($totalPages <= 1) {
        return; // Pas besoin de pagination
    }

    // Base de l'URL (campagnes.php, monstres.php, etc.)
    $baseUrl = basename($_SERVER['PHP_SELF']);

    echo '<div class="pagination">';

    // Bouton "Précédent"
    if ($currentPage > 1) {
        $prevParams = array_merge($extraParams, ['page' => $currentPage - 1]);
        echo '<a class="page-link prev" href="' . htmlspecialchars($baseUrl . '?' . http_build_query($prevParams)) . '">&laquo;</a>';
    } else {
        echo '<span class="page-link disabled">&laquo;</span>';
    }

    // Liens de pages (simple : on affiche toutes les pages; tu pourras optimiser plus tard)
    for ($p = 1; $p <= $totalPages; $p++) {
        $params = array_merge($extraParams, ['page' => $p]);
        $class  = 'page-link';
        if ($p === $currentPage) {
            $class .= ' active';
            echo '<span class="' . $class . '">' . $p . '</span>';
        } else {
            echo '<a class="' . $class . '" href="' . htmlspecialchars($baseUrl . '?' . http_build_query($params)) . '">' . $p . '</a>';
        }
    }

    // Bouton "Suivant"
    if ($currentPage < $totalPages) {
        $nextParams = array_merge($extraParams, ['page' => $currentPage + 1]);
        echo '<a class="page-link next" href="' . htmlspecialchars($baseUrl . '?' . http_build_query($nextParams)) . '">&raquo;</a>';
    } else {
        echo '<span class="page-link disabled">&raquo;</span>';
    }

    echo '</div>';
}
?>
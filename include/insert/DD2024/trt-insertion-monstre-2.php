<?php
// trt-insertion-monstre.php DD2024

// Variables attendues : $ligne, $monstre, $info_trt
// Utilise également $db (PDO) pour les recherches en base

//global $db;

// Nettoyage basique de la ligne
$ligne = rtrim($ligne);

// Si ligne vide -> on ignore, mais on peut garder l'info
if ($ligne === '') {
    $info_trt = '<div class="mt5">Ligne vide ignorée</div>';
    return;
}

// -----------------------------------------------------------------------------
// CAS SPÉCIAL : "@@@" → début du bloc Caractéristiques
// -----------------------------------------------------------------------------
if (trim($ligne) === '@@@'):
  $mode_caracteristiques = true;
  $buffer_caracteristiques = [];
  $info_trt = 'Début du bloc Caractéristiques';
  return;
endif;

// -----------------------------------------------------------------------------
// SI ON EST EN MODE CARACTÉRISTIQUES
// -----------------------------------------------------------------------------
if ($mode_caracteristiques):

  // Fin du bloc (attention au caractère ¤)
  if (trim($ligne) === '¤¤¤'):
    $mode_caracteristiques = false;
    $monstre .= traiter_bloc_caracteristiques($buffer_caracteristiques);
    $info_trt = 'Fin du bloc Caractéristiques';
    return;
  endif;
  // On stocke les lignes brutes (sans HTML)
  $buffer_caracteristiques[] = trim($ligne);
  return;
endif;

// -----------------------------------------------------------------------------
// CAS SPÉCIAL : "..." → passage en mode Pouvoirs
// -----------------------------------------------------------------------------
if (trim($ligne) === '...') {
    $mode_pouvoirs = true;
    $info_trt = 'Séparateur "..." : passage en mode Pouvoirs';
    return;
}

// -----------------------------------------------------------------------------
// SI ON EST EN MODE POUVOIRS
// -----------------------------------------------------------------------------
if ($mode_pouvoirs):

  // Ligne "$$$" : fin du mode Pouvoirs
  if (trim($ligne) === '$$$'):
    $mode_pouvoirs = false;
    $info_trt = 'Fin du mode Pouvoirs (ligne "$$$")';
    return;
  endif;

  // ---------------------------------------------------------------------------
  // Exception : ligne = mot-clé (tolérant casse + accents)
  // ---------------------------------------------------------------------------
  $mots_cles_bloc = [
    'Actions',
    'Actions légendaires',
    'Actions bonus',
    'Réactions',
    'Traits',
    'Capacités'
  ];

  $ligne_norm = normaliser_chaine($ligne);

  foreach ($mots_cles_bloc as $mot):
    if ($ligne_norm === normaliser_chaine($mot)):

      $monstre .=
        '<div class="blocAction">'.
        htmlspecialchars(trim($ligne)).
        '</div>'."\n";

      $info_trt = 'Ligne Pouvoirs (mot-clé) : '.$ligne_orig;
      return;

    endif;
  endforeach;

  // ---------------------------------------------------------------------------
  // Traitement normal des pouvoirs
  // ---------------------------------------------------------------------------
  $pos = strpos($ligne, '.');

  if ($pos !== false):
    $avant = trim(substr($ligne, 0, $pos));
    $apres = substr($ligne, $pos);

    $ligne_formatee =
      '<strong>'.htmlspecialchars($avant).'</strong>'.
      htmlspecialchars($apres);
  else:
    $ligne_formatee =
      '<strong>'.htmlspecialchars($ligne).'</strong>';
  endif;

  $monstre .= '<div>'.$ligne_formatee.'</div>'."\n";
  $info_trt = 'Ligne Pouvoirs : '.$ligne_orig;
  return;

endif;


/**
 * 1) LISTES DE MOTS-CLÉS
 * ----------------------
 * - $motcles_debut : mots/expressions à détecter en début de ligne
 * - $motcles_interne : mots/expressions à mettre en gras où qu’ils apparaissent 
 */
$motcles_debut = [
    'CA',
    'Pv',
    'Initiative',
    'Vitesse',
    'Compétences',
    'Équipement',
    'Sens',
    'Langues',
    'FP',
    'Immunités',
    'Résistances'
    // ajoute ce que tu veux ici
];

$motcles_interne = [
    'contact',
    'pris au dépourvu',
    'corps à corps',
    // etc. à compléter selon tes besoins
];

// On garde une version "brute" pour l’info debug
$ligne_orig = $ligne;


// -----------------------------------------------------------------------------
// CAS SPÉCIAL : "***" → <hr>
// -----------------------------------------------------------------------------
if (trim($ligne) === '***') {
    $monstre  .= "<hr>\n";
    $info_trt = '<div class="mt5">Séparateur détecté</div>';
    return;
}

// -----------------------------------------------------------------------------
// CAS SPÉCIAL : DONS
// -----------------------------------------------------------------------------
if (preg_match('/^Dons?\s*:\s*(.+)$/ui', $ligne, $m)) {
    $label = $m[0];            // "Dons : xxx"
    $liste = $m[1];            // juste la liste "Alerte, Vigilance, ..."

    $dons = array_map('trim', explode(',', $liste));

    static $stmtDon = null;
    if ($stmtDon === null) {
        $stmtDon = $db->prepare("
            SELECT do_id, do_nom 
            FROM dd_dons 
            WHERE do_ruleset_var_id='".$_SESSION['ruleset']."' AND do_nom = :nom
        ");
    }

    $dons_html = [];
    $debug_parts = [];

    foreach ($dons as $donNom) {
        if ($donNom === '') continue;

        $stmtDon->execute([':nom' => $donNom]);
        $dn = $stmtDon->fetch(PDO::FETCH_ASSOC);

        if ($dn) {
            // Substitution par le span cliquable
            $dons_html[] = '<span onClick="afficherDon('.(int)$dn['do_id'].')" class="lien">'.htmlspecialchars($dn['do_nom']).'</span>';
            $debug_parts[] = $donNom.' → id '.$dn['do_id'];
        } else {
            // Non trouvé : on laisse le texte tel quel
            $dons_html[] = htmlspecialchars($donNom);
            $debug_parts[] = $donNom.' (non trouvé)';
        }
    }

    $ligne_formatee = '<strong>Dons</strong> : '.implode(', ', $dons_html);

    $monstre  .= '<div>'.$ligne_formatee.'</div>'."\n";
    $info_trt = '<div class="mt5"><strong>Ligne "Dons" traitée</strong> : '.implode(' | ', $debug_parts).'</div>';

    return;
}

// -----------------------------------------------------------------------------
// CAS SPÉCIAL : COMPÉTENCES
// -----------------------------------------------------------------------------
if (preg_match('/^Compétences?\s*:\s*(.+)$/ui', $ligne, $m)) {
    $liste = $m[1]; // tout ce qui suit "Compétences :"
    
    // chaque compétence est séparée par une virgule
    $comps = array_map('trim', explode(',', $liste));

    static $stmtComp = null;
    if ($stmtComp === null) {
        $stmtComp = $db->prepare("
            SELECT comp_id, comp_nom 
            FROM dd_competences 
            WHERE comp_ruleset_var_id='".$_SESSION['ruleset']."' AND comp_nom = :nom
        ");
    }

    $comps_html  = [];
    $debug_parts = [];

    foreach ($comps as $compItem) {
        if ($compItem === '') continue;

        // On découpe : [nom_compétence] [modificateur éventuel]
        // ex : "Art de la magie +15"
        //      "Maîtrise des cordes +0 (+2 pour ligoter)"
        if (preg_match('/^\s*(.+?)\s*(\+.*)?$/u', $compItem, $mc)) {
            $nomComp  = trim($mc[1]);                 // ex : "Art de la magie"
            $modifier = isset($mc[2]) ? $mc[2] : '';  // ex : " +15" ou " +0 (+2 pour ligoter)"
        } else {
            // Si pour une raison quelconque ça ne matche pas, on prend tout comme nom
            $nomComp  = trim($compItem);
            $modifier = '';
        }

        // Recherche en base uniquement sur le nom de la compétence (sans le +xx)
        $stmtComp->execute([':nom' => $nomComp]);
        $dn = $stmtComp->fetch(PDO::FETCH_ASSOC);

        if ($dn) {
            // Seul le nom est cliquable, le modificateur est ajouté après en texte
            $htmlItem = 
                '<span onClick="affichercompetence('.(int)$dn['comp_id'].')" class="lien">'.
                    htmlspecialchars($dn['comp_nom']).
                '</span>'.
                htmlspecialchars($modifier);

            $debug_parts[] = $compItem.' → '.$dn['comp_nom'].' (id '.$dn['comp_id'].')';
        } else {
            // Non trouvé : on laisse l’item complet en texte
            $htmlItem = htmlspecialchars($compItem);
            $debug_parts[] = $compItem.' (non trouvé)';
        }

        $comps_html[] = $htmlItem;
    }

    $ligne_formatee = '<strong>Compétences</strong> : '.implode(', ', $comps_html);

    $monstre  .= '<div>'.$ligne_formatee.'</div>'."\n";
    $info_trt = '<div class="mt5"><strong>Ligne "Compétences" traitée</strong> : '.implode(' | ', $debug_parts).'</div>';

    return;
}

// -----------------------------------------------------------------------------
// MOTS-CLÉS EN DÉBUT DE LIGNE
// -----------------------------------------------------------------------------
$ligne_formatee = $ligne;
$mot_debut_trouve = null;

foreach ($motcles_debut as $motCle) {
    // On teste si la ligne commence par ce mot-clé, éventuellement suivi de ":" 
    if (preg_match('/^'.preg_quote($motCle, '/').'\b\s*:?\s*(.*)$/u', $ligne, $m)) {
        $mot_debut_trouve = $motCle;
        $reste = $m[1]; // ce qui vient après le mot-clé (+ ":" éventuel)

        if ($reste !== '') {
            $ligne_formatee = '<strong>'.htmlspecialchars($motCle).'</strong> : '.htmlspecialchars($reste);
        } else {
            $ligne_formatee = '<strong>'.htmlspecialchars($motCle).'</strong>';
        }
        break;
    }
}

// -----------------------------------------------------------------------------
// MOTS-CLÉS INTERNES (si aucun mot en début de ligne n’a été trouvé)
// -----------------------------------------------------------------------------
if ($mot_debut_trouve === null && !empty($motcles_interne)) {
    $ligne_travail = $ligne;

    foreach ($motcles_interne as $motCle) {
        $pattern = '/\b'.preg_quote($motCle, '/').'\b/u';
        $rempl   = '<strong>'.$motCle.'</strong>';
        $ligne_travail = preg_replace($pattern, $rempl, $ligne_travail);
    }

    $ligne_formatee = $ligne_travail;
}

// -----------------------------------------------------------------------------
// Ajout au HTML du monstre et info de debug
// -----------------------------------------------------------------------------
$monstre  .= '<div>'.$ligne_formatee.'</div>'."\n";

?>
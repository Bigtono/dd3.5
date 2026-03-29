# Architecture Globale

## Convention d'affichage (largeur)

- **Mode normal**: largeur d'affichage **>= 992px**
- **Mode responsive**: largeur d'affichage **<= 991px**

Cette convention sert de vocabulaire commun pour les échanges et les futurs travaux d'architecture.

## Conventions UI Transverses

### Blocs repliables
- Déclencheur standard: bouton burger `fa-bars`
- Mécanisme standard: `togglePlus('<id_bloc>')`
- Conteneur masqué/affiché: `accordion-content noDisplay`
- Zone de contenu: `box-data`
- Style validé commun (gris perle):
  - fond `#f3f3ef`
  - bordure `#e2e2dd`
  - rayon `0.35rem`
  - padding `10px`

### Alignement des actions globales
- Les contrôles "sélection + action" sont placés dans un conteneur flex.
- Le bouton d'action est aligné à droite du select.
- Les hauteurs doivent être harmonisées (référence actuelle: `24px`).

## Conventions Overlay Consultation/Edition

- `detail-pp` = consultation
- `modification` = formulaire d'édition superposé
- Fermer `modification` n'implique pas fermer `detail-pp`
- Après sauvegarde impactant la liste: rafraîchir la liste + réafficher le détail à jour

Voir `doc/ARCHITECTURE_DETAIL_PP_MODIFICATION.md` pour les flux détaillés et les mécanismes JS (dont `reload + reopen`, stockage de contexte et callbacks par page).

## Conventions Multi-Ruleset

- Tronc commun: contrôleurs/auth/DB/pagination/helpers
- Variantes: templates spécifiques ruleset
- Contrat minimal template: rendu HTML uniquement, pas d'auth/session/redirection
- Sélection ruleset via whitelist stricte de `rulesetRep`

Voir `doc/ARCHITECTURE_RULESETS.md` pour le contrat détaillé, la structure de dossiers, les check-lists et les flux d'ajout d'écran multi-ruleset.

## Sécurité Applicative (Socle)

- Contrôle d'accès avant tout write
- Requêtes SQL préparées (`prepare/execute`)
- Échappement HTML systématique (`htmlspecialchars`) hors contenu riche explicitement maîtrisé

## Répartition des responsabilités documentaires

### Ce qui reste dans `ARCHITECTURE_DETAIL_PP_MODIFICATION.md`
- Détails d'implémentation JS spécifiques (`sessionStorage`, propagation de contexte, callbacks)
- Cas décisionnels spécifiques notes (`notes.php`, `personnage-connaissances.php`)

### Ce qui reste dans `ARCHITECTURE_RULESETS.md`
- Détails opérationnels du découpage ruleset
- Contrats d'entrées/sorties des templates ruleset
- Check-list de revue et flux d'ajout d'un écran multi-ruleset

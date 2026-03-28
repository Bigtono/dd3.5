# Architecture - detail-pp / modification

## Objectif
Definir un cadre unique pour les flux:
- affichage de donnees dans `detail-pp`,
- edition dans `modification`,
- comportement de fermeture/sauvegarde,
- coherence visuelle des blocs repliables (burger).

Ce document complete `doc/ARCHITECTURE_COMMIT_GLOBAL.md`.

## Regles Fonctionnelles
1. `detail-pp` sert a afficher/consulter une donnee.
2. `modification` sert a afficher un formulaire d edition.
3. `modification` s affiche au-dessus de `detail-pp` (superposition), sans fermer `detail-pp`.
4. Fermer `modification` (Annuler) ne ferme pas `detail-pp`.
5. Si une sauvegarde modifie des donnees visibles dans `detail-pp`, ce contenu doit etre rafraichi.
6. Si la sauvegarde impacte aussi la liste de la page principale, la liste doit etre rafraichie egalement.

## Comportement Technique
1. Affichage lecture:
- endpoint AJAX de lecture -> `actualiserPage(...)` -> rendu dans `#detail-pp`.
2. Affichage formulaire:
- endpoint AJAX de modif -> `actualiserPageModif(...)` -> rendu dans `#modification`.
- `actualiserPageModif(...)` ne doit pas masquer `#detail-pp`.
3. Pages de type `*-modifier.php`:
- edition locale (JS/DOM),
- persistance au commit global uniquement (`*-enregistrement.php`),
- pas d ecriture AJAX immediate.
4. Pages classiques (ex: `notes.php`, `personnage-connaissances.php`):
- edition via popup `modification`,
- apres validation: mode `reload + reopen detail-pp` si la liste peut etre impactee.
5. Reopen apres reload (notes):
- conservation d un contexte de note (`noteId`, `accreditation`, `perso`, `sourcePage`) en `sessionStorage`,
- reouverture automatique du detail apres rechargement.
6. Propagation du contexte:
- les actions "modifier" depuis `detail-pp` doivent passer au minimum le `perso` quand il existe.

## Regles UI / Design
1. Blocs repliables:
- utiliser le burger existant (`fa-bars`) avec `togglePlus('<id_bloc>')`,
- contenu dans `div` de type `accordion-content noDisplay`.
2. Bloc de contenu toggle:
- utiliser une structure `box-data` pour la zone affichee/masquee.
3. Rendu visuel valide (test approuve):
- bloc diffusion note: fond gris perle leger (`#f3f3ef`), bordure legere (`#e2e2dd`), rayon `0.35rem`, padding `10px`.
- bloc tags note: meme rendu que diffusion.
4. Controle global + action:
- pour les actions "appliquer a tous", le bouton est aligne a droite du select dans un conteneur flex.
- hauteur harmonisee avec le select (reference actuelle: `24px`).

## Cas Notes (Decision actee)
1. Dans `notes.php` et `personnage-connaissances.php`:
- ouvrir une note -> `detail-pp`,
- cliquer modifier -> `modification`,
- valider -> fermer `modification`, recharger page, reouvrir `detail-pp` sur la note mise a jour.
2. Ce mode evite les desynchronisations de liste (checkbox actions, tri, tags, filtres).

## Checklist de Conformite
1. Le flux lecture/modif respecte bien la superposition `detail-pp` -> `modification`.
2. Aucun bouton Annuler de modif ne ferme `detail-pp`.
3. Les pages liste impactees sont rafraichies apres sauvegarde.
4. Le detail est reaffiche automatiquement avec donnees a jour.
5. Les nouveaux blocs burger suivent le rendu gris perle valide.

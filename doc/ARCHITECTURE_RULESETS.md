# Architecture rulesets DD3.5 / DD2024

## Objectif
L'application gère deux versions de règles (DD3.5 et DD2024) avec des variations métier réelles.
Le découpage par templates est une stratégie voulue, pas une dette technique.

## Principe d'architecture
- Tronc commun: pages contrôleurs (`campagne.php`, `scenario.php`, etc.), auth, connexion DB, pagination, helpers.
- Variantes ruleset: vues/partials métier dans `include/insert/DD3.5/` et `include/insert/DD2024/`.
- Sélection dynamique: via `$_SESSION['rulesetRep']` (ex: `DD3.5`, `DD2024`).

## Contrat template ruleset
Chaque template ruleset doit respecter le même contrat:

1. Entrées attendues
- Variables fournies par la page appelante (ex: `$db`, `$camp_id`, `$rows`, `$currentPage`).
- Session active avec `$_SESSION['user_id']`, `$_SESSION['ruleset']`, `$_SESSION['rulesetRep']`.

2. Sorties attendues
- Rendu HTML uniquement (pas de `header()` / `exit`).
- Pas de redirection ni gestion de session dans les templates.

3. Responsabilités interdites dans un template
- Authentification/autorisation.
- Décision du ruleset actif.
- Initialisation PDO.

4. Responsabilités autorisées
- Rendu des champs spécifiques au ruleset.
- Mapping d'affichage propre au ruleset.

## Sécurité (obligatoire pour les 2 rulesets)
- Auth + droits doivent être contrôlés dans le contrôleur (ou endpoint AJAX) avant inclusion template.
- Requêtes SQL en préparé (`prepare/execute`) côté tronc commun et côté logique spécifique.
- Interdire les includes arbitraires: whitelist stricte des valeurs possibles de `rulesetRep`.
- Échapper systématiquement la sortie HTML (`htmlspecialchars`), sauf contenu riche explicitement maîtrisé.
- Prévoir un token CSRF pour formulaires POST et endpoints AJAX sensibles.

## Pattern recommandé par écran
1. Contrôleur (tronc commun)
- Charge session + DB + contrôle d'accès.
- Récupère données communes.
- Valide ruleset (`DD3.5` ou `DD2024`).
- Inclut le template ruleset correspondant.

2. Template ruleset
- Rend uniquement les différences DD3.5/DD2024.

3. Traitement (POST/AJAX)
- Contrôle auth/droits.
- Validation inputs.
- SQL préparé.
- Retour unifié (redirect ou JSON).

## Ajout d'un nouvel écran multi-ruleset
1. Créer le contrôleur commun.
2. Créer:
- `include/insert/DD3.5/<ecran>.php`
- `include/insert/DD2024/<ecran>.php`
3. Vérifier que les deux templates respectent le même contrat d'entrées.
4. Ajouter tests manuels minimum sur les deux rulesets:
- lecture,
- création/modification/suppression,
- contrôle d'accès.

## Bonnes pratiques de nommage
- Conserver des noms de fichiers identiques entre DD3.5 et DD2024 pour le même rôle.
- Garder la logique SQL lourde hors template quand possible.
- Centraliser le code partagé avant de spécialiser ruleset.

## Check-list revue de code
- Le contrôleur valide-t-il `rulesetRep` via whitelist?
- Les droits sont-ils testés avant tout write?
- Le template évite-t-il la logique d'auth/session?
- Les requêtes sont-elles préparées?
- Le rendu est-il échappé?
- Le comportement DD3.5 et DD2024 est-il cohérent sur le flux utilisateur?

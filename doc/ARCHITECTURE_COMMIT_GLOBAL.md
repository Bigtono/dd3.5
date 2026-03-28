# Standard Architecture Codex - Commit Global

## Objectif
Uniformiser les pages d'edition pour eviter les regressions et garantir une persistance coherente.

## Regles
1. Une page `*-modifier.php` fait de l'edition locale uniquement (DOM/JS), sans ecriture BD immediate.
2. Toute persistance passe par un commit global unique dans `*-enregistrement.php`.
3. Les endpoints AJAX servent a afficher des donnees (`detail-pp`) ou charger des options, pas a ecrire (dans un flux formulaire global).
4. `detail-pp` est reserve a la consultation et a la previsualisation.
5. `modification` est reserve aux formulaires de saisie/modification.
6. Le formulaire `modification` s'affiche par-dessus `detail-pp`; a la fermeture, `detail-pp` reste visible.
7. Les actions UI (ajout/suppression/modification de ligne) sont stockees dans un etat JS local, puis serialisees en champs hidden.
8. Validation metier obligatoire cote serveur (ruleset, bornes de niveau, doublons, coherence des IDs).
9. Ecriture serveur en transaction: mise a jour fiche + operations liees + commit/rollback.
10. Les cas non bloquants (exemple: NLS incomplet) doivent etre explicitement definis, puis signales en UI.
11. Les endpoints AJAX historiques peuvent rester pour compatibilite mais ne doivent plus etre utilises par les pages migrees en commit global.
12. Toute nouvelle feature doit definir avant dev: source de verite, payload POST, validations, gestion d'erreur.
13. Nommage POST stable (`mp_*`) et mapping DB documente.
14. Pas de logique metier dupliquee entre JS et PHP: JS pour l'ergonomie, PHP pour l'autorite.
15. Toute exception a ces regles doit etre documentee dans `doc/` avant implementation.

## Contrat de mise en oeuvre
1. UI: l'utilisateur peut enchainer plusieurs modifications avant submit.
2. Submit: une seule requete applique l'ensemble des changements.
3. Relecture: apres commit, la fiche doit refleter l'etat final sans ecart.

## Checklist rapide avant merge
1. Aucun write AJAX actif dans la page `*-modifier.php`.
2. Payload hidden complet et coherent au submit.
3. Validations serveur couvrent les cas invalides.
4. Transaction globale active sur `*-enregistrement.php`.
5. Message utilisateur clair en cas d'erreur.

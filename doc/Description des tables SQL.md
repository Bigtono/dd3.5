Description des tables de ma base.
Version 3

Informations sur la construction de la base :
- toutes les tables sont préfixées par dd. Les préfixes sont suivis du caractère _ pour les différencier du reste du nom des tables. Exemple : dd_personnages.
- Tous les champs sont préfixés afin d'éviter les homonymies. Par exemple la table dd_personnages a comme préfixe "pe" et la table dd_classes a comme préfixe "cla". Les préfixes sont suivis du caractère _ pour les différencier du reste du nom du champ. Exemple : pe_nom est le champ nom de la table personnage préfixée par pe.
- tous les champs de la base sont donc distincts, il n'y aucun doublons sur les noms des champs. 
- par défaut (il y a quelques exceptions que je n'ai pas encore corrigé), à l'exception du premier champ qui est systématiquement l'id index de la table, si un nom de champ contient le mot-clé id, il renvoie alors à l'id index d'une autre table en reprenant la syntaxe [préfixe de la table]_id. Ex : pe_ra_id est le champ contenant l'id de la race du personnage (table dd_races, préfixée avec ra).
- pour les tables les plus complexes, seuls les champs principaux sont décrits. Les autres champs seront ajoutés à la liste au fur et à mesure des besoins en développement

Listes des principales tables
-----------------------------

dd_caracteristiques (liste des 6 caractéristiques que possède chaque personnage. Préfixe car)
- car_id (id, index de la table)
- car_nom (nom de la caractéristique)
- car_diminutif (abréviation de la caractéristique)
Note : la valeur de car_diminutif est constituée des trois premières lettres du nom de la caractéristique. Le jeu comptes six caractéristiques (Force, Constitution, Dextérité, Intelligence, Sagesse, Charisme). Dans la table dd_personnages, la valeur chaque valeur de caractéristique du personnage (de 1 à 30) est stocké dans un champ nommé "pe_" suivi de la valeur car_diminutif. Exemple : pe_for pour force, pe_dex pour dextérité. Dans la table dd_classes, le champ cla_car_id contient l'id de la caractéristique principale de lanceur de sort si cette classe permet de lancer des sorts (cla_mag_id supérieur à 0).

dd_classes (liste des classes, préfixe cla)
- cla_id (id, index de la table) 
- cla_nom (nom du sort)
- cla_clt_id (type de la classe. Par défaut 1 = classe de base. Certains rulesets introduisent d'autres types de classe)
- cla_abreviation
- cla_dV
- cla_pointsCompetences (Ruleset DD3.5 uniquement)
- cla_alignement
- cla_car_id (id de la caractéristique de lanceur de sort, table dd_caracteristiques. Par défaut 0, supérieur à 0 uniquement sur cla_mag_id supérieur à 0)
- cla_po_niveau1 (ruleset DD3.5)
- cla_conditions (ruleset DD3.5 : conditions à remplir pour pouvoir choisir cette classe de prestique)
- cla_description
- cla_traits
- cla_mag_id (id du type de magie, table dd_typeMagie. Indique si la classe permet de lancer des sorts profanes des sorts divins, ou par défaut aucun sort 0)
- cla_connu (non nul si cla_mag_id>0 : indique si le lanceur a accès par défaut à toute sa liste de sorts de classe ou non)
- cla_compris (non nul si cla_mag_id>0 : indique si le lanceur comprend par défaut tous les sorts auxquels il a accès ou s'il doit les apprendre)
- cla_prepare (non nul si cla_mag_id>0 : indique si le lanceur doit préparer les sorts avant de lancer ou s'il peur les lancer sans préparation préalable)
- cla_domaine_divin (1 si la classe donne accès aux domanes divins, sinon 0)
- cla_niveauMax (nombre de niveaux que possède la classe. Selon les types de classes et les rulesets, cla_niveauMax peut avoir les valeurs 3, 5, 10 ou 20)
- cla_critere_rec
- cla_cla_id
- cla_caracteristiques
- cla_armes (pour le ruleset DD305 : contient aussi les armures)
- cla_armures (ruleset DD2024)
- cla_outils
- cla_competences
- cla_sauvegardes
- cla_equipement
- cla_sorts
- cla_pouvoir1 (intitulé du pouvoir 1 de la classe. Ruleset DD204 uniquement)
- cla_pouvoir2 (intitulé du pouvoir 2 de la classe. Ruleset DD204 uniquement)
- cla_pouvoir3 (intitulé du pouvoir 3 de la classe. Ruleset DD204 uniquement)
- cla_pouvoir4 (intitulé du pouvoir 4 de la classe. Ruleset DD204 uniquement)
- cla_res_id (id du livre/supplément dont est issu la classe. Table dd_ressources)
- cla_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)


dd_races (liste des races, préfixe ra)
- ra_id (id, index de la table)
- ra_nom (nom de la race)
- ra_rat_id (id du type de race, table dd_race_type. Indique si la race est une race de base ou un archetype. 1 : race de base; 2 : archétype)
- ra_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)
Note : un personnage a forcément une race indiquée dans le champ 

dd_race_type (liste des types de race. Préfixe rat)
- rat_id (id, index de la table)
- rat_nom (nom du type de race)
- rat_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_classe_niveau (description des niveau de chaque classe)
- cn_id (id, index de la table)
- cn_cla_id (id de la classe, table dd_classes)
- cn_niveau (int, valeur de 1 à n, n égal à la valeur cla_niveauMax de la classe dans la table dd_classes)
- cn_bba (varchar, bonus de base à l'attaque)
- cn_reflexes (int, bonus au jet de sauvegarde de réflexes)
- cn_vigueur (int, bonus au jet de sauvegarde de vigueur)
- cn_volonte (int, bonus au jet de sauvegarde de volonté)
- cn_sortConnu_n0 (int, ruleset DD3.5 : nombre de sorts du niveau 0 connus)
- cn_sortConnu_n1 (int, ruleset DD3.5 : nombre de sorts du niveau 1 connus)
- cn_sortConnu_n2 (int, ruleset DD3.5 : nombre de sorts du niveau 2 connus)
- cn_sortConnu_n3 (int, ruleset DD3.5 : nombre de sorts du niveau 3 connus)
- cn_sortConnu_n4 (int, ruleset DD3.5 : nombre de sorts du niveau 4 connus)
- cn_sortConnu_n5 (int, ruleset DD3.5 : nombre de sorts du niveau 5 connus)
- cn_sortConnu_n6 (int, ruleset DD3.5 : nombre de sorts du niveau 6 connus)
- cn_sortConnu_n7 (int, ruleset DD3.5 : nombre de sorts du niveau 7 connus)
- cn_sortConnu_n8 (int, ruleset DD3.5 : nombre de sorts du niveau 8 connus)
- cn_sortConnu_n9 (int, ruleset DD3.5 : nombre de sorts du niveau 9 connus)
- cn_sort_n0 (int, nombre de sorts disponibles par jour pour le niveau 0)
- cn_sort_n1 (int, nombre de sorts disponibles par jour pour le niveau 1)
- cn_sort_n2 (int, nombre de sorts disponibles par jour pour le niveau 2)
- cn_sort_n3 (int, nombre de sorts disponibles par jour pour le niveau 3)
- cn_sort_n4 (int, nombre de sorts disponibles par jour pour le niveau 4)
- cn_sort_n5 (int, nombre de sorts disponibles par jour pour le niveau 5)
- cn_sort_n6 (int, nombre de sorts disponibles par jour pour le niveau 6)
- cn_sort_n7 (int, nombre de sorts disponibles par jour pour le niveau 7)
- cn_sort_n8 (int, nombre de sorts disponibles par jour pour le niveau 8)
- cn_sort_n9 (int, nombre de sorts disponibles par jour pour le niveau 9)
- cn_niveauSortArcane (ruleset DD3.5 : classe de prestige, modificateur au NLS pour une classe de lanceur de sort profane)
- cn_niveauSortDivin (ruleset DD3.5 : classe de prestige, modificateur au NLS pour une classe de lanceur de sort divin)
- cn_niveauSortEffectif (ruleset DD3.5 : classe de prestige, modificateur au NLS pour une classe de lanceur de sort profane ou divin, au choix)
- cn_pouvoir1 (varchar)
- cn_pouvoir2 (varchar)
- cn_pouvoir3 (varchar)
- cn_pouvoir4 (varchar)
- cn_sortPrepare (int, ruleset DD2024 : nombre de sorts préparés par jour)

dd_sorts (contient la description de tous les sorts du jeu. Préfixe so)
- so_id (id, index de la table)
- so_nom (nom du sort)
- so_res_id (id de la ressource, table dd_resssources)
- so_co_id (id du collège, table dd_colleges)
- so_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_sortclasse (liste des sorts pour chaque classe. Préfixe sc)
- sc_id (id, index de la table)
- sc_so_id (id du sort, table dd_sorts)
- sc_cla_id (id de la classe, table dd_classes 
- sc_niveau (niveau auquel la classe peut lancer ce sort, de 0 à 9)
Note : une classe peut avoir plusieurs sort mais une seule fois le même sort

dd_personnages (liste des personnages. Préfixe pe)
- pe_id (id, index de la table)
- pe_nom (nom du personnage)
- pe_ra_id (id de la race du personnage. Table dd_races. Uniquement un enregistrement dont le champ ra_rat_id égal à 1)
- pe_arc_id (DD3.5 uniquement : id de l'archétype personnage. Table dd_races. Uniquement un enregistrement dont le champ ra_rat_id égal à 2. L'archétype est optionnel, le champ par défaut est égal à 0 et indique que le personnage n'a pas d'archétype? Pour DD2024, le champ est toujours égal à 0)
- pe_for (caractéristique de Force)
- pe_con (caractéristique de Constitution)
- pe_dex (caractéristique de Dextérité)
- pe_int (caractéristique d'Intelligence)
- pe_sag (caractéristique de Sagesse)
- pe_cha (caractéristique de Charisme)
- pe_ca (classe d'armure)
- pe_pv (points de vie)
- pe_background
- pe_notes
- pe_notes_mj
- pe_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)
- pe_camp_id
- pe_j_id


dd_personnages_classes (affectation d'une classe au personnage avec un niveau. Préfixe pc)
- pc_id (id, index de la table)
- pc_pe_id (id du personnage, table dd_ personnages)
- pc_cla_id (id de la classe, table dd_classes)
- pc_niveau (niveau du personnage dans cette classe)
- pc_do_id_1 (id du premier domaine divin. Uniquement si cla_mag_id=2 et cla_domaine_divin=1 pour la classe concernée)
- pc_do_id_2 (id du deuxième domaine divin. Uniquement si cla_mag_id=2 et cla_domaine_divin=1 pour la classe concernée)
Note : un personnage peut avoir plusieurs classes mais une seule fois la même classe

dd_personnages_sorts (contient la liste des sorts possédés par le personnage. Préfixe pes)
- pes_id (id, index de la table)
- pes_pc_id (id de l'affectation de la classe, table dd_personnages_classes)
- pes_so_id (id du sort, table dd_sorts)
- pes_connu (valeur 0/1 indiquant si le sort est disponible pour le personnage. Pertinent si cla_sort_connus=0 dans dd_classes)
- pes_favori (valeur 0/1, indique si le sort a été ajouté à la liste des sorts favoris du personnage. Pertinent si cla_sort_connu=1 dans dd_classes)
- pes_compris (valeur 0/1, indique si le sort est compris par le personnage. Pertinent si cla_sort_compris=0 dans dd_classes)
Note : un personnage peut avoir plusieurs sorts mais une seule fois le même sort

dd_personnages_sorts_prepares (liste des sorts préparés par le personnage. préfixe pesp)
- pesp_id (id, index de la table)
- pesp_pe_id (id du personnage, table dd_personnages)
- pesp_cla_id (id de la classe de base de lanceur de sorts, table dd_classes)
- pesp_so_id (id du sort, table dd_sorts)
- pesp_metamagie (DD3.5 : liste des id des dons de métamagie appliqués au sort, séparés par une virgule)
- pesp_niveau (DD3.5 : niveau effectif du sort après application du don de métamagie)
- pesp_nb (DD3.5 : Nombre de fois que le sort a été préparé. DD2024 : 0/1 indique si le sort a été préparé)
Note : pour un personnage donné, les sorts de cette liste sont forcément dans dd_personnages_sorts. Un personnage ne peut pas lancer un sort auquel il n'a pas accès


dd_personnages_nls (affectation des modificateurs de niveaux de lanceur de sort des classes de prestige, préfixe penl) (spécifique au ruleset DD3.5)
- penl_id (id, index de la table)
- penl_pc_id_base (id de l'enregistrement de la classe de personnage possédé la personnage, table dd_personnages_classes)
- penl_pc_id_prestige (id de la classe de prestige dont le niveau de lanceur de sort doit être attribué, table dd_classes, cla_clt_id=2)
- penl_niveau (niveau de la classe prestige. Permet de ventiler les choix du joueur pour chaque niveau possédé dnas la classe de prestige)

dd_personnages_competences (compétences du perosnnage)
- pec_id (id, indexd de latable)
- pec_pe_id (id du personnage, table dd_personnages)
- pec_comp_id (id de la compétence, table dd_competences)
- pec_maitrise (DD3.5 : valeur numérique correspondant au degré de maitrise du perosnnage. DD2024 : maitrise de la compétence >> 0 = pas de maitrise, 1 = maitrise de la compétence, 2 = expertise de la compétence)


dd_ressources (livres de règles, aussi appelés ressources, dont sont issus les sorts. Préfixe res)
- res_id (id, index de la table)
- res_nom (nom du livre)
- res_abreviation (abreviation du nom du livre)
- res_selection (indique si le livre est utilisé par le maitre de jeu. O : non, 1 : oui)
- res_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_colleges (liste des collèges de magie auxquels appartiennent les sorts. Préfixe co)
- co_id (id, index de la table)
- co_nom (nom du collège)

dd_typeMagie (liste des types de magie. Préfixe mag)
- mag_id (id, index de la table)
- mag_nom (nom du type de magie)
- mag_abreviation (abréviation du type de magie)
- mag_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_modificateurs (liste des modificateurs des caractéristiques. Préfixe mod)
- mod_id (id, index de la table)
- mod_carac (valeur de la caractéristique, de 1 à 30)
- mod_modificateur (modificateur de caractéristique)
- mod_bonusSort0 (nombre de sort bonus de niveau 0 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort1 (nombre de sort bonus de niveau 1 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort2 (nombre de sort bonus de niveau 2 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort3 (nombre de sort bonus de niveau 3 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort4 (nombre de sort bonus de niveau 4 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort5 (nombre de sort bonus de niveau 5 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort6 (nombre de sort bonus de niveau 6 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort7 (nombre de sort bonus de niveau 7 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort8 (nombre de sort bonus de niveau 8 que le personnage peut mémoriser s'il est un lanceur de sort)
- mod_bonusSort9 (nombre de sort bonus de niveau 9 que le personnage peut mémoriser s'il est un lanceur de sort)

dd_campagnes (liste des campagnes. Chaque campagne est rattaché à un joueur qui en est donc le MJ. Chaque campagne est rattaché à un set de règles.)
- camp_id (id, index de la table)
- camp_nom (nom de la campagne)
- camp_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)
- camp_j_id (id du joueur propriétaire de la campagne, le MJ. Issu de la table dd_joueurs)
- camp_resume (résumé présentant succinctement la campagne)
- camp_description (description plus étendue de la campagne)

dd_scenarios (liste des scenarios. Chaque scénario est rattaché à une campagne)
- sc_id (id, index de la table)
- sc_nom (nom du scénario)
- sc_ordre (ordre chronologique du scénariorio dans la campagne)
- sc_description (description du scénario)
- sc_camp_id (id de la campagne à laquelle le scénario appartient. Issu de la table dd_campagnes)
- sc_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_scenarios_chapitres (liste des chapitres. Chaque chapitre appartient à un scenario)
- scc_id (id, index de la table)
- scc_ordre (ordre dans lequel les chapitres se déroulent)
- scc_sc_id (id du scénario auquel appartient le chapitre, issu de la table dd_scenarios)
- scc_nom (nom de la campagne)
- scc_abreviation (préfixe composé d'une lettreombre permettant d'identifier le scénario et son chapitre. Exemple : D12. Valeur optionnelle).
- scc_description (résumé du chapitre)

dd_rencontres (liste des rencontres. Chaque rencontre est rattaché à un chapitre)
- re_id (id, index de la table)
- re_nom (nom de la rencontre)
- re_code (valeur numérique, qui concaténé avec ssc_abreviation de la table dd_scenarios_chapitres, permet de générer une identification unique de la rencontre. Valeur optionnelle)
- re_scc_id (id du chapitre auquel la rencontre est rattachée. Issu de la table dd_scenarios_chapitres. Valeur optionnelle. Si une rencontre n'est pas rattachée à un chapitre, elle est considérée comme orpheline et gérée dans la page rencontres.php)
- re_description (descriptio de la rencontre)

dd_rencontres_monstres (affectation de monstres à une rencontre)
- rem_id (id, index de la table)
- rem_re_id (id de la rencontre. Issu de la table dd_rencontres)
- rem_mo_id (id du monstre. issu de la table dd_monstres)
- rem_effectif (effectif des monstres dans la rencontre. valeur numérique. par défaut 1)
- rem_j_id (id du joueur ayan saisi le monstre. Valeur optionelle. Si la valeur est renseignée, seul ce joueur voit le monstre dans la liste des monstres et le menu permettant d'affecter un mnstre à une rencontre. Un joueur disposant des droits d'admin voit tout)

dd_monstres (liste des monstres)
- mo_id (id, index de la table)
- mo_nom (nom du monstre)
- mo_mocat_id (id de la catégorie du monstre. Issu de la table d_monstres_categories)
- mo_stats (bloc de texte exposant les spécificités du monstre)
- mo_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)
- mo_fp_id (facteur de puissance du monstre, valeur alphanumérique)
- mo_mogr_id (id du groupe auquel appatient le monstre. Issu de la table dd_montres_groupes)
- mo_j_id (id du joueur ayan saisi le monstre. Issu de la table dd_joueurs Valeur optionelle. Si la valeur est renseignée, seul ce joueur voit le monstre dans la liste des monstres et le menu permettant d'affecter un mnstre à une rencontre. Un joueur disposant des droits d'admin voit tout)

dd_notes (notes de jeu)
- no_id (id, index de la table)
- no_nom (nom de la note)
- no_tyno_id (type de la note. Issu de la table dd_types_notes)
- no_date (date de création de la note)
- no_j_id (rédacteur de la note)
- Note: les anciens champs no_texte_* sont legacy et seront supprimés ultérieurement.

dd_notes_contenus (contenu des notes de jeu)
- noc_id (id, index de la table)
- noc_no_id (id de la note, issu de la table dd_notes)
- noc_dd (degré de difficulté pour accéder à ce contenu.)
- noc_texte (contenu texte du bloc)

 dd_personnages_notes (attribution d'une note à un perosnnage)
- pno_id (id, index de la table)
- pno_pe_id (id du personnage, issu de la table dd_personnages)
- pno_no_id (id du note, issu de la table dd_notes)
- pno_dd (niveau de connaissance du personnage sur cette note. Renvoit à noc_dd de la table dd_notes_contenus)
- pno_actif
- Note: le champ pno_niveau est obsolète (remplacé par pno_dd).

dd_campagnes_notes (attribution d'une note de jeu à une campagne)
- cpno_id (id, index de la table)
- cpno_no_id (id de la note)
- cpno_camp_id (id de la campagne)

dd_tags (liste des tags de notes)
- tag_id (id, index de la table)
- tag_nom (libellé du tag)
- tag_slug (version normalisée du tag, utilisée pour l'unicité)
- tag_j_id (id du joueur propriétaire du tag)
- tag_date (date de création)

dd_notes_tags (association n-n entre notes et tags)
- notag_id (id, index de la table)
- notag_no_id (id de la note, issu de dd_notes)
- notag_tag_id (id du tag, issu de dd_tags)

commentaire sur le fonctionnement des notes. Une note est constituées de plusieurs contenus correspondant chacun à un enregistrement dans la table dd_notes_contenus. Chaque contenu a un degré de difficulté associé (champ noc_dd). Un personnage a accès à une note si un enregistrement existe avecson id (pno_pe_id) dans la table dd_personnages_notes. Il voit tous les contenus de la notes dont noc_dd est inférieur ou égal à pno_dd.
Une note peut aussi être rattaché à une campagne (table dd_campagnes_notes).
Les tags sont gérés sans limite via la relation n-n dd_notes_tags.
La table dd_niveaux_notes est obsolète et doit être supprimée après migration applicative.

dd_dons (listes des dons)
- do_id (id, index de la table)
- do_nom (nom du don)
- do_dado_id (id de la catégorie du don, issu de la table dd_data_don)
- do_conditions (DD3.5 uniquement : conditions d'accès au don)
- do_texte (descriptif du don)
- do_res_id (id de la ressource, table dd_resssources)
- do_page_source (obsolète, conservé pour la compatibilité)
- do_resume (résumé du don)
- do_ruleset_var_id (id de la version des règles, stocké dans la table dd_variables)

dd_joueurs (liste des utilisateurs du site)
- j_id
- j_prenom
- j_nom
- j_pseudo
- j_avatar_url
- j_bio
- j_pass
- j_password_hash
- j_remember_token
- j_remember_token_expires
- j_email
- j_date_inscription
- j_derniere_connexion
- j_telephone
- j_admin
- j_default_ruleset_var_id
- j_notes
- j_dd_onglet_sort (affiche un bouton pour permettre l'affichage d'un sort dans un nouvel onglet plutôt que dans le DIV dtail-PP)
- j_dd_onglet_don (affiche un bouton pour permettre l'affichage d'un don dans un nouvel onglet plutôt que dans le DIV dtail-PP)
- j_dd_onglet_om (affiche un bouton pour permettre l'affichage d'un objet dans un nouvel onglet plutôt que dans le DIV dtail-PP)
- j_mode_campagne (0/1 : active ou non les fonctionnalites de gestion de campagne dans l'interface utilisateur)
- j_affichage_ruleset (0/1 : affiche ou non le ruleset actif dans le header)
- j_items_par_page
- j_visible

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

// reception du critere
$critere = isset($_GET['critere_recherche']) ? trim((string)$_GET['critere_recherche']) : '';
?>
<!doctype html>
<html>
<head>
<? include("include/head.php"); ?>
<script type='text/javascript' src='js/moncode-regles.js'></script>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>
</head>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">Recherche : <? echo htmlspecialchars($critere, ENT_QUOTES, 'UTF-8'); ?></div>
        <div></div>
      </div>
      <?
      if ($critere != ''):
        $needle = '%' . $critere . '%';
        $collation = 'utf8mb4_unicode_ci';

        // Extraction robuste des IDs depuis la variable legacy $selection : "(1, 2, 3)".
        $selectedResourceIds = [];
        if (isset($selection) && preg_match_all('/\d+/', (string)$selection, $matches)):
          $selectedResourceIds = array_map('intval', $matches[0]);
        endif;

        $params = [':needle' => $needle];
        $inResourceIds = '';
        if (count($selectedResourceIds) > 0):
          $inTokens = [];
          foreach ($selectedResourceIds as $idx => $resId):
            $token = ':res' . $idx;
            $inTokens[] = $token;
            $params[$token] = $resId;
          endforeach;
          $inResourceIds = implode(', ', $inTokens);
        endif;

        $requeteParts = [];
        $requeteParts[] = '
          SELECT
            re_id AS id,
            CONVERT(re_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("regles" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("regle.php?regle" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_regles
          WHERE CONVERT(re_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(re_texte USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        $requeteParts[] = '
          SELECT
            om_id AS id,
            CONVERT(om_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("objets magiques" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("objet_magique.php?om" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_objets_magiques
          WHERE CONVERT(om_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(om_description USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        $requeteParts[] = '
          SELECT
            comp_id AS id,
            CONVERT(comp_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("competences" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("competence.php?competence" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_competences
          WHERE CONVERT(comp_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_description USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_test USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_action USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_nouvelleTentative USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_special USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        if ($inResourceIds != ''):
          $requeteParts[] = '
            SELECT
              so_id AS id,
              CONVERT(so_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
              CAST("sorts" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
              CAST("sort.php?sort" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
            FROM dd_sorts
            WHERE so_res_id IN (' . $inResourceIds . ')
              AND (
                CONVERT(so_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
                OR CONVERT(so_texte USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
              )';

          $requeteParts[] = '
            SELECT
              do_id AS id,
              CONVERT(do_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
              CAST("dons" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
              CAST("don.php?don" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
            FROM dd_dons
            WHERE do_res_id IN (' . $inResourceIds . ')
              AND (
                CONVERT(do_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
                OR CONVERT(do_texte USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
                OR CONVERT(do_resume USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
              )';
        endif;

        $requeteParts[] = '
          SELECT
            cla_id AS id,
            CONVERT(cla_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("classes" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("classe.php?classe" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_classe_competence
          JOIN dd_classes ON cc_cla_id = cla_id
          JOIN dd_competences ON cc_comp_id = comp_id
          WHERE CONVERT(cla_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(cla_description USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(comp_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        $requeteParts[] = '
          SELECT
            ra_id AS id,
            CONVERT(ra_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("races" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("race.php?race" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_races
          WHERE CONVERT(ra_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(ra_description USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        $requeteParts[] = '
          SELECT
            no_id AS id,
            CONVERT(no_nom USING utf8mb4) COLLATE ' . $collation . ' AS nom,
            CAST("notes" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS source,
            CAST("note.php?note" AS CHAR CHARACTER SET utf8mb4) COLLATE ' . $collation . ' AS lapage
          FROM dd_notes
          WHERE CONVERT(no_nom USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(no_texte_basique USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(no_texte_intermediaire USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(no_texte_avance USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle
             OR CONVERT(no_texte_expert USING utf8mb4) COLLATE ' . $collation . ' LIKE :needle';

        $requete = '(' . implode(') UNION (', $requeteParts) . ') ORDER BY nom';

        $stmt = $db->prepare($requete);
        foreach ($params as $paramName => $paramValue):
          $paramType = is_int($paramValue) ? PDO::PARAM_INT : PDO::PARAM_STR;
          $stmt->bindValue($paramName, $paramValue, $paramType);
        endforeach;
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $num_rows = count($resultats);

        if ($num_rows > 0):
          $sourceConfig = [
            'objets magiques' => ['fn' => 'afficherOM', 'categorie' => 'Objet magique'],
            'dons' => ['fn' => 'afficherDon', 'categorie' => 'Don'],
            'competences' => ['fn' => 'afficherCompetence', 'categorie' => 'Comp&eacute;tence'],
            'sorts' => ['fn' => 'afficherSort', 'categorie' => 'Sort'],
            'regles' => ['fn' => 'afficherRegle', 'categorie' => 'R&egrave;gle'],
            'classes' => ['fn' => 'afficherClasse', 'categorie' => 'Classe'],
            'races' => ['fn' => 'afficherRace', 'categorie' => 'Race'],
            'notes' => ['fn' => 'afficherNote', 'categorie' => 'Note'],
          ];

          echo '<div class="item entete">';
          echo '  <div class="nom_recherche">Nom</div>';
          echo '  <div class="categorie_recherche">Cat&eacute;gorie</div>';
          echo '</div><!-- item entete --->';

          foreach ($resultats as $dn):
            $source = isset($dn['source']) ? $dn['source'] : '';
            if (!isset($sourceConfig[$source])):
              continue;
            endif;

            $click = $sourceConfig[$source]['fn'] . '(' . (int)$dn['id'] . ')';
            $categorie = $sourceConfig[$source]['categorie'];
            echo '<div class="item data">';
            echo '<div onClick="' . $click . '" class="nom_recherche">' . htmlspecialchars($dn['nom'], ENT_QUOTES, 'UTF-8') . '</div>';
            echo '<div onClick="' . $click . '" class="categorie_recherche">' . $categorie . '</div>';
            echo '</div>';
          endforeach;
        else:
          echo '<div class="nodata">Aucun r&eacute;sultat disponible !</div>';
        endif;
      else:
        echo '<div class="nodata">Aucun crit&egrave;re de recherche !</div>';
      endif;
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour eviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div><!-- wrapper -->
  </div> <!-- page --->
</body>
<div id="detail-pp"></div>
<div id="modification"></div>
</html>

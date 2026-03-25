<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

$action = isset($_POST['action']) ? $_POST['action'] : '';
$c = isset($_POST['mp_cla_id'])
   ? (int) $_POST['mp_cla_id']
   : 0;

function postv(string $key, $default = '')
{
   return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function tableColumns(PDO $db, string $table): array
{
   static $cache = [];
   if (isset($cache[$table])) {
      return $cache[$table];
   }
   $cols = [];
   $stmt = $db->query("SHOW COLUMNS FROM `" . $table . "`");
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $cols[$row['Field']] = true;
   }
   $cache[$table] = $cols;
   return $cols;
}

if ($action == 'cancel'):
   header("Location: classes.php");
   exit;
endif;

try {
   $db->beginTransaction();

   $paramsClasse = [
      ':cla_nom' => postv('mp_cla_nom'),
      ':cla_clt_id' => postv('mp_cla_clt_id'),
      ':cla_abreviation' => postv('mp_cla_abreviation'),
      ':cla_dV' => postv('mp_cla_dV'),
      ':cla_pointsCompetences' => postv('mp_cla_pointsCompetences'),
      ':cla_alignement' => postv('mp_cla_alignement'),
      ':cla_car_id' => postv('mp_cla_car_id'),
      ':cla_po_niveau1' => postv('mp_cla_po_niveau1'),
      ':cla_conditions' => postv('mp_cla_conditions'),
      ':cla_description' => postv('mp_cla_description'),
      ':cla_traits' => postv('mp_cla_traits'),
      ':cla_caracteristiques' => postv('mp_cla_caracteristiques'),
      ':cla_sauvegardes' => postv('mp_cla_sauvegardes'),
      ':cla_competences' => postv('mp_cla_competences'),
      ':cla_armes' => postv('mp_cla_armes'),
      ':cla_armures' => postv('mp_cla_armures'),
      ':cla_outils' => postv('mp_cla_outils'),
      ':cla_equipement' => postv('mp_cla_equipement'),
      ':cla_pouvoir1' => postv('mp_cla_pouvoir1'),
      ':cla_pouvoir2' => postv('mp_cla_pouvoir2'),
      ':cla_pouvoir3' => postv('mp_cla_pouvoir3'),
      ':cla_pouvoir4' => postv('mp_cla_pouvoir4'),
      ':cla_sorts' => postv('mp_cla_sorts'),
      ':cla_mag_id' => postv('mp_cla_mag_id'),
      ':cla_niveauMax' => (int)postv('mp_cla_niveauMax', 20),
      ':cla_res_id' => postv('mp_cla_res_id'),
   ];

   if ($c > 0):
      $sql = "UPDATE dd_classes SET
      cla_nom = :cla_nom,
      cla_clt_id = :cla_clt_id,
      cla_abreviation = :cla_abreviation,
      cla_dV = :cla_dV,
      cla_pointsCompetences = :cla_pointsCompetences,
      cla_alignement = :cla_alignement,
      cla_car_id = :cla_car_id,
      cla_po_niveau1 = :cla_po_niveau1,
      cla_conditions = :cla_conditions,
      cla_description = :cla_description,
      cla_traits = :cla_traits,
      cla_caracteristiques = :cla_caracteristiques,
      cla_sauvegardes = :cla_sauvegardes,
      cla_competences = :cla_competences,
      cla_armes = :cla_armes,
      cla_armures = :cla_armures,
      cla_outils = :cla_outils,
      cla_equipement = :cla_equipement,
      cla_pouvoir1 = :cla_pouvoir1,
      cla_pouvoir2 = :cla_pouvoir2,
      cla_pouvoir3 = :cla_pouvoir3,
      cla_pouvoir4 = :cla_pouvoir4,
      cla_sorts = :cla_sorts,
      cla_mag_id = :cla_mag_id,
      cla_niveauMax = :cla_niveauMax,
      cla_res_id = :cla_res_id
      WHERE cla_id = :cla_id";
      $stmt = $db->prepare($sql);
      $paramsClasse[':cla_id'] = $c;
      $stmt->execute($paramsClasse);
      $msg = "M";
   else:
      $sql = "INSERT INTO dd_classes (
      cla_nom, cla_clt_id, cla_abreviation, cla_dV, cla_pointsCompetences, cla_alignement,
      cla_car_id, cla_po_niveau1, cla_conditions, cla_description, cla_traits,
      cla_caracteristiques, cla_sauvegardes, cla_competences, cla_armes, cla_armures,
      cla_outils, cla_equipement, cla_pouvoir1, cla_pouvoir2, cla_pouvoir3, cla_pouvoir4,
      cla_sorts, cla_mag_id, cla_niveauMax, cla_res_id, cla_ruleset_var_id
      ) VALUES (
      :cla_nom, :cla_clt_id, :cla_abreviation, :cla_dV, :cla_pointsCompetences, :cla_alignement,
      :cla_car_id, :cla_po_niveau1, :cla_conditions, :cla_description, :cla_traits,
      :cla_caracteristiques, :cla_sauvegardes, :cla_competences, :cla_armes, :cla_armures,
      :cla_outils, :cla_equipement, :cla_pouvoir1, :cla_pouvoir2, :cla_pouvoir3, :cla_pouvoir4,
      :cla_sorts, :cla_mag_id, :cla_niveauMax, :cla_res_id, :cla_ruleset_var_id
      )";
      $stmt = $db->prepare($sql);
      $paramsClasse[':cla_ruleset_var_id'] = $_SESSION['ruleset'];
      $stmt->execute($paramsClasse);
      $c = (int)$db->lastInsertId();
      $msg = "C";
   endif;

   $niveauMax = max(1, (int)postv('mp_cla_niveauMax', 20));
   $niveaux = isset($_POST['niveaux']) && is_array($_POST['niveaux']) ? $_POST['niveaux'] : [];
   if (!empty($niveaux)) {
      $cnCols = tableColumns($db, 'dd_classe_niveau');
      $columnsNiveauAllowed = [
         'cn_bba', 'cn_reflexes', 'cn_vigueur', 'cn_volonte',
         'cn_pouvoir1', 'cn_pouvoir2', 'cn_pouvoir3', 'cn_pouvoir4',
         'cn_sort_n0', 'cn_sort_n1', 'cn_sort_n2', 'cn_sort_n3', 'cn_sort_n4',
         'cn_sort_n5', 'cn_sort_n6', 'cn_sort_n7', 'cn_sort_n8', 'cn_sort_n9',
         'cn_sortConnu_n0', 'cn_sortConnu_n1', 'cn_sortConnu_n2', 'cn_sortConnu_n3', 'cn_sortConnu_n4',
         'cn_sortConnu_n5', 'cn_sortConnu_n6', 'cn_sortConnu_n7', 'cn_sortConnu_n8', 'cn_sortConnu_n9',
      ];
      $columnsNiveau = [];
      foreach ($columnsNiveauAllowed as $col) {
         if (isset($cnCols[$col])) {
            $columnsNiveau[] = $col;
         }
      }

      $stmtExists = $db->prepare("SELECT cn_id FROM dd_classe_niveau WHERE cn_cla_id=:cla AND cn_niveau=:niveau");
      foreach ($niveaux as $nKey => $row) {
         $niveau = (int)$nKey;
         if ($niveau < 1 || $niveau > $niveauMax) {
            continue;
         }

         $values = [];
         foreach ($columnsNiveau as $col) {
            $values[$col] = isset($row[$col]) ? trim((string)$row[$col]) : '';
         }

         $stmtExists->execute([':cla' => $c, ':niveau' => $niveau]);
         $exists = (bool)$stmtExists->fetchColumn();

         if ($exists) {
            if (!empty($columnsNiveau)) {
               $setSql = [];
               $params = [':cla' => $c, ':niveau' => $niveau];
               foreach ($columnsNiveau as $col) {
                  $setSql[] = $col . '=:' . $col;
                  $params[':' . $col] = $values[$col];
               }
               $sqlUpdate = "UPDATE dd_classe_niveau SET " . implode(', ', $setSql) . " WHERE cn_cla_id=:cla AND cn_niveau=:niveau";
               $stmtUpdate = $db->prepare($sqlUpdate);
               $stmtUpdate->execute($params);
            }
         } else {
            $insertCols = ['cn_cla_id', 'cn_niveau'];
            $insertVals = [':cn_cla_id' => $c, ':cn_niveau' => $niveau];
            foreach ($columnsNiveau as $col) {
               $insertCols[] = $col;
               $insertVals[':' . $col] = $values[$col];
            }
            $sqlInsert = "INSERT INTO dd_classe_niveau (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', array_keys($insertVals)) . ")";
            $stmtInsert = $db->prepare($sqlInsert);
            $stmtInsert->execute($insertVals);
         }
      }
   }

   $capPayloadRaw = postv('capacites_payload', '');
   $affPayloadRaw = postv('affectations_payload', '');
   $capacitesReady = (string)postv('capacites_payload_ready', '0') === '1';
   $capsPayload = json_decode($capPayloadRaw, true);
   $affPayload = json_decode($affPayloadRaw, true);

   if ($capacitesReady && is_array($capsPayload) && is_array($affPayload)) {
      $capCols = tableColumns($db, 'dd_capacites_speciales');
      $ccCols = tableColumns($db, 'dd_classe_capacite');

      $hasCapType = isset($capCols['cap_type']);
      $hasCapCategorie = isset($capCols['cap_categorie_var_id']);
      $hasCcPrecision = isset($ccCols['cc_precision']);

      $capKeyToId = [];
      foreach ($capsPayload as $capRow) {
         $capKey = isset($capRow['cap_key']) ? (string)$capRow['cap_key'] : '';
         if ($capKey === '') {
            continue;
         }

         $capId = isset($capRow['cap_id']) ? (int)$capRow['cap_id'] : 0;
         $nom = isset($capRow['cap_nom']) ? trim((string)$capRow['cap_nom']) : '';
         if ($nom === '') {
            continue;
         }
         $desc = isset($capRow['cap_description']) ? (string)$capRow['cap_description'] : '';
         $type = isset($capRow['cap_type']) ? trim((string)$capRow['cap_type']) : '';
         $cat = isset($capRow['cap_categorie_var_id']) ? (int)$capRow['cap_categorie_var_id'] : 0;

         if ($capId > 0) {
            $setParts = ['cap_nom=:cap_nom', 'cap_description=:cap_description'];
            $params = [':cap_nom' => $nom, ':cap_description' => $desc, ':cap_id' => $capId];
            if ($hasCapType) {
               $setParts[] = 'cap_type=:cap_type';
               $params[':cap_type'] = $type;
            }
            if ($hasCapCategorie) {
               $setParts[] = 'cap_categorie_var_id=:cap_categorie_var_id';
               $params[':cap_categorie_var_id'] = $cat;
            }
            $stmtCap = $db->prepare("UPDATE dd_capacites_speciales SET " . implode(', ', $setParts) . " WHERE cap_id=:cap_id");
            $stmtCap->execute($params);
            $capKeyToId[$capKey] = $capId;
         } else {
            $insertCols = ['cap_nom', 'cap_description'];
            $insertParams = [':cap_nom' => $nom, ':cap_description' => $desc];
            if ($hasCapType) {
               $insertCols[] = 'cap_type';
               $insertParams[':cap_type'] = $type;
            }
            if ($hasCapCategorie) {
               $insertCols[] = 'cap_categorie_var_id';
               $insertParams[':cap_categorie_var_id'] = $cat;
            }
            $stmtCap = $db->prepare(
               "INSERT INTO dd_capacites_speciales (" . implode(', ', $insertCols) . ") VALUES (" . implode(', ', array_keys($insertParams)) . ")"
            );
            $stmtCap->execute($insertParams);
            $newId = (int)$db->lastInsertId();
            $capKeyToId[$capKey] = $newId;
         }
      }

      $stmtDeleteAffects = $db->prepare("DELETE FROM dd_classe_capacite WHERE cc_cla_id=:cla");
      $stmtDeleteAffects->execute([':cla' => $c]);

      $seen = [];
      foreach ($affPayload as $affRow) {
         $capKey = isset($affRow['cap_key']) ? (string)$affRow['cap_key'] : '';
         if ($capKey === '' || !isset($capKeyToId[$capKey])) {
            continue;
         }
         $capId = $capKeyToId[$capKey];
         $niveau = isset($affRow['cc_niveau']) ? (int)$affRow['cc_niveau'] : 0;
         if ($niveau < 1 || $niveau > $niveauMax) {
            continue;
         }
         $precision = isset($affRow['cc_precision']) ? (string)$affRow['cc_precision'] : '';
         $signature = $capId . '|' . $niveau . '|' . $precision;
         if (isset($seen[$signature])) {
            continue;
         }
         $seen[$signature] = true;

         if ($hasCcPrecision) {
            $stmtInsertAff = $db->prepare(
               "INSERT INTO dd_classe_capacite (cc_cla_id, cc_cap_id, cc_niveau, cc_precision) VALUES (:cla, :cap, :niveau, :precision)"
            );
            $stmtInsertAff->execute([
               ':cla' => $c,
               ':cap' => $capId,
               ':niveau' => $niveau,
               ':precision' => $precision
            ]);
         } else {
            $stmtInsertAff = $db->prepare(
               "INSERT INTO dd_classe_capacite (cc_cla_id, cc_cap_id, cc_niveau) VALUES (:cla, :cap, :niveau)"
            );
            $stmtInsertAff->execute([
               ':cla' => $c,
               ':cap' => $capId,
               ':niveau' => $niveau
            ]);
         }
      }
   }

   $db->commit();
} catch (Throwable $e) {
   if ($db->inTransaction()) {
      $db->rollBack();
   }
   $msg = 'E';
}

header("location: classe.php?classe=" . $c . "&tri=" . $_GET['tri'] . "&msg=" . $msg);

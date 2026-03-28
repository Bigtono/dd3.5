<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$p = isset($_POST['mp_pe_id']) ? $_POST['mp_pe_id'] : '';
$c = isset($_POST['campagne']) ? $_POST['campagne'] : 0;
$notesMjValue = isset($_POST['mp_pe_notes_mj']) ? $_POST['mp_pe_notes_mj'] : '';
$isDd35 = isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] === 'DD3.5';
if ($isDd35) {
  include_once("include/insert/DD3.5/personnage_nls_helper.php");
}

if (isset($_POST['ok']) && !empty($p)):
  if ($p != "n"):
    $stmtPerso = $db->prepare("SELECT pe_j_id, pe_camp_id, pe_notes_mj FROM dd_personnages WHERE pe_id = :pid LIMIT 1");
    $stmtPerso->execute([':pid' => (int)$p]);
    $currentPerso = $stmtPerso->fetch(PDO::FETCH_ASSOC);
    if (!$currentPerso):
      header("location: personnages.php");
      exit;
    endif;

    $isPersonnageOwner = isset($_SESSION['user_id']) && (int)$currentPerso['pe_j_id'] === (int)$_SESSION['user_id'];
    $campaignOwnerId = 0;
    if ((int)$currentPerso['pe_camp_id'] > 0):
      $stmtCamp = $db->prepare("SELECT camp_j_id FROM dd_campagnes WHERE camp_id = :cid LIMIT 1");
      $stmtCamp->execute([':cid' => (int)$currentPerso['pe_camp_id']]);
      $camp = $stmtCamp->fetch(PDO::FETCH_ASSOC);
      if ($camp) $campaignOwnerId = (int)$camp['camp_j_id'];
    endif;

    $isCampaignOwner = (int)$currentPerso['pe_camp_id'] > 0
      && isset($_SESSION['user_id'])
      && $campaignOwnerId === (int)$_SESSION['user_id'];
    $canEditPersonnage = $isPersonnageOwner || $isCampaignOwner;
    if (!$canEditPersonnage):
      $retour = "personnage.php?personnage=" . (int)$p;
      if ($c && $c > 0) $retour .= "&campagne=" . (int)$c;
      header("location: " . $retour);
      exit;
    endif;

    if (!$isCampaignOwner):
      $notesMjValue = (string)$currentPerso['pe_notes_mj'];
    endif;

    try {
      $db->beginTransaction();

      $stmtUpdatePerso = $db->prepare("UPDATE dd_personnages SET
        pe_nom = :nom,
        pe_ra_id = :ra,
        pe_arc_id = :arc,
        pe_sexe = :sexe,
        pe_al_id = :al,
        pe_org_id = :org,
        pe_for = :forc,
        pe_dex = :dex,
        pe_con = :con,
        pe_int = :intell,
        pe_sag = :sag,
        pe_cha = :cha,
        pe_background = :background,
        pe_notes = :notes,
        pe_notes_mj = :notes_mj,
        pe_j_id = :jid
        WHERE pe_id = :pid");
      $stmtUpdatePerso->execute([
        ':nom' => isset($_POST['mp_pe_nom']) ? $_POST['mp_pe_nom'] : '',
        ':ra' => (int)$_POST['mp_pe_ra_id'],
        ':arc' => (int)$_POST['mp_pe_arc_id'],
        ':sexe' => isset($_POST['mp_pe_sexe']) ? $_POST['mp_pe_sexe'] : '',
        ':al' => (int)$_POST['mp_pe_al_id'],
        ':org' => (int)$_POST['mp_pe_org_id'],
        ':forc' => (int)$_POST['mp_pe_for'],
        ':dex' => (int)$_POST['mp_pe_dex'],
        ':con' => (int)$_POST['mp_pe_con'],
        ':intell' => (int)$_POST['mp_pe_int'],
        ':sag' => (int)$_POST['mp_pe_sag'],
        ':cha' => (int)$_POST['mp_pe_cha'],
        ':background' => isset($_POST['mp_pe_background']) ? $_POST['mp_pe_background'] : '',
        ':notes' => isset($_POST['mp_pe_notes']) ? $_POST['mp_pe_notes'] : '',
        ':notes_mj' => $notesMjValue,
        ':jid' => (int)$_POST['mp_pe_j_id'],
        ':pid' => (int)$p,
      ]);

      // --- Classes : source de verite = payload du formulaire ---
      $classesPayloadReady = isset($_POST['mp_classes_payload_ready']) && (int)$_POST['mp_classes_payload_ready'] === 1;
      if ($classesPayloadReady):
        $keepIdsRaw = isset($_POST['mp_class_keep_ids']) && is_array($_POST['mp_class_keep_ids']) ? $_POST['mp_class_keep_ids'] : [];
        $deleteIdsRaw = isset($_POST['mp_class_delete_ids']) && is_array($_POST['mp_class_delete_ids']) ? $_POST['mp_class_delete_ids'] : [];
        $keepNiveauxRaw = isset($_POST['mp_class_keep_niveau']) && is_array($_POST['mp_class_keep_niveau']) ? $_POST['mp_class_keep_niveau'] : [];
        $addClaIdsRaw = isset($_POST['mp_class_add_cla_id']) && is_array($_POST['mp_class_add_cla_id']) ? $_POST['mp_class_add_cla_id'] : [];
        $addNiveauxRaw = isset($_POST['mp_class_add_niveau']) && is_array($_POST['mp_class_add_niveau']) ? $_POST['mp_class_add_niveau'] : [];

      $keepSet = [];
      foreach ($keepIdsRaw as $idKeep) {
        $idKeep = (int)$idKeep;
        if ($idKeep > 0) $keepSet[$idKeep] = true;
      }
      $deleteSet = [];
      foreach ($deleteIdsRaw as $idDelete) {
        $idDelete = (int)$idDelete;
        if ($idDelete > 0) $deleteSet[$idDelete] = true;
      }

      $stmtAllowedClasses = $db->prepare("SELECT cla_id, cla_niveauMax FROM dd_classes WHERE cla_ruleset_var_id = :ruleset");
      $stmtAllowedClasses->execute([':ruleset' => (int)$_SESSION['ruleset']]);
      $allowedClasses = [];
      while ($rowAllowed = $stmtAllowedClasses->fetch(PDO::FETCH_ASSOC)) {
        $allowedClasses[(int)$rowAllowed['cla_id']] = (int)$rowAllowed['cla_niveauMax'];
      }

      $stmtExistingClasses = $db->prepare("SELECT pc_id, pc_cla_id, pc_niveau FROM dd_personnages_classes WHERE pc_pe_id = :pid");
      $stmtExistingClasses->execute([':pid' => (int)$p]);
      $existingClasses = [];
      while ($rowExisting = $stmtExistingClasses->fetch(PDO::FETCH_ASSOC)) {
        $existingClasses[(int)$rowExisting['pc_id']] = [
          'cla_id' => (int)$rowExisting['pc_cla_id'],
          'niveau' => (int)$rowExisting['pc_niveau'],
        ];
      }

      $stmtDeleteNlsByPc = $db->prepare("DELETE FROM dd_personnages_nls WHERE penl_pc_id_base = :pcid OR penl_pc_id_prestige = :pcid");
      $stmtDeleteClass = $db->prepare("DELETE FROM dd_personnages_classes WHERE pc_id = :pcid");
      $stmtUpdateClassNiveau = $db->prepare("UPDATE dd_personnages_classes SET pc_niveau = :niveau WHERE pc_id = :pcid");
      $stmtInsertClass = $db->prepare("INSERT INTO dd_personnages_classes (pc_pe_id, pc_cla_id, pc_niveau) VALUES (:pid, :claid, :niveau)");

        $finalClasseIds = [];
        foreach ($existingClasses as $pcId => $existingData) {
          $isDeleted = isset($deleteSet[$pcId]) || !isset($keepSet[$pcId]);
          if ($isDeleted) {
            $stmtDeleteNlsByPc->execute([':pcid' => (int)$pcId]);
            $stmtDeleteClass->execute([':pcid' => (int)$pcId]);
            continue;
          }

          $claId = (int)$existingData['cla_id'];
          if (!isset($allowedClasses[$claId])) {
            $stmtDeleteNlsByPc->execute([':pcid' => (int)$pcId]);
            $stmtDeleteClass->execute([':pcid' => (int)$pcId]);
            continue;
          }

          $niveauMax = (int)$allowedClasses[$claId];
          $niveau = isset($keepNiveauxRaw[$pcId]) ? (int)$keepNiveauxRaw[$pcId] : (int)$existingData['niveau'];
          if ($niveau < 1) $niveau = 1;
          if ($niveau > $niveauMax) $niveau = $niveauMax;

          $stmtUpdateClassNiveau->execute([
            ':niveau' => $niveau,
            ':pcid' => (int)$pcId,
          ]);
          $finalClasseIds[$claId] = true;
        }

        $countAdd = min(count($addClaIdsRaw), count($addNiveauxRaw));
        for ($i = 0; $i < $countAdd; $i++) {
          $claId = (int)$addClaIdsRaw[$i];
          $niveau = (int)$addNiveauxRaw[$i];
          if ($claId <= 0 || !isset($allowedClasses[$claId])) continue;
          if (isset($finalClasseIds[$claId])) continue; // pas de doublon de classe

          $niveauMax = (int)$allowedClasses[$claId];
          if ($niveau < 1) $niveau = 1;
          if ($niveau > $niveauMax) $niveau = $niveauMax;

          $stmtInsertClass->execute([
            ':pid' => (int)$p,
            ':claid' => $claId,
            ':niveau' => $niveau,
          ]);
          $finalClasseIds[$claId] = true;
        }
      endif;

      // --- NLS DD3.5 non bloquant ---
      if ($isDd35 && dd35_ruleset_active()):
        $nlsContext = dd35_load_personnage_nls_context($db, (int)$p);
        $nlsRowsToSave = [];

        if ($nlsContext['has_section']):
          foreach ($nlsContext['prestige_classes'] as $pcIdPrestige => $prestigeData):
            foreach ($prestigeData['levels'] as $niveau => $levelData):
              $fieldName = 'mp_penl_base_' . (int)$pcIdPrestige . '_' . (int)$niveau;
              $pcIdBase = isset($_POST[$fieldName]) ? (int)$_POST[$fieldName] : 0;
              if ($pcIdBase > 0 && isset($levelData['options'][$pcIdBase])):
                $nlsRowsToSave[] = [
                  'pc_id_base' => $pcIdBase,
                  'pc_id_prestige' => (int)$pcIdPrestige,
                  'niveau' => (int)$niveau,
                ];
              endif;
            endforeach;
          endforeach;
        endif;

        $sqlDeleteNlsPerso = "
          DELETE n
          FROM dd_personnages_nls n
          LEFT JOIN dd_personnages_classes pcb ON pcb.pc_id = n.penl_pc_id_base
          LEFT JOIN dd_personnages_classes pcp ON pcp.pc_id = n.penl_pc_id_prestige
          WHERE pcb.pc_pe_id = :pid OR pcp.pc_pe_id = :pid
        ";
        $stmtDeleteNlsPerso = $db->prepare($sqlDeleteNlsPerso);
        $stmtDeleteNlsPerso->execute([':pid' => (int)$p]);

        if (!empty($nlsRowsToSave)):
          $stmtInsertNls = $db->prepare("INSERT INTO dd_personnages_nls (penl_pc_id_base, penl_pc_id_prestige, penl_niveau) VALUES (:pc_id_base, :pc_id_prestige, :niveau)");
          foreach ($nlsRowsToSave as $nlsRow):
            $stmtInsertNls->execute([
              ':pc_id_base' => (int)$nlsRow['pc_id_base'],
              ':pc_id_prestige' => (int)$nlsRow['pc_id_prestige'],
              ':niveau' => (int)$nlsRow['niveau'],
            ]);
          endforeach;
        endif;
      endif;

      $db->commit();
      $message = 1;
    } catch (Exception $e) {
      if ($db->inTransaction()) $db->rollBack();
      $message = 'Erreur: ' . $e->getMessage();
    }

  else:
    $notesMjValue = '';
    $sql = "INSERT INTO dd_personnages (pe_nom, pe_ra_id, pe_arc_id, pe_sexe, pe_al_id, pe_org_id, pe_for, pe_dex, pe_con, pe_int, pe_sag, pe_cha, pe_background, pe_notes, pe_notes_mj, pe_j_id, pe_ruleset_var_id) VALUES ('" . addslashes($_POST['mp_pe_nom']) . "', '" . $_POST['mp_pe_ra_id'] . "', '" . $_POST['mp_pe_arc_id'] . "', '" . $_POST['mp_pe_sexe'] . "', '" . $_POST['mp_pe_al_id'] . "', '" . $_POST['mp_pe_org_id'] . "', '" . $_POST['mp_pe_for'] . "', '" . $_POST['mp_pe_dex'] . "', '" . $_POST['mp_pe_con'] . "', '" . $_POST['mp_pe_int'] . "', '" . $_POST['mp_pe_sag'] . "', '" . $_POST['mp_pe_cha'] . "', '" . addslashes($_POST['mp_pe_background']) . "', '" . addslashes($_POST['mp_pe_notes']) . "', '" . addslashes($notesMjValue) . "', '" . addslashes($_POST['mp_pe_j_id']) . "', '" . $_SESSION['ruleset'] . "')";
    $resultat = execPDO($sql);
    $p = lastID("dd_personnages", "pe");
    $message = 0;
  endif;
else:
  $message = isset($_POST['ok']) ? $_POST['ok'] . "/" . $p : '';
endif;

$complement = "";
if ($c && $c > 0) $complement = '&campagne=' . $c;
header("location: personnage.php?personnage=" . $p . $complement);

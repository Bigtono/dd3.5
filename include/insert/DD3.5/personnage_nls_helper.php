<?php

if (!function_exists('dd35_ruleset_active')) {
  function dd35_ruleset_active()
  {
    return isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] === 'DD3.5';
  }
}

if (!function_exists('dd35_nls_mode_for_level')) {
  function dd35_nls_mode_for_level($arcane, $divin, $effectif)
  {
    $arcane = (int)$arcane;
    $divin = (int)$divin;
    $effectif = (int)$effectif;
    $nbFlags = ($arcane ? 1 : 0) + ($divin ? 1 : 0) + ($effectif ? 1 : 0);

    // Cas incoherent (plusieurs flags actifs) => mode effectif.
    if ($nbFlags > 1) return 'effectif';
    if ($effectif === 1) return 'effectif';
    if ($arcane === 1) return 'arcane';
    if ($divin === 1) return 'divin';
    return '';
  }
}

if (!function_exists('dd35_load_personnage_nls_context')) {
  function dd35_load_personnage_nls_context($db, $personnageId)
  {
    $context = [
      'has_section' => false,
      'base_classes' => [],
      'base_by_mode' => [
        'arcane' => [],
        'divin' => [],
        'effectif' => [],
      ],
      'prestige_classes' => [],
      'assignments' => [],
    ];

    $personnageId = (int)$personnageId;
    if ($personnageId <= 0) return $context;

    $sqlBase = "
      SELECT
        pc.pc_id,
        pc.pc_cla_id,
        pc.pc_niveau,
        c.cla_nom,
        c.cla_mag_id
      FROM dd_personnages_classes pc
      JOIN dd_classes c ON c.cla_id = pc.pc_cla_id
      WHERE pc.pc_pe_id = :pid
        AND c.cla_clt_id = 1
        AND c.cla_mag_id > 0
      ORDER BY c.cla_mag_id, c.cla_nom
    ";
    $stmtBase = $db->prepare($sqlBase);
    $stmtBase->execute([':pid' => $personnageId]);
    while ($row = $stmtBase->fetch(PDO::FETCH_ASSOC)) {
      $pcId = (int)$row['pc_id'];
      $context['base_classes'][$pcId] = [
        'pc_id' => $pcId,
        'pc_cla_id' => (int)$row['pc_cla_id'],
        'pc_niveau' => (int)$row['pc_niveau'],
        'cla_nom' => (string)$row['cla_nom'],
        'cla_mag_id' => (int)$row['cla_mag_id'],
      ];
      $context['base_by_mode']['effectif'][$pcId] = (string)$row['cla_nom'];
      if ((int)$row['cla_mag_id'] === 1) {
        $context['base_by_mode']['arcane'][$pcId] = (string)$row['cla_nom'];
      }
      if ((int)$row['cla_mag_id'] === 2) {
        $context['base_by_mode']['divin'][$pcId] = (string)$row['cla_nom'];
      }
    }

    if (count($context['base_classes']) === 0) return $context;

    $sqlPrestiges = "
      SELECT
        pc.pc_id,
        pc.pc_cla_id,
        pc.pc_niveau,
        c.cla_nom
      FROM dd_personnages_classes pc
      JOIN dd_classes c ON c.cla_id = pc.pc_cla_id
      WHERE pc.pc_pe_id = :pid
        AND c.cla_clt_id = 2
      ORDER BY c.cla_nom
    ";
    $stmtPrestiges = $db->prepare($sqlPrestiges);
    $stmtPrestiges->execute([':pid' => $personnageId]);
    $prestiges = $stmtPrestiges->fetchAll(PDO::FETCH_ASSOC);

    if (!$prestiges) return $context;

    $sqlNiveaux = "
      SELECT
        cn_niveau,
        cn_niveauSortArcane,
        cn_niveauSortDivin,
        cn_niveauSortEffectif
      FROM dd_classe_niveau
      WHERE cn_cla_id = :claId
        AND cn_niveau <= :niveauMax
      ORDER BY cn_niveau
    ";
    $stmtNiveaux = $db->prepare($sqlNiveaux);

    foreach ($prestiges as $prestige) {
      $pcIdPrestige = (int)$prestige['pc_id'];
      $claPrestigeId = (int)$prestige['pc_cla_id'];
      $niveauMax = (int)$prestige['pc_niveau'];
      if ($pcIdPrestige <= 0 || $claPrestigeId <= 0 || $niveauMax <= 0) continue;

      $stmtNiveaux->execute([
        ':claId' => $claPrestigeId,
        ':niveauMax' => $niveauMax,
      ]);
      $rowsNiveaux = $stmtNiveaux->fetchAll(PDO::FETCH_ASSOC);
      if (!$rowsNiveaux) continue;

      $levels = [];
      $isInfluente = false;
      foreach ($rowsNiveaux as $niveauRow) {
        $niveau = (int)$niveauRow['cn_niveau'];
        if ($niveau <= 0 || $niveau > $niveauMax) continue;

        $mode = dd35_nls_mode_for_level(
          $niveauRow['cn_niveauSortArcane'],
          $niveauRow['cn_niveauSortDivin'],
          $niveauRow['cn_niveauSortEffectif']
        );
        if ($mode !== '') $isInfluente = true;
        $levels[$niveau] = [
          'niveau' => $niveau,
          'mode' => $mode !== '' ? $mode : 'effectif',
          'options' => $mode !== '' ? $context['base_by_mode'][$mode] : $context['base_by_mode']['effectif'],
          'assigned_pc_id_base' => 0,
          'assigned_cla_nom' => '',
        ];
      }

      if (!$isInfluente || count($levels) === 0) continue;

      // Completer les niveaux manquants (cas d'ecart en table dd_classe_niveau).
      for ($n = 1; $n <= $niveauMax; $n++) {
        if (!isset($levels[$n])) {
          $levels[$n] = [
            'niveau' => $n,
            'mode' => 'effectif',
            'options' => $context['base_by_mode']['effectif'],
            'assigned_pc_id_base' => 0,
            'assigned_cla_nom' => '',
          ];
        }
      }
      ksort($levels);

      $context['prestige_classes'][$pcIdPrestige] = [
        'cla_id' => $claPrestigeId,
        'pc_id' => $pcIdPrestige,
        'pc_niveau' => $niveauMax,
        'cla_nom' => (string)$prestige['cla_nom'],
        'levels' => $levels,
      ];
    }

    if (count($context['prestige_classes']) === 0) return $context;

    $sqlAssign = "
      SELECT
        n.penl_pc_id_prestige,
        n.penl_niveau,
        n.penl_pc_id_base
      FROM dd_personnages_nls n
      JOIN dd_personnages_classes pcb ON pcb.pc_id = n.penl_pc_id_base
      JOIN dd_personnages_classes pcp ON pcp.pc_id = n.penl_pc_id_prestige
      WHERE pcb.pc_pe_id = :pid
        AND pcp.pc_pe_id = :pid
    ";
    $stmtAssign = $db->prepare($sqlAssign);
    $stmtAssign->execute([':pid' => $personnageId]);
    while ($row = $stmtAssign->fetch(PDO::FETCH_ASSOC)) {
      $pcIdPrestige = (int)$row['penl_pc_id_prestige'];
      $niveau = (int)$row['penl_niveau'];
      $pcIdBase = (int)$row['penl_pc_id_base'];
      if (!isset($context['prestige_classes'][$pcIdPrestige])) continue;
      if (!isset($context['prestige_classes'][$pcIdPrestige]['levels'][$niveau])) continue;
      $context['assignments'][$pcIdPrestige][$niveau] = $pcIdBase;
    }

    foreach ($context['prestige_classes'] as $pcIdPrestige => &$prestigeData) {
      foreach ($prestigeData['levels'] as $niveau => &$levelData) {
        $pcIdBase = isset($context['assignments'][$pcIdPrestige][$niveau])
          ? (int)$context['assignments'][$pcIdPrestige][$niveau]
          : 0;
        if ($pcIdBase > 0 && isset($context['base_classes'][$pcIdBase])) {
          $levelData['assigned_pc_id_base'] = $pcIdBase;
          $levelData['assigned_cla_nom'] = $context['base_classes'][$pcIdBase]['cla_nom'];
        }
      }
      unset($levelData);
    }
    unset($prestigeData);

    $context['has_section'] = count($context['base_classes']) > 0 && count($context['prestige_classes']) > 0;
    return $context;
  }
}

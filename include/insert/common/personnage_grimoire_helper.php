<?php

if (!function_exists('pg_magic_table_columns')) {
  function pg_magic_table_columns($db, $tableName)
  {
    static $cache = [];
    if (isset($cache[$tableName])) return $cache[$tableName];

    $columns = [];
    try {
      $stmt = $db->query('SHOW COLUMNS FROM ' . $tableName);
      if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if (!empty($row['Field'])) {
            $columns[] = (string)$row['Field'];
          }
        }
      }
    } catch (Exception $e) {
      // Table absente ou inaccessible : on retourne une liste vide.
    }

    $cache[$tableName] = $columns;
    return $columns;
  }
}

if (!function_exists('pg_magic_has_column')) {
  function pg_magic_has_column($db, $tableName, $columnName)
  {
    return in_array($columnName, pg_magic_table_columns($db, $tableName), true);
  }
}

if (!function_exists('pg_magic_parse_resource_ids')) {
  function pg_magic_parse_resource_ids($selectionSql)
  {
    if (!is_string($selectionSql) || $selectionSql === '') return [];
    preg_match_all('/\d+/', $selectionSql, $matches);
    if (empty($matches[0])) return [];

    $ids = [];
    foreach ($matches[0] as $rawId) {
      $id = (int)$rawId;
      if ($id > 0) $ids[$id] = true;
    }
    return array_map('intval', array_keys($ids));
  }
}

if (!function_exists('pg_magic_normalize_filter')) {
  function pg_magic_normalize_filter($value)
  {
    $value = (int)$value;
    if ($value < 1 || $value > 4) return 0;
    return $value;
  }
}

if (!function_exists('pg_magic_get_session_filter')) {
  function pg_magic_get_session_filter($personnageId)
  {
    $personnageId = (int)$personnageId;
    if ($personnageId <= 0) return 0;

    if (!isset($_SESSION['personnage_grimoire_filter_pid'])) {
      $_SESSION['personnage_grimoire_filter_pid'] = $personnageId;
    }

    $storedPid = (int)$_SESSION['personnage_grimoire_filter_pid'];
    if ($storedPid !== $personnageId) {
      $_SESSION['personnage_grimoire_filter_pid'] = $personnageId;
      $_SESSION['personnage_grimoire_filter_value'] = 0;
      return 0;
    }

    return isset($_SESSION['personnage_grimoire_filter_value'])
      ? pg_magic_normalize_filter($_SESSION['personnage_grimoire_filter_value'])
      : 0;
  }
}

if (!function_exists('pg_magic_set_session_filter')) {
  function pg_magic_set_session_filter($personnageId, $filterValue)
  {
    $personnageId = (int)$personnageId;
    if ($personnageId <= 0) return;
    $_SESSION['personnage_grimoire_filter_pid'] = $personnageId;
    $_SESSION['personnage_grimoire_filter_value'] = pg_magic_normalize_filter($filterValue);
  }
}

if (!function_exists('pg_magic_default_filter_for_class')) {
  function pg_magic_default_filter_for_class(array $classData)
  {
    return !empty($classData['sort_known_all']) ? 2 : 1;
  }
}

if (!function_exists('pg_magic_class_flag_fields')) {
  function pg_magic_class_flag_fields($db)
  {
    $fields = [
      'known' => null,
      'understood' => null,
      'prepare' => null,
    ];

    if (pg_magic_has_column($db, 'dd_classes', 'cla_sort_connu')) {
      $fields['known'] = 'cla_sort_connu';
    } elseif (pg_magic_has_column($db, 'dd_classes', 'cla_connu')) {
      $fields['known'] = 'cla_connu';
    }

    if (pg_magic_has_column($db, 'dd_classes', 'cla_sort_compris')) {
      $fields['understood'] = 'cla_sort_compris';
    } elseif (pg_magic_has_column($db, 'dd_classes', 'cla_compris')) {
      $fields['understood'] = 'cla_compris';
    }

    if (pg_magic_has_column($db, 'dd_classes', 'cla_sort_prepare')) {
      $fields['prepare'] = 'cla_sort_prepare';
    } elseif (pg_magic_has_column($db, 'dd_classes', 'cla_prepare')) {
      $fields['prepare'] = 'cla_prepare';
    }

    return $fields;
  }
}

if (!function_exists('pg_magic_spell_level_field')) {
  function pg_magic_spell_level_field($db, $rulesetRep)
  {
    if ($rulesetRep === 'DD2024' && pg_magic_has_column($db, 'dd_sorts', 'so_niveau')) {
      return 'so_niveau';
    }
    return 'sc_niveau';
  }
}

if (!function_exists('pg_magic_load_context')) {
  function pg_magic_load_context($db, $personnageId, $rulesetRep, array $resourceIds = [])
  {
    $context = [
      'personnage_id' => (int)$personnageId,
      'ruleset' => (string)$rulesetRep,
      'resource_ids' => array_values(array_map('intval', $resourceIds)),
      'session_filter' => pg_magic_get_session_filter((int)$personnageId),
      'classes' => [],
      'ordered_class_ids' => [],
      'active_class_id' => 0,
      'has_spellcasting' => false,
      'debug_sql' => [],
    ];

    $personnageId = (int)$personnageId;
    if ($personnageId <= 0) return $context;

    $stmtPerso = $db->prepare('SELECT * FROM dd_personnages WHERE pe_id = :pid LIMIT 1');
    $stmtPerso->execute([':pid' => $personnageId]);
    $personnage = $stmtPerso->fetch(PDO::FETCH_ASSOC);
    if (!$personnage) return $context;

    $flagFields = pg_magic_class_flag_fields($db);
    $knownSql = $flagFields['known'] ? ('COALESCE(c.' . $flagFields['known'] . ',0)') : '0';
    $understoodSql = $flagFields['understood'] ? ('COALESCE(c.' . $flagFields['understood'] . ',0)') : '0';
    $prepareSql = $flagFields['prepare'] ? ('COALESCE(c.' . $flagFields['prepare'] . ',0)') : '0';
    $domainSql = pg_magic_has_column($db, 'dd_classes', 'cla_domaine_divin') ? 'COALESCE(c.cla_domaine_divin,0)' : '0';

    $sqlClasses = "
      SELECT
        pc.pc_id,
        pc.pc_cla_id,
        pc.pc_niveau,
        c.cla_nom,
        c.cla_clt_id,
        c.cla_mag_id,
        c.cla_car_id,
        {$knownSql} AS sort_known_all,
        {$understoodSql} AS sort_auto_understood,
        {$prepareSql} AS sort_requires_prepare,
        {$domainSql} AS domain_divin
      FROM dd_personnages_classes pc
      JOIN dd_classes c ON c.cla_id = pc.pc_cla_id
      WHERE pc.pc_pe_id = :pid
        AND c.cla_mag_id > 0
      ORDER BY c.cla_nom
    ";
    $stmtClasses = $db->prepare($sqlClasses);
    $stmtClasses->execute([':pid' => $personnageId]);
    $classesAll = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);
    if (empty($classesAll)) return $context;

    // Cas nominal: classes de base lanceuses (types 1/3). Fallback defensif:
    // si aucune classe de base n'est detectee, on conserve les classes magiques
    // restantes pour eviter un ecran vide sur des donnees heterogenes.
    $classes = [];
    foreach ($classesAll as $rowClass) {
      $clt = (int)$rowClass['cla_clt_id'];
      if ($clt === 1 || $clt === 3) {
        $classes[] = $rowClass;
      }
    }
    if (empty($classes)) {
      $classes = $classesAll;
    }

    $nlsBonusByBasePc = [];
    if ($rulesetRep === 'DD3.5') {
      $nlsCols = pg_magic_table_columns($db, 'dd_personnages_nls');
      $basePcCol = in_array('penl_pc_id_base', $nlsCols, true) ? 'penl_pc_id_base' : null;
      $prestigePcCol = null;
      if (in_array('penl_pc_id_prestige', $nlsCols, true)) {
        $prestigePcCol = 'penl_pc_id_prestige';
      } elseif (in_array('penl_pc_id', $nlsCols, true)) {
        $prestigePcCol = 'penl_pc_id';
      }
      $hasPenlNiveau = in_array('penl_niveau', $nlsCols, true);

      if ($basePcCol && $prestigePcCol) {
        $sqlNlsBonus = "
          SELECT
            n.{$basePcCol} AS pc_id_base,
            COUNT(*) AS bonus_nls
          FROM dd_personnages_nls n
          JOIN dd_personnages_classes pcp ON pcp.pc_id = n.{$prestigePcCol}
          WHERE pcp.pc_pe_id = :pid
          " . ($hasPenlNiveau ? "AND COALESCE(n.penl_niveau,0) <= pcp.pc_niveau" : "") . "
          GROUP BY n.{$basePcCol}
        ";
        $stmtNlsBonus = $db->prepare($sqlNlsBonus);
        $stmtNlsBonus->execute([':pid' => $personnageId]);
        while ($rowBonus = $stmtNlsBonus->fetch(PDO::FETCH_ASSOC)) {
          $pcIdBase = (int)$rowBonus['pc_id_base'];
          $nlsBonusByBasePc[$pcIdBase] = (int)$rowBonus['bonus_nls'];
        }
      } elseif ($prestigePcCol && in_array('penl_b_cla_id', $nlsCols, true)) {
        // Compat legacy: base stockee par cla_id (et non pc_id)
        $sqlNlsBonusLegacy = "
          SELECT
            pcb.pc_id AS pc_id_base,
            COUNT(*) AS bonus_nls
          FROM dd_personnages_nls n
          JOIN dd_personnages_classes pcp ON pcp.pc_id = n.{$prestigePcCol}
          JOIN dd_personnages_classes pcb ON pcb.pc_pe_id = :pid AND pcb.pc_cla_id = n.penl_b_cla_id
          WHERE pcp.pc_pe_id = :pid
          " . ($hasPenlNiveau ? "AND COALESCE(n.penl_niveau,0) <= pcp.pc_niveau" : "") . "
          GROUP BY pcb.pc_id
        ";
        $stmtNlsBonusLegacy = $db->prepare($sqlNlsBonusLegacy);
        $stmtNlsBonusLegacy->execute([':pid' => $personnageId]);
        while ($rowBonus = $stmtNlsBonusLegacy->fetch(PDO::FETCH_ASSOC)) {
          $pcIdBase = (int)$rowBonus['pc_id_base'];
          $nlsBonusByBasePc[$pcIdBase] = (int)$rowBonus['bonus_nls'];
        }
      }
    }

    $carMap = [];
    $stmtCars = $db->query('SELECT car_id, car_diminutif FROM dd_caracteristiques');
    if ($stmtCars) {
      while ($rowCar = $stmtCars->fetch(PDO::FETCH_ASSOC)) {
        $carMap[(int)$rowCar['car_id']] = (string)$rowCar['car_diminutif'];
      }
    }

    $modMap = [];
    $stmtMods = $db->query('SELECT * FROM dd_modificateurs');
    if ($stmtMods) {
      while ($rowMod = $stmtMods->fetch(PDO::FETCH_ASSOC)) {
        $modMap[(int)$rowMod['mod_carac']] = $rowMod;
      }
    }

    $stmtSlotsExact = $db->prepare('SELECT * FROM dd_classe_niveau WHERE cn_cla_id = :claid AND cn_niveau = :niveau LIMIT 1');
    $stmtSlotsFallback = $db->prepare('SELECT * FROM dd_classe_niveau WHERE cn_cla_id = :claid AND cn_niveau <= :niveau ORDER BY cn_niveau DESC LIMIT 1');
    $spellLevelField = pg_magic_spell_level_field($db, $rulesetRep);

    foreach ($classes as $rowClass) {
      $pcId = (int)$rowClass['pc_id'];
      $claId = (int)$rowClass['pc_cla_id'];
      $pcNiveau = (int)$rowClass['pc_niveau'];
      $bonusNls = ($rulesetRep === 'DD3.5' && isset($nlsBonusByBasePc[$pcId])) ? (int)$nlsBonusByBasePc[$pcId] : 0;
      $nls = $pcNiveau + $bonusNls;
      if ($nls < 1) $nls = 1;

      $classData = [
        'pc_id' => $pcId,
        'cla_id' => $claId,
        'cla_nom' => (string)$rowClass['cla_nom'],
        'pc_niveau' => $pcNiveau,
        'nls' => $nls,
        'cla_mag_id' => (int)$rowClass['cla_mag_id'],
        'sort_known_all' => ((int)$rowClass['sort_known_all'] === 1) ? 1 : 0,
        'sort_auto_understood' => ((int)$rowClass['sort_auto_understood'] === 1) ? 1 : 0,
        'sort_requires_prepare' => ((int)$rowClass['sort_requires_prepare'] === 1) ? 1 : 0,
        'domain_bonus' => (((int)$rowClass['domain_divin'] === 1) || ((int)$rowClass['cla_clt_id'] === 3)) ? 1 : 0,
        'slots' => [],
        'spells' => [],
        'known_spells' => [],
        'has_understood' => false,
      ];

      if ($rulesetRep === 'DD3.5') {
        $stmtSlotsExact->execute([':claid' => $claId, ':niveau' => $nls]);
        $slotRow = $stmtSlotsExact->fetch(PDO::FETCH_ASSOC);
        if (!$slotRow) {
          $stmtSlotsFallback->execute([':claid' => $claId, ':niveau' => $nls]);
          $slotRow = $stmtSlotsFallback->fetch(PDO::FETCH_ASSOC);
        }

        $modRow = [];
        $carId = (int)$rowClass['cla_car_id'];
        if ($carId > 0 && isset($carMap[$carId])) {
          $fieldCar = 'pe_' . $carMap[$carId];
          if (isset($personnage[$fieldCar])) {
            $carValue = (int)$personnage[$fieldCar];
            if (isset($modMap[$carValue])) {
              $modRow = $modMap[$carValue];
            }
          }
        }

        for ($lvl = 0; $lvl <= 9; $lvl++) {
          $baseValue = null;
          if ($slotRow && isset($slotRow['cn_sort_n' . $lvl]) && $slotRow['cn_sort_n' . $lvl] !== '' && $slotRow['cn_sort_n' . $lvl] !== null) {
            $baseValue = (int)$slotRow['cn_sort_n' . $lvl];
          }
          $bonusValue = (isset($modRow['mod_bonusSort' . $lvl])) ? (int)$modRow['mod_bonusSort' . $lvl] : 0;
          if ($bonusValue < 0) $bonusValue = 0;

          if ($baseValue === null && $bonusValue === 0) {
            $classData['slots'][$lvl] = null;
            continue;
          }

          $total = (int)$baseValue + $bonusValue;
          if ($classData['domain_bonus'] === 1 && $lvl > 0 && $total > 0) {
            $total += 1;
          }
          $classData['slots'][$lvl] = $total;
        }
      }

      $resourceSql = '';
      $resourceParams = [];
      if (!empty($resourceIds)) {
        $resourcePlaceholders = [];
        $idxRes = 0;
        foreach ($resourceIds as $resId) {
          $ph = ':res' . $idxRes;
          $resourcePlaceholders[] = $ph;
          $resourceParams[$ph] = (int)$resId;
          $idxRes++;
        }
        $resourceSql = ' AND s.so_res_id IN (' . implode(',', $resourcePlaceholders) . ')';
      } else {
        // Aucune ressource active = aucune ligne (filtre strict)
        $resourceSql = ' AND 1=0';
      }

      $sqlSpells = "
        SELECT
          s.so_id,
          s.so_nom,
          COALESCE(co.co_nom, '') AS so_ecole,
          COALESCE(res.res_abreviation, '') AS so_source,
          COALESCE(" . ($spellLevelField === 'so_niveau' ? 's.so_niveau' : 'sc.sc_niveau') . ", 0) AS spell_level
        FROM dd_sortclasse sc
        JOIN dd_sorts s ON s.so_id = sc.sc_so_id
        LEFT JOIN dd_colleges co ON co.co_id = s.so_co_id
        LEFT JOIN dd_ressources res ON res.res_id = s.so_res_id
        WHERE sc.sc_cla_id = :claid
          {$resourceSql}
        ORDER BY spell_level ASC, s.so_nom ASC
      ";
      $paramsSpells = array_merge([':claid' => $claId], $resourceParams);
      $stmtSpells = $db->prepare($sqlSpells);
      $stmtSpells->execute($paramsSpells);
      $spellRows = $stmtSpells->fetchAll(PDO::FETCH_ASSOC);
      if (!isset($context['debug_sql'][$pcId])) $context['debug_sql'][$pcId] = [];
      $context['debug_sql'][$pcId]['spells'] = $sqlSpells;

      $debugSqlPes = null;
      $existingStates = pg_magic_load_existing_spell_states($db, $personnageId, $pcId, $claId, $spellRows, $debugSqlPes);
      $preparedStates = pg_magic_load_existing_prepared_states($db, $personnageId, $pcId, $claId);
      if ($debugSqlPes) {
        $context['debug_sql'][$pcId]['pes'] = [
          'sql' => $debugSqlPes,
          'params' => ['pcid' => $pcId],
        ];
      }

      // Liste stricte des sorts connus (dd_personnages_sorts) pour la vue "Sorts connus"
      $knownSpellRows = [];
      try {
        $knownSql = "
          SELECT
            s.so_id,
            s.so_nom,
            COALESCE(co.co_nom, '') AS so_ecole,
            COALESCE(sc.sc_niveau, 0) AS spell_level,
            ps.pes_id,
            COALESCE(ps.pes_compris,0) AS pes_compris
          FROM dd_personnages_sorts ps
          JOIN dd_sorts s ON s.so_id = ps.pes_so_id
          LEFT JOIN dd_colleges co ON co.co_id = s.so_co_id
          LEFT JOIN dd_sortclasse sc ON sc.sc_so_id = s.so_id AND sc.sc_cla_id = :claid
          WHERE ps.pes_pc_id = :pcid
        ";
        $stmtKnown = $db->prepare($knownSql);
        $stmtKnown->execute([':pcid' => $pcId, ':claid' => $claId]);
        $knownSpellRows = $stmtKnown->fetchAll(PDO::FETCH_ASSOC);
        $context['debug_sql'][$pcId]['known'] = ['sql' => $knownSql, 'params' => [':pcid' => $pcId, ':claid' => $claId]];
      } catch (Exception $e) {
        // ignore
      }

      // Indexe les sorts connus/compris (dd_personnages_sorts) par so_id pour marquer la liste de classe
      $knownMap = [];
      if (!empty($knownSpellRows)) {
        foreach ($knownSpellRows as $krow) {
          $knownMap[(int)$krow['so_id']] = [
            'pes_id' => (int)$krow['pes_id'],
            'pes_compris' => (int)$krow['pes_compris'],
          ];
        }
      }

      foreach ($spellRows as $spellRow) {
        $soId = (int)$spellRow['so_id'];
        $known = isset($knownMap[$soId]) ? 1 : 0;
        $understood = isset($knownMap[$soId]) ? (int)$knownMap[$soId]['pes_compris'] : 0;
        $existingPesId = isset($knownMap[$soId]) ? (int)$knownMap[$soId]['pes_id'] : null;

        $prepared = isset($preparedStates[$soId]) ? 1 : 0;
        $preparedLevel = $prepared ? (int)$preparedStates[$soId] : null;
        if ($prepared === 1) $known = 1;
        if ($known === 0 && $classData['sort_auto_understood'] !== 1) {
          $understood = 0;
        }
        if ($classData['sort_auto_understood'] === 1 && $known === 1) {
          $understood = 1;
        }

        if ($understood === 1) $classData['has_understood'] = true;

        $classData['spells'][$soId] = [
          'so_id' => $soId,
          'nom' => (string)$spellRow['so_nom'],
          'ecole' => (string)$spellRow['so_ecole'],
          'source' => (string)$spellRow['so_source'],
          'niveau' => (int)$spellRow['spell_level'],
          'known' => $known,
          'understood' => $understood,
          'prepared' => $prepared,
          'prepared_level' => $preparedLevel,
          'pes_id' => $existingPesId,
        ];

      }

      // Remplit la vue "sorts connus" exclusivement avec les lignes de dd_personnages_sorts
      $classData['known_spells'] = [];
      if (!empty($knownSpellRows)) {
        foreach ($knownSpellRows as $krow) {
          $classData['known_spells'][] = [
            'so_id' => (int)$krow['so_id'],
            'nom' => (string)$krow['so_nom'],
            'ecole' => (string)$krow['so_ecole'],
            'source' => '',
            'niveau' => (int)$krow['spell_level'],
            'known' => 1,
            'understood' => (int)$krow['pes_compris'] === 1 ? 1 : 0,
            'prepared' => 0,
            'prepared_level' => null,
            'pes_id' => (int)$krow['pes_id'],
          ];
          if ((int)$krow['pes_compris'] === 1) $classData['has_understood'] = true;
        }
      }

      // Liste dédiée aux sorts compris (dd_personnages_sorts.pes_compris = 1)
      $classData['understood_spells'] = [];
      if (!empty($knownSpellRows)) {
        foreach ($knownSpellRows as $krow) {
          if ((int)$krow['pes_compris'] !== 1) continue;
          $classData['understood_spells'][] = [
            'so_id' => (int)$krow['so_id'],
            'nom' => (string)$krow['so_nom'],
            'ecole' => (string)$krow['so_ecole'],
            'source' => '',
            'niveau' => (int)$krow['spell_level'],
            'known' => 1,
            'understood' => 1,
            'prepared' => 0,
            'prepared_level' => null,
            'pes_id' => (int)$krow['pes_id'],
          ];
        }
      }
      $context['classes'][$pcId] = $classData;
    }

    uasort($context['classes'], function ($a, $b) {
      if ((int)$a['nls'] === (int)$b['nls']) {
        return strcasecmp((string)$a['cla_nom'], (string)$b['cla_nom']);
      }
      return ((int)$b['nls'] <=> (int)$a['nls']);
    });

    foreach ($context['classes'] as $pcId => $classData) {
      $context['ordered_class_ids'][] = (int)$pcId;
    }

    $context['active_class_id'] = !empty($context['ordered_class_ids']) ? (int)$context['ordered_class_ids'][0] : 0;
    $context['has_spellcasting'] = !empty($context['ordered_class_ids']);
    return $context;
  }
}

if (!function_exists('pg_magic_load_existing_spell_states')) {
  function pg_magic_load_existing_spell_states($db, $personnageId, $pcId, $claId, array $spellRows, &$debugSql = null)
  {
    $personnageId = (int)$personnageId;
    $pcId = (int)$pcId;
    $claId = (int)$claId;
    if ($personnageId <= 0 || $pcId <= 0 || $claId <= 0) return [];

    $columns = pg_magic_table_columns($db, 'dd_personnages_sorts');
    if (empty($columns) || !in_array('pes_so_id', $columns, true)) return [];

    // pes_connu n'est plus utilisé : la présence de la ligne suffit à indiquer "connu"
    $knownCol = null;
    $understoodCol = in_array('pes_compris', $columns, true) ? 'pes_compris' : null;

    $selectKnown = '1';
    $selectUnderstood = $understoodCol ? "COALESCE({$understoodCol},0)" : '0';

    $where = '';
    $params = [];
    if (in_array('pes_pc_id', $columns, true)) {
      $where = 'pes_pc_id = :pcid';
      $params[':pcid'] = $pcId;
    } elseif (in_array('pes_pe_id', $columns, true) && in_array('pes_cla_id', $columns, true)) {
      $where = 'pes_pe_id = :pid AND pes_cla_id = :claid';
      $params[':pid'] = $personnageId;
      $params[':claid'] = $claId;
    } elseif (in_array('pes_pe_id', $columns, true)) {
      if (empty($spellRows)) return [];
      $where = 'pes_pe_id = :pid AND pes_so_id IN (';
      $params[':pid'] = $personnageId;
      $idx = 0;
      $placeholders = [];
      foreach ($spellRows as $spellRow) {
        $ph = ':so' . $idx;
        $placeholders[] = $ph;
        $params[$ph] = (int)$spellRow['so_id'];
        $idx++;
      }
      $where .= implode(',', $placeholders) . ')';
    } else {
      return [];
    }

    $sql = "
      SELECT
        pes_id,
        pes_so_id,
        {$selectKnown} AS known,
        {$selectUnderstood} AS understood
      FROM dd_personnages_sorts
      WHERE {$where}
    ";
    if (is_string($debugSql) || $debugSql === null) {
      $debugSql = $sql;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $states = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $soId = (int)$row['pes_so_id'];
      $states[$soId] = [
        'pes_id' => (int)$row['pes_id'],
        'known' => (int)$row['known'],
        'understood' => (int)$row['understood'],
      ];
    }
    return $states;
  }
}

if (!function_exists('pg_magic_load_existing_prepared_states')) {
  function pg_magic_load_existing_prepared_states($db, $personnageId, $pcId, $claId)
  {
    $columns = pg_magic_table_columns($db, 'dd_personnages_sorts_prepares');
    if (empty($columns) || !in_array('pesp_so_id', $columns, true)) return [];

    $where = '';
    $params = [];
    if (in_array('pesp_pc_id', $columns, true)) {
      $where = 'pesp_pc_id = :pcid';
      $params[':pcid'] = (int)$pcId;
    } elseif (in_array('pesp_pe_id', $columns, true) && in_array('pesp_cla_id', $columns, true)) {
      $where = 'pesp_pe_id = :pid AND pesp_cla_id = :claid';
      $params[':pid'] = (int)$personnageId;
      $params[':claid'] = (int)$claId;
    } else {
      return [];
    }

    $selectLevel = in_array('pesp_niveau', $columns, true) ? 'COALESCE(pesp_niveau,0)' : '0';
    $sql = "SELECT pesp_so_id, {$selectLevel} AS pesp_niveau FROM dd_personnages_sorts_prepares WHERE {$where}";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $prepared = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $prepared[(int)$row['pesp_so_id']] = (int)$row['pesp_niveau'];
    }
    return $prepared;
  }
}

if (!function_exists('pg_magic_normalize_state_flag')) {
  function pg_magic_normalize_state_flag($value)
  {
    return ((int)$value === 1) ? 1 : 0;
  }
}

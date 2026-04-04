<?php if (!$grimoireContext['has_spellcasting']): ?>
  <div class="nodata">Ce personnage ne possede aucune classe de lanceur de sorts.</div>
<?php else: ?>
  <div id="pg-root" class="personnage-grimoire" data-ruleset="DD2024">
    <div class="personnage-section">
      <div class="titre">
        <span class="personnage-section-title">
          <i class="fa-solid fa-hat-wizard personnage-section-icon"></i>
          <span>Classes de lanceur de sorts</span>
        </span>
      </div>
      <div id="pg-class-menu" class="menu_main contenu"></div>
    </div>

    <div class="personnage-section">
      <div class="titre">
        <span class="personnage-section-title">
          <i class="fa-solid fa-filter personnage-section-icon"></i>
          <span>Affichage des sorts</span>
        </span>
      </div>
      <div class="ligne">
        <span class="label">Filtre</span>
        <select id="pg-filter-select"></select>
      </div>
    </div>

    <div class="personnage-section">
      <div class="titre">
        <span class="personnage-section-title">
          <i class="fa-solid fa-wand-magic-sparkles personnage-section-icon"></i>
          <span>Sorts</span>
        </span>
      </div>
      <div id="pg-level-menu" class="menu_main contenu menu-chiffres"></div>
      <? if (isset($_SESSION['debug']) && (int)$_SESSION['debug'] === 1 && !empty($grimoireContext['debug_sql'])): ?>
        <?
        $activePcId = isset($grimoireContext['active_class_id']) ? (int)$grimoireContext['active_class_id'] : 0;
        $debugSqlSpells = '';
        $debugSqlKnownSql = '';
        $debugSqlKnownParams = [];
        if ($activePcId && isset($grimoireContext['debug_sql'][$activePcId])) {
          if (isset($grimoireContext['debug_sql'][$activePcId]['spells'])) $debugSqlSpells = $grimoireContext['debug_sql'][$activePcId]['spells'];
          if (isset($grimoireContext['debug_sql'][$activePcId]['known'])) {
            $debugSqlKnownSql = is_array($grimoireContext['debug_sql'][$activePcId]['known']) && isset($grimoireContext['debug_sql'][$activePcId]['known']['sql'])
              ? $grimoireContext['debug_sql'][$activePcId]['known']['sql']
              : (is_string($grimoireContext['debug_sql'][$activePcId]['known']) ? $grimoireContext['debug_sql'][$activePcId]['known'] : '');
            if (is_array($grimoireContext['debug_sql'][$activePcId]['known']) && isset($grimoireContext['debug_sql'][$activePcId]['known']['params'])) {
              $debugSqlKnownParams = $grimoireContext['debug_sql'][$activePcId]['known']['params'];
            }
          }
        }
        ?>
        <? if ($debugSqlKnownSql !== ''): ?>
          <div class="mb10">
            <div class="gris">SQL sorts connus (dd_personnages_sorts)</div>
            <? debug($debugSqlKnownSql); ?>
            <? if (!empty($debugSqlKnownParams)) debug(print_r($debugSqlKnownParams, true)); ?>
          </div>
        <? endif; ?>
      <? endif; ?>
      <div id="pg-spells-list" class="mt10"></div>
    </div>
  </div>
<?php endif; ?>

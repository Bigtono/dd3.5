// Gestion locale (sans ecriture BD immediate) de la page personnage-grimoire.php
function initPersonnageGrimoireEditor(config) {
  const ctx = (config && config.contexte) ? config.contexte : {};
  const canEdit = !!(config && config.canEdit);
  const filterSyncUrl = (config && config.filterSyncUrl) ? config.filterSyncUrl : '';

  const root = document.getElementById('pg-root');
  if (!root) return;

  const form = document.getElementById('personnage-grimoire-form');
  const menuClasses = document.getElementById('pg-class-menu');
  const filterSelect = document.getElementById('pg-filter-select');
  const levelMenu = document.getElementById('pg-level-menu');
  const spellsList = document.getElementById('pg-spells-list');
  const slotsTable = document.getElementById('pg-slots-table');
  const payloadInput = document.getElementById('mp_magic_state');
  const filterInput = document.getElementById('mp_magic_filter');

  const classes = ctx.classes || {};
  const orderedClassIds = (ctx.ordered_class_ids || []).map(function(id) { return String(id); });
  if (orderedClassIds.length === 0) return;

  const touchedClassIds = new Set();
  let activeClassId = String(ctx.active_class_id || orderedClassIds[0]);
  if (!classes[activeClassId]) activeClassId = orderedClassIds[0];

  let currentFilter = normalizeFilter(ctx.session_filter);
  let currentLevel = 1;

  function normalizeFilter(value) {
    const num = parseInt(value, 10) || 0;
    if (num < 1 || num > 4) return 0;
    return num;
  }

  function escapeHtml(value) {
    const str = String(value == null ? '' : value);
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function getClassData(pcId) {
    return classes[String(pcId)] || null;
  }

  function defaultFilterForClass(classData) {
    return (parseInt(classData.sort_known_all, 10) === 1) ? 2 : 1;
  }

  function recalcClassMeta(classData) {
    let hasUnderstood = false;
    Object.keys(classData.spells || {}).forEach(function(soId) {
      const spell = classData.spells[soId];
      if (parseInt(spell.understood, 10) === 1) hasUnderstood = true;
    });
    classData.has_understood = hasUnderstood;
  }

  function getFilterOptions(classData) {
    const autoUnderstood = parseInt(classData.sort_auto_understood, 10) === 1;

    return [
      { value: 1, label: 'Liste de sorts de classe', disabled: false },
      { value: 2, label: 'Sorts connus', disabled: false },
      { value: 3, label: 'Sorts compris', disabled: autoUnderstood || !classData.has_understood },
      { value: 4, label: 'Sorts prepares', disabled: false }
    ];
  }

  function isFilterAvailable(classData, filterValue) {
    const options = getFilterOptions(classData);
    for (let i = 0; i < options.length; i++) {
      if (options[i].value === filterValue) return options[i];
    }
    return null;
  }

  function chooseFilterForClass(classData, wantedFilter) {
    const options = getFilterOptions(classData);
    const match = isFilterAvailable(classData, wantedFilter);
    if (match && !match.disabled) return match.value;

    for (let i = 0; i < options.length; i++) {
      if (!options[i].disabled) return options[i].value;
    }
    return defaultFilterForClass(classData);
  }

  function getSpellsForFilter(classData, filterValue) {
    const spells = [];
    Object.keys(classData.spells || {}).forEach(function(soId) {
      spells.push(classData.spells[soId]);
    });
    const knownAll = parseInt(classData.sort_known_all, 10) === 1;

    let filtered = [];
    if (filterValue === 1) {
      filtered = spells;
    } else if (filterValue === 2) {
      filtered = Array.isArray(classData.known_spells) ? classData.known_spells.slice() : [];
    } else if (filterValue === 3) {
      if (Array.isArray(classData.understood_spells)) {
        filtered = classData.understood_spells.slice();
      } else {
        filtered = spells.filter(function(spell) { return parseInt(spell.understood, 10) === 1; });
      }
    } else if (filterValue === 4) {
      filtered = spells.filter(function(spell) { return parseInt(spell.prepared, 10) === 1; });
    } else {
      filtered = spells;
    }

    filtered.sort(function(a, b) {
      const lvlA = (filterValue === 4 && a.prepared_level != null) ? parseInt(a.prepared_level, 10) || 0 : (parseInt(a.niveau, 10) || 0);
      const lvlB = (filterValue === 4 && b.prepared_level != null) ? parseInt(b.prepared_level, 10) || 0 : (parseInt(b.niveau, 10) || 0);
      if (lvlA !== lvlB) return lvlA - lvlB;
      return String(a.nom).localeCompare(String(b.nom));
    });
    return filtered;
  }

  function actionConfigForFilter(classData, filterValue) {
    const autoUnderstood = parseInt(classData.sort_auto_understood, 10) === 1;

    if (filterValue === 1) return ['known'];
    if (filterValue === 2) {
      const actions = ['known'];
      if (!autoUnderstood) actions.push('understood');
      return actions;
    }
    if (filterValue === 3) return ['understood'];
    if (filterValue === 4) return [];
    return ['known'];
  }

  function actionLabel(actionName) {
    if (actionName === 'known') return 'Connaitre';
    if (actionName === 'understood') return 'Comprendre';
    if (actionName === 'prepared') return 'Preparer';
    return actionName;
  }

  function actionIcon(actionName) {
    if (actionName === 'known') return 'fa-check';
    if (actionName === 'understood') return 'fa-lightbulb';
    if (actionName === 'prepared') return 'fa-bookmark';
    return 'fa-circle';
  }

  function renderClassMenu() {
    if (!menuClasses) return;
    let html = '';
    orderedClassIds.forEach(function(pcId) {
      const classData = getClassData(pcId);
      if (!classData) return;
      const isActive = (String(pcId) === String(activeClassId));
      html += '<button type="button" class="btMain pg-class-item' + (isActive ? ' is-active' : '') + '" data-pc-id="' + escapeHtml(pcId) + '">';
      html += '<span class="titre_menu">' + escapeHtml(classData.cla_nom) + ' (' + escapeHtml(classData.nls) + ')</span>';
      html += '</button>';
    });
    menuClasses.innerHTML = html;
  }

  function renderFilterSelect() {
    if (!filterSelect) return;
    const classData = getClassData(activeClassId);
    if (!classData) return;

    currentFilter = chooseFilterForClass(classData, currentFilter || defaultFilterForClass(classData));
    const options = getFilterOptions(classData);
    let html = '';
    options.forEach(function(opt) {
      const selected = (opt.value === currentFilter) ? ' selected' : '';
      const disabled = opt.disabled ? ' disabled' : '';
      html += '<option value="' + opt.value + '"' + selected + disabled + '>' + escapeHtml(opt.label) + '</option>';
    });
    filterSelect.innerHTML = html;

    if (filterInput) filterInput.value = String(currentFilter);
  }

  function renderSlots() {
    if (!slotsTable) return;
    const classData = getClassData(activeClassId);
    if (!classData) {
      slotsTable.innerHTML = '';
      return;
    }

    if (String(ctx.ruleset) !== 'DD3.5') {
      slotsTable.innerHTML = '<div class="nodata">Cette section est reservee au ruleset DD3.5.</div>';
      return;
    }

    let html = '<table class="pg-slots-grid"><thead><tr>';
    for (let lvl = 0; lvl <= 9; lvl++) {
      html += '<th>' + lvl + '</th>';
    }
    html += '</tr></thead><tbody><tr>';
    for (let lvl = 0; lvl <= 9; lvl++) {
      const value = (classData.slots && classData.slots.hasOwnProperty(lvl)) ? classData.slots[lvl] : null;
      html += '<td>' + ((value === null || value === '') ? '-' : escapeHtml(value)) + '</td>';
    }
    html += '</tr></tbody></table>';
    slotsTable.innerHTML = html;
  }

  function spellLevelForView(spell) {
    if (currentFilter === 4 && spell.prepared_level != null) {
      return parseInt(spell.prepared_level, 10) || 0;
    }
    return parseInt(spell.niveau, 10) || 0;
  }

  function renderLevelsAndSpells() {
    const classData = getClassData(activeClassId);
    if (!classData) {
      if (levelMenu) levelMenu.innerHTML = '';
      if (spellsList) spellsList.innerHTML = '';
      return;
    }

    const spells = getSpellsForFilter(classData, currentFilter);
    const levelsMap = {};
    spells.forEach(function(spell) {
      const level = spellLevelForView(spell);
      if (!levelsMap[level]) levelsMap[level] = [];
      levelsMap[level].push(spell);
    });

    if (!levelsMap[currentLevel]) {
      if (levelsMap[1] && levelsMap[1].length > 0) {
        currentLevel = 1;
      } else {
        const availableLevels = Object.keys(levelsMap).map(function(l) { return parseInt(l, 10); }).sort(function(a, b) { return a - b; });
        currentLevel = (availableLevels.length > 0) ? availableLevels[0] : 0;
      }
    }

    if (levelMenu) {
      let menuHtml = '';
      for (let lvl = 0; lvl <= 9; lvl++) {
        const count = levelsMap[lvl] ? levelsMap[lvl].length : 0;
        const isActive = (lvl === currentLevel);
        const disabled = (count === 0) ? ' is-disabled' : '';
        menuHtml += '<button type="button" class="btMain pg-level-item' + (isActive ? ' is-active' : '') + disabled + '" data-level="' + lvl + '"' + (count === 0 ? ' disabled' : '') + '>';
        menuHtml += '<span class="titre_menu">' + lvl + '</span>';
        menuHtml += '</button>';
      }
      levelMenu.innerHTML = menuHtml;
    }

    if (!spellsList) return;
    const currentSpells = levelsMap[currentLevel] || [];
    if (currentSpells.length === 0) {
      spellsList.innerHTML = '<div class="nodata">Aucun sort pour cette vue.</div>';
      return;
    }

    const actions = actionConfigForFilter(classData, currentFilter);
    let html = '<div class="pg-spell-rows">';
    currentSpells.forEach(function(spell) {
      html += '<div class="pg-spell-row" data-so-id="' + spell.so_id + '">';
      html += '<div class="pg-spell-actions">';
      actions.forEach(function(action) {
        const isOn = parseInt(spell[action], 10) === 1;
        html += '<button type="button" class="pg-action-icon' + (isOn ? ' is-on' : '') + '" data-action="' + action + '" title="' + escapeHtml(actionLabel(action)) + '"' + (canEdit ? '' : ' disabled') + '>';
        html += '<i class="fa-solid ' + actionIcon(action) + '"></i>';
        html += '</button>';
      });
      html += '</div>';
      const idSuffix = (currentFilter === 2 && spell.pes_id) ? ' (' + escapeHtml(spell.pes_id) + ')' : '';
      html += '<div class="pg-spell-name lien" data-show-sort="' + spell.so_id + '">' + escapeHtml(spell.nom) + idSuffix + '</div>';
      html += '<div class="pg-spell-school">' + escapeHtml(spell.ecole || '') + '</div>';
      html += '</div>';
    });
    html += '</div>';
    spellsList.innerHTML = html;
  }

  function syncFilterToSession(filterValue) {
    if (!filterSyncUrl || !ctx.personnage_id) return;
    const params = new URLSearchParams();
    params.set('personnage', String(ctx.personnage_id));
    params.set('filtre', String(filterValue));
    fetch(filterSyncUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body: params.toString()
    }).catch(function() {});
  }

  function toggleSpellAction(classData, spell, actionName) {
    if (!canEdit) return;

    if (actionName === 'known') {
      spell.known = (parseInt(spell.known, 10) === 1) ? 0 : 1;
      if (parseInt(spell.known, 10) === 0) {
        if (parseInt(classData.sort_auto_understood, 10) !== 1) {
          spell.understood = 0;
        }
      } else if (parseInt(classData.sort_auto_understood, 10) === 1) {
        spell.understood = 1;
      }
    } else if (actionName === 'understood') {
      if (parseInt(classData.sort_auto_understood, 10) === 1) return;
      spell.understood = (parseInt(spell.understood, 10) === 1) ? 0 : 1;
      if (parseInt(spell.understood, 10) === 1) spell.known = 1;
    } else if (actionName === 'prepared') {
      spell.prepared = (parseInt(spell.prepared, 10) === 1) ? 0 : 1;
      if (parseInt(spell.prepared, 10) === 1) {
        spell.known = 1;
        if (parseInt(classData.sort_auto_understood, 10) === 1) spell.understood = 1;
      }
    }

    if (parseInt(spell.known, 10) === 0) {
      spell.prepared = 0;
      if (parseInt(classData.sort_auto_understood, 10) !== 1) {
        spell.understood = 0;
      }
    }
    if (parseInt(classData.sort_auto_understood, 10) === 1 && parseInt(spell.known, 10) === 1) {
      spell.understood = 1;
    }

    touchedClassIds.add(String(classData.pc_id));
    recalcClassMeta(classData);
  }

  function buildPayload() {
    const payload = {
      touched_class_ids: [],
      classes: {}
    };

    touchedClassIds.forEach(function(pcId) {
      const classData = getClassData(pcId);
      if (!classData) return;

      payload.touched_class_ids.push(parseInt(pcId, 10));
      const classPayload = {
        pc_id: parseInt(classData.pc_id, 10),
        cla_id: parseInt(classData.cla_id, 10),
        spells: {}
      };

      Object.keys(classData.spells || {}).forEach(function(soId) {
        const spell = classData.spells[soId];
        classPayload.spells[soId] = {
          known: parseInt(spell.known, 10) === 1 ? 1 : 0,
          understood: parseInt(spell.understood, 10) === 1 ? 1 : 0,
          prepared: parseInt(spell.prepared, 10) === 1 ? 1 : 0,
          niveau: parseInt(spell.niveau, 10) || 0
        };
      });

      payload.classes[pcId] = classPayload;
    });

    return payload;
  }

  if (menuClasses) {
    menuClasses.addEventListener('click', function(event) {
      const btn = event.target.closest('.pg-class-item');
      if (!btn) return;
      const pcId = String(btn.getAttribute('data-pc-id') || '');
      if (!pcId || !getClassData(pcId)) return;
      activeClassId = pcId;
      const activeClass = getClassData(activeClassId);
      currentFilter = chooseFilterForClass(activeClass, currentFilter || defaultFilterForClass(activeClass));
      currentLevel = 1;
      renderClassMenu();
      renderSlots();
      renderFilterSelect();
      renderLevelsAndSpells();
    });
  }

  if (filterSelect) {
    filterSelect.addEventListener('change', function() {
      const classData = getClassData(activeClassId);
      if (!classData) return;
      const wanted = normalizeFilter(filterSelect.value);
      currentFilter = chooseFilterForClass(classData, wanted);
      currentLevel = 1;
      renderFilterSelect();
      renderLevelsAndSpells();
      syncFilterToSession(currentFilter);
    });
  }

  if (levelMenu) {
    levelMenu.addEventListener('click', function(event) {
      const btn = event.target.closest('.pg-level-item');
      if (!btn || btn.disabled) return;
      currentLevel = parseInt(btn.getAttribute('data-level'), 10) || 0;
      renderLevelsAndSpells();
    });
  }

  if (spellsList) {
    spellsList.addEventListener('click', function(event) {
      const sortLink = event.target.closest('[data-show-sort]');
      if (sortLink) {
        const soId = parseInt(sortLink.getAttribute('data-show-sort'), 10) || 0;
        if (soId > 0 && typeof afficherSort === 'function') {
          afficherSort(soId);
        }
        return;
      }

      const actionBtn = event.target.closest('.pg-action-icon');
      if (!actionBtn || actionBtn.disabled) return;
      const row = actionBtn.closest('.pg-spell-row');
      if (!row) return;
      const soId = String(row.getAttribute('data-so-id') || '');
      if (!soId) return;
      const classData = getClassData(activeClassId);
      if (!classData || !classData.spells || !classData.spells[soId]) return;

      const actionName = actionBtn.getAttribute('data-action') || '';
      toggleSpellAction(classData, classData.spells[soId], actionName);
      currentFilter = chooseFilterForClass(classData, currentFilter);
      renderFilterSelect();
      renderLevelsAndSpells();
    });
  }

  if (form) {
    form.addEventListener('submit', function() {
      if (filterInput) filterInput.value = String(currentFilter);
      if (payloadInput) {
        payloadInput.value = JSON.stringify(buildPayload());
      }
    });
  }

  const activeClass = getClassData(activeClassId);
  currentFilter = activeClass ? chooseFilterForClass(activeClass, currentFilter || defaultFilterForClass(activeClass)) : 0;
  renderClassMenu();
  renderSlots();
  renderFilterSelect();
  renderLevelsAndSpells();
}

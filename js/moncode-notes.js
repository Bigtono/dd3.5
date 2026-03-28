// JavaScript Document
// Fonctions dediees a la gestion des notes

(function (window, $) {
  if (!$) return;
  const NOTE_REOPEN_KEY = 'dd_note_reopen_context';
  let noteContext = null;

  function nextContenuIndex() {
    let max = 0;
    $('#note-contenus-list .note-contenu-row').each(function () {
      const val = parseInt($(this).attr('data-row'), 10);
      if (!Number.isNaN(val) && val > max) max = val;
    });
    return max + 1;
  }

  function buildContenuRowHtml(index, dd) {
    const rowId = 'mp_noc_' + index;
    const selectedDd = parseInt(dd || 10, 10);
    let options = '';
    for (let i = 1; i <= 99; i++) {
      options += '<option value="' + i + '"' + (i === selectedDd ? ' selected="selected"' : '') + '>DD ' + i + '</option>';
    }
    return '' +
      '<div class="note-contenu-row line-data-fr100 mb10" data-row="' + index + '">' +
      '  <div class="ligne mb5">' +
      '    <div class="label w90">DD</div>' +
      '    <select class="note-contenu-dd">' + options + '</select>' +
      '    <button type="button" class="bouton ml15" onclick="NoteActions.removeContenuRow(this)">Supprimer</button>' +
      '  </div>' +
      '  <textarea id="' + rowId + '" class="input_texte note-contenu-texte"></textarea>' +
      '</div>';
  }

  function collectContenusPayload() {
    const contenus = [];
    $('#note-contenus-list .note-contenu-row').each(function () {
      const $row = $(this);
      const dd = parseInt($row.find('.note-contenu-dd').val(), 10) || 0;
      const textareaId = $row.find('.note-contenu-texte').attr('id');
      let texte = '';
      if (textareaId && window.CKEDITOR && CKEDITOR.instances[textareaId]) {
        texte = CKEDITOR.instances[textareaId].getData();
      } else {
        texte = $row.find('.note-contenu-texte').val() || '';
      }
      texte = (texte || '').trim();
      if (dd > 0 && texte !== '') contenus.push({ dd: dd, texte: texte });
    });
    return contenus;
  }

  function parseIdsCsv(csv) {
    const out = [];
    const parts = String(csv || '').split(',');
    for (let i = 0; i < parts.length; i++) {
      const id = parseInt(parts[i], 10);
      if (!Number.isNaN(id) && id > 0) out.push(id);
    }
    return out;
  }

  function renderDetailResponse(reponse) {
    const raw = String(reponse || '');
    const sepIdx = raw.indexOf('@');
    if (sepIdx < 0) {
      alert('Reponse serveur invalide.');
      return;
    }
    const payload = raw.substring(sepIdx + 1);
    if (!payload) {
      alert('Aucun contenu a afficher.');
      return;
    }
    $('#modification').hide();
    $('#detail-pp').html(payload).show('fast');
  }

  function getCurrentSourcePage() {
    const path = String((window.location && window.location.pathname) || '');
    const parts = path.split('/');
    const file = parts.length ? parts[parts.length - 1] : '';
    return file || '';
  }

  function parseIntSafe(value, fallback) {
    const parsed = parseInt(value, 10);
    return Number.isNaN(parsed) ? fallback : parsed;
  }

  function updateNoteContext(note, accreditation, perso) {
    noteContext = {
      noteId: parseIntSafe(note, 0),
      accreditation: parseIntSafe(accreditation, 0),
      perso: parseIntSafe(perso, 0),
      sourcePage: getCurrentSourcePage()
    };
  }

  function getNoteContext() {
    return noteContext;
  }

  function shouldReloadAndReopen(ctx) {
    const currentPage = getCurrentSourcePage();
    if (currentPage === 'notes.php' || currentPage === 'personnage-connaissances.php') return true;
    if ($('#bulk-notes').length > 0) return true;
    if ($('#listeNotesPerso').length > 0) return true;
    if (!ctx || !ctx.sourcePage) return false;
    return ctx.sourcePage === 'notes.php' || ctx.sourcePage === 'personnage-connaissances.php';
  }

  function persistReopenContext(ctx) {
    if (!window.sessionStorage || !ctx) return;
    try {
      window.sessionStorage.setItem(NOTE_REOPEN_KEY, JSON.stringify(ctx));
    } catch (e) {
      // Ignore storage failures (private mode/quota).
    }
  }

  function consumeReopenContext() {
    if (!window.sessionStorage) return null;
    try {
      const raw = window.sessionStorage.getItem(NOTE_REOPEN_KEY);
      if (!raw) return null;
      window.sessionStorage.removeItem(NOTE_REOPEN_KEY);
      const parsed = JSON.parse(raw);
      if (!parsed || !parsed.noteId) return null;
      return {
        noteId: parseIntSafe(parsed.noteId, 0),
        accreditation: parseIntSafe(parsed.accreditation, 0),
        perso: parseIntSafe(parsed.perso, 0),
        sourcePage: String(parsed.sourcePage || '')
      };
    } catch (e) {
      return null;
    }
  }

  function getSelectedBulkIds(scope) {
    const ids = [];
    $('.bulk-row-checkbox[data-bulk-scope="' + scope + '"]:checked').each(function () {
      const id = parseInt($(this).attr('data-bulk-id'), 10);
      if (!Number.isNaN(id) && id > 0) ids.push(id);
    });
    return ids;
  }

  const BulkActions = {
    registries: {},

    register: function (scope, config) {
      this.registries[scope] = config;
      this.init(scope);
    },

    init: function (scope) {
      const cfg = this.registries[scope];
      if (!cfg) return;

      const $select = $(cfg.actionSelectSelector);
      const $selectAll = $(cfg.selectAllSelector);
      if (!$select.length || !$selectAll.length) return;

      $select.empty();
      $select.append('<option value="">Choisir une action</option>');
      Object.keys(cfg.actions).forEach(function (key) {
        const a = cfg.actions[key];
        const disabledAttr = a.enabled ? '' : ' disabled="disabled"';
        const label = a.enabled ? a.label : (a.label + ' (indisponible)');
        $select.append('<option value="' + key + '"' + disabledAttr + '>' + label + '</option>');
      });

      $selectAll.off('change').on('change', function () {
        const checked = $(this).is(':checked');
        $(cfg.rowCheckboxSelector).prop('checked', checked);
      });

      $(document).off('change.bulk.' + scope, cfg.rowCheckboxSelector).on('change.bulk.' + scope, cfg.rowCheckboxSelector, function () {
        const total = $(cfg.rowCheckboxSelector).length;
        const checked = $(cfg.rowCheckboxSelector + ':checked').length;
        $selectAll.prop('checked', total > 0 && total === checked);
      });

      this.updateHint(scope);
      $select.off('change').on('change', () => this.updateHint(scope));
    },

    updateHint: function (scope) {
      const cfg = this.registries[scope];
      if (!cfg) return;
      const actionKey = $(cfg.actionSelectSelector).val();
      const $hint = $(cfg.hintSelector);
      if (!$hint.length) return;
      $hint.removeClass('bulk-action-disabled');
      if (!actionKey || !cfg.actions[actionKey]) {
        $hint.text('');
        return;
      }
      const action = cfg.actions[actionKey];
      if (action.enabled) {
        $hint.text('');
        return;
      }
      $hint.text(action.disabledReason || 'Action indisponible.');
      $hint.addClass('bulk-action-disabled');
    },

    openActionForm: function (scope) {
      const cfg = this.registries[scope];
      if (!cfg) return;

      const ids = getSelectedBulkIds(scope);
      if (!ids.length) {
        alert('Selectionne au moins une ligne.');
        return;
      }

      const actionKey = $(cfg.actionSelectSelector).val();
      if (!actionKey || !cfg.actions[actionKey]) {
        alert('Choisis une action.');
        return;
      }

      const action = cfg.actions[actionKey];
      if (!action.enabled) {
        alert(action.disabledReason || 'Cette action est indisponible.');
        return;
      }

      $.ajax({
        type: 'POST',
        url: action.formUrl,
        data: 'note_ids=' + encodeURIComponent(ids.join(',')),
        dataType: 'text',
        success: renderDetailResponse,
        error: function () { alert('Erreur ouverture formulaire action de masse.'); }
      });
    }
  };

  const NoteActions = {
    afficherNote: function (note, accreditation, perso) {
      const noteId = parseIntSafe(note, 0);
      const acc = parseIntSafe(accreditation, 0);
      const peId = parseIntSafe(perso, 0);
      updateNoteContext(noteId, acc, peId);
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-affichageNote.php',
        data: 'note=' + noteId + '&accreditation=' + acc + '&perso=' + peId,
        dataType: 'text',
        success: actualiserPage,
        error: function () { alert('Erreur afficherNote'); }
      });
    },

    modifierNote: function (note, perso) {
      const noteId = parseIntSafe(note, 0);
      let peId = parseIntSafe(perso, 0);
      const ctx = getNoteContext();
      if (peId <= 0 && ctx && ctx.noteId === noteId) peId = parseIntSafe(ctx.perso, 0);
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-modifierNote.php',
        data: 'note=' + noteId + '&perso=' + peId,
        dataType: 'text',
        success: actualiserPageModif,
        error: function () { alert('Erreur modifierNote'); }
      });
    },

    supprimerNote: function (note, perso) {
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-validerSupprNote.php',
        data: 'perso=' + (perso || 0) + '&note=' + note,
        dataType: 'text',
        success: NoteActions.actualiserNote,
        error: function () { alert('Erreur supprimerNote'); }
      });
    },

    addContenuRow: function (dd) {
      const index = nextContenuIndex();
      $('#note-contenus-list').append(buildContenuRowHtml(index, dd || 10));
      const editorId = 'mp_noc_' + index;
      if (window.CKEDITOR) CKEDITOR.replace(editorId);
    },

    removeContenuRow: function (btn) {
      const $row = $(btn).closest('.note-contenu-row');
      if ($('#note-contenus-list .note-contenu-row').length <= 1) {
        alert('Une note doit contenir au moins un contenu.');
        return;
      }
      const textareaId = $row.find('.note-contenu-texte').attr('id');
      if (textareaId && window.CKEDITOR && CKEDITOR.instances[textareaId]) {
        CKEDITOR.instances[textareaId].destroy(true);
      }
      $row.remove();
    },

    validerModifNote: function (perso) {
      let diffusion = '';
      $('.diffusion').each(function () {
        if ($(this).val() > 0) diffusion += $(this).attr('id') + 'a' + $(this).val();
      });

      let mp_no_tags = [];
      $('.note-tag-checkbox:checked').each(function () {
        mp_no_tags.push($(this).val());
      });
      const mp_new_tags = $('#mp_new_tags').length ? $('#mp_new_tags').val() : '';
      const contenus = collectContenusPayload();

      if (contenus.length === 0) {
        alert('Ajoute au moins un contenu avec un DD et un texte.');
        return;
      }

      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-validerModifNote.php',
        data: 'mp_no_id=' + $('#mp_no_id').val() +
          '&mp_no_nom=' + encodeURIComponent($('#mp_no_nom').val()) +
          '&mp_no_tyno_id=' + $('#mp_no_tyno_id').val() +
          '&contenus_json=' + encodeURIComponent(JSON.stringify(contenus)) +
          '&diffusion=' + diffusion +
          '&mp_no_tags=' + encodeURIComponent(mp_no_tags.join(',')) +
          '&mp_new_tags=' + encodeURIComponent(mp_new_tags),
        dataType: 'text',
        success: function (reponse) {
          var resultat = reponse.split('@');
          if (!resultat[0] || parseInt(resultat[0], 10) <= 0) {
            alert(reponse);
            return;
          }
          const savedNoteId = parseIntSafe(resultat[0], 0);
          const ctx = getNoteContext() || {};
          const effectiveContext = {
            noteId: savedNoteId,
            accreditation: parseIntSafe(ctx.accreditation, 999),
            perso: parseIntSafe((perso || 0), parseIntSafe(ctx.perso, 0)),
            sourcePage: String(ctx.sourcePage || getCurrentSourcePage())
          };

          if (shouldReloadAndReopen(effectiveContext)) {
            persistReopenContext(effectiveContext);
            $('#modification').hide();
            window.location.reload();
            return;
          }

          $('#no' + savedNoteId).html(resultat[1]);
          $('#modification').hide();
          NoteActions.afficherNote(savedNoteId, effectiveContext.accreditation, effectiveContext.perso);
        },
        error: function () { alert('Erreur validerModifNote()'); }
      });
    },

    actualiserNote: function (reponse) {
      var resultat = (reponse || '').split('@');
      if (resultat[0]) $('#' + resultat[0]).hide();
      $('#detail-pp').hide();
      $('#modification').hide();
    },

    bulkOpenActionForm: function (scope) {
      BulkActions.openActionForm(scope || 'notes');
    },

    bulkApplyDelete: function () {
      const ids = parseIdsCsv($('#bulk-note-ids').val());
      if (!ids.length) {
        alert('Aucune note selectionnee.');
        return;
      }

      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-note-bulk-delete-apply.php',
        data: 'note_ids=' + encodeURIComponent(ids.join(',')),
        dataType: 'json',
        success: function (data) {
          if (!data || !data.success) {
            alert((data && data.message) ? data.message : 'Erreur suppression en masse.');
            return;
          }
          if (typeof fermerDetail === 'function') fermerDetail();
          window.location.reload();
        },
        error: function () { alert('Erreur validation suppression en masse.'); }
      });
    },

    bulkAssignApplyAll: function () {
      const dd = parseInt($('#bulk-global-dd').val(), 10) || 0;
      if (dd <= 0) return;
      $('.bulk-assign-dd').val(String(dd));
    },

    applyDiffusionDdToAll: function () {
      const dd = parseInt($('#note-global-dd').val(), 10) || 0;
      if (dd <= 0) return;
      $('.diffusion').val(String(dd));
    },

    bulkApplyAssign: function () {
      const ids = parseIdsCsv($('#bulk-note-ids').val());
      if (!ids.length) {
        alert('Aucune note selectionnee.');
        return;
      }

      const assignments = {};
      $('.bulk-assign-dd').each(function () {
        const peId = parseInt($(this).attr('data-pe-id'), 10);
        const dd = parseInt($(this).val(), 10);
        if (!Number.isNaN(peId) && peId > 0 && !Number.isNaN(dd) && dd >= 1 && dd <= 35) {
          assignments[peId] = dd;
        }
      });

      if (!Object.keys(assignments).length) {
        alert('Aucune affectation valide a enregistrer.');
        return;
      }

      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-note-bulk-assign-apply.php',
        data: 'note_ids=' + encodeURIComponent(ids.join(',')) + '&assignments_json=' + encodeURIComponent(JSON.stringify(assignments)),
        dataType: 'json',
        success: function (data) {
          if (!data || !data.success) {
            alert((data && data.message) ? data.message : 'Erreur affectation en masse.');
            return;
          }
          if (typeof fermerDetail === 'function') fermerDetail();
          window.location.reload();
        },
        error: function () { alert('Erreur validation affectation en masse.'); }
      });
    },

    bulkApplyAddTags: function () {
      const ids = parseIdsCsv($('#bulk-note-ids').val());
      if (!ids.length) {
        alert('Aucune note selectionnee.');
        return;
      }

      let selectedTags = [];
      $('.note-tag-checkbox:checked').each(function () {
        selectedTags.push($(this).val());
      });
      const newTags = $('#mp_new_tags').length ? $('#mp_new_tags').val() : '';

      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-note-bulk-tags-apply.php',
        data: 'note_ids=' + encodeURIComponent(ids.join(',')) +
          '&mp_no_tags=' + encodeURIComponent(selectedTags.join(',')) +
          '&mp_new_tags=' + encodeURIComponent(newTags),
        dataType: 'json',
        success: function (data) {
          if (!data || !data.success) {
            alert((data && data.message) ? data.message : 'Erreur ajout de tags en masse.');
            return;
          }
          if (typeof fermerDetail === 'function') fermerDetail();
          window.location.reload();
        },
        error: function () { alert('Erreur validation ajout tags en masse.'); }
      });
    }
  };

  window.NoteActions = NoteActions;
  window.BulkActions = BulkActions;

  $(function () {
    const reopenCtx = consumeReopenContext();
    if (reopenCtx && reopenCtx.noteId > 0) {
      const currentPage = getCurrentSourcePage();
      if (!reopenCtx.sourcePage || reopenCtx.sourcePage === currentPage) {
        NoteActions.afficherNote(reopenCtx.noteId, reopenCtx.accreditation, reopenCtx.perso);
      }
    }

    const $bulk = $('#bulk-notes[data-bulk-scope="notes"]');
    if (!$bulk.length) return;

    const hasCampagneActive = parseInt($bulk.attr('data-campagne-active'), 10) === 1;
    BulkActions.register('notes', {
      rowCheckboxSelector: '.bulk-row-checkbox[data-bulk-scope="notes"]',
      selectAllSelector: '#bulk-select-all-notes',
      actionSelectSelector: '#bulk-action-select-notes',
      hintSelector: '#bulk-action-hint-notes',
      actions: {
        delete: {
          label: 'Supprimer les notes',
          formUrl: 'ajax/ajax-note-bulk-delete-form.php',
          enabled: true,
          disabledReason: ''
        },
        assign: {
          label: 'Affecter les notes',
          formUrl: 'ajax/ajax-note-bulk-assign-form.php',
          enabled: hasCampagneActive,
          disabledReason: 'Selectionnez une campagne active pour activer cette action.'
        },
        add_tags: {
          label: 'Ajouter des tags',
          formUrl: 'ajax/ajax-note-bulk-tags-form.php',
          enabled: true,
          disabledReason: ''
        }
      }
    });
  });

  window.getCurrentNoteContext = getNoteContext;
})(window, window.jQuery);

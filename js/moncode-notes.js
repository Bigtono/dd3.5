// JavaScript Document
// Fonctions dediees a la gestion des notes

(function (window, $) {
  if (!$) return;

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

  const NoteActions = {
    afficherNote: function (note, accreditation, perso) {
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-affichageNote.php',
        data: "note=" + note + "&accreditation=" + (accreditation || 0) + "&perso=" + (perso || 0),
        dataType: 'text',
        success: actualiserPage,
        error: function () { alert('Erreur afficherNote'); }
      });
    },

    modifierNote: function (note, perso) {
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-modifierNote.php',
        data: "note=" + note + "&perso=" + (perso || 0),
        dataType: 'text',
        success: actualiserPageModif,
        error: function () { alert('Erreur modifierNote'); }
      });
    },

    supprimerNote: function (note, perso) {
      $.ajax({
        type: 'POST',
        url: 'ajax/ajax-validerSupprNote.php',
        data: "perso=" + (perso || 0) + "&note=" + note,
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
      let diffusion = "";
      $(".diffusion").each(function () {
        if ($(this).val() > 0) diffusion += $(this).attr('id') + 'a' + $(this).val();
      });

      let mp_no_tags = [];
      $(".note-tag-checkbox:checked").each(function () {
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
        data: "mp_no_id=" + $('#mp_no_id').val() +
          "&mp_no_nom=" + encodeURIComponent($('#mp_no_nom').val()) +
          "&mp_no_tyno_id=" + $('#mp_no_tyno_id').val() +
          "&contenus_json=" + encodeURIComponent(JSON.stringify(contenus)) +
          "&diffusion=" + diffusion +
          "&mp_no_tags=" + encodeURIComponent(mp_no_tags.join(',')) +
          "&mp_new_tags=" + encodeURIComponent(mp_new_tags),
        dataType: 'text',
        success: function (reponse) {
          var resultat = reponse.split("@");
          if (!resultat[0] || parseInt(resultat[0], 10) <= 0) {
            alert(reponse);
            return;
          }
          $("#no" + resultat[0]).html(resultat[1]);
          $("#modification").hide();
          NoteActions.afficherNote(resultat[0], 0, perso || 0);
        },
        error: function () { alert('Erreur validerModifNote()'); }
      });
    },

    actualiserNote: function (reponse) {
      var resultat = (reponse || '').split("@");
      if (resultat[0]) $('#' + resultat[0]).hide();
      $("#detail-pp").hide();
      $("#modification").hide();
    }
  };

  window.NoteActions = NoteActions;
})(window, window.jQuery);


// JavaScript Document
//##############################MOTEUR AJAX 1############################################
var personnageClassesState = null;
var personnageCompetencesState = null;

function escHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function buildNiveauOptions(selected, niveauMax) {
  var html = '';
  for (var i = 1; i <= niveauMax; i++) {
    html += '<option value="' + i + '"' + (i === selected ? ' selected' : '') + '>' + i + '</option>';
  }
  return html;
}

function getCurrentSelectedClasseIds() {
  if (!personnageClassesState) return {};
  var selected = {};
  personnageClassesState.existing.forEach(function (item) {
    if (!item.deleted) selected[item.cla_id] = true;
  });
  personnageClassesState.added.forEach(function (item) {
    if (!item.deleted) selected[item.cla_id] = true;
  });
  return selected;
}

function buildClassesPayloadInputs() {
  if (!personnageClassesState) return;
  var payload = document.getElementById('classesPayload');
  if (!payload) return;

  var html = '';
  html += '<input type="hidden" name="mp_classes_payload_ready" value="1">';
  personnageClassesState.existing.forEach(function (item) {
    if (item.deleted) {
      html += '<input type="hidden" name="mp_class_delete_ids[]" value="' + item.pc_id + '">';
    } else {
      html += '<input type="hidden" name="mp_class_keep_ids[]" value="' + item.pc_id + '">';
      html += '<input type="hidden" name="mp_class_keep_niveau[' + item.pc_id + ']" value="' + item.niveau + '">';
    }
  });
  personnageClassesState.added.forEach(function (item) {
    if (item.deleted) return;
    html += '<input type="hidden" name="mp_class_add_cla_id[]" value="' + item.cla_id + '">';
    html += '<input type="hidden" name="mp_class_add_niveau[]" value="' + item.niveau + '">';
  });
  payload.innerHTML = html;
}

function refreshNlsPrestigeSectionsVisibility() {
  var blocks = document.querySelectorAll('.js-nls-prestige-block');
  if (!blocks || blocks.length === 0 || !personnageClassesState) return;

  var activePrestigePcIds = {};
  personnageClassesState.existing.forEach(function (item) {
    if (!item.deleted) activePrestigePcIds[item.pc_id] = true;
  });

  var visibleCount = 0;
  blocks.forEach(function (block) {
    var pcId = parseInt(block.getAttribute('data-pc-id-prestige') || '0', 10);
    var visible = !!activePrestigePcIds[pcId];
    block.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  var wrapper = document.getElementById('nls-section-wrapper');
  if (wrapper) wrapper.style.display = visibleCount > 0 ? '' : 'none';
}

function renderClassesEditor() {
  if (!personnageClassesState) return;
  var classesDiv = document.getElementById('classes');
  if (!classesDiv) return;

  var html = '';
  personnageClassesState.existing.forEach(function (item) {
    if (item.deleted) return;
    var selectId = 'pcnexisting-' + item.pc_id;
    html += '<div id="pc' + item.pc_id + '" class="classe">';
    html += '  <div onClick="supprimerClassePerso(' + personnageClassesState.personnageId + ',\'existing-' + item.pc_id + '\')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
    html += '  <div class="libelle_classe">' + escHtml(item.cla_nom) + '</div>';
    html += '  <select class="niveau_classe" id="' + selectId + '" onChange="majNiveauClassePerso(\'' + selectId + '\')">';
    html += buildNiveauOptions(item.niveau, item.niveau_max);
    html += '  </select>';
    html += '</div>';
  });

  personnageClassesState.added.forEach(function (item, idx) {
    if (item.deleted) return;
    var selectId = 'pcnnew-' + idx;
    html += '<div id="new' + idx + '" class="classe">';
    html += '  <div onClick="supprimerClassePerso(' + personnageClassesState.personnageId + ',\'new-' + idx + '\')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
    html += '  <div class="libelle_classe">' + escHtml(item.cla_nom) + '</div>';
    html += '  <select class="niveau_classe" id="' + selectId + '" onChange="majNiveauClassePerso(\'' + selectId + '\')">';
    html += buildNiveauOptions(item.niveau, item.niveau_max);
    html += '  </select>';
    html += '</div>';
  });

  if (html === '') html = '<div class="ml10">Aucune classe</div>';
  classesDiv.innerHTML = html;
  buildClassesPayloadInputs();
  refreshNlsPrestigeSectionsVisibility();
}

function initPersonnageClassesEditor(config) {
  personnageClassesState = {
    personnageId: parseInt(config.personnageId || 0, 10),
    existing: (config.classesExistantes || []).map(function (item) {
      return {
        pc_id: parseInt(item.pc_id, 10),
        cla_id: parseInt(item.cla_id, 10),
        cla_nom: item.cla_nom,
        niveau: parseInt(item.niveau, 10),
        niveau_max: parseInt(item.niveau_max, 10),
        deleted: false
      };
    }),
    added: [],
    catalog: (config.classesCatalogue || []).map(function (item) {
      return {
        cla_id: parseInt(item.cla_id, 10),
        cla_nom: item.cla_nom,
        niveau_max: parseInt(item.niveau_max, 10)
      };
    })
  };

  var form = document.getElementById('modif-personnage');
  if (form) {
    form.addEventListener('submit', function () {
      buildClassesPayloadInputs();
    });
  }
  renderClassesEditor();
}

function ajouterClassePerso(id) {
  if (!personnageClassesState || parseInt(id, 10) <= 0) return;
  var selected = getCurrentSelectedClasseIds();
  var available = personnageClassesState.catalog.filter(function (item) {
    return !selected[item.cla_id];
  });

  var detail = document.getElementById('detail-pp');
  if (!detail) return;
  if (available.length === 0) {
    detail.innerHTML = '<div class="affichage"><div class="popup-contenu"><div class="titreAction"><div class="titreA">Ajouter une classe</div><div class="lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div><div>Toutes les classes du ruleset sont deja affectees.</div></div></div>';
    detail.style.display = 'block';
    return;
  }

  var options = '';
  available.forEach(function (item) {
    options += '<option value="' + item.cla_id + '" data-niveaumax="' + item.niveau_max + '">' + escHtml(item.cla_nom) + '</option>';
  });

  var html = '';
  html += '<div class="affichage">';
  html += '<div class="popup-contenu">';
  html += '  <div class="titreAction"><div class="titreA">Ajouter une classe</div><div class="lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div>';
  html += '  <div class="ligne"><span class="label">Classe</span><select id="mp_cla_id" onChange="majNiveauAjoutClasseForm()">' + options + '</select></div>';
  html += '  <div class="ligne"><span class="label">Niveau</span><select id="mp_cp_niveau"></select></div>';
  html += '  <div class="ligneBouton"><button type="button" class="btNoir" onClick="validerAjoutClasse(' + personnageClassesState.personnageId + ')">Valider</button><button type="button" class="btNoir" onClick="fermerDetail()">Annuler</button></div>';
  html += '</div>';
  html += '</div>';
  detail.innerHTML = html;
  detail.style.display = 'block';
  majNiveauAjoutClasseForm();
}

function majNiveauAjoutClasseForm() {
  var selectClasse = document.getElementById('mp_cla_id');
  var selectNiveau = document.getElementById('mp_cp_niveau');
  if (!selectClasse || !selectNiveau) return;
  var option = selectClasse.options[selectClasse.selectedIndex];
  var niveauMax = parseInt(option.getAttribute('data-niveaumax') || '1', 10);
  if (niveauMax < 1) niveauMax = 1;
  selectNiveau.innerHTML = buildNiveauOptions(1, niveauMax);
}

function validerAjoutClasse(id) {
  if (!personnageClassesState || parseInt(id, 10) <= 0) return;
  var selectClasse = document.getElementById('mp_cla_id');
  var selectNiveau = document.getElementById('mp_cp_niveau');
  if (!selectClasse || !selectNiveau) return;

  var claId = parseInt(selectClasse.value || '0', 10);
  var niveau = parseInt(selectNiveau.value || '1', 10);
  var option = selectClasse.options[selectClasse.selectedIndex];
  var niveauMax = parseInt(option.getAttribute('data-niveaumax') || '1', 10);
  var claNom = option ? option.text : '';
  if (claId <= 0) return;
  if (niveau < 1) niveau = 1;
  if (niveau > niveauMax) niveau = niveauMax;

  personnageClassesState.added.push({
    cla_id: claId,
    cla_nom: claNom,
    niveau: niveau,
    niveau_max: niveauMax,
    deleted: false
  });
  fermerDetail();
  renderClassesEditor();
}

function supprimerClassePerso(perso, classeToken) {
  if (!personnageClassesState) return;
  if (!confirm('Voulez-vous supprimer cette classe ?')) return;
  var token = String(classeToken || '');
  if (token.indexOf('existing-') === 0) {
    var pcId = parseInt(token.substring(9), 10);
    personnageClassesState.existing.forEach(function (item) {
      if (item.pc_id === pcId) item.deleted = true;
    });
  } else if (token.indexOf('new-') === 0) {
    var idx = parseInt(token.substring(4), 10);
    if (!isNaN(idx) && personnageClassesState.added[idx]) personnageClassesState.added[idx].deleted = true;
  }
  renderClassesEditor();
}

function actualiserDivClassesPerso() { }
function actualiserDivNouvelleClasse() { }
function actualiserNiveauClassePerso() { }

function majNiveauClassePerso(selectId) {
  if (!personnageClassesState) return;
  var select = document.getElementById(selectId);
  if (!select) return;
  var niveau = parseInt(select.value || '1', 10);
  if (selectId.indexOf('pcnexisting-') === 0) {
    var pcId = parseInt(selectId.substring(12), 10);
    personnageClassesState.existing.forEach(function (item) {
      if (item.pc_id === pcId) item.niveau = niveau;
    });
  } else if (selectId.indexOf('pcnnew-') === 0) {
    var idx = parseInt(selectId.substring(7), 10);
    if (!isNaN(idx) && personnageClassesState.added[idx]) personnageClassesState.added[idx].niveau = niveau;
  }
  buildClassesPayloadInputs();
}

function getCurrentSelectedCompetenceIds() {
  if (!personnageCompetencesState) return {};
  var selected = {};
  personnageCompetencesState.existing.forEach(function (item) {
    if (!item.deleted) selected[item.comp_id] = true;
  });
  personnageCompetencesState.added.forEach(function (item) {
    if (!item.deleted) selected[item.comp_id] = true;
  });
  return selected;
}

function buildCompetencesPayloadInputs() {
  if (!personnageCompetencesState) return;
  var payload = document.getElementById('competencesPayload');
  if (!payload) return;

  var html = '<input type="hidden" name="mp_comp_payload_ready" value="1">';
  personnageCompetencesState.existing.forEach(function (item) {
    if (item.deleted) return;
    html += '<input type="hidden" name="mp_comp_ids[]" value="' + item.comp_id + '">';
    html += '<input type="hidden" name="mp_comp_maitrise[' + item.comp_id + ']" value="' + item.maitrise + '">';
  });
  personnageCompetencesState.added.forEach(function (item) {
    if (item.deleted) return;
    html += '<input type="hidden" name="mp_comp_ids[]" value="' + item.comp_id + '">';
    html += '<input type="hidden" name="mp_comp_maitrise[' + item.comp_id + ']" value="' + item.maitrise + '">';
  });
  payload.innerHTML = html;
}

function renderCompetencesEditor() {
  if (!personnageCompetencesState) return;
  var compDiv = document.getElementById('competences');
  if (!compDiv) return;

  var html = '';
  personnageCompetencesState.existing.forEach(function (item) {
    if (item.deleted) return;
    var inputId = 'pcmexisting-' + item.comp_id;
    html += '<div class="classe">';
    html += '  <div onClick="supprimerCompetencePerso(\'existing-' + item.comp_id + '\')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
    html += '  <div class="libelle_classe">' + escHtml(item.comp_nom) + '</div>';
    html += '  <input type="number" class="input_comp_maitrise" id="' + inputId + '" value="' + item.maitrise + '" onChange="majValeurCompetence(\'' + inputId + '\')">';
    html += '</div>';
  });

  personnageCompetencesState.added.forEach(function (item, idx) {
    if (item.deleted) return;
    var inputId = 'pcmnew-' + idx;
    html += '<div class="classe">';
    html += '  <div onClick="supprimerCompetencePerso(\'new-' + idx + '\')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
    html += '  <div class="libelle_classe">' + escHtml(item.comp_nom) + '</div>';
    html += '  <input type="number" class="input_comp_maitrise" id="' + inputId + '" value="' + item.maitrise + '" onChange="majValeurCompetence(\'' + inputId + '\')">';
    html += '</div>';
  });

  if (html === '') html = '<div class="ml10">Aucune compétence</div>';
  compDiv.innerHTML = html;
  buildCompetencesPayloadInputs();
}

function initPersonnageCompetencesEditor(config) {
  personnageCompetencesState = {
    personnageId: parseInt(config.personnageId || 0, 10),
    existing: (config.competencesExistantes || []).map(function (item) {
      return {
        comp_id: parseInt(item.comp_id, 10),
        comp_nom: item.comp_nom,
        maitrise: parseInt(item.maitrise, 10) || 0,
        deleted: false
      };
    }),
    added: [],
    catalog: (config.competencesCatalogue || []).map(function (item) {
      return {
        comp_id: parseInt(item.comp_id, 10),
        comp_nom: item.comp_nom
      };
    })
  };

  var form = document.getElementById('modif-personnage');
  if (form) {
    form.addEventListener('submit', function () {
      buildCompetencesPayloadInputs();
    });
  }
  renderCompetencesEditor();
}

function ajouterCompetencePerso(id) {
  if (!personnageCompetencesState || parseInt(id, 10) <= 0) return;
  var selected = getCurrentSelectedCompetenceIds();
  var available = personnageCompetencesState.catalog.filter(function (item) {
    return !selected[item.comp_id];
  });

  var detail = document.getElementById('detail-pp');
  if (!detail) return;
  if (available.length === 0) {
    detail.innerHTML = '<div class="affichage"><div class="popup-contenu"><div class="titreAction"><div class="titreA">Ajouter une competence</div><div class="lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div><div>Toutes les competences du ruleset sont deja affectees.</div></div></div>';
    detail.style.display = 'block';
    return;
  }

  var options = '';
  available.forEach(function (item) {
    options += '<option value="' + item.comp_id + '">' + escHtml(item.comp_nom) + '</option>';
  });

  var html = '';
  html += '<div class="affichage">';
  html += '<div class="popup-contenu">';
  html += '  <div class="titreAction"><div class="titreA">Ajouter une compétence</div><div class="lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div>';
  html += '  <div class="ligne popup-competence-row"><span class="label">Compétence</span><select id="mp_comp_id">' + options + '</select></div>';
  html += '  <div class="ligne popup-competence-row"><span class="label">Valeur</span><input type="number" id="mp_comp_maitrise" value="0" class="input_comp_maitrise"></div>';
  html += '  <div class="ligneBouton popup-competence-actions"><button type="button" class="btNoir" onClick="validerAjoutCompetence(' + personnageCompetencesState.personnageId + ')">Valider</button><button type="button" class="btNoir" onClick="fermerDetail()">Annuler</button></div>';
  html += '</div>';
  html += '</div>';
  detail.innerHTML = html;
  detail.style.display = 'block';
}

function validerAjoutCompetence(id) {
  if (!personnageCompetencesState || parseInt(id, 10) <= 0) return;
  var selectComp = document.getElementById('mp_comp_id');
  var inputMaitrise = document.getElementById('mp_comp_maitrise');
  if (!selectComp || !inputMaitrise) return;

  var compId = parseInt(selectComp.value || '0', 10);
  var maitrise = parseInt(inputMaitrise.value || '0', 10);
  var option = selectComp.options[selectComp.selectedIndex];
  var compNom = option ? option.text : '';
  if (compId <= 0) return;

  personnageCompetencesState.added.push({
    comp_id: compId,
    comp_nom: compNom,
    maitrise: isNaN(maitrise) ? 0 : maitrise,
    deleted: false
  });
  fermerDetail();
  renderCompetencesEditor();
}

function supprimerCompetencePerso(token) {
  if (!personnageCompetencesState) return;
  if (!confirm('Voulez-vous supprimer cette competence ?')) return;
  var t = String(token || '');
  if (t.indexOf('existing-') === 0) {
    var compId = parseInt(t.substring(9), 10);
    personnageCompetencesState.existing.forEach(function (item) {
      if (item.comp_id === compId) item.deleted = true;
    });
  } else if (t.indexOf('new-') === 0) {
    var idx = parseInt(t.substring(4), 10);
    if (!isNaN(idx) && personnageCompetencesState.added[idx]) personnageCompetencesState.added[idx].deleted = true;
  }
  renderCompetencesEditor();
}

function majValeurCompetence(inputId) {
  if (!personnageCompetencesState) return;
  var input = document.getElementById(inputId);
  if (!input) return;
  var val = parseInt(input.value || '0', 10);
  if (isNaN(val)) val = 0;

  if (inputId.indexOf('pcmexisting-') === 0) {
    var compId = parseInt(inputId.substring(12), 10);
    personnageCompetencesState.existing.forEach(function (item) {
      if (item.comp_id === compId) item.maitrise = val;
    });
  } else if (inputId.indexOf('pcmnew-') === 0) {
    var idx = parseInt(inputId.substring(7), 10);
    if (!isNaN(idx) && personnageCompetencesState.added[idx]) personnageCompetencesState.added[idx].maitrise = val;
  }
  buildCompetencesPayloadInputs();
}
// nouvelle fonction
// passage de la fonction jquery accordion dans l'appel ajax
// la fonction n'utilise plus actualiserPageModif dans success:
function gererEquipement(perso) {
  console.log('gererEquipement Personnage #' + perso);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-gererEquipement.php',
    data: "perso=" + perso,
    dataType: 'text',
    success: function (result) {
      var resultat = result.split("@");
      console.log('Personnage : ' + resultat[0]);
      $("#modification").html(resultat[1]);
      $('#accordion').accordion({
        heightStyle: "content",
        active: false,
        collapsible: true
      });
      console.log('Affichage formulaire #');
      $("#modification").show('fast');
    },
    error: function () { alert('Erreur gererEquipement'); }
  });
}
function ajouterEqt(eqt, perso) {
  const liste = document.getElementById('mod' + eqt);
  let mod = '';
  if (liste) {
    mod = liste.value;
  } else {
    console.warn("Ã‰lÃ©ment 'mod" + eqt + "' introuvable. Valeur mod laissÃ©e vide.");
  }
  const liste2 = document.getElementById('so' + eqt);
  let sort = '';
  if (liste2) {
    sort = liste2.value;
  } else {
    console.warn("Ã‰lÃ©ment 'sort" + eqt + "' introuvable. Valeur sort laissÃ©e vide.");
  }
  console.log('Ajouter Objet magique #' + eqt + ' du personnage #' + perso + ', mod : ' + mod + ', sort #' + sort);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutEqt.php',
    data: "perso=" + perso + "&eqt=" + eqt + "&mod=" + mod + "&sort=" + sort,
    dataType: 'text',
    success: actualiserEqt,
    error: function () { alert('Erreur ajouterEqt()'); }
  });
}

function afficherEqt(eqt) {
  console.log('Affichage Eqt #' + eqt);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageEqt.php',
    data: "eqt=" + eqt,
    dataType: 'text',
    success: actualiserPage,
    error: function () { alert('Erreur afficherEqt()'); }
  });
}

function modifierEqt(eqt) {
  //alert('ID '+idRegle);
  console.log('Modification regle #' + idRegle);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierRegle.php',
    data: "regle=" + idRegle,
    dataType: 'text',
    success: actualiserPageModif,
    error: function () { alert('Erreur modifierRegle'); }
  });
}

function validerModifEqt() {
  console.log('Nom : ' + $('#mp_re_nom').val());
  var mp_re_texte = CKEDITOR.instances.mp_re_texte.getData(); // traitement du champ textarea modifiÃ© par CKEDITOR
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifRegle.php',
    data: "mp_re_id=" + $('#mp_re_id').val() +
      "&mp_re_cr_id=" + $('#mp_re_cr_id').val() +
      "&mp_re_re_id=" + $('#mp_re_re_id').val() +
      "&mp_re_nom=" + encodeURIComponent($('#mp_re_nom').val()) +
      "&mp_re_texte=" + encodeURIComponent(mp_re_texte),
    dataType: 'text',
    success: function (reponse) {
      var resultat = reponse.split("@");
      console.log('Affichage regle modifiÃ©e #' + resultat[0]);
      console.log('Requete : ' + resultat[1]);
      $("#nomRegle" + resultat[0]).html(resultat[2]);
      $("#catRegle" + resultat[0]).html(resultat[3]);
      afficherRegle(resultat[0]);
      $("#modification").hide();
    },
    error: function () { alert('Erreur validerModifRegle()'); }
  });
}

function supprimerEqt(eqt, perso) {
  console.log('Supprimer Objet magique #' + eqt + ' du perso #' + perso);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerSupprEqt.php',
    data: "eqt=" + eqt + "&perso=" + perso,
    dataType: 'text',
    success: actualiserEqt,
    error: function () { alert('Erreur supprimerEqt()'); }
  });
}

function actualiserEqt(reponse) {
  var resultat = reponse.split("@");
  console.log('Perso : ' + resultat[2] + ', mod : ' + resultat[3] + ', sort #' + resultat[4] + ', Objet : ' + resultat[0]);
  $("#listeOM").html(resultat[1]);
}




function afficherNote(note, accreditation) {
  var persoId = 0;
  try {
    var params = new URLSearchParams(window.location.search || '');
    persoId = parseInt(params.get('personnage') || '0', 10);
    if (isNaN(persoId) || persoId < 0) persoId = 0;
  } catch (e) {
    persoId = 0;
  }
  if (window.NoteActions && typeof window.NoteActions.afficherNote === 'function') return window.NoteActions.afficherNote(note, accreditation, persoId);
  alert('Module notes indisponible');
}

function toggleAttributionNoteCampagne(noteId, checkboxEl, event) {
  if (event) event.stopPropagation();
  if (!checkboxEl) return;

  var checked = checkboxEl.checked ? 1 : 0;
  checkboxEl.disabled = true;

  $.ajax({
    type: 'POST',
    url: 'ajax/note_campagne_toggle.php',
    data: "note_id=" + noteId + "&checked=" + checked,
    dataType: 'json',
    success: function (reponse) {
      if (reponse && reponse.success) {
        checkboxEl.checked = parseInt(reponse.checked, 10) === 1;
      } else {
        checkboxEl.checked = (checked === 1) ? false : true;
        alert((reponse && reponse.message) ? reponse.message : 'Erreur attribution note/campagne.');
      }
    },
    error: function () {
      checkboxEl.checked = (checked === 1) ? false : true;
      alert('Erreur toggleAttributionNoteCampagne()');
    },
    complete: function () {
      checkboxEl.disabled = false;
    }
  });
}

function modifierNote(note, perso) {
  var persoId = parseInt(perso || '0', 10);
  if (isNaN(persoId) || persoId < 0) persoId = 0;
  if (window.NoteActions && typeof window.NoteActions.modifierNote === 'function') return window.NoteActions.modifierNote(note, persoId);
  alert('Module notes indisponible');
}

function supprimerNote(note, perso) {
  if (window.NoteActions && typeof window.NoteActions.supprimerNote === 'function') return window.NoteActions.supprimerNote(note, perso);
  alert('Module notes indisponible');
}

function validerModifNote(perso) {
  if (window.NoteActions && typeof window.NoteActions.validerModifNote === 'function') return window.NoteActions.validerModifNote(perso);
  alert('Module notes indisponible');
}

function actualiserNote(reponse) {
  if (window.NoteActions && typeof window.NoteActions.actualiserNote === 'function') return window.NoteActions.actualiserNote(reponse);
}

function diffuser(note, perso) {
  let diffusion = "";
  $("input[type='checkbox']:checked").each(function () {
    diffusion = diffusion + "@" + $(this).attr('id');
  }
  );
  console.log(diffusion);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-diffusionNote.php',
    data: "diffusion=" + diffusion,
    dataType: 'text',
    success: actualiserNote,
    error: function () { alert('Erreur diffuser()'); }
  });
}

function ajouterGrimoire() {
  //alert('ID '+idDon);
  console.log('Ajout Grimoire');
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-ajouterGrimoire.php',
    data: 'grimoire=n',
    dataType: 'text',
    success: actualiserPageModif,
    error: function () { alert('Erreur ajouterGrimoire()'); }
  });
}

function validerAjoutGrimoire() {
  console.log('Valider Ajout Grimoire. Format #' + $('#mp_gr_grf_id').val() + ', Classe #' + $('#mp_gr_cla_id').val() + ', Perso #' + $('#mp_gr_pe_id').val());
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutGrimoire.php',
    data: "mp_gr_nom=" + encodeURIComponent($('#mp_gr_nom').val()) +
      "&mp_gr_grf_id=" + $('#mp_gr_grf_id').val() +
      "&mp_gr_pe_id=" + $('#mp_gr_pe_id').val() +
      "&mp_gr_cla_id=" + $('#mp_gr_cla_id').val(),
    dataType: 'text',
    success: actualiserPageGrimoire,
    error: function () { alert('Erreur validerPageGrimoire()'); }
  });
}

function actualiserPageGrimoire(reponse) {
  //recup du rÃ©sultat > tableau 
  var resultat = reponse.split("@");
  $("#ListeGrimoires").html(resultat[1]);
  $("#modification").hide();
}

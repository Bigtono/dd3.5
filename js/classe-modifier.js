// Classe modifier: local buffer for special abilities (no immediate DB write)
function initClasseModifierPage() {
  "use strict";

  function h(value) {
    return String(value === null || value === undefined ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function byId(id) {
    return document.getElementById(id);
  }

  var data = window.classeModifierData || {};
  var form = byId("form-classe-modifier");
  var tableBody = byId("table-capacites-body");
  var detailPanel = byId("detailPP");
  var detailContent = byId("detailPP-content");
  var capsPayload = byId("capacites_payload");
  var affPayload = byId("affectations_payload");
  var payloadReady = byId("capacites_payload_ready");

  if (!form || !capsPayload || !affPayload) {
    return;
  }

  var state = {
    nextTempId: -1,
    capsByKey: {},
    current: null,
    draft: null,
    editMode: false,
    draftNew: false,
    niveauMax: parseInt(data.niveauMax || 20, 10),
  };

  function normalizeCap(cap) {
    var key = String(cap.cap_key || cap.cap_id || state.nextTempId--);
    return {
      cap_key: key,
      cap_id: parseInt(cap.cap_id || 0, 10),
      cap_nom: cap.cap_nom || "",
      cap_description: cap.cap_description || "",
      cap_type: cap.cap_type || "",
      cap_categorie_var_id: parseInt(cap.cap_categorie_var_id || 0, 10),
      affectations: Array.isArray(cap.affectations)
        ? cap.affectations.map(function (a) {
            return {
              cc_niveau: parseInt(a.cc_niveau || 0, 10),
              cc_precision: a.cc_precision || "",
            };
          })
        : [],
    };
  }

  function initState() {
    var list = Array.isArray(data.capacites) ? data.capacites : [];
    list.forEach(function (cap) {
      var normalized = normalizeCap(cap);
      state.capsByKey[normalized.cap_key] = normalized;
    });
    serializePayloads();
    renderCapTable();
  }

  function uniqueSortedAffects(affects) {
    var seen = {};
    var out = [];
    affects.forEach(function (a) {
      var lvl = parseInt(a.cc_niveau || 0, 10);
      if (lvl < 1 || lvl > state.niveauMax) return;
      var precision = String(a.cc_precision || "");
      var sig = lvl + "|" + precision;
      if (seen[sig]) return;
      seen[sig] = true;
      out.push({ cc_niveau: lvl, cc_precision: precision });
    });
    out.sort(function (a, b) {
      if (a.cc_niveau !== b.cc_niveau) return a.cc_niveau - b.cc_niveau;
      return a.cc_precision.localeCompare(b.cc_precision);
    });
    return out;
  }

  function serializePayloads() {
    var caps = [];
    var affects = [];
    Object.keys(state.capsByKey).forEach(function (key) {
      var cap = state.capsByKey[key];
      var capRecord = {
        cap_key: cap.cap_key,
        cap_id: parseInt(cap.cap_id || 0, 10),
        cap_nom: cap.cap_nom || "",
        cap_description: cap.cap_description || "",
        cap_type: cap.cap_type || "",
        cap_categorie_var_id: parseInt(cap.cap_categorie_var_id || 0, 10),
      };
      caps.push(capRecord);
      uniqueSortedAffects(cap.affectations).forEach(function (a) {
        affects.push({
          cap_key: cap.cap_key,
          cc_niveau: a.cc_niveau,
          cc_precision: a.cc_precision,
        });
      });
    });
    capsPayload.value = JSON.stringify(caps);
    affPayload.value = JSON.stringify(affects);
    if (payloadReady) {
      payloadReady.value = "1";
    }
  }

  function buildByLevel() {
    var byLevel = {};
    Object.keys(state.capsByKey).forEach(function (key) {
      var cap = state.capsByKey[key];
      uniqueSortedAffects(cap.affectations).forEach(function (a) {
        if (!byLevel[a.cc_niveau]) {
          byLevel[a.cc_niveau] = [];
        }
        byLevel[a.cc_niveau].push({
          cap_key: cap.cap_key,
          cap_id: cap.cap_id,
          cap_nom: cap.cap_nom,
          cc_precision: a.cc_precision,
        });
      });
    });
    return byLevel;
  }

  function renderCapTable() {
    if (!tableBody) return;
    var byLevel = buildByLevel();
    var html = "";
    for (var lvl = 1; lvl <= state.niveauMax; lvl++) {
      var entries = byLevel[lvl] || [];
      var labels = entries
        .map(function (e) {
          var label = h(e.cap_nom);
          if (e.cc_precision) {
            label += " (" + h(e.cc_precision) + ")";
          }
          return (
            '<span class="lien" onclick="affecterCapacite(\'' +
            String(e.cap_key).replace(/'/g, "\\'") +
            "')\">" +
            label +
            "</span>"
          );
        })
        .join(", ");
      if (!labels) {
        labels = "&mdash;";
      }
      html += "<tr><td>" + lvl + "</td><td class=\"cell-large\">" + labels + "</td></tr>";
    }
    tableBody.innerHTML = html;
    serializePayloads();
  }

  function categoryOptions(selectedId) {
    var list = Array.isArray(data.categories) ? data.categories : [];
    var html = '<option value="0"></option>';
    list.forEach(function (cat) {
      var id = parseInt(cat.var_id || 0, 10);
      var selected = id === parseInt(selectedId || 0, 10) ? ' selected="selected"' : "";
      html += "<option value=\"" + id + "\"" + selected + ">" + h(cat.var_valeur) + "</option>";
    });
    return html;
  }

  function ensureDraftAffects() {
    if (!state.draft) return;
    state.draft.affectations = Array.isArray(state.draft.affectations)
      ? state.draft.affectations
      : [];
  }

  function syncDraftFromFormNoValidate() {
    if (!state.draft || !detailContent) return;
    var nom = byId("dp_cap_nom");
    if (nom) state.draft.cap_nom = nom.value;
    var desc = byId("dp_cap_description");
    if (desc) state.draft.cap_description = desc.value;
    var type = byId("dp_cap_type");
    if (type) state.draft.cap_type = type.value;
    var cat = byId("dp_cap_categorie");
    if (cat) state.draft.cap_categorie_var_id = parseInt(cat.value || 0, 10);

    var rows = detailContent.querySelectorAll("tr[data-affect-index]");
    var affects = [];
    rows.forEach(function (row) {
      var lvlInput = row.querySelector("input[data-affect-niveau]");
      var precInput = row.querySelector("input[data-affect-precision]");
      var lvl = lvlInput ? parseInt(lvlInput.value || 0, 10) : 0;
      var precision = precInput ? precInput.value : "";
      if (lvl >= 1 && lvl <= state.niveauMax) {
        affects.push({ cc_niveau: lvl, cc_precision: precision });
      }
    });
    state.draft.affectations = uniqueSortedAffects(affects);
  }

  function addAffectationDraft() {
    syncDraftFromFormNoValidate();
    ensureDraftAffects();
    state.draft.affectations.push({ cc_niveau: 1, cc_precision: "" });
    renderDetail();
  }

  function removeAffectationDraft(index) {
    syncDraftFromFormNoValidate();
    ensureDraftAffects();
    state.draft.affectations.splice(index, 1);
    renderDetail();
  }

  function readDraftFromForm() {
    if (!state.draft) return false;
    var nom = byId("dp_cap_nom");
    if (!nom || nom.value.trim() === "") {
      alert("Le nom de la capacité est obligatoire.");
      return false;
    }
    state.draft.cap_nom = nom.value.trim();
    var desc = byId("dp_cap_description");
    state.draft.cap_description = desc ? desc.value : "";
    var type = byId("dp_cap_type");
    state.draft.cap_type = type ? type.value.trim() : "";
    var cat = byId("dp_cap_categorie");
    state.draft.cap_categorie_var_id = cat ? parseInt(cat.value || 0, 10) : 0;

    var rows = detailContent.querySelectorAll("tr[data-affect-index]");
    var newAffects = [];
    rows.forEach(function (row) {
      var idx = parseInt(row.getAttribute("data-affect-index"), 10);
      if (isNaN(idx)) return;
      var lvlInput = row.querySelector("input[data-affect-niveau]");
      var precInput = row.querySelector("input[data-affect-precision]");
      var lvl = lvlInput ? parseInt(lvlInput.value || 0, 10) : 0;
      var precision = precInput ? precInput.value : "";
      if (lvl >= 1 && lvl <= state.niveauMax) {
        newAffects.push({ cc_niveau: lvl, cc_precision: precision });
      }
    });
    state.draft.affectations = uniqueSortedAffects(newAffects);
    return true;
  }

  function closeDetail() {
    state.current = null;
    state.draft = null;
    state.editMode = false;
    state.draftNew = false;
    detailPanel.style.display = "none";
    detailContent.innerHTML = "";
  }

  function commitCurrentDraft() {
    if (!readDraftFromForm()) return;
    if (state.draftNew) {
      // First validation for a new ability: keep it local, keep popup open,
      // and unlock affectation editing before global commit.
      state.draft = normalizeCap(state.draft);
      state.capsByKey[state.draft.cap_key] = cloneCap(state.draft);
      state.draftNew = false;
      state.editMode = true;
      renderCapTable();
      renderDetail();
      return;
    }
    state.capsByKey[state.draft.cap_key] = normalizeCap(state.draft);
    renderCapTable();
    closeDetail();
  }

  function cloneCap(cap) {
    return JSON.parse(JSON.stringify(cap));
  }

  function renderDetail() {
    if (!state.draft) return;
    ensureDraftAffects();
    var cap = state.draft;

    var header = '<div class="menu2"><div class="ga lien" onclick="fermerDetailPP()"><i class="fa fa-close"></i></div></div>';
    var bloc1 = "";
    if (!state.editMode) {
      bloc1 += '<div class="nom_objet">' + h(cap.cap_nom) + "</div>";
      bloc1 += '<div class="texte">' + (cap.cap_description ? cap.cap_description : "") + "</div>";
      bloc1 += '<div class="ligne mt10"><span class="label w150">Type</span>' + h(cap.cap_type) + "</div>";
      var catValue = "";
      (Array.isArray(data.categories) ? data.categories : []).forEach(function (item) {
        if (parseInt(item.var_id, 10) === parseInt(cap.cap_categorie_var_id || 0, 10)) {
          catValue = item.var_valeur;
        }
      });
      bloc1 += '<div class="ligne"><span class="label w150">Catégorie</span>' + h(catValue) + "</div>";
      bloc1 += '<div class="mt10"><button type="button" class="btNoir" onclick="modifierDetailCapacite()">Modifier</button></div>';
    } else {
      bloc1 += '<div class="detail-grid">';
      bloc1 += '<div class="full"><span class="label">Nom</span><input type="text" id="dp_cap_nom" class="input_nom" value="' + h(cap.cap_nom) + '"></div>';
      bloc1 += '<div class="full"><span class="label">Description</span><textarea id="dp_cap_description">' + h(cap.cap_description) + "</textarea></div>";
      bloc1 += '<div><span class="label">Type</span><input type="text" id="dp_cap_type" value="' + h(cap.cap_type) + '"></div>';
      bloc1 += '<div><span class="label">Catégorie</span><select id="dp_cap_categorie">' + categoryOptions(cap.cap_categorie_var_id) + "</select></div>";
      bloc1 += "</div>";
    }

    var bloc2 = '<div class="mt15"><div class="label">Affectations par niveau</div>';
    if (!state.draftNew || !state.editMode) {
      if (state.editMode) {
        bloc2 += '<div class="affect-list"><table><thead><tr><th>Niveau</th><th>Précision</th><th></th></tr></thead><tbody>';
        if (!cap.affectations.length) {
          bloc2 += '<tr><td colspan="3" class="nodata">Aucune affectation</td></tr>';
        } else {
          cap.affectations.forEach(function (a, idx) {
            bloc2 += '<tr data-affect-index="' + idx + '">';
            bloc2 += '<td><input data-affect-niveau type="number" min="1" max="' + state.niveauMax + '" value="' + parseInt(a.cc_niveau || 1, 10) + '"></td>';
            bloc2 += '<td><input data-affect-precision type="text" value="' + h(a.cc_precision || "") + '"></td>';
            bloc2 += '<td><button type="button" class="btGris" onclick="supprimerAffectationDetail(' + idx + ')">Supprimer</button></td>';
            bloc2 += "</tr>";
          });
        }
        bloc2 += "</tbody></table></div>";
        bloc2 += '<div class="mt10"><button type="button" class="btNoir" onclick="ajouterAffectationDetail()">Ajouter une affectation</button></div>';
      } else {
        if (!cap.affectations.length) {
          bloc2 += '<div class="nodata">Aucune affectation</div>';
        } else {
          bloc2 += "<ul>";
          cap.affectations.forEach(function (a) {
            var label = "Niveau " + a.cc_niveau;
            if (a.cc_precision) {
              label += " (" + h(a.cc_precision) + ")";
            }
            bloc2 += "<li>" + label + "</li>";
          });
          bloc2 += "</ul>";
        }
      }
    } else {
      bloc2 += '<div class="nodata">Les affectations seront disponibles après validation de la nouvelle capacité.</div>';
    }
    bloc2 += "</div>";

    var footer = '<div class="mt15">';
    if (state.editMode) {
      footer += '<button type="button" class="btNoir" onclick="validerDetailPP()">Valider</button> ';
      footer += '<button type="button" class="btGris" onclick="annulerDetailPP()">Annuler</button>';
    } else {
      footer += '<button type="button" class="btGris" onclick="fermerDetailPP()">Fermer</button>';
    }
    footer += "</div>";

    detailContent.innerHTML = header + bloc1 + bloc2 + footer;
    detailPanel.style.display = "block";
  }

  function openExisting(capKey) {
    var cap = state.capsByKey[String(capKey)];
    if (!cap) return;
    state.current = String(capKey);
    state.draft = cloneCap(cap);
    state.editMode = false;
    state.draftNew = false;
    renderDetail();
  }

  function openNew() {
    var newKey = String(state.nextTempId--);
    state.current = newKey;
    state.draft = {
      cap_key: newKey,
      cap_id: 0,
      cap_nom: "",
      cap_description: "",
      cap_type: "",
      cap_categorie_var_id: 0,
      affectations: [],
    };
    state.editMode = true;
    state.draftNew = true;
    renderDetail();
  }

  window.affecterCapacite = function (id) {
    openExisting(String(id));
  };
  window.nouvelleCapacite = function () {
    openNew();
  };
  window.fermerDetailPP = function () {
    closeDetail();
  };
  window.modifierDetailCapacite = function () {
    state.editMode = true;
    state.draftNew = false;
    renderDetail();
  };
  window.annulerDetailPP = function () {
    closeDetail();
  };
  window.validerDetailPP = function () {
    commitCurrentDraft();
  };
  window.ajouterAffectationDetail = function () {
    addAffectationDraft();
  };
  window.supprimerAffectationDetail = function (index) {
    removeAffectationDraft(index);
  };

  form.addEventListener("submit", function () {
    serializePayloads();
    if (payloadReady) {
      payloadReady.value = "1";
    }
  });

  initState();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initClasseModifierPage);
} else {
  initClasseModifierPage();
}

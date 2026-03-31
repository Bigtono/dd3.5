<div class="personnage-section">
  <div class="info_personnage">
    <div>
      <div class="label">Nom</div><input type="text" id="mp_pe_nom" name="mp_pe_nom" value="<? echo htmlspecialchars($dn['pe_nom']); ?>" class="input_left">
    </div>
    <div>
      <div class="label">Race</div><? echo $race; ?>
    </div>
    <div>
      <div class="label">Archetype</div><? echo $archetype; ?>
    </div>
    <div>
      <div class="label">Sexe</div><input type="text" id="mp_pe_sexe" name="mp_pe_sexe" value="<? echo htmlspecialchars($dn['pe_sexe']); ?>" class="input_left">
    </div>
    <div>
      <div class="label">Alignement</div><? echo $alignement; ?>
    </div>
    <div>
      <div class="label">Organisation</div><? echo $organisation; ?>
    </div>
  </div>

  <div class="info_personnage">
    <div class="titreAction">
      <div class="titreA">
        Classes
        <? if ($p != "n"): ?>
          <a href="javascript:void(0)" class="lien" onClick="ajouterClassePerso(<? echo (int)$p; ?>)" title="Ajouter une classe">
            <i class="icon fa-solid fa-circle-plus"></i>
          </a>
        <? endif; ?>
      </div>
      <div></div>
    </div>
    <div id="classes"><!-- rendu dynamique JS -->
      <? if ($p == "n"): ?>
        <div class="ml10">Veuillez enregistrer le personnage avant de lui ajouter des classes</div>
      <? endif; ?>
    </div>
    <div id="classesPayload"></div>
  </div>

  <? if ($isAdmin): ?>
    <div class="info_personnage">
      <div>
        <div class="label">Joueur</div><select id="mp_pe_j_id" name="mp_pe_j_id"><? echo OptionListeJoueurs($j); ?></select>
      </div>
    </div>
  <? else: ?>
    <input type="hidden" id="mp_pe_j_id" name="mp_pe_j_id" value="<? echo (int)$j; ?>" />
  <? endif; ?>
</div>

<div class="personnage-section">
  <div class="titre">
    <span class="personnage-section-title">
      <i class="fa-solid fa-chart-column personnage-section-icon"></i>
      <span>Caracteristiques</span>
    </span>
  </div>
  <div class="carac_personnage">
    <div class="carac_ligne">
      <div class="label">For</div><input type="number" id="mp_pe_for" name="mp_pe_for" min="0" max="30" value="<? echo (int)$dn['pe_for']; ?>" class="input_carac">
    </div>
    <div class="carac_ligne">
      <div class="label">Dex</div><input type="number" id="mp_pe_dex" name="mp_pe_dex" min="0" max="30" value="<? echo (int)$dn['pe_dex']; ?>" class="input_carac">
    </div>
    <div class="carac_ligne">
      <div class="label">Con</div><input type="number" id="mp_pe_con" name="mp_pe_con" min="0" max="30" value="<? echo (int)$dn['pe_con']; ?>" class="input_carac">
    </div>
    <div class="carac_ligne">
      <div class="label">Int</div><input type="number" id="mp_pe_int" name="mp_pe_int" min="0" max="30" value="<? echo (int)$dn['pe_int']; ?>" class="input_carac">
    </div>
    <div class="carac_ligne">
      <div class="label">Sag</div><input type="number" id="mp_pe_sag" name="mp_pe_sag" min="0" max="30" value="<? echo (int)$dn['pe_sag']; ?>" class="input_carac">
    </div>
    <div class="carac_ligne">
      <div class="label">Cha</div><input type="number" id="mp_pe_cha" name="mp_pe_cha" min="0" max="30" value="<? echo (int)$dn['pe_cha']; ?>" class="input_carac">
    </div>
  </div>
</div>

<div class="personnage-section">
  <div class="titre">
    <span class="personnage-section-title">
      <i class="fa-solid fa-shield-halved personnage-section-icon"></i>
      <span>Combat</span>
    </span>
  </div>
  <div class="personnage-combat-values personnage-combat-values-form">
    <div class="personnage-combat-item">
      <span class="label">Classe d'armure :</span>
      <input type="number" id="mp_pe_ca" name="mp_pe_ca" min="0" value="<? echo (int)$dn['pe_ca']; ?>" class="input_carac">
    </div>
    <div class="personnage-combat-item">
      <span class="label">Points de vie :</span>
      <input type="number" id="mp_pe_pv" name="mp_pe_pv" min="0" value="<? echo (int)$dn['pe_pv']; ?>" class="input_carac">
    </div>
  </div>
</div>

<? if ($nlsPrestigeContext['has_section']): ?>
  <div class="personnage-section" id="nls-section-wrapper">
    <div class="titre">
      <span class="personnage-section-title">
        <i class="fa-solid fa-bolt personnage-section-icon"></i>
        <span>NLS (classes de prestige)</span>
      </span>
    </div>
    <? if ($nlsValidationError): ?>
      <div style="color:#c62828;font-weight:700;">
        Toutes les affectations NLS doivent etre renseignées avant validation.
      </div>
    <? endif; ?>
    <? foreach ($nlsPrestigeContext['prestige_classes'] as $pcIdPrestige => $prestigeData): ?>
      <? $blocId = 'nls-prestige-' . (int)$pcIdPrestige; ?>
      <div class="mt10 js-nls-prestige-block" data-pc-id-prestige="<? echo (int)$pcIdPrestige; ?>">
        <div class="gras mr10 lien" onClick="togglePlus('<? echo $blocId; ?>')">
          <? echo htmlspecialchars($prestigeData['cla_nom']); ?>
          <span id="toggle-<? echo $blocId; ?>"><i class="fa-solid fa-bars"></i></span>
        </div>
        <div id="<? echo $blocId; ?>" class="accordion-content noDisplay box-data personnage-accordion-box">
          <table>
            <thead>
              <tr>
                <td>Niveau</td>
                <td>Classe de base affectee</td>
              </tr>
            </thead>
            <tbody>
              <? foreach ($prestigeData['levels'] as $levelData): ?>
                <? $fieldName = 'mp_penl_base_' . (int)$pcIdPrestige . '_' . (int)$levelData['niveau']; ?>
                <? $selectedPcId = (int)$levelData['assigned_pc_id_base']; ?>
                <tr>
                  <td><? echo (int)$levelData['niveau']; ?></td>
                  <td>
                    <select name="<? echo $fieldName; ?>" id="<? echo $fieldName; ?>">
                      <option value="0">Choisir une classe</option>
                      <? foreach ($levelData['options'] as $pcIdBase => $claNomBase): ?>
                        <option value="<? echo (int)$pcIdBase; ?>" <? echo ((int)$pcIdBase === $selectedPcId) ? 'selected' : ''; ?>>
                          <? echo htmlspecialchars($claNomBase); ?>
                        </option>
                      <? endforeach; ?>
                    </select>
                  </td>
                </tr>
              <? endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <? endforeach; ?>
  </div>
<? endif; ?>

<div class="personnage-section">
  <div class="titre personnage-section-toggle">
    <div class="gras mr10 lien" onClick="togglePlus('personnage-competences-modif')">
      <span class="personnage-section-title">
        <i class="fa-solid fa-book-open personnage-section-icon"></i>
        <span>Competences</span>
      </span>
      <span id="toggle-personnage-competences-modif"><i class="fa-solid fa-bars"></i></span>
    </div>
  </div>
  <div id="personnage-competences-modif" class="accordion-content noDisplay box-data personnage-accordion-box">
    <div id="competences"><!-- rendu dynamique JS -->
      <? if ($p == "n"): ?>
        <div class="ml10">Veuillez enregistrer le personnage avant de lui ajouter des competences</div>
      <? endif; ?>
    </div>
    <div id="competencesPayload"></div>
    <? if ($p != "n"): ?>
      <a href="javascript:void(0)" class="lien" onClick="ajouterCompetencePerso(<? echo (int)$p; ?>)" title="Ajouter une competence">
        Ajouter une compétence <i class="icon fa-solid fa-circle-plus"></i>
      </a>
    <? endif; ?>
    <? if ($p != "n" && empty($personnageCompetences)): ?>
      <div class="ml10">Aucune competence renseignee pour ce personnage.</div>
    <? endif; ?>
  </div>
</div>
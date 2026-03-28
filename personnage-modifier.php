<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$nlsPrestigeContext = ['has_section' => false];
$nlsValidationError = isset($_GET['nls_error']) && (int)$_GET['nls_error'] === 1;

if (isset($_GET['personnage'])):
  // appel Include
  $p = $_GET['personnage'];
else:
  $p = "";
endif;

if (isset($p) && $p != "n"): // il s'agit d'une modification
  $requete = "SELECT * FROM dd_personnages WHERE pe_id='" . $p . "'";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  $titre = "Modification de " . stripslashes($dn['pe_nom']);
  $j = $dn['pe_j_id'];
  $personnageCampagneId = isset($dn['pe_camp_id']) ? (int)$dn['pe_camp_id'] : 0;
  $campaignOwnerId = 0;
  if ($personnageCampagneId > 0):
    $stmtCampOwner = $db->prepare("SELECT camp_j_id FROM dd_campagnes WHERE camp_id = :cid LIMIT 1");
    $stmtCampOwner->execute([':cid' => $personnageCampagneId]);
    $campOwner = $stmtCampOwner->fetch(PDO::FETCH_ASSOC);
    if ($campOwner) $campaignOwnerId = (int)$campOwner['camp_j_id'];
  endif;
  $isPersonnageOwner = isset($_SESSION['user_id']) && (int)$dn['pe_j_id'] === (int)$_SESSION['user_id'];
  $isCampaignOwner = $personnageCampagneId > 0 && isset($_SESSION['user_id']) && $campaignOwnerId === (int)$_SESSION['user_id'];
  $canEditPersonnage = $isPersonnageOwner || $isCampaignOwner;
  if (!$canEditPersonnage):
    $retour = 'personnage.php?personnage=' . (int)$p;
    if (!empty($_GET['campagne'])) $retour .= '&campagne=' . (int)$_GET['campagne'];
    header('Location: ' . $retour);
    exit;
  endif;
  $canEditNotesMj = $isCampaignOwner;
  if (isset($_SESSION['rulesetRep']) && $_SESSION['rulesetRep'] === 'DD3.5'):
    include_once("include/insert/DD3.5/personnage_nls_helper.php");
    if (dd35_ruleset_active()):
      $nlsPrestigeContext = dd35_load_personnage_nls_context($db, (int)$p);
    endif;
  endif;
else: // il s'agit d'un ajout
  $num_rows = 1;
  $a = "n";
  $titre = "Cr&eacute;ation d'un personnage";
  $j = $_SESSION['user_id'];
  $personnageCampagneId = 0;
  $canEditNotesMj = false;
endif;
?>
<!doctype html>

<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>
</HEAD>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div id="personnage">
        <div class="titreAction">
          <div class="titreA"><? echo $titre; ?></div>
        </div>
        <?
        if ($num_rows > 0):
          // mise en forme du contenu
          $race = '<select id="mp_pe_ra_id" name="mp_pe_ra_id">' . optionList("dd_races", "ra", "nom", $dn['pe_ra_id'], "ra_rat_id=1") . '</select>';
          $archetype = '<select id="mp_pe_arc_id" name="mp_pe_arc_id">' . optionList("dd_races", "ra", "nom", $dn['pe_arc_id'], "ra_rat_id=2") . '</select>';
          $organisation = '<select id="mp_pe_org_id" name="mp_pe_org_id">' . optionList("dd_organisations", "org", "nom", $dn['pe_org_id']) . '</select>';
          $alignement = '<select id="mp_pe_al_id" name="mp_pe_al_id">' . optionList("dd_alignements", "al", "abreviation", $dn['pe_al_id'], "", 1, "", "al_id") . '</select>';
          $classesExistantes = [];
          $classesCatalogue = [];
          if ($p != "n"):
            $stmtClassesExistantes = $db->prepare("
              SELECT pc.pc_id, pc.pc_cla_id, pc.pc_niveau, c.cla_nom, c.cla_niveauMax
              FROM dd_personnages_classes pc
              JOIN dd_classes c ON c.cla_id = pc.pc_cla_id
              WHERE pc.pc_pe_id = :pid
              ORDER BY c.cla_nom
            ");
            $stmtClassesExistantes->execute([':pid' => (int)$p]);
            while ($rowClasse = $stmtClassesExistantes->fetch(PDO::FETCH_ASSOC)):
              $classesExistantes[] = [
                'pc_id' => (int)$rowClasse['pc_id'],
                'cla_id' => (int)$rowClasse['pc_cla_id'],
                'cla_nom' => (string)$rowClasse['cla_nom'],
                'niveau' => (int)$rowClasse['pc_niveau'],
                'niveau_max' => (int)$rowClasse['cla_niveauMax'],
              ];
            endwhile;

            $stmtCatalogue = $db->prepare("
              SELECT cla_id, cla_nom, cla_niveauMax
              FROM dd_classes
              WHERE cla_ruleset_var_id = :ruleset
              ORDER BY cla_nom
            ");
            $stmtCatalogue->execute([':ruleset' => (int)$_SESSION['ruleset']]);
            while ($rowCat = $stmtCatalogue->fetch(PDO::FETCH_ASSOC)):
              $classesCatalogue[] = [
                'cla_id' => (int)$rowCat['cla_id'],
                'cla_nom' => (string)$rowCat['cla_nom'],
                'niveau_max' => (int)$rowCat['cla_niveauMax'],
              ];
            endwhile;
          endif;
        ?>
          <form action="personnage-enregistrement.php?personnage=<? echo $p; ?>&tri=<? echo $_GET['tri']; ?>" class="formulaire" method="post" name="modif-personnage" id="modif-personnage">
            <input type="hidden" name="actionflag" value="modif" />
            <input type="hidden" name="mp_pe_id" value="<? echo $p; ?>" />
            <input type="hidden" name="campagne" value="<? echo $_GET['campagne']; ?>" />
            <div id="description" class="principal">
              <div class="info_personnage">
                <div>
                  <div class="label">Nom</div><input type="text" id="mp_pe_nom" name="mp_pe_nom" value="<? echo $dn['pe_nom']; ?>" class="input_left">
                </div>
                <div>
                  <div class="label">Race</div><? echo $race; ?>
                </div>
                <div>
                  <div class="label">Archetype</div><? echo $archetype; ?>
                </div>
                <div>
                  <div class="label">Sexe</div><input type="text" id="mp_pe_sexe" name="mp_pe_sexe" value="<? echo $dn['pe_sexe']; ?>" class="input_left">
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
                  <div>
                  </div>
                </div>
                <div id="classes"><!-- rendu dynamique JS -->
                  <? if ($p == "n"): ?>
                    <div class="ml10">Veuillez enregistrer le personnage avant de lui ajouter des classes</div>
                  <? endif; ?>
                </div>
                <div id="classesPayload"></div>
              </div>

              <div class="carac_personnage">
                <div class="titre">Caract&eacute;ristiques</div>
                <div class="carac_ligne">
                  <div class="label">For</div>
                  <input
                    type="number"
                    id="mp_pe_for"
                    name="mp_pe_for"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_for']; ?>"
                    class="input_carac">
                </div>

                <div class="carac_ligne">
                  <div class="label">Dex</div>
                  <input
                    type="number"
                    id="mp_pe_dex"
                    name="mp_pe_dex"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_dex']; ?>"
                    class="input_carac">
                </div>

                <div class="carac_ligne">
                  <div class="label">Con</div>
                  <input
                    type="number"
                    id="mp_pe_con"
                    name="mp_pe_con"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_con']; ?>"
                    class="input_carac">
                </div>

                <div class="carac_ligne">
                  <div class="label">Int</div>
                  <input
                    type="number"
                    id="mp_pe_int"
                    name="mp_pe_int"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_int']; ?>"
                    class="input_carac">
                </div>

                <div class="carac_ligne">
                  <div class="label">Sag</div>
                  <input
                    type="number"
                    id="mp_pe_sag"
                    name="mp_pe_sag"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_sag']; ?>"
                    class="input_carac">
                </div>

                <div class="carac_ligne">
                  <div class="label">Cha</div>
                  <input
                    type="number"
                    id="mp_pe_cha"
                    name="mp_pe_cha"
                    min="0"
                    max="30"
                    value="<? echo (int)$dn['pe_cha']; ?>"
                    class="input_carac">
                </div>

              </div>

              <? if ($nlsPrestigeContext['has_section']): ?>
                <div class="mt10">
                  <div class="titre">Affectation NLS (classes de prestige)</div>
                  <? if ($nlsValidationError): ?>
                    <div style="color:#c62828;font-weight:700;">
                      Toutes les affectations NLS doivent etre renseignees avant validation.
                    </div>
                  <? endif; ?>
                  <? foreach ($nlsPrestigeContext['prestige_classes'] as $pcIdPrestige => $prestigeData): ?>
                    <? $blocId = 'nls-prestige-' . (int)$pcIdPrestige; ?>
                    <div class="mt10">
                      <div class="gras mr10 lien" onCLick="togglePlus('<? echo $blocId; ?>')">
                        <? echo htmlspecialchars($prestigeData['cla_nom']); ?>
                        <span id="toggle-<? echo $blocId; ?>"><i class="fa-solid fa-bars"></i></span>
                      </div>
                      <div id="<? echo $blocId; ?>" class="accordion-content noDisplay">
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

              <? if ($isAdmin): ?>
                <div class="info_personnage">
                  <div>
                    <div class="label">Joueur</div><select id="mp_pe_j_id" name="mp_pe_j_id"><? echo OptionListeJoueurs($j); ?></select>
                  </div>
                </div>
              <? else: ?>
                <input type="hidden" id="mp_pe_j_id" name="mp_pe_j_id" value="<? echo $j; ?>" />
              <? endif; ?>

              <div class="mt10">
                <div class="label">Background</div>
                <textarea id="mp_pe_background" name="mp_pe_background" class="ckeditor input_notes" rows="10" cols="100"><? echo $dn['pe_background']; ?></textarea>
                <script>
                  CKEDITOR.replace('mp_pe_background', {
                    allowedContent: true, // désactive le filtre de contenu
                    // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
                    extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
                    contentsCss: 'include/_styles_.css'
                  });
                </script>
              </div>

              <div class="mt10">
                <div class="label">Notes</div>
                <textarea id="mp_pe_notes" name="mp_pe_notes" class="ckeditor input_notes" rows="10" cols="100"><? echo $dn['pe_notes']; ?></textarea>
                <script>
                  CKEDITOR.replace('mp_pe_notes', {
                    allowedContent: true, // désactive le filtre de contenu
                    // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
                    extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
                    contentsCss: 'include/_styles_.css'
                  });
                </script>
              </div>

              <? if ($canEditNotesMj): ?>
                <div class="mt10">
                  <div class="label">Notes MJ</div>
                  <textarea id="mp_pe_notes_mj" name="mp_pe_notes_mj" class="ckeditor input_notes" rows="10" cols="100"><? echo $dn['pe_notes_mj']; ?></textarea>
                  <script>
                    CKEDITOR.replace('mp_pe_notes_mj', {
                      allowedContent: true, // désactive le filtre de contenu
                      // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
                      extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
                      contentsCss: 'include/_styles_.css'
                    });
                  </script>
                </div>
              <? else: ?>
                <input type="hidden" id="mp_pe_notes_mj" name="mp_pe_notes_mj" value="<? echo isset($dn['pe_notes_mj']) ? htmlspecialchars($dn['pe_notes_mj']) : ''; ?>" />
              <? endif; ?>
              <!-- affichage des boutons --->
              <div class="ligneBouton">
                <button type="submit" class="btNoir" name="ok">Modifier</button>
                <button type="submit" class="btNoir" name="nok">Annuler</button>
              </div>

            </div> <!--- principal --->
          </form>
        <?
        else:
          echo '<div class="nodata">Aucun personnage selectionné !</div>';
        endif;
        ?>
      </div> <!-- #contenu --->
    </div><!-- #page --->
    <div id="detail-pp"></div>
    <div id="modification"></div>
    <? if ($num_rows > 0 && $p != "n"): ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          initPersonnageClassesEditor({
            personnageId: <? echo (int)$p; ?>,
            classesExistantes: <? echo json_encode($classesExistantes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
            classesCatalogue: <? echo json_encode($classesCatalogue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
          });
        });
      </script>
    <? endif; ?>
</body>

</html>
<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

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
else: // il s'agit d'un ajout
  $num_rows = 1;
  $a = "n";
  $titre = "Cr&eacute;ation d'un personnage";
  $j = $_SESSION['user_id'];
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
                <div class="label">Classes : </div>
                <div id="classes"><!-- equal-height-container --->
                  <?
                  if ($p == "n"):
                    echo '<div class="ml10">Veuillez enregistrer le personnage avant de lui ajouter des classes</div>';
                  else:
                    $liste = '';
                    // gestion des classes
                    $requete_cl = 'SELECT pc_id, pc_niveau, cla_nom, cla_niveauMax FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_pe_id="' . $p . '" ORDER BY pc_niveau DESC;';
                    $result_cl = queryPDO($requete_cl);
                    $num_rows_cl = $result_cl->rowCount();
                    if ($num_rows_cl > 0):
                      while ($dncl = $result_cl->fetch(PDO::FETCH_ASSOC)):
                        $liste .= '<div id="pc' . $dncl['pc_id'] . '" class="classe">';
                        $liste .= '  <div onClick="supprimerClassePerso(' . $p . ',' . $dncl['pc_id'] . ')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
                        $liste .= '  <div class="libelle_classe">' . $dncl['cla_nom'] . '</div>';
                        $liste .= '  <select class="niveau_classe" id="pcn' . $dncl['pc_id'] . '" name="pcn' . $dncl['pc_id'] . '" onChange="majNiveauClassePerso(\'pcn' . $dncl['pc_id'] . '\')">';
                        $liste .= optionListInt(1, $dncl['cla_niveauMax'], $dncl['pc_niveau'], "T");
                        $liste .= '  </select>';
                        $liste .= '</div>';
                      endwhile;
                    endif;
                    echo $liste;
                  endif;
                  ?>
                </div>
                <? debug($requete_cl); ?>
                <? if ($p == "n"): ?>
                  <div onClick="ajouterClassePerso(<? echo $p; ?>)" class="ajout_classe_bouton"><i class="fa-solid fa-circle-plus"></i> Ajouter une nouvelle classe</div>
                <? endif; ?>
                <div id="nouvelleClasse"></div> <!--- DIV qui contient le formulaire d'ajout de classe après le click ajouterClassePerso() --->
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

              <? if ($isAdmin): ?>
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
</body>

</html>
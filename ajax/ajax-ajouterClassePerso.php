<?php
include("../include/session.php");
include_once("../include/dblib.inc.php");
include_once("../include/diverslib.inc.php");

if (isset($_POST['perso'])):
  $p = (int)$_POST['perso'];
  $ruleset = isset($_SESSION['ruleset']) ? (int)$_SESSION['ruleset'] : 0;

  $optionsClasses = '';
  if ($ruleset > 0):
    $stmt = $db->prepare("SELECT cla_id, cla_nom, cla_niveauMax FROM dd_classes WHERE cla_ruleset_var_id = :ruleset ORDER BY cla_nom");
    $stmt->execute([':ruleset' => $ruleset]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
      $niveauMax = (int)$row['cla_niveauMax'];
      if ($niveauMax < 1) $niveauMax = 1;
      $optionsClasses .= '<option value="' . (int)$row['cla_id'] . '" data-niveaumax="' . $niveauMax . '">' . htmlspecialchars($row['cla_nom']) . '</option>';
    endwhile;
  endif;

  $result = '<div class="affichage">';
  $result .= '  <div class="contenu">';
  $result .= '    <div class="titreAction"><div class="titreA">Ajouter une classe</div><div class="lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div>';

  if ($optionsClasses === ''):
    $result .= '    <div>Aucune classe disponible pour ce ruleset.</div>';
  else:
    $result .= '    <form class="ajout_classe_form" method="post" onSubmit="return false;">';
    $result .= '      <input type="hidden" id="mp_pe_id" value="' . $p . '">';
    $result .= '      <div class="ligne"><span class="label">Classe</span><select id="mp_cla_id" class="libelle_classe" onChange="majNiveauAjoutClasseForm()">' . $optionsClasses . '</select></div>';
    $result .= '      <div class="ligne"><span class="label">Niveau</span><select id="mp_cp_niveau" class="niveau_classe"></select></div>';
    $result .= '      <div class="ligneBouton"><button type="button" class="btNoir" onClick="validerAjoutClasse(' . $p . ')">Valider</button><button type="button" class="btNoir" onClick="fermerDetail()">Annuler</button></div>';
    $result .= '    </form>';
    $result .= '    <script>if (typeof majNiveauAjoutClasseForm === "function") { majNiveauAjoutClasseForm(); }</script>';
  endif;

  $result .= '  </div>';
  $result .= '</div>';

  echo $p . "@" . $result;
else:
  echo "0@Erreur";
endif;
?>

<?
// Préparation du contenu
$nom = stripslashes(ucfirst($dnno['no_nom']));
$click = 'afficherNote(' . $dnno['no_id'] . ',' . $accreditation . ')';
if ($_SESSION['debug'] == 1 && $_SESSION['mj'] == 1) $idno = ' (' . $dnno['no_id'] . ')';
$ligne = '';
if ($_SESSION['mj'] == 1) $ligne .= '  <div class="icone_suppr"><span onClick="suppression(\'dd_notes\',\'no\',' . $dnno['no_id'] . ')"><i class="fa fa-trash"></i></span></div>';
if ($_SESSION['mj'] == 1) $ligne .= '  <div class="icone_modif"><span onclick="modifierNote(' . $dnno['no_id'] . ',' . $p . ')"><i class="fa fa-pencil"></i></span></div>';
$ligne .= '  <div id="nomNo' . $dnno['no_id'] . '" class="nom_note" onclick="' . $click . '">' . $nom . $idno . '</div>';
$ligne .= '  <div id="catNo' . $dnno['no_id'] . '" class="categorie_note" onclick="' . $click . '">' . libelle("dd_types_notes", "tyno", "nom", $dnno['no_tyno_id']) . '</div>';
if ($campagneActive > 0):
  $noteId = (int)$dnno['no_id'];
  $campagneId = (int)$campagneActive;
  $utilisateurId = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
  $estMj = !empty($_SESSION['mj']) && (int)$_SESSION['mj'] === 1;
  $estAuteur = isset($dnno['no_j_id']) && (int)$dnno['no_j_id'] === $utilisateurId;
  $canEditAttribution = ($estMj || $estAuteur);

  $requeteAttr = 'SELECT cpno_id FROM dd_campagnes_notes WHERE cpno_no_id="' . $noteId . '" AND cpno_camp_id="' . $campagneId . '" LIMIT 1';
  $resultAttr = queryPDO($requeteAttr);
  $isAttribueeCampagneActive = ($resultAttr && $resultAttr->rowCount() > 0);

  $checked = $isAttribueeCampagneActive ? ' checked="checked"' : '';
  $disabled = $canEditAttribution ? '' : ' disabled="disabled"';
  $onClickAttr = $canEditAttribution ? ' onclick="event.stopPropagation();"' : '';

  $ligne .= '  <div id="attrNo' . $noteId . '" class="attribution_note niveau_note" onclick="' . $click . '">';
  $ligne .= '<input type="checkbox" id="chkAttrNo' . $noteId . '" class="attr_note_campagne" value="1"' . $checked . $disabled . $onClickAttr . ' onchange="toggleAttributionNoteCampagne(' . $noteId . ', this, event)">';
  $ligne .= '</div>';
endif;
//$ligne.='</div>';
?>

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$p = $_POST['mp_pe_id'];
$c = $_POST['campagne'];
$notesMjValue = isset($_POST['mp_pe_notes_mj']) ? $_POST['mp_pe_notes_mj'] : '';

if (isset($_POST['ok']) && !empty($p)):
	if ($p != "n"):
    $stmtPerso = $db->prepare("SELECT pe_j_id, pe_camp_id, pe_notes_mj FROM dd_personnages WHERE pe_id = :pid LIMIT 1");
    $stmtPerso->execute([':pid' => (int)$p]);
    $currentPerso = $stmtPerso->fetch(PDO::FETCH_ASSOC);
    if (!$currentPerso):
      header("location: personnages.php");
      exit;
    endif;
    $isPersonnageOwner = isset($_SESSION['user_id']) && (int)$currentPerso['pe_j_id'] === (int)$_SESSION['user_id'];
    $campaignOwnerId = 0;
    if ((int)$currentPerso['pe_camp_id'] > 0):
      $stmtCamp = $db->prepare("SELECT camp_j_id FROM dd_campagnes WHERE camp_id = :cid LIMIT 1");
      $stmtCamp->execute([':cid' => (int)$currentPerso['pe_camp_id']]);
      $camp = $stmtCamp->fetch(PDO::FETCH_ASSOC);
      if ($camp) $campaignOwnerId = (int)$camp['camp_j_id'];
    endif;
    $isCampaignOwner = (int)$currentPerso['pe_camp_id'] > 0
      && isset($_SESSION['user_id'])
      && $campaignOwnerId === (int)$_SESSION['user_id'];
    $canEditPersonnage = $isPersonnageOwner || $isCampaignOwner;
    if (!$canEditPersonnage):
      $retour = "personnage.php?personnage=" . (int)$p;
      if ($c && $c > 0) $retour .= "&campagne=" . (int)$c;
      header("location: " . $retour);
      exit;
    endif;
    if (!$isCampaignOwner):
      $notesMjValue = (string)$currentPerso['pe_notes_mj'];
    endif;
		// Modification 
		$sql = "UPDATE dd_personnages SET pe_nom='" . addslashes($_POST['mp_pe_nom']) . "', pe_ra_id='" . $_POST['mp_pe_ra_id'] . "', pe_arc_id='" . $_POST['mp_pe_arc_id'] . "', pe_sexe='" . $_POST['mp_pe_sexe'] . "', pe_al_id='" . $_POST['mp_pe_al_id'] . "', pe_org_id='" . $_POST['mp_pe_org_id'] . "', pe_for='" . $_POST['mp_pe_for'] . "', pe_dex='" . $_POST['mp_pe_dex'] . "', pe_con='" . $_POST['mp_pe_con'] . "', pe_int='" . $_POST['mp_pe_int'] . "', pe_sag='" . $_POST['mp_pe_sag'] . "', pe_cha='" . $_POST['mp_pe_cha'] . "', pe_background='" . addslashes($_POST['mp_pe_background']) . "', pe_notes='" . addslashes($_POST['mp_pe_notes']) . "', pe_notes_mj='" . addslashes($notesMjValue) . "', pe_j_id='" . addslashes($_POST['mp_pe_j_id']) . "' WHERE pe_id='" . $p . "'";
		$resultat = execPDO($sql);
		$message = 1;
	else:
    $notesMjValue = '';
		$sql = "INSERT INTO dd_personnages (pe_nom, pe_ra_id, pe_arc_id, pe_sexe, pe_al_id, pe_org_id, pe_for, pe_dex, pe_con, pe_int, pe_sag, pe_cha, pe_background, pe_notes, pe_notes_mj, pe_j_id, pe_ruleset_var_id) VALUES ('" . addslashes($_POST['mp_pe_nom']) . "', '" . $_POST['mp_pe_ra_id'] . "', '" . $_POST['mp_pe_arc_id'] . "', '" . $_POST['mp_pe_sexe'] . "', '" . $_POST['mp_pe_al_id'] . "', '" . $_POST['mp_pe_org_id'] . "', '" . $_POST['mp_pe_for'] . "', '" . $_POST['mp_pe_dex'] . "', '" . $_POST['mp_pe_con'] . "', '" . $_POST['mp_pe_int'] . "', '" . $_POST['mp_pe_sag'] . "', '" . $_POST['mp_pe_cha'] . "', '" . addslashes($_POST['mp_pe_background']) . "', '" . addslashes($_POST['mp_pe_notes']) . "', '" . addslashes($notesMjValue) . "', '" . addslashes($_POST['mp_pe_j_id']) . "', '" . $_SESSION['ruleset'] . "')";
		$resultat = execPDO($sql);
		$p = lastID("dd_personnages", "pe");
		$message = 0;
	endif;
else:
	$message = $_POST['ok'] . "/" . $p;
endif;
// gestion de la provenance
$complement = "";
if ($c && $c > 0) $complement = '&campagne=' . $c;
header("location: personnage.php?personnage=" . $p . $complement);

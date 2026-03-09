<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$p = $_POST['mp_pe_id'];
$c = $_POST['campagne'];

if (isset($_POST['ok']) && !empty($p)):
	if ($p != "n"):
		// Modification 
		$sql = "UPDATE dd_personnages SET pe_nom='" . addslashes($_POST['mp_pe_nom']) . "', pe_ra_id='" . $_POST['mp_pe_ra_id'] . "', pe_arc_id='" . $_POST['mp_pe_arc_id'] . "', pe_sexe='" . $_POST['mp_pe_sexe'] . "', pe_al_id='" . $_POST['mp_pe_al_id'] . "', pe_org_id='" . $_POST['mp_pe_org_id'] . "', pe_for='" . $_POST['mp_pe_for'] . "', pe_dex='" . $_POST['mp_pe_dex'] . "', pe_con='" . $_POST['mp_pe_con'] . "', pe_int='" . $_POST['mp_pe_int'] . "', pe_sag='" . $_POST['mp_pe_sag'] . "', pe_cha='" . $_POST['mp_pe_cha'] . "', pe_background='" . addslashes($_POST['mp_pe_background']) . "', pe_notes='" . addslashes($_POST['mp_pe_notes']) . "', pe_notes_mj='" . addslashes($_POST['mp_pe_notes_mj']) . "', pe_j_id='" . addslashes($_POST['mp_pe_j_id']) . "' WHERE pe_id='" . $p . "'";
		$resultat = execPDO($sql);
		$message = 1;
	else:
		$sql = "INSERT INTO dd_personnages (pe_nom, pe_ra_id, pe_arc_id, pe_sexe, pe_al_id, pe_org_id, pe_for, pe_dex, pe_con, pe_int, pe_sag, pe_cha, pe_background, pe_notes, pe_notes_mj, pe_j_id, pe_ruleset_var_id) VALUES ('" . addslashes($_POST['mp_pe_nom']) . "', '" . $_POST['mp_pe_ra_id'] . "', '" . $_POST['mp_pe_arc_id'] . "', '" . $_POST['mp_pe_sexe'] . "', '" . $_POST['mp_pe_al_id'] . "', '" . $_POST['mp_pe_org_id'] . "', '" . $_POST['mp_pe_for'] . "', '" . $_POST['mp_pe_dex'] . "', '" . $_POST['mp_pe_con'] . "', '" . $_POST['mp_pe_int'] . "', '" . $_POST['mp_pe_sag'] . "', '" . $_POST['mp_pe_cha'] . "', '" . addslashes($_POST['mp_pe_background']) . "', '" . addslashes($_POST['mp_pe_notes']) . "', '" . addslashes($_POST['mp_pe_notes_mj']) . "', '" . addslashes($_POST['mp_pe_j_id']) . "', '" . $_SESSION['ruleset'] . "')";
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

<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

function tableColumns(string $table): array
{
	global $db;
	static $cache = [];

	if (isset($cache[$table])) {
		return $cache[$table];
	}

	$cols = [];
	$stmt = $db->query("SHOW COLUMNS FROM `" . $table . "`");
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cols[$row['Field']] = true;
	}
	$cache[$table] = $cols;
	return $cols;
}

function h($value): string
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function jsonForJs($value, string $fallback = '[]'): string
{
	$flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
		$flags |= JSON_INVALID_UTF8_SUBSTITUTE;
	}
	$json = json_encode($value, $flags);
	if ($json === false) {
		return $fallback;
	}
	return $json;
}

$classeParam = isset($_GET['classe']) ? $_GET['classe'] : '';
$isCreation = ($classeParam === 'n' || (int)$classeParam <= 0);
$c = $isCreation ? 0 : (int)$classeParam;

$dn = [
	'cla_id' => 0,
	'cla_nom' => '',
	'cla_abreviation' => '',
	'cla_clt_id' => 1,
	'cla_dV' => '',
	'cla_pointsCompetences' => '',
	'cla_alignement' => '',
	'cla_car_id' => 0,
	'cla_po_niveau1' => '',
	'cla_conditions' => '',
	'cla_description' => '',
	'cla_traits' => '',
	'cla_caracteristiques' => '',
	'cla_sauvegardes' => '',
	'cla_competences' => '',
	'cla_armes' => '',
	'cla_armures' => '',
	'cla_outils' => '',
	'cla_equipement' => '',
	'cla_sorts' => '',
	'cla_mag_id' => 0,
	'cla_res_id' => 0,
	'cla_niveauMax' => 20,
	'cla_pouvoir1' => '',
	'cla_pouvoir2' => '',
	'cla_pouvoir3' => '',
	'cla_pouvoir4' => '',
];

$num_rows = 1;
if ($c > 0) {
	$requete = "SELECT * FROM dd_classes WHERE cla_id='" . $c . "'";
	$result = queryPDO($requete);
	$num_rows = $result->rowCount();
	if ($num_rows > 0) {
		$dn = $result->fetch(PDO::FETCH_ASSOC);
	}
}

$cnCols = tableColumns('dd_classe_niveau');
$capCols = tableColumns('dd_capacites_speciales');
$ccCols = tableColumns('dd_classe_capacite');
$hasSortConnu = isset($cnCols['cn_sortConnu_n0']);

$niveaux = [];
$niveauMax = max(1, (int)$dn['cla_niveauMax']);
if ($c > 0) {
	$reqNiveaux = 'SELECT * FROM dd_classe_niveau WHERE cn_cla_id=' . $c . ' ORDER BY cn_niveau';
	$resNiveaux = queryPDO($reqNiveaux);
	while ($dnn = $resNiveaux->fetch(PDO::FETCH_ASSOC)) {
		$niveaux[(int)$dnn['cn_niveau']] = $dnn;
	}
}

for ($i = 1; $i <= $niveauMax; $i++) {
	if (!isset($niveaux[$i])) {
		$niveaux[$i] = ['cn_niveau' => $i];
	}
	for ($s = 0; $s <= 9; $s++) {
		if (!isset($niveaux[$i]['cn_sort_n' . $s])) {
			$niveaux[$i]['cn_sort_n' . $s] = '';
		}
		if (!isset($niveaux[$i]['cn_sortConnu_n' . $s])) {
			$niveaux[$i]['cn_sortConnu_n' . $s] = '';
		}
	}
	for ($p = 1; $p <= 4; $p++) {
		if (!isset($niveaux[$i]['cn_pouvoir' . $p])) {
			$niveaux[$i]['cn_pouvoir' . $p] = '';
		}
	}
	foreach (['cn_bba', 'cn_reflexes', 'cn_vigueur', 'cn_volonte'] as $col) {
		if (!isset($niveaux[$i][$col])) {
			$niveaux[$i][$col] = '';
		}
	}
}

ksort($niveaux);

$capacitesState = [];
if ($c > 0) {
	$selectCapFields = ['cap.cap_id', 'cap.cap_nom', 'cap.cap_description'];
	if (isset($capCols['cap_type'])) {
		$selectCapFields[] = 'cap.cap_type';
	}
	if (isset($capCols['cap_categorie_var_id'])) {
		$selectCapFields[] = 'cap.cap_categorie_var_id';
	}
	$selectCapFields[] = 'cc.cc_niveau';
	$selectCapFields[] = isset($ccCols['cc_precision']) ? 'cc.cc_precision' : "'' AS cc_precision";

	$sqlCaps = 'SELECT ' . implode(', ', $selectCapFields) . '
		FROM dd_classe_capacite cc
		JOIN dd_capacites_speciales cap ON cap.cap_id = cc.cc_cap_id
		WHERE cc.cc_cla_id = :classe
		ORDER BY cap.cap_nom, cc.cc_niveau';
	$stmtCaps = $db->prepare($sqlCaps);
	$stmtCaps->execute([':classe' => $c]);

	$tmpCaps = [];
	while ($row = $stmtCaps->fetch(PDO::FETCH_ASSOC)) {
		$capId = (int)$row['cap_id'];
		if (!isset($tmpCaps[$capId])) {
			$tmpCaps[$capId] = [
				'cap_key' => (string)$capId,
				'cap_id' => $capId,
				'cap_nom' => $row['cap_nom'],
				'cap_description' => $row['cap_description'],
				'cap_type' => isset($row['cap_type']) ? $row['cap_type'] : '',
				'cap_categorie_var_id' => isset($row['cap_categorie_var_id']) ? (int)$row['cap_categorie_var_id'] : 0,
				'affectations' => [],
			];
		}
		$tmpCaps[$capId]['affectations'][] = [
			'cc_niveau' => (int)$row['cc_niveau'],
			'cc_precision' => isset($row['cc_precision']) ? $row['cc_precision'] : '',
		];
	}
	$capacitesState = array_values($tmpCaps);
}

$categoriesCapacite = [];
$stmtVar = $db->prepare("SELECT var_id, var_valeur FROM dd_variables WHERE var_cat='tcap' ORDER BY var_valeur");
$stmtVar->execute();
while ($var = $stmtVar->fetch(PDO::FETCH_ASSOC)) {
	$categoriesCapacite[] = [
		'var_id' => (int)$var['var_id'],
		'var_valeur' => $var['var_valeur'],
	];
}

$isDD35 = ($_SESSION['rulesetRep'] === 'DD3.5');
$activePouvoirs = [];
for ($p = 1; $p <= 4; $p++) {
	if (trim((string)$dn['cla_pouvoir' . $p]) !== '') {
		$activePouvoirs[] = $p;
	}
}
?>
<!doctype html>

<HEAD>
	<? include("include/head.php"); ?>
	<script type='text/javascript' src='js/classe-modifier.js'></script>
	<style>
		.classe-section {
			border: thin solid #bfa887;
			border-radius: 0.35rem;
			padding: 12px;
			margin-bottom: 14px;
			background: #fff;
		}

		.classe-section h3 {
			margin: 0 0 10px 0;
			color: #4e2e00;
		}

		.table-scroll {
			overflow-x: auto;
			margin-top: 8px;
			max-width: 100%;
		}

		.table-classe-modif {
			width: 100%;
			border-collapse: collapse;
			font-size: 0.9rem;
			table-layout: fixed;
		}

		.table-classe-modif th,
		.table-classe-modif td {
			border: thin solid #d7c7b0;
			padding: 4px;
			text-align: center;
			vertical-align: bottom;
			background: #fff;
			overflow-wrap: anywhere;
			word-break: break-word;
		}

		.table-classe-modif th {
			background: #f5efe6;
			color: #4e2e00;
			white-space: normal;
			line-height: 1.15;
			vertical-align: bottom;
		}

		.table-classe-modif input[type="text"],
		.table-classe-modif input[type="number"] {
			width: 100%;
			min-width: 0;
			max-width: 100%;
			text-align: center;
			padding: 2px 4px;
			box-sizing: border-box;
		}

		.table-classe-modif .cell-large {
			min-width: 120px;
		}

		.table-classe-modif .col-niveau {
			width: 54px;
		}

		.table-classe-modif .col-stat {
			width: 68px;
		}

		.table-classe-modif .col-pouvoir {
			width: 92px;
		}

		.table-classe-modif .col-sort {
			width: 62px;
		}

		.section-title-line {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 10px;
		}

		.detail-pp-local {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 1002;
			display: none;
			background: rgba(0, 0, 0, 0.4);
		}

		.detail-pp-local .panel {
			max-width: 900px;
			margin: 40px auto;
			background: #fff;
			border-radius: 8px;
			padding: 12px;
			max-height: calc(100% - 80px);
			overflow: auto;
		}

		.detail-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 10px;
		}

		.detail-grid .full {
			grid-column: 1 / -1;
		}

		.detail-grid textarea {
			min-height: 140px;
			width: 100%;
		}

		.affect-list table {
			width: 100%;
			border-collapse: collapse;
		}

		.affect-list th,
		.affect-list td {
			border: thin solid #d7c7b0;
			padding: 4px;
		}
	</style>
</HEAD>

<body>
	<div id="page">
		<? include_once("include/affichageSelectionSources.php"); ?>
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
		<div class="wrapper">
			<? include('include/ariane.php'); ?>
			<? if ($num_rows > 0): ?>
				<div id="classe" class="formulaire">
					<div class="titreAction">
						<div class="titreA"><? echo $c > 0 ? h(stripslashes($dn['cla_nom'])) : 'Nouvelle classe'; ?></div>
						<div></div>
					</div>
					<form id="form-classe-modifier" action="classe-enregistrement.php?classe=<? echo $c; ?>&tri=<? echo isset($_GET['tri']) ? h($_GET['tri']) : ''; ?>" method="post">
						<input type="hidden" name="actionflag" value="modif" />
						<input type="hidden" name="mp_cla_id" value="<? echo $c; ?>" />
						<input type="hidden" id="capacites_payload" name="capacites_payload" value="[]">
						<input type="hidden" id="affectations_payload" name="affectations_payload" value="[]">
						<input type="hidden" id="capacites_payload_ready" name="capacites_payload_ready" value="0">

						<div class="classe-section">
							<h3>Section 1 - Donn&eacute;es de classe</h3>
							<div class="ligne">
								<div class="label w100">Nom</div><input type="text" class="input_nom" id="mp_cla_nom" name="mp_cla_nom" value="<? echo h($dn['cla_nom']); ?>">
								<div class="label w100 ml25">Abreviation</div><input type="text" class="input_abreviation" id="mp_cla_abreviation" name="mp_cla_abreviation" value="<? echo h(stripslashes($dn['cla_abreviation'])); ?>">
							</div>
							<div class="ligne"><span class="label w200">Type de magie</span><select id="mp_cla_mag_id" name="mp_cla_mag_id"><? echo optionList("dd_typeMagie", "mag", "nom", $dn['cla_mag_id'], "mag_ruleset_var_id='" . $_SESSION['ruleset'] . "'"); ?></select></div>
							<? include('include/insert/' . $_SESSION['rulesetRep'] . '/bloc_classe_modif.php'); ?>
							<div class="ligne mt10">
								<div class="label">Source</div><select id="mp_cla_res_id" name="mp_cla_res_id"><? echo optionList("dd_ressources", "res", "nom", $dn['cla_res_id'], "res_id IN " . $selection); ?></select>
							</div>
						</div>

						<? if ($c > 0): ?>
							<div class="classe-section">
								<h3>Section 2 - Table des bonus de classe</h3>
								<div class="table-scroll">
									<table class="table-classe-modif">
										<thead>
											<tr>
												<th class="col-niveau">Niveau</th>
												<th class="col-stat"><? echo $isDD35 ? 'BBA' : 'Bonus maitrise'; ?></th>
												<? if ($isDD35): ?>
													<th class="col-stat">Ref.</th>
													<th class="col-stat">Vig.</th>
													<th class="col-stat">Vol.</th>
												<? endif; ?>
												<? foreach ($activePouvoirs as $pow): ?>
													<th class="col-pouvoir"><? echo h($dn['cla_pouvoir' . $pow]); ?></th>
												<? endforeach; ?>
												<? for ($s = 0; $s <= 9; $s++): ?>
													<th class="col-sort">Sort <? echo $s; ?></th>
												<? endfor; ?>
											</tr>
										</thead>
										<tbody>
											<? foreach ($niveaux as $niv => $row): ?>
												<tr>
													<td>
														<? echo $niv; ?>
														<input type="hidden" name="niveaux[<? echo $niv; ?>][cn_niveau]" value="<? echo $niv; ?>">
													</td>
													<td class="col-stat"><input type="text" name="niveaux[<? echo $niv; ?>][cn_bba]" value="<? echo h($row['cn_bba']); ?>"></td>
													<? if ($isDD35): ?>
														<td class="col-stat"><input type="text" name="niveaux[<? echo $niv; ?>][cn_reflexes]" value="<? echo h($row['cn_reflexes']); ?>"></td>
														<td class="col-stat"><input type="text" name="niveaux[<? echo $niv; ?>][cn_vigueur]" value="<? echo h($row['cn_vigueur']); ?>"></td>
														<td class="col-stat"><input type="text" name="niveaux[<? echo $niv; ?>][cn_volonte]" value="<? echo h($row['cn_volonte']); ?>"></td>
													<? endif; ?>
													<? foreach ($activePouvoirs as $pow): ?>
														<td class="col-pouvoir"><input type="text" name="niveaux[<? echo $niv; ?>][cn_pouvoir<? echo $pow; ?>]" value="<? echo h($row['cn_pouvoir' . $pow]); ?>"></td>
													<? endforeach; ?>
													<? for ($s = 0; $s <= 9; $s++): ?>
														<td class="col-sort"><input type="text" name="niveaux[<? echo $niv; ?>][cn_sort_n<? echo $s; ?>]" value="<? echo h($row['cn_sort_n' . $s]); ?>"></td>
													<? endfor; ?>
												</tr>
											<? endforeach; ?>
										</tbody>
									</table>
								</div>

								<h3 class="mt10">Sorts connus par niveau</h3>
								<? if ($hasSortConnu): ?>
									<div class="table-scroll">
										<table class="table-classe-modif">
											<thead>
												<tr>
													<th class="col-niveau">Niveau</th>
													<? for ($s = 0; $s <= 9; $s++): ?>
														<th class="col-sort">Connu <? echo $s; ?></th>
													<? endfor; ?>
												</tr>
											</thead>
											<tbody>
												<? foreach ($niveaux as $niv => $row): ?>
													<tr>
														<td class="col-niveau"><? echo $niv; ?></td>
														<? for ($s = 0; $s <= 9; $s++): ?>
															<td class="col-sort"><input type="text" name="niveaux[<? echo $niv; ?>][cn_sortConnu_n<? echo $s; ?>]" value="<? echo h($row['cn_sortConnu_n' . $s]); ?>"></td>
														<? endfor; ?>
													</tr>
												<? endforeach; ?>
											</tbody>
										</table>
									</div>
								<? else: ?>
									<div class="nodata">Colonnes `cn_sortConnu_n0..n9` non disponibles dans `dd_classe_niveau`.</div>
								<? endif; ?>
							</div>

							<div class="classe-section">
								<div class="section-title-line">
									<h3>Section 3 - Tableau des capacit&eacute;s sp&eacute;ciales</h3>
									<button type="button" class="btNoir" onclick="nouvelleCapacite()">Nouvelle capacit&eacute;</button>
								</div>
								<div class="table-scroll">
									<table class="table-classe-modif" id="table-capacites-classe">
										<thead>
											<tr>
												<th>Niveau</th>
												<th>Capacit&eacute;s</th>
											</tr>
										</thead>
										<tbody id="table-capacites-body">
										</tbody>
									</table>
								</div>
							</div>
						<? else: ?>
							<div class="classe-section">
								<h3>Section 2 et 3</h3>
								<div class="nodata">Cr&eacute;e d'abord la classe (Section 1 + Enregistrer). Les sections progression et capacit&eacute;s seront ensuite disponibles.</div>
							</div>
						<? endif; ?>

						<div class="mt25 mb10">
							<button type="submit" name="action" value="save" class="btNoir">Enregistrer</button>
							<button type="submit" name="action" value="cancel" class="btGris">Annuler</button>
						</div>
					</form>
				</div>
			<? else: ?>
				<div class="nodata">Aucune classe selectionn&eacute;e (<? echo $c; ?>)!</div>
			<? endif; ?>
		</div> <!-- #wrapper --->
		<p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
		<button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
	</div>

	<div id="detailPP" class="detail-pp-local">
		<div class="panel">
			<div id="detailPP-content"></div>
		</div>
	</div>

	<script>
		window.classeModifierData = {
			classeId: <? echo (int)$c; ?>,
			niveauMax: <? echo (int)$niveauMax; ?>,
			capacites: <? echo jsonForJs($capacitesState, '[]'); ?>,
			categories: <? echo jsonForJs($categoriesCapacite, '[]'); ?>
		};
	</script>
</body>

</html>

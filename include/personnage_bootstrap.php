<?
if (!isset($_GET['personnage']) || (int)$_GET['personnage'] <= 0) {
  header('Location: personnages.php');
  exit;
}

$p = (int)$_GET['personnage'];
$campagneId = isset($_GET['campagne']) ? (int)$_GET['campagne'] : 0;

if ($campagneId > 0) {
  $_SESSION['campagne'] = $campagneId;
} else {
  unset($_SESSION['campagne']);
  unset($_SESSION['scenario']);
  unset($_SESSION['chapitre']);
}

$sqlPersonnage = "
  SELECT
    p.*,
    c.camp_id,
    c.camp_nom,
    c.camp_j_id
  FROM dd_personnages p
  LEFT JOIN dd_campagnes c ON c.camp_id = p.pe_camp_id
  WHERE p.pe_id = :pid
  LIMIT 1
";
$stmtPersonnage = $db->prepare($sqlPersonnage);
$stmtPersonnage->execute([':pid' => $p]);
$dn = $stmtPersonnage->fetch(PDO::FETCH_ASSOC);

if (!$dn) {
  header('Location: personnages.php');
  exit;
}

$_SESSION['perso'] = $p;
$_SESSION['personnage'] = $p;

$nls = nls($p);
$personnageNom = stripslashes((string)$dn['pe_nom']);
$personnageCampagneId = (int)$dn['pe_camp_id'];

$isPersonnageOwner = isset($_SESSION['user_id']) && (int)$dn['pe_j_id'] === (int)$_SESSION['user_id'];
$isCampaignOwner = $personnageCampagneId > 0
  && isset($_SESSION['user_id'])
  && (int)$dn['camp_j_id'] === (int)$_SESSION['user_id'];
$canEditPersonnage = $isPersonnageOwner || $isCampaignOwner;
$canViewNotesMj = $isCampaignOwner;

$personnageQuery = ['personnage' => $p];
if ($campagneId > 0) {
  $personnageQuery['campagne'] = $campagneId;
}

$buildPersonnageUrl = function ($page) use ($personnageQuery) {
  return $page . '?' . http_build_query($personnageQuery);
};

$personnageUrls = [
  'fiche' => $buildPersonnageUrl('personnage.php'),
  'background' => $buildPersonnageUrl('personnage-background.php'),
  'possessions' => $buildPersonnageUrl('personnage-possessions.php'),
  'magie' => $buildPersonnageUrl('personnage-magie.php'),
  'connaissances' => $buildPersonnageUrl('personnage-connaissances.php'),
  'notes-mj' => $buildPersonnageUrl('personnage-notes-mj.php'),
];

$retourFicheUrl = $personnageUrls['fiche'];

$campagnePerso = null;
if ($personnageCampagneId > 0 && !empty($dn['camp_nom'])) {
  $campagnePerso = [
    'camp_id' => $personnageCampagneId,
    'camp_nom' => $dn['camp_nom'],
  ];
}

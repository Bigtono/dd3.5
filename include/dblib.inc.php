<?
//******************************************************************************************
/* DD3.5
//******************************************************************************************/
// connexion à la base de données : 
global $db;
if ($_SERVER['SERVER_NAME'] == "maikastel.fr"):
  $user = 'maikasteiymaika';
  $pass = 'Mai150222290858';
  $dsn = 'mysql:host=maikasteiymaika.mysql.db;dbname=maikasteiymaika';
  $site = "maikastel.fr";
else:
  $user = 'maikasteiymaika';
  $pass = 'Mai15022228';
  $dsn = 'mysql:host=maikasteiymaika.mysql.db;dbname=maikasteiymaika';
  $site = "maikastel.fr";
endif;
try {
  $db = new PDO(
    $dsn,
    $user,
    $pass,
    array(
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    )
  );
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // si tu veux, tu peux aussi fixer le fetch mode par défaut :
  // $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $msg = 'ERREUR connexion PDO (' . $dsn . ') dans ' . $e->getFile()
    . ' Ligne ' . $e->getLine() . ' : ' . $e->getMessage();
  die($msg);
}


//******************************************************************************************
// requete standard PDO

function queryPDO($requete)
{
  global $db;
  $resultat = $db->query($requete) or die(print_r($bdd->errorInfo()));
  return $resultat;
}

function execPDO($requete)
{
  global $db;
  $resultat = $db->exec($requete);
  return $resultat;
}

//******************************************************************************************
// Fonction diverses
//******************************************************************************************

function classesPersonnage($id)
{
  global $db;
  $classe = '';
  // recherche des classes
  $requete = 'SELECT cla_nom as nom, pc_niveau as niveau FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_pe_id="' . $id . '" ORDER BY niveau DESC;';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      if ($classe != '') $classe .= ", ";
      $classe .= $dn['nom'] . " " . $dn['niveau'];
    endwhile;
  endif;
  // recherche des niveau de race
  $requete = 'SELECT  ra.ra_nom as nom1, arc.ra_nom as nom2, ra.ra_mod_niveau as mod1, arc.ra_mod_niveau as mod2 FROM dd_personnages JOIN dd_races as ra ON pe_ra_id=ra.ra_id JOIN dd_races as arc ON pe_arc_id=arc.ra_id WHERE pe_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    if ($dn['mod1'] > 0) $classe .= $dn['nom1'] . " " . $dn['mod1'];
    if ($dn['mod2'] > 0):
      if ($classe != '') $classe .= ', ';
      $classe .= $dn['nom2'] . " " . $dn['mod2'];
    endif;
  endif;
  return $classe;
}
function niveauPersonnage($id)
{
  global $db;
  $niveau = 0;
  // recherche des niveau de race
  $requete = 'SELECT  ra.ra_mod_niveau as mod1, arc.ra_mod_niveau as mod2 FROM dd_personnages JOIN dd_races as ra ON pe_ra_id=ra.ra_id JOIN dd_races as arc ON pe_arc_id=arc.ra_id WHERE pe_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $niveau += $dn['mod1'] + $dn['mod2'];
  endif;
  // recherche des classes
  $requete = 'SELECT cla_nom as nom, pc_niveau as niveau FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_pe_id="' . $id . '" ORDER BY niveau DESC;';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $niveau += $dn['niveau'];
    endwhile;
  endif;
  return $niveau;
}
function nls($id)
{ // niveau de lanceur de sort
  global $db;
  $requete = 'SELECT pe_nom, sum(`cn_niveauSortArcane`) as nls_p, sum(`cn_niveauSortDivin`) as nls_d, sum(`cn_niveauSortEffectif`) as nls_e FROM `dd_personnages` JOIN `dd_personnages_classes` ON pe_id=pc_pe_id JOIN `dd_classes` ON pc_cla_id=cla_id JOIN dd_classe_niveau ON pc_cla_id=cn_cla_id WHERE pe_id="' . $id . '" AND cn_niveau<=pc_niveau;';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $nls = 0;
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    if ($dn['nls_p'] > 0):
      $nls = $dn['nls_p'] + $dn['nls_e'];
    elseif ($dn['nls_d'] > 0):
      $nls = $dn['nls_d'] + $dn['nls_e'];
    endif;
  endif;
  return $nls;
}

function sortbonus($perso, $niveau)
{ // bonus de sort pour le niveau et le perso indiqués
  global $db;
  $requete = 'SELECT pe_int, pe_sag, pe_char FROM `dd_personnages` WHERE pe_id="' . $perso . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $mod = 0;
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);

  endif;
  return $mod;
}

function tag($critere, $champ)
{
  $avant = array(strtolower($critere));
  $apres = array('<span class="resultat_recherche">' . strtolower($critere) . '</span>');
  $resultat = str_replace($avant, $apres, $champ);
  $avant = array(ucfirst(strtolower($critere)));
  $apres = array('<span class="resultat_recherche">' . ucfirst(strtolower($critere)) . '</span>');
  $resultat = str_replace($avant, $apres, $resultat);
  return $resultat;
}

//******************************************************************************************
// récupération de données 
//******************************************************************************************

function valid_donnees($donnees)
{
  if (strlen($donnees) > 0):
    $donnees = trim($donnees);
    $donnees = stripslashes($donnees);
    $donnees = htmlspecialchars($donnees);
  endif;
  return $donnees;
}

function parametres()
{
  global $db;
  $requete = "SELECT * FROM dd_parametres";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $_SESSION[$dn['nom']] = $dn['valeur'];
    endwhile;
    return "OK";
  else:
    return "NOK";
  endif;
}

/* fonction générique pour remonter un champ d'une table à partir d'un id */
function libelle($table, $prefixe, $champ, $id = "", $critere = "")
{
  if ($critere != "") $critere = $critere . ' AND ';
  global $db;
  $requete = 'SELECT ' . $prefixe . '_id, ' . $prefixe . '_' . $champ . ' FROM ' . $table . ' WHERE ' . $critere . $prefixe . '_id="' . $id . '"';
  //writeLog("log.txt",$requete);
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = $dn[$prefixe . '_' . $champ];
  else:
    $text = '';
  endif;
  return $text;
}

/* fonction pour remonter le libellé d'une variable de la table dd_variables */
function libvar($id)
{
  global $db;
  $requete = 'SELECT var_cat, var_valeur FROM dd_variables WHERE var_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = $dn['var_valeur'];
  else:
    $text = '';
  endif;
  return $text;
}

/* fonction générique pour remonter un champ d'une table à partir d'un id */
function libelle_joueur($id)
{
  global $db;
  $requete = 'SELECT j_id, j_prenom, j_nom FROM joueurs WHERE j_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = stripslashes(substr($dn['j_prenom'], 0, 1)) . ". " . stripslashes($dn['j_nom']);
  else:
    $text = "erreur : " . $requete;
  endif;
  return $text;
}

/* fonction générique pour remonter le nom d'une rencontre avec son abreviation (contaténation du code du chapitre et du code de la rencontre. Ex : D2 */
function libelle_rencontre($id)
{
  global $db;
  $sql = 'SELECT re_nom, re_code, scc_abreviation FROM dd_rencontres JOIN dd_scenarios_chapitres ON re_scc_id=scc_id WHERE re_id= :id';
  $stmt = $db->prepare($sql);
  $stmt->execute([':id' => $id]);
  $rencontre = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($rencontre):
    if ($rencontre['scc_abreviation'] != "" && $rencontre['re_code'] != ""):
      $text = stripslashes($rencontre['scc_abrieviation']) . stripslashes($rencontre['re_code']) . " : ";
    else:
      $text = "";
    endif;
    $text .= stripslashes($rencontre['re_nom']);
  else:
    $text = "erreur : " . $id;
  endif;
  return $text;
}

/* personnage joué */
function pj($id)
{
  global $db;
  $requete = 'SELECT pe_id FROM dd_personnages WHERE pe_j_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = $dn['pe_id'];
  else:
    $text = 0;
  endif;
  return $text;
}


// remplit une liste de saisie standard
function optionList($table, $prefixe, $champ, $id = 0, $critere = "", $obligatoire = 0, $tout = "", $ordre = "")
{
  global $db;
  if ($critere != "") $critere = " WHERE " . $critere;
  if ($ordre != ""):
    $critere_ordre = $ordre;
  else:
    $critere_ordre = $prefixe . '_' . $champ;
  endif;
  $requete = 'SELECT ' . $prefixe . '_id, ' . $prefixe . '_' . $champ . ' FROM ' . $table . $critere . ' ORDER BY ' . $critere_ordre;
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($obligatoire == 0) $liste = '<option value="' . $tout . '">' . $tout . '</option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn[$prefixe . '_id'] . '"';
      if ($dn[$prefixe . '_id'] == $id) $liste .= ' selected="SELECTED"';
      $liste .= '>' . ucwords($dn[$prefixe . '_' . $champ]) . '</option>';
    endwhile;
  endif;
  return $liste;
}

// remplit une liste de saisie standard
function optionListVar(int $selected_id = 0, string $cat = ''): string
{
  global $db; // PDO

  $options_html = '';

  // Base de la requête
  $sql = "SELECT var_id, var_valeur FROM dd_variables";
  $params = [];

  // Filtre éventuel sur var_cat
  if ($cat !== '') {
    $sql .= " WHERE var_cat = :categorie";
    $params[':categorie'] = $cat;
  }

  $sql .= " ORDER BY var_valeur";

  // Préparation
  $stmt = $db->prepare($sql);
  if (!$stmt) {
    return ''; // ou gestion d'erreur plus poussée
  }

  // Exécution
  $stmt->execute($params);

  // Construction des <option>
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id     = (int)$row['var_id'];
    $valeur = htmlspecialchars($row['var_valeur'], ENT_QUOTES, 'UTF-8');

    $selected = ($selected_id !== 0 && $id === $selected_id)
      ? ' selected="selected"'
      : '';

    $options_html .= '<option value="' . $id . '"' . $selected . '>' . $valeur . "</option>\n";
  }

  return $options_html;
}

/*
function OptionListeClassesLS($id=0,$tout="")
{
global $db;	
$requete="SELECT cla_id, cla_nom FROM dd_classes WHERE cla_ruleset_var_id='".$_SESSION['ruleset']."' AND (cla_mag_id >0) ORDER BY cla_nom";
$result=queryPDO($requete);
$num_rows=$result->rowCount();
$liste='<option value="">'.$tout.'</option>';
if ($num_rows > 0):
	while($dn = $result->fetch(PDO::FETCH_ASSOC)):
		$liste.='<option value="'.$dn['cla_id'].'"';
    if ($id == $dn['cla_id']) $liste.=' selected="SELECTED"';
    $liste.='>'.$dn['cla_nom'].'</option>';	
	endwhile;
endif;
return $liste;
}
*/

function OptionListeClassesLS($id = 0, $tout = "")
{
  global $db;
  $requete = "SELECT cla_id, cla_nom 
            FROM dd_classes 
            WHERE cla_ruleset_var_id='" . $_SESSION['ruleset'] . "' 
            AND (cla_mag_id >0) 
            ORDER BY cla_nom";

  $result = queryPDO($requete);
  $num_rows = $result->rowCount();

  $liste = '<option value="all"';
  if ($id === "all") $liste .= ' selected="SELECTED"';
  $liste .= '>' . $tout . '</option>';

  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['cla_id'] . '"';
      if ($id == $dn['cla_id']) $liste .= ' selected="SELECTED"';
      $liste .= '>' . $dn['cla_nom'] . '</option>';
    endwhile;
  endif;

  return $liste;
}


// renvoie la liste des livres 
function OptionListeRessources($id = 0, $tout = "")
{
  global $db;
  // recherche des références de livres sélectionnées
  $requete = 'SELECT res_id, res_nom, res_editeur, res_selection FROM dd_ressources WHERE res_ruleset_var_id="' . $_SESSION['ruleset'] . '" AND res_selection="1" ORDER BY res_nom';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $num_rows = $result->rowCount();
  $selection = '';
  if ($num_rows > 0):
    //while($sort = mysql_fetch_array ($result)):
    while ($dn2 = $result->fetch(PDO::FETCH_ASSOC)):
      if ($selection != '') $selection .= ',';
      $selection .= $dn2['res_id'];
    endwhile;
  endif;
  $requete = 'SELECT * FROM dd_ressources WHERE res_id IN (' . $selection . ') ORDER BY res_nom';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  //$liste='<option value="'.$tout.'">'.$tout.'</option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['res_id'] . '"';
      if ($id == $dn['res_id']) $liste .= ' selected="SELECTED"';
      $liste .= '>' . $dn['res_nom'] . '</option>';
    endwhile;
  endif;
  return $liste;
}

// renvoie la liste des classes lanceur de sorts
function OptionListeJoueurs($id = 0)
{
  global $db;
  $requete = "SELECT j_id, j_nom, j_prenom FROM joueurs ORDER BY j_prenom, j_nom";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $liste = '<option value=""></option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['j_id'] . '"';
      if ($id == $dn['j_id']) $liste .= ' selected="SELECTED"';
      $liste .= '>' . stripslashes($dn['j_prenom']) . " " . stripslashes($dn['j_nom']) . '</option>';
    endwhile;
  endif;
  return $liste;
}

// renvoie la liste des catégorie de variables
function OptionListeCatVar($abrev = '', $tout = '')
{
  global $db;
  $requete = "SELECT * FROM dd_variables_categories ORDER BY varcat_nom";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $liste = '<option value="' . $tout . '">' . $tout . '</option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['varcat_abreviation'] . '"';
      if ($abrev == $dn['varcat_abreviation']) $liste .= ' selected="SELECTED"';
      $liste .= '>' . $dn['varcat_nom'] . '</option>';
    endwhile;
  endif;
  return $liste;
}

// renvoie la liste des rencontres
function OptionListeRencontre($id = 0, $tout = "")
{
  global $db;
  $requete = "SELECT re_id, sc_nom, scc_nom, re_abreviation, re_nom  FROM dd_rencontres JOIN dd_scenarios_chapitres ON re_scc_id=scc_id JOIN dd_scenarios ON scc_sc_id=sc_id ORDER BY sc_nom, scc_ordre, re_abreviation";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $liste = '<option value=""></option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['re_id'] . '"';
      if ($id == $dn['re_id']) $liste .= ' selected="SELECTED"';
      $liste .= '>' . $dn['sc_nom'] . ' / .' . $dn['scc_nom'] . ' / .' . $dn['re_abreviation'] . ' : <strong>' . $dn['re_nom'] . '</strong></option>';
    endwhile;
  endif;
  return $liste;
}

function niveaux_classe($id)
{
  global $db;
  $requete = "SELECT pc_niveau, cla_niveauMax FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_id='" . $id . "'";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  $liste = optionListInt(1, $dn['cla_niveauMax'], $dn['pc_niveau'], "T");
  return $liste;
}

// Affiche les règles en cascade
function regles($id, $iteration, $retour = "regles")
{
  $requete = 'SELECT * FROM dd_regles WHERE re_ruleset_var_id="' . $_SESSION['ruleset'] . '" AND re_re_id="' . $id . '" ORDER BY re_ordre, re_nom';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $iteration++;
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      if (strlen($id) > 0 && is_numeric($id)):
        $retour = $id;
      else:
        $retour = "regles";
      endif;
      $decalage = 2;
      for ($i = 1; $i < $iteration; $i++):
        $decalage += 1;
      endfor;
      echo '<div class="uneRegle">';
      if (nbRegles($dn['re_id']) > 0):
        //$cat='<i class="menu--icon fa fa-fw fa-caret-right nom"></i>';
        echo '<div class="gras lien" onClick="toggle(\'regles' . $dn['re_id'] . '\')"><i class="icon text-black fa fa-bookmark-o"></i></div>';
        $classe = ' noshow';
      else:
        echo '<div class="gras"><i class="icon text-grey fa fa-bookmark-o""></i></div>';
        $classe = '';
      endif;
      echo '<div><a href="regle.php?regle=' . $dn['re_id'] . '">' . f_nom($dn['re_nom']) . '</a></div></div>';
      echo '<div id="regles' . $dn['re_id'] . '" class="regles' . $classe . ' decalage2"' . $action . '>';
      regles($dn['re_id'], $iteration, $retour);
      echo '</div>';
    endwhile;
  endif;
  debug($requete);
}

function nbRegles($id)
{
  $requete = 'SELECT * FROM dd_regles WHERE re_ruleset_var_id="' . $_SESSION['ruleset'] . '" AND re_re_id="' . $id . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  return $num_rows;
}

// remplit une liste de saisie standard
function listeSorts($id, $selection)
{
  global $db;
  $critere = '';
  if ($selection != "") $critere = " AND so_res_id IN " . $selection;
  $requete = 'SELECT so_id, so_nom FROM dd_sorts WHERE re_ruleset_var_id="' . $_SESSION['ruleset'] . '"' . $critere . ' ORDER BY so_nom';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $liste = '<option value=""></option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['so_id'] . '"';
      if ($dn['so_id'] == $id) $liste .= ' selected="SELECTED"';
      $liste .= '>' . ucwords($dn['so_nom']) . '</option>';
    endwhile;
  endif;
  return $liste;
}

function niveauNote($id)
{
  global $db;
  $requete = "SELECT * FROM dd_notes WHERE no_id='" . $id . "'";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $niveau = 0;
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $niveau = 1;
    if (strlen($dn['no_texte_intermediaire']) > 0) $niveau++;
    if (strlen($dn['no_texte_avance']) > 0) $niveau++;
    if (strlen($dn['no_texte_expert']) > 0) $niveau++;
  endif;
  return $niveau;
}

function optionListNiveauNote($id)
{
  global $db;
  $requete = 'SELECT * FROM dd_niveaux_notes';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  $liste = '<option value=""></option>';
  if ($num_rows > 0):
    while ($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $liste .= '<option value="' . $dn['nino_id'] . '"';
      if ($dn['nino_id'] == $id) $liste .= ' selected="SELECTED"';
      $liste .= '>' . ucwords($dn['nino_nom']) . '</option>';
    endwhile;
  endif;
  return $liste;
}

function nbEnr($table)
{
  $requete = 'SELECT * FROM ' . $table;
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  return $num_rows;
}


//******************************************************************************************
// ajout d'un enregistrement dans une table

function addData($form, $table)
{
  $chaine = "";
  foreach ($form as $key => $value) {
    $value = addslashes($value);
    if ($key != "id") $chaine = $chaine . ", $key='$value'";
  }
  $requete = "INSERT INTO $table SET " . substr($chaine, 2);
  $result = execPDO($requete);
}

//******************************************************************************************
// RAZ des selection de livres de référence

function razRessourceLivre()
{
  global $db;
  $requete = 'UPDATE dd_ressources SET res_selection=0';
  $result = execPDO($requete);
  if (! $result)
    die("Erreur d'écriture dans la table ressources (raz) : " . $requete);
}
//******************************************************************************************
// MAJ de la table des livres de références

function updateRessourceLivre($id)
{
  global $db;
  $requete = 'UPDATE dd_ressources SET res_selection=1 WHERE res_id="' . $id . '"';
  $result = execPDO($requete);
  if (! $result)
    die("Erreur d'écriture dans la table ressources : " . $requete);
}

/******************************************************************************************/
// fonctions techniques
// remplit une liste de saisie standard

function tono_implode($tableau)
{
  if (!empty($tableau)):
    return implode(",", $tableau);
  else:
    return "";
  endif;
}

function f_nom($texte)
{
  return mb_ucfirst(stripslashes($texte));
}

function ouiNon($value)
{
  if ($value == 1):
    return "Oui";
  else:
    return "Non";
  endif;
}

function strip_tags_tono($text)
{
  //return strip_tags($text, "<br><ul><ol><li><strong><b><a>");
  return preg_replace('/<p>.*?<\/p>/s', '', $text, 1);
}

function optionListOuiNon($value)
{
  $liste = '<option value=""></option>';
  $liste .= '<option value="1"';
  if ($value == 1) $liste .= " SELECTED";
  $liste .= '>Oui</ option>';
  $liste .= '<option value="0"';
  if ($value == 0) $liste .= " SELECTED";
  $liste .= '>Non</ option>';
  return $liste;
}

function lastID($table, $prefixe)
{
  $sql = "SELECT max(" . $prefixe . "_id) as id FROM " . $table;
  $result = queryPDO($sql);
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  return $dn['id'];
}

function optionListInt($limitb, $limith, $value = "", $format = "I")
{
  /*
$limitb : borne inferieure
$limith : borne supérieure
$value  : valeur à sélectionner par défaut
$format : 'T' texte, 'I' integer
*/
  $liste = '';
  for ($i = $limitb; $i <= $limith; $i++):
    if ($format == "T" and $i < 10):
      $val = "0" . $i;
    else:
      $val = $i;
    endif;
    $liste .= '<option value="' . $val . '"';
    if ($value == $val) $liste .= " SELECTED";
    $liste .= '>' . $val . '</option>';
  endfor;
  return $liste;
}

// gestion du fil d'ariane poour les règles
function getBreadcrumb($re_id)
{
  global $db;
  $breadcrumb = [];
  while ($re_id):
    $stmt = $db->prepare("SELECT re_id, re_nom, re_re_id FROM dd_regles WHERE re_id = ?");
    $stmt->execute([$re_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) break;
    $breadcrumb[] = [
      'id' => $row['re_id'],
      'nom' => $row['re_nom']
    ];
    $re_id = $row['re_re_id'];
  endwhile;
  // On renverse le tableau pour aller de la racine vers la feuille
  $breadcrumb = array_reverse($breadcrumb);
  // Génération du HTML du fil d’Ariane
  $html = '';
  foreach ($breadcrumb as $i => $item):
    if ($i < count($breadcrumb) - 1) {
      $html .= '<span><a href="regle.php?regle=' . $item['id'] . '">' . htmlspecialchars($item['nom']) . '</a></span> <span> / </span>';
    } else {
      $html .= '<span>' . htmlspecialchars($item['nom']) . '</span>';
    }
  endforeach;
  return $html;
}

function nettoyerTexteTextarea($texte)
{
  // Supprime la balise <p> ouvrante et </p> fermante
  $texte = preg_replace('/^<p>|<\/p>$/i', '', trim($texte));
  // Remplace <br>, <br/> ou <br /> par des retours à la ligne
  $texte = preg_replace('/<br\s*\/?>/i', "\n", $texte);
  // Décodage des entités HTML (&eacute; → é, etc.)
  $texte = html_entity_decode($texte, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  return $texte;
}

/* Traitement des caractéristiques lors de l'ajout d'un monstre */
function traiter_bloc_caracteristiques($lignes)
{
  $html = '';

  // Conteneur global du bloc caractéristiques
  $html .= "<div class='bloc-caracteristiques'>";

  // ---------------------------------------------------------------------------
  // 1) Entête MOD / JS
  // ---------------------------------------------------------------------------
  $html .= "<div class='ligne-car ligne-entete'>";

  for ($i = 0; $i < 3; $i++):
    $html .= "<div class='car'></div>";
    $html .= "<div class='car'></div>";
    $html .= "<div class='car'>MOD</div>";
    $html .= "<div class='car'>JS</div>";
  endfor;

  $html .= "</div>";

  // ---------------------------------------------------------------------------
  // 2) Parsing des caractéristiques
  // ---------------------------------------------------------------------------
  $regex = '/(For|Dex|Con|Int|Sag|Cha)(\d+)([+\-]\d+)([+\-]\d+)/';

  // Ligne 2 → For / Dex / Con
  $html .= "<div class='ligne-car ligne-physique'>";

  if (isset($lignes[1])):
    preg_match_all($regex, $lignes[1], $matches, PREG_SET_ORDER);
    foreach ($matches as $m):
      $html .= "<div class='car1'>{$m[1]}</div>";
      $html .= "<div class='car2'>{$m[2]}</div>";
      $html .= "<div class='car3'>{$m[3]}</div>";
      $html .= "<div class='car3'>{$m[4]}</div>";
    endforeach;
  endif;

  $html .= "</div>";

  // Ligne 3 → Int / Sag / Cha
  $html .= "<div class='ligne-car ligne-mentale'>";

  if (isset($lignes[2])):
    preg_match_all($regex, $lignes[2], $matches, PREG_SET_ORDER);
    foreach ($matches as $m):
      $html .= "<div class='car4'>{$m[1]}</div>";
      $html .= "<div class='car5'>{$m[2]}</div>";
      $html .= "<div class='car6'>{$m[3]}</div>";
      $html .= "<div class='car6'>{$m[4]}</div>";
    endforeach;
  endif;

  $html .= "</div>";

  // Fin conteneur global
  $html .= "</div>\n";

  return $html;
}


/* ---------------------------------------------
 * Construit un sommaire à partir des <h2> / <h3> du HTML
 * Retourne [TOC_HTML, HTML_MODIFIE]
 */
function buildTocFromHtml(string $html, array $tags = ['h2', 'h3']): array
{
  if ($html === '') {
    return ['', $html];
  }
  // Préparer le parseur DOM
  $dom = new DOMDocument('1.0', 'UTF-8');
  // Empêcher les warnings pour HTML imparfait
  libxml_use_internal_errors(true);

  // Charger le HTML (forcer l'encodage)
  $wrapped = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $html . '</body></html>';
  $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
  libxml_clear_errors();
  $xpath = new DOMXPath($dom);
  // Sélectionner h2 et h3 dans l'ordre du document
  $xpathQuery = '//' . implode(' | //', array_map('strtolower', $tags));
  $nodes = $xpath->query($xpathQuery);
  if (!$nodes || $nodes->length === 0) {
    // Pas de titres -> pas de sommaire
    $body = $dom->getElementsByTagName('body')->item(0);
    $inner = innerHTML($body);
    return ['', $inner];
  }
  // Pour éviter les id dupliqués
  $usedIds = [];
  // Construction du TOC
  $toc = [];
  $currentH2Index = -1;
  foreach ($nodes as $node) {
    /** @var DOMElement $node */
    // Récupérer/attribuer un id unique
    $text = trim($node->textContent);
    if ($text === '') continue;
    $id = $node->getAttribute('id');
    if ($id === ''):
      $id = slugify($text);
      $idBase = $id;
      // assurer l'unicité
      $i = 2;
      while (isset($usedIds[$id])):
        $id = $idBase . '-' . $i++;
      endwhile;
      $node->setAttribute('id', $id);
      // Insérer un lien de retour juste avant le titre
      $returnLink = $dom->createElement('a', '↩ retour au sommaire');
      $returnLink->setAttribute('href', '#toc');
      $returnLink->setAttribute('class', 'back-to-toc');
      // On insère le lien avant le titre dans le DOM
      $node->parentNode->insertBefore($returnLink, $node);
    endif;
    $usedIds[$id] = true;
    $level = strtolower($node->tagName); // 'h2' ou 'h3'
    if ($level === 'h2') {
      $toc[] = [
        'id' => $id,
        'text' => $text,
        'children' => []
      ];
      $currentH2Index = count($toc) - 1;
    } else { // h3
      if ($currentH2Index === -1) {
        // H3 avant tout H2 : on crée un "groupe" orphelin
        $toc[] = [
          'id' => null,
          'text' => null,
          'children' => [
            ['id' => $id, 'text' => $text]
          ]
        ];
        $currentH2Index = count($toc) - 1;
      } else {
        $toc[$currentH2Index]['children'][] = ['id' => $id, 'text' => $text];
      }
    }
  }
  // Générer le HTML du TOC
  $tocHtml = renderToc($toc);
  // Extraire le body modifié
  $body = $dom->getElementsByTagName('body')->item(0);
  $htmlWithIds = innerHTML($body);
  return [$tocHtml, $htmlWithIds];
}

/** Transforme un texte en identifiant "slug" */
function slugify(string $text): string
{
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $text = mb_strtolower($text, 'UTF-8');
  // Remplacer accents
  $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
  // Garder lettres/chiffres/espaces-tirets
  $text = preg_replace('~[^a-z0-9\s-]~', '', $text);
  $text = preg_replace('~\s+~', '-', trim($text));
  $text = preg_replace('~-+~', '-', $text);
  return $text ?: 'section';
}

/**  Rendu HTML du sommaire (<nav><ol>…) */
function renderToc(array $toc): string
{
  if (empty($toc)) return '';
  // <details> = bloc repliable natif ; id="toc" pour les liens de retour
  $html = '<details id="toc" class="toc" open>';
  $html .= '<summary class="toc-summary">Sommaire</summary>';
  $html .= '<ol class="toc-list">';
  foreach ($toc as $item) {
    // cas rare : groupe orphelin de h3 sans h2
    if ($item['text'] === null) {
      if (!empty($item['children'])) {
        $html .= '<li><ol class="toc-sublist">';
        foreach ($item['children'] as $child) {
          $html .= '<li><i class="fa-solid fa-circle-chevron-right"></i> <a href="#' . htmlspecialchars($child['id']) . '">' . htmlspecialchars($child['text']) . '</a></li>';
        }
        $html .= '</ol></li>';
      }
      continue;
    }
    $html .= '<li>';
    $html .= '<a href="#' . htmlspecialchars($item['id']) . '">' . htmlspecialchars($item['text']) . '</a>';
    if (!empty($item['children'])) {
      $html .= '<ol class="toc-sublist">';
      foreach ($item['children'] as $child) {
        $html .= '<li><i class="fa-solid fa-circle-chevron-right"></i> <a href="#' . htmlspecialchars($child['id']) . '">' . htmlspecialchars($child['text']) . '</a></li>';
      }
      $html .= '</ol>';
    }
    $html .= '</li>';
  }
  $html .= '</ol></details>';
  return $html;
}

/** Récupère le HTML interne d’un noeud DOM */
function innerHTML(DOMNode $node): string
{
  $html = '';
  foreach ($node->childNodes as $child):
    $html .= $node->ownerDocument->saveHTML($child);
  endforeach;
  return $html;
}

/******************************************************************************************
  VARIABLES
 *****************************************************************************************/

/******************************************************************************************
fonction pour remonter la description d'une variable.
Utilisé pour afficher des libellés d'interface (var_cat="lib") tels que boutons, titres, aides
Utilisé pour la gestion des variables */
function descVariable($valeur, $defaut = '')
{
  $requete = 'SELECT var_description FROM dd_variables WHERE var_valeur="' . $valeur . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = $dn['var_description'];
  else:
    $text = $defaut;
  endif;
  return $text;
}

function nomCatVar($valeur, $defaut = '')
{
  $requete = 'SELECT varcat_nom FROM dd_variables_categories WHERE varcat_abreviation="' . $valeur . '"';
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows > 0):
    $dn = $result->fetch(PDO::FETCH_ASSOC);
    $text = $dn['varcat_nom'];
  else:
    $text = $defaut;
  endif;
  return $text;
}




//******************************************************************************************
// --- Sécurité des sessions ---
/*
ini_set('session.use_strict_mode', 1);
session_name('dd_session');

session_set_cookie_params([
    'lifetime' => 0,                // session (fermeture navigateur)
    'path'     => '/',
    'domain'   => '',               // à adapter si besoin
    'secure'   => isset($_SERVER['HTTPS']), // true si HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
*/

// --------- Handler de sessions en BDD ---------
class DbSessionHandler implements SessionHandlerInterface
{
  private PDO $db;
  private int $ttl;

  public function __construct(PDO $db, int $ttl = 1440)
  {
    $this->db  = $db;
    $this->ttl = $ttl;
  }

  public function open($savePath, $sessionName): bool
  {
    return true;
  }

  public function close(): bool
  {
    return true;
  }

  public function read($id): string
  {
    $sql = "SELECT data, last_access FROM dd_sessions WHERE id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
      return '';
    }

    $now = time();
    if ($row['last_access'] + $this->ttl < $now) {
      $this->destroy($id);
      return '';
    }

    return (string)$row['data'];
  }

  public function write($id, $data): bool
  {
    $now = time();

    $sql = "INSERT INTO dd_sessions (id, data, last_access)
                VALUES (:id, :data, :time)
                ON DUPLICATE KEY UPDATE
                    data = VALUES(data),
                    last_access = VALUES(last_access)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
      ':id'   => $id,
      ':data' => $data,
      ':time' => $now,
    ]);
  }

  public function destroy($id): bool
  {
    $sql = "DELETE FROM dd_sessions WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([':id' => $id]);
  }

  public function gc($max_lifetime): int|false
  {
    $limit = time() - $this->ttl;
    $sql   = "DELETE FROM dd_sessions WHERE last_access < :limit";
    $stmt  = $this->db->prepare($sql);
    $stmt->execute([':limit' => $limit]);
    return $stmt->rowCount();
  }
}

// --------- Paramètres de session ---------

$sessionTtl = 60 * 60 * 24; // 1 jour

ini_set('session.gc_maxlifetime', (string)$sessionTtl);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');

session_name('dd_session');

// Domaine du cookie : laisser vide pour que le navigateur gère
$cookieDomain = '';

// Si tu veux forcer en prod uniquement :
// if ($_SERVER['SERVER_NAME'] === 'maikastel.fr' || $_SERVER['SERVER_NAME'] === 'www.maikastel.fr') {
//     $cookieDomain = 'maikastel.fr';
// }

session_set_cookie_params([
  'lifetime' => 0,
  'path'     => '/',
  'domain'   => $cookieDomain,    // IMPORTANT : '' pour éviter les problèmes
  'secure'   => isset($_SERVER['HTTPS']),
  'httponly' => true,
  'samesite' => 'Lax',
]);

// Handler branché AVANT session_start
$handler = new DbSessionHandler($db, $sessionTtl);
session_set_save_handler($handler, true);


session_start();

// chargement des variables de session
$chargement = parametres();

$isAdmin = !empty($_SESSION['mj']) && $_SESSION['mj'] == 1;
$isDebug = !empty($_SESSION['debug']) && $_SESSION['debug'] == 1;

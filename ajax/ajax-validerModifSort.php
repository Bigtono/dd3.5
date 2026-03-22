<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$maintenant = new DateTime();
$s = $_POST['mp_so_id'];

if (!empty($s)):
  if ($s == "n"):
    // création d'un sort
    $requete = "INSERT INTO dd_sorts (
    so_nom,
    so_co_id,
    so_branche,
    so_portee,
    so_cible,
    so_zone_effet,
    so_duree_sort,
    so_duree_incantation,
    so_resistance,
    so_jet_sauvegarde,
    so_vocal,
    so_gestuel,
    so_materiel,
    so_focalisateur,
    so_focalisateur_divin,
    so_texte,
    so_resume,
    so_res_id,
    so_date,
    so_ruleset_var_id,
    so_j_id)
    VALUES ('" .
      addslashes($_POST['mp_so_nom']) . "','" .
      addslashes($_POST['mp_so_co_id']) . "','" .
      addslashes($_POST['mp_so_branche']) . "','" .
      addslashes($_POST['mp_so_portee']) . "','" .
      addslashes($_POST['mp_so_cible']) . "','" .
      addslashes($_POST['mp_so_zone_effet']) . "','" .
      addslashes($_POST['mp_so_duree_sort']) . "','" .
      addslashes($_POST['mp_so_duree_incantation']) . "','" .
      $_POST['mp_so_resistance'] . "','" .
      addslashes($_POST['mp_so_jet_sauvegarde']) . "','" .
      $_POST['mp_so_vocal'] . "','" .
      $_POST['mp_so_gestuel'] . "','" .
      $_POST['mp_so_materiel'] . "','" .
      $_POST['mp_so_focalisateur'] . "','" .
      $_POST['mp_so_focalisateur_divin'] . "','" .
      addslashes($_POST['mp_so_texte']) . "','" .
      addslashes($_POST['mp_so_resume']) . "','" .
      $_POST['mp_so_res_id'] . "','" .
      $maintenant->format('Y:m:d H:i:s') . "','" .
      $_SESSION['ruleset'] . "','" .
      $_SESSION['user_id'] . "')";
    $resultat = execPDO($requete);
    $s = lastID("dd_sorts", "so");
  else:
    // MAJ du sort
    $requete = "UPDATE dd_sorts
      SET so_nom='" . addslashes($_POST['mp_so_nom']) .
      "', so_co_id='" . addslashes($_POST['mp_so_co_id']) .
      "', so_branche='" . addslashes($_POST['mp_so_branche']) .
      "', so_portee='" . addslashes($_POST['mp_so_portee']) .
      "', so_cible='" . addslashes($_POST['mp_so_cible']) .
      "', so_zone_effet='" . addslashes($_POST['mp_so_zone_effet']) .
      "', so_duree_sort='" . addslashes($_POST['mp_so_duree_sort']) .
      "', so_duree_incantation='" . addslashes($_POST['mp_so_duree_incantation']) .
      "', so_resistance='" . $_POST['mp_so_resistance'] .
      "', so_jet_sauvegarde='" . addslashes($_POST['mp_so_jet_sauvegarde']) .
      "', so_vocal='" . $_POST['mp_so_vocal'] .
      "', so_gestuel='" . $_POST['mp_so_gestuel'] .
      "', so_materiel='" . $_POST['mp_so_materiel'] .
      "', so_focalisateur='" . $_POST['mp_so_focalisateur'] .
      "', so_focalisateur_divin='" . $_POST['mp_so_focalisateur_divin'] .
      "', so_texte='" . addslashes($_POST['mp_so_texte']) .
      "', so_resume='" . addslashes($_POST['mp_so_resume']) .
      "', so_res_id='" . $_POST['mp_so_res_id'] .
      "', so_date='" . $maintenant->format('Y:m:d H:i:s') .
      "', so_ruleset_var_id='" . $_SESSION['ruleset'] .
      "', so_j_id='" . $_SESSION['user_id'] .
      "' WHERE so_id='" . $s . "'";
    $resultat = execPDO($requete);
  endif;

  // ajout des données dans la table sortclasse
  // étape 1 - effacer les données existantes
  $requete2 = 'DELETE FROM dd_sortclasse WHERE sc_so_id="' . $s . '"';
  $result_suppr = queryPDO($requete2);
  // étape 2 - ajouter les nouvelles données
  $requete_ls = 'SELECT * FROM dd_classes WHERE cla_mag_id>0';
  $result_ls = queryPDO($requete_ls);
  $num_row_ls = $result_ls->rowCount();
  $msg_ls = "";
  if ($num_row_ls > 0):
    $msg_ls = "[LS] ";
    while ($dnls = $result_ls->fetch(PDO::FETCH_ASSOC)):
      $champ = "ls" . $dnls['cla_id'];
      $niveau = $_POST[$champ];
      $msg_ls .= $dnls['cla_nom'] . ' (' . $champ . ') => ';
      // on vérifie s'il faut créer l'enregistrement
      if ($niveau != ""):
        $requete_maj_ls = 'INSERT INTO dd_sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ("' . $s . '","' . $dnls['cla_id'] . '", "' . $niveau . '")';
        $resultat_maj_ls = execPDO($requete_maj_ls);
        $msg_ls .= 'N' . $niveau . '. / ';
      else:
        $msg_ls .= 'Aucun niveau. / ';
      endif;
    endwhile;
  else:
    $msg_ls .= 'Pas de classe de LS. ';
  endif;

  // ajout des données dans la table sortdomaine
  // étape 1 - effacer les données existantes
  $requete = 'DELETE FROM dd_sortdomaine WHERE sd_so_id="' . $s . '"';
  $result_suppr = queryPDO($requete);
  // étape 2 - ajouter les nouvelles données
  $requete_do = 'SELECT * FROM dd_domaines ORDER BY do_nom';
  $result_do = queryPDO($requete_do);
  $num_row_do = $result_do->rowCount();
  if ($num_row_do > 0):
    $msg_ls .= " //// [DOM]";
    while ($dndo = $result_do->fetch(PDO::FETCH_ASSOC)):
      $champ = "ds" . $dndo['do_id'];
      $niveau = $_POST[$champ];
      $msg_ls .= $dndo['do_nom'] . ' (' . $champ . ') => ';
      // on vérifie s'il faut créer l'enregistrement
      if ($niveau != ""):
        $requete_maj_do = 'INSERT INTO dd_sortdomaine (sd_so_id, sd_do_id, sd_niveau) VALUES ("' . $s . '","' . $dndo['do_id'] . '", "' . $niveau . '")';
        $resultat_maj_do = execPDO($requete_maj_do);
        $msg_ls .= 'N' . $niveau . '. / ';
      else:
        $msg_ls .= 'Aucun niveau. / ';
      endif;
    endwhile;
  else:
    $msg_ls .= 'Pas de domaine. ';
  endif;
  // On ajoute les donnnées dans un tableau
  echo $s . "@" . $requete . "@" . $_POST['mp_so_nom'] . "@" . $_POST['mp_so_college'] . "@" . $domaines . "@" . $_POST['mp_so_resume'] . "@" . $msg_ls;
else:
  echo "prout@" . $_POST['mp_so_id'];
endif;

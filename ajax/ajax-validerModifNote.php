<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$maintenant = new DateTime();

$n = $_POST['mp_no_id'];

if (!empty($n)):
  if ($n == "n"):
    // création d'une note
    $requete = "INSERT INTO dd_notes (no_nom, no_tyno_id, no_cumulatif, no_texte_basique, no_texte_intermediaire, no_texte_avance, no_texte_expert, no_date, no_j_id) VALUES ('" .
      addslashes($_POST['mp_no_nom']) . "','" .
      $_POST['mp_no_tyno_id'] . "','" .
      $_POST['mp_no_cumulatif'] . "','" .
      addslashes($_POST['mp_no_texte_basique']) . "','" .
      addslashes($_POST['mp_no_texte_intermediaire']) . "','" .
      addslashes($_POST['mp_no_texte_avance']) . "','" .
      addslashes($_POST['mp_no_texte_expert']) . "','" .
      $maintenant->format('Y:m:d H:i:s') . "','" .
      $_SESSION['user_id'] . "')";
    $resultat = execPDO($requete);
    $n = lastID("dd_notes", "no");
  else:
    // MAJ de la note
    $requete = "UPDATE dd_notes
      SET no_nom='" . addslashes($_POST['mp_no_nom']) .
      "', no_tyno_id='" . $_POST['mp_no_tyno_id'] .
      "', no_cumulatif='" . $_POST['mp_no_cumulatif'] .
      "', no_texte_basique='" . addslashes($_POST['mp_no_texte_basique']) .
      "', no_texte_intermediaire='" . addslashes($_POST['mp_no_texte_intermediaire']) .
      "', no_texte_avance='" . addslashes($_POST['mp_no_texte_avance']) .
      "', no_texte_expert='" . addslashes($_POST['mp_no_texte_expert']) .
      "', no_date='" . $maintenant->format('Y:m:d H:i:s') .
      "', no_j_id='" . $_SESSION['user_id'] .
      "' WHERE no_id='" . $n . "'";
    $resultat = execPDO($requete);
  endif;

  // on met à jour la diffusion de la note
  // on supprime la diffusion actuelle
  $requete_del = 'DELETE FROM dd_personnages_notes WHERE pno_no_id="' . $n . '"';
  $result_del = execPDO($requete_del);
  // on crée les nouvelles entrées dans la table
  $listdif = explode("pe", $_POST['diffusion']);
  $debug = $_POST['diffusion'] . ': ';
  foreach ($listdif as $key => $value):
    $dif = explode("a", $value);
    //if ($value && $value!=''):
    $requete_add = 'INSERT INTO dd_personnages_notes (pno_no_id, pno_pe_id, pno_niveau) VALUES ("' . $n . '","' . $dif[0] . '","' . $dif[1] . '")';
    $debug .= $requete_add . '\n\r';
    $result_add = execPDO($requete_add);
  //endif;
  endforeach;

  // on réactualise la liste des notes
  $requete_no = "SELECT * FROM dd_notes WHERE no_id='" . $n . "'";
  $result_no = queryPDO($requete_no);
  $num_rows_no = $result_no->rowCount();
  $dnno = $result_no->fetch(PDO::FETCH_ASSOC);
  include('../include/insert/' . $_SESSION['rulesetRep'] . '/ligneNote.php');

  // On ajoute les donnnées dans un tableau
  echo $n . "@" . $ligne . "@" . $requete_no . "@" . $debug;
else:
  echo "0@Erreur " . $_POST['mp_no_id'];
endif;

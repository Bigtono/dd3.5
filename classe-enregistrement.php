<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("connexion-mj.php");

include("include/diverslib.inc.php");
include("include/date.inc.php");

$c = isset($_POST['mp_cla_id'])
   ? (int) $_POST['mp_cla_id']
   : 0;

if ($action == 'cancel'):
   header("Location: classes.php");
   exit;
endif;

if ($c > 0):
   // Modification 
   $sql = "UPDATE dd_classes SET
   cla_nom = :cla_nom,
   cla_clt_id = :cla_clt_id,
   cla_abreviation = :cla_abreviation,
   cla_dV = :cla_dV,
   cla_pointsCompetences = :cla_pointsCompetences,
   cla_alignement = :cla_alignement,
   cla_car_id = :cla_car_id,
   cla_po_niveau1 = :cla_po_niveau1,
   cla_conditions = :cla_conditions,
   cla_description = :cla_description,
   cla_traits = :cla_traits,
   cla_caracteristiques = :cla_caracteristiques,
   cla_sauvegardes = :cla_sauvegardes,
   cla_competences = :cla_competences,
   cla_armes = :cla_armes,
   cla_armures = :cla_armures,
   cla_outils = :cla_outils,
   cla_equipement = :cla_equipement,
   cla_pouvoir1 = :cla_pouvoir1,
   cla_pouvoir2 = :cla_pouvoir2,
   cla_pouvoir3 = :cla_pouvoir3,
   cla_pouvoir4 = :cla_pouvoir4,
   cla_sorts = :cla_sorts,
   cla_mag_id = :cla_mag_id,
   cla_niveauMax = :cla_niveauMax,
   cla_res_id = :cla_res_id
   WHERE cla_id = :cla_id";
   $stmt = $db->prepare($sql);
   $resultat = $stmt->execute([
      ':cla_nom' => $_POST['mp_cla_nom'],
      ':cla_clt_id' => $_POST['mp_cla_clt_id'],
      ':cla_abreviation' => $_POST['mp_cla_abreviation'],
      ':cla_dV' => $_POST['mp_cla_dV'],
      ':cla_pointsCompetences' => $_POST['mp_cla_pointsCompetences'],
      ':cla_alignement' => $_POST['mp_cla_alignement'],
      ':cla_car_id' => $_POST['mp_cla_car_id'],
      ':cla_po_niveau1' => $_POST['mp_cla_po_niveau1'],
      ':cla_conditions' => $_POST['mp_cla_conditions'],
      ':cla_description' => $_POST['mp_cla_description'],
      ':cla_traits' => $_POST['mp_cla_traits'],
      ':cla_caracteristiques' => $_POST['mp_cla_caracteristiques'],
      ':cla_sauvegardes' => $_POST['mp_cla_sauvegardes'],
      ':cla_competences' => $_POST['mp_cla_competences'],
      ':cla_armes' => $_POST['mp_cla_armes'],
      ':cla_armures' => $_POST['mp_cla_armures'],
      ':cla_outils' => $_POST['mp_cla_outils'],
      ':cla_equipement' => $_POST['mp_cla_equipement'],
      ':cla_pouvoir1' => $_POST['mp_cla_pouvoir1'],
      ':cla_pouvoir2' => $_POST['mp_cla_pouvoir2'],
      ':cla_pouvoir3' => $_POST['mp_cla_pouvoir3'],
      ':cla_pouvoir4' => $_POST['mp_cla_pouvoir4'],
      ':cla_sorts' => $_POST['mp_cla_sorts'],
      ':cla_mag_id' => $_POST['mp_cla_mag_id'],
      ':cla_niveauMax' => $_POST['mp_cla_niveauMax'],
      ':cla_res_id' => $_POST['mp_cla_res_id'],
      ':cla_id' => $c,
   ]);
   $msg = "M";
else:
   // Ajout
   $sql = "INSERT INTO dd_classes (
   cla_nom, cla_clt_id, cla_abreviation, cla_dV, cla_pointsCompetences, cla_alignement,
   cla_car_id, cla_po_niveau1, cla_conditions, cla_description, cla_traits,
   cla_caracteristiques, cla_sauvegardes, cla_competences, cla_armes, cla_armures,
   cla_outils, cla_equipement, cla_pouvoir1, cla_pouvoir2, cla_pouvoir3, cla_pouvoir4,
   cla_sorts, cla_mag_id, cla_niveauMax, cla_res_id, cla_ruleset_var_id
   ) VALUES (
   :cla_nom, :cla_clt_id, :cla_abreviation, :cla_dV, :cla_pointsCompetences, :cla_alignement,
   :cla_car_id, :cla_po_niveau1, :cla_conditions, :cla_description, :cla_traits,
   :cla_caracteristiques, :cla_sauvegardes, :cla_competences, :cla_armes, :cla_armures,
   :cla_outils, :cla_equipement, :cla_pouvoir1, :cla_pouvoir2, :cla_pouvoir3, :cla_pouvoir4,
   :cla_sorts, :cla_mag_id, :cla_niveauMax, :cla_res_id, :cla_ruleset_var_id
   )";
   $stmt = $db->prepare($sql);
   $resultat = $stmt->execute([
      ':cla_nom' => $_POST['mp_cla_nom'],
      ':cla_clt_id' => $_POST['mp_cla_clt_id'],
      ':cla_abreviation' => $_POST['mp_cla_abreviation'],
      ':cla_dV' => $_POST['mp_cla_dV'],
      ':cla_pointsCompetences' => $_POST['mp_cla_pointsCompetences'],
      ':cla_alignement' => $_POST['mp_cla_alignement'],
      ':cla_car_id' => $_POST['mp_cla_car_id'],
      ':cla_po_niveau1' => $_POST['mp_cla_po_niveau1'],
      ':cla_conditions' => $_POST['mp_cla_conditions'],
      ':cla_description' => $_POST['mp_cla_description'],
      ':cla_traits' => $_POST['mp_cla_traits'],
      ':cla_caracteristiques' => $_POST['mp_cla_cla_caracteristiques'],
      ':cla_sauvegardes' => $_POST['mp_cla_sauvegardes'],
      ':cla_competences' => $_POST['mp_cla_competences'],
      ':cla_armes' => $_POST['mp_cla_armes'],
      ':cla_armures' => $_POST['mp_cla_armures'],
      ':cla_outils' => $_POST['mp_cla_outils'],
      ':cla_equipement' => $_POST['mp_cla_equipement'],
      ':cla_pouvoir1' => $_POST['mp_cla_pouvoir1'],
      ':cla_pouvoir2' => $_POST['mp_cla_pouvoir2'],
      ':cla_pouvoir3' => $_POST['mp_cla_pouvoir3'],
      ':cla_pouvoir4' => $_POST['mp_cla_pouvoir4'],
      ':cla_sorts' => $_POST['mp_cla_sorts'],
      ':cla_mag_id' => $_POST['mp_cla_mag_id'],
      ':cla_niveauMax' => $_POST['mp_cla_niveauMax'],
      ':cla_res_id' => $_POST['mp_cla_res_id'],
      ':cla_ruleset_var_id' => $_SESSION['ruleset'],
   ]);
   $c = $db->lastInsertId($resultat);
   $msg = "C";
endif;

header("location: classe.php?classe=" . $c . "&tri=" . $_GET['tri'] . "&msg=" . $msg);

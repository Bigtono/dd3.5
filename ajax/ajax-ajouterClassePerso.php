<?php
include_once("../include/dblib.inc.php");
include_once("../include/diverslib.inc.php");

if (isset($_POST['perso'])):
  $p = $_POST['perso'];

  //**********************************************************************
  // mise en forme du contenu
  $result = '<form class="affichage ajout_classe_form" method="post">';
  $result .= '  <input type="hidden" id="mp_pe_id" value="' . $p . '">';
  $result .= '  <select id="mp_cla_id" class="libelle_classe">' . optionList("dd_classes", "cla", "nom") . '</select>';
  $result .= '  <select id="mp_cp_niveau" class="niveau_classe">' . optionListInt(1, 20) . '</select>';
  $result .= '  <span id="validAjouterClasse" onclick="validerAjoutClasse(' . $p . ')" class="ajouter_classe"><i class="fa-solid fa-circle-plus"></i></span>';
  $result .= '</form>';

  // On ajoute les donnnées dans un tableau
  echo $p . "@" . $result;
else:
  echo "erreur";
endif;

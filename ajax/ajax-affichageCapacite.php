<?php
include_once("../include/dblib.inc.php");

if (isset($_POST['cap'])):
  $q = $_POST['cap'];
  $requete = "SELECT * FROM dd_capacites_speciales WHERE cap_id='" . $q . "'";
  $resultat = queryPDO($requete);
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);

  $result = '<div id="capacite" class="affichage">';

  $result .= '  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien" onclick="modifierCapacit(' . $dn['cap_id'] . ')"><i class="fa fa-pencil"></i></div></div>';

  $result .= '  <div class="nom_objet">' . stripslashes($dn['cap_nom']) . '</div>';

  $result .= '<div class="texte">' . stripslashes($dn['cap_description']) . '</div>';
  $result .= '</div>';

  // On ajoute les donnnées dans un tableau
  echo $dn['cap_id'] . "@" . $result;
else:
  echo "rien...";
endif;

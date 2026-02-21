<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$p=$_POST['perso'];
$om=$_POST['eqt'];
$mod=$_POST['mod'];
  
// recherche de la dotatation
$requete='SELECT * FROM dd_personnages_objets_magiques WHERE pom_om_id="'.$om.'" AND pom_pe_id="'.$p.'" AND pom_modificateur="'.$mod.'"';
$result_dot=queryPDO($requete);
$num_rows_dot=$result_dot->rowCount();
if ($num_rows_dot>0):
  $dot = $result_dot->fetch(PDO::FETCH_ASSOC);
  $eqt=$dot['pom_id'];
  $dotation=$dot['pom_qte'];
  $mod=$dot['pom_modificateur']; // valeur récupérée pour la MAJ de la Qté dans la liste des objets
  else:
  $dotation=0;
endif;

// ajout de l'équipement
if ($dotation>0): 
  $requete='UPDATE dd_personnages_objets_magiques SET pom_qte=pom_qte+1 WHERE pom_om_id="'.$om.'" AND pom_pe_id="'.$p.'"';
  $resultat=execPDO($requete);
  $nouvelle_dotation=$dotation+1;
  // MAJ du bouton suppression après un ajout d'une nouvelle dotation 
  $suppr='<button id="qte'.$om.'m'.$mod.'"class="bouton" onClick="supprimer('.$eqt.')">Supprimer</button>';
  else:
  $requete='INSERT INTO dd_personnages_objets_magiques (pom_om_id, pom_pe_id, pom_modificateur) VALUES ("'.$om.'","'.$p.'","'.$mod.'")';
  $resultat=execPDO($requete);
  $nouvelle_dotation=1; 
  $eqt=lastID('dd_personnages_objets_magiques', 'pom');
  // MAJ du bouton suppression après un ajout d'une nouvelle dotation
  $suppr='<button id="qte'.$om.'m'.$mod.'"class="bouton" onClick="supprimer('.$eqt.')">Supprimer</button>';
endif;
$msg="";
// actualisation de la liste des équipements
$requete_maj='SELECT pom_id, om_id, om_nom, pom_qte as qte, pom_modificateur as modificateur FROM dd_personnages_objets_magiques JOIN dd_objets_magiques ON pom_om_id=om_id WHERE pom_pe_id="'.$p.'"';
$result_dot=queryPDO($requete_maj);
$num_rows_dot=$result_dot->rowCount();
$equipements='';
if ($num_rows_dot>0):
  while($dot = $result_dot->fetch(PDO::FETCH_ASSOC)):
    if ($dot['qte']>1):
      $qte=" x".$dot['qte'];
      else:
      $qte="";
    endif;
    if ($dot['modificateur']>0):
      $modif=" (+".$dot['modificateur'].')';
      else:
      $modif="";
    endif;
    $equipements.='<div class="data dflex">';
    if ($_SESSION['mj']==1) $equipements.='  <div onClick="supprimer('.$dot['pom_id'].')" class="icone"><i class="fa fa-trash" aria-hidden="true"></i></div>';
    $equipements.='  <div onclick="afficherOM('.$dot['om_id'].')" class="lien">'.$dot['om_nom'].$modif.$qte.'</div>';
    $equipements.='</div>';
  endwhile;
endif;

// On ajoute les donnnées dans un tableau
echo $p."@".$eqt."@".$dotation."@".$nouvelle_dotation."@".$equipements."@".$mod."@".$om."@".$suppr;
?>
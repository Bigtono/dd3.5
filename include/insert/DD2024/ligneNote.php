<?
  // Préparation du contenu
  $nom=stripslashes(ucfirst($dnno['no_nom']));
  $click='afficherNote('.$dnno['no_id'].','.$accreditation.')';
  if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idno=' ('.$dnno['no_id'].')';
  $ligne='';
  if ($_SESSION['mj']==1) $ligne.='  <div class="icone_suppr"><span onClick="suppression(\'dd_notes\',\'no\','.$dnno['no_id'].')"><i class="fa fa-trash"></i></span></div>';
  if ($_SESSION['mj']==1) $ligne.='  <div class="icone_modif"><span onclick="modifierNote('.$dnno['no_id'].','.$p.')"><i class="fa fa-pencil"></i></span></div>';    
  $ligne.='  <div id="nomNo'.$dnno['no_id'].'" class="nom_note" onclick="'.$click.'">'.$nom.$idno.'</div>';
  $ligne.='  <div id="catNo'.$dnno['no_id'].'" class="categorie_note" onclick="'.$click.'">'.libelle("dd_types_notes","tyno","nom",$dnno['no_tyno_id']).'</div>';
  $ligne.='  <div id="catNo'.$dnno['no_id'].'" class="niveau_note" onclick="'.$click.'">DD '.(int)$accreditation.'</div>';
  //$ligne.='</div>';
?>

<?
  $ligne='<div id="var'.$dn['var_id'].'" class="item data">';
  $ligne.='  <div class="icone_suppr"><span onClick="suppressionVariable('.$dn['var_id'].',\''.f_nom($dn['var_valeur']).'\')"><i class="fa fa-trash"></i></span></div>';
  $ligne.='  <div class="icone_modif" onClick="modifierVariable(\''.$dn['var_id'].'\')"><i class="fa fa-pencil"></i></a></div>';
  $ligne.='  <div id="nomEqt'.$dn['var_id'].'" class="nom_variable" onClick="afficherVariable('.$dn['var_id'].')">'.$dn['var_valeur'].'</div>';  
  $ligne.='  <div id="catEqt'.$dn['var_id'].'" class="categorie_variable">'.nomCatVar($dn['var_cat']).'</div>';
  $ligne.='  <div id="catEqt'.$dn['var_id'].'" class="variable_variable">'.$dn['var_cat'].'</div>';
  $ligne.='  <div id="catEqt'.$dn['var_id'].'" class="description_variable">'.f_nom($dn['var_description']).'</div>';
  $ligne.='</div>';
?>

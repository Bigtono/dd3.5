<?
  $liste='';
  // gestion des classes
  $requete_cl='SELECT pc_id, cla_nom, pc_niveau FROM dd_personnages_classes JOIN dd_classes ON pc_cla_id=cla_id WHERE pc_pe_id="'.$p.'" ORDER BY pc_niveau DESC;';
  $result_cl=queryPDO($requete_cl);
  $num_rows_cl=$result_cl->rowCount();
  if ($num_rows_cl > 0):
    while($dncl = $result_cl->fetch(PDO::FETCH_ASSOC)):
      $liste.='<div id="pc'.$dncl['pc_id'].'" class="classe">';
      $liste.='  <div onClick="supprimerClassePerso('.$p.','.$dncl['pc_id'].')" class="suppression"><i class="fa-solid fa-trash"></i></div>';
      $liste.='  <div class="libelle_classe">'.$dncl['cla_nom'].'</div>';
      $liste.='  <select class="niveau_classe" id="pcn'.$dncl['pc_id'].'" name="pcn'.$dncl['pc_id'].'" onChange="majNiveauClassePerso(\'pcn'.$dncl['pc_id'].'\')">';
      $liste.=niveaux_classe($dncl['pc_id']);
      $liste.='  </select>';
      $liste.='</div>';
    endwhile;
  endif;
?>
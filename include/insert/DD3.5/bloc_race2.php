<div class="label mt10">Capacit&eacute;s raciales :</div>     
<?
  $cap='<ul>';

  // Origine
  $requete_vd = "SELECT cap_description FROM dd_race_capacite LEFT JOIN dd_capacites_speciales ON cr_cap_id=cap_id WHERE cr_ra_id='".$r."' AND cap_type='origine'";
  $resultat_vd = queryPDO($requete_vd);
  $num_rows_vd=$resultat_vd->rowCount();
  if ($num_rows_vd>0):
    $dnvd = $resultat_vd->fetch(PDO::FETCH_ASSOC);
    $cap.='<li>'.stripslashes($dnvd['cap_description']).'</li>';
    else:
    $cap.='';
  endif;

  // Compétences
  $carac='';    
  if ($dn['ra_modifFor']!=0):
    if ($dn['ra_modifFor']>0) $carac.='+';
    $carac.=$dn['ra_modifFor'].' en Force';
  endif;
  if ($dn['ra_modifCon']!=0):
    if ($carac!='') $carac.=', ';
    if ($dn['ra_modifCon']>0) $carac.='+';
    $carac.=$dn['ra_modifCon'].' en Constitution';
  endif;

  if ($dn['ra_modifDex']!=0):
    if ($carac!='') $carac.=', ';
    if ($dn['ra_modifDex']>0) $carac.='+';
    $carac.=$dn['ra_modifDex'].' en dextérité';
  endif;

  if ($dn['ra_modifInt']!=0):
    if ($carac!='') $carac.=', ';
    if ($dn['ra_modifInt']>0) $carac.='+';
    $carac.=$dn['ra_modifInt'].' en Intelligence';
  endif;

  if ($dn['ra_modifSag']!=0):
    if ($carac!='') $carac.=', ';
    if ($dn['ra_modifSag']>0) $carac.='+';
    $carac.=$dn['ra_modifSag'].' en Sagesse';
  endif;

  if ($dn['ra_modifCha']!=0):
    if ($carac!='') $carac.=', ';
    if ($dn['ra_modifCha']>0) $carac.='+';
    $carac.=$dn['ra_modifCha'].' en Charisme';
  endif;
  if ($carac!='') $cap.='<li>'.utf8_encode($carac).'</li>';

  // Vitesse de déplacement
  $requete_vd = "SELECT cap_description FROM dd_race_capacite LEFT JOIN dd_capacites_speciales ON cr_cap_id=cap_id WHERE cr_ra_id='".$r."' AND cap_type='vitesse'";
  $resultat_vd = queryPDO($requete_vd);
  $num_rows_vd=$resultat_vd->rowCount();
  if ($num_rows_vd>0):
    $dnvd = $resultat_vd->fetch(PDO::FETCH_ASSOC);
    $cap.='<li>'.stripslashes($dnvd['cap_description']).'</li>';
    else:
    $cap.='';
  endif;

  // Taille
  $requete_cr = "SELECT cap_description FROM dd_race_capacite LEFT JOIN dd_capacites_speciales ON cr_cap_id=cap_id WHERE cr_ra_id='".$r."' AND cap_type='taille'";
  $resultat_cr = queryPDO($requete_cr);
  $num_rows_cr=$resultat_cr->rowCount();
  if ($num_rows_cr>0):
    $dncr = $resultat_cr->fetch(PDO::FETCH_ASSOC);
    $cap.='<li>'.stripslashes($dncr['cap_description']).'</li>';
  endif;

  // Autres capacités spéciales
  $requete_cr = "SELECT cap_description FROM dd_race_capacite LEFT JOIN dd_capacites_speciales ON cr_cap_id=cap_id WHERE cr_ra_id='".$r."' AND cap_type NOT IN ('taille','vitesse','origine')";
  $resultat_cr = queryPDO($requete_cr);
  $num_rows_cr=$resultat_cr->rowCount();
  if ($num_rows_cr>0):

    while ($dncr = $resultat_cr->fetch(PDO::FETCH_ASSOC)):
      $cap.='<li>'.stripslashes($dncr['cap_description']).'</li>';
    endwhile;
    else: 
    $cap.='<div>aucune capacit&eacute; raciale</div>';
  endif;

  // Ajustement de niveau
  if ($dn['ra_mod_niveau']>0):
    $cap.='<li><strong>Ajustement de niveau : </strong>+'.$dn['ra_mod_niveau'].'</li>';      
  endif;

  $cap.='</ul>';

?>
<div class="texte capacites"><? echo $cap; ?></div>    

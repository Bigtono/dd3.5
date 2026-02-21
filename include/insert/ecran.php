<?
  echo '<div class="mt10 mb10 gras">Écran</div>';
  $requete='SELECT * FROM dd_regles WHERE re_ecran >0 ORDER BY re_ecran';
  $result_e=queryPDO($requete);
  $num_rows_e=$result_e->rowCount();
  if ($num_rows_e>0):
    while($dne=$result_e->fetch(PDO::FETCH_ASSOC)):
      echo '<div onClick="afficherRegle('.$dne['re_id'].')" class="lien mb5">'.$dne['re_nom'].'</div>';
    endwhile;
  endif;
?>
<?
  if (strlen($critere_sql)>0): 
    $requete_le="SELECT * FROM dd_variables WHERE ".$critere_sql." ORDER BY var_valeur";
    else:
    $requete_le="SELECT * FROM dd_variables ORDER BY var_valeur";
  endif;
  $resultat_le=queryPDO($requete_le);
  $num_rows_le=$resultat_le->rowCount();
  $liste_var='';
  if ($num_rows_le>0):
    // contenu de la recherche
    $liste_var.='<div>';
    while($dn=$resultat_le->fetch(PDO::FETCH_ASSOC)):        
      include("include/insert/ligneVariable.php");
      $liste_var.=$ligne;
    endwhile;
    $liste_var.='</div>';
    else: // aucun équipement
    $liste_var='<div class="nodata">Aucune variable ne correspond à ce critère</div>';
  endif;
  //$liste_var='<div class="nodata">Erreur dans le critère SQL</div>';

?>
<?
//************************************************************************************************************************
// Recherche et affichage du détail d'une rencontre : DD3.5
//************************************************************************************************************************
$onglets = '';
$contenu = '';
$sql = "SELECT *
        FROM dd_monstres
        LEFT JOIN dd_rencontres_monstres ON rem_mo_id = mo_id
        JOIN dd_rencontres ON rem_re_id = re_id
        WHERE re_id = :re_id";
$stmt = $db->prepare($sql);
$stmt->execute([':re_id' => $re]);
$monstres = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_rows = count($monstres);
if ($num_rows > 0):
  if ($num_rows > 1) $onglets = '<div class="menu_main contenu">'; // au moins deux monstres détectés, traitement de l'onglet
  foreach ($monstres as $dn):
    if ($num_rows > 1): // gestion d'un onglet
      $onglets .= '
      <div class="btMain" data-key="mo' . $dn['mo_id'] . '">
        <span class="titre_menu gras">' . f_nom($dn['mo_nom']) . '</span>
      </div>';
    endif; //'.$classe_monstre.'
    $contenu .= '     
    <div id="mo' . $dn['mo_id'] . '" class="monstre contenu"> 
      <div class="titreAction">
        <div class="titreMonstre"><a id="#m' . $dn['mo_id'] . '" name="#m' . $dn['mo_id'] . '">' . $dn['mo_nom'] . ' (' . $dn['rem_effectif'] . ')</a></div>
        <div>
          <span class="lien" onClick="supprimerMonstreRencontre(\'' . $dn['rem_id'] . '\')"><i class="icon fa fa-trash"></i></span>
          <a href="monstre-modifier.php?mo=' . $dn['mo_id'] . '&rencontre=' . $re . '"><i class="icon fa fa-pencil"></i></a>
        </div>
      </div>            
      <div>' . stripslashes($dn['mo_stats']) . '</div>
    </div>';
  endforeach;
  if ($onglets != '') $onglets .= '</div> <!-- menu_main --->';
  $renc = $onglets;
  $renc .= $contenu;
else:
  $renc = '<div class="nodata">Aucun monstre dans cette rencontre</div>';
endif;
?>

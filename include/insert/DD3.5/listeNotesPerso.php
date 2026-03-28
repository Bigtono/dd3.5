<?
  $filtreTypeSql = '';
  if (isset($_GET['type']) && ctype_digit((string)$_GET['type']) && (int)$_GET['type'] > 0):
    $filtreTypeSql = ' AND no_tyno_id="' . (int)$_GET['type'] . '"';
  endif;
  $requete_no='SELECT * FROM dd_notes JOIN dd_types_notes ON no_tyno_id=tyno_id LEFT JOIN dd_personnages_notes ON no_id=pno_no_id WHERE pno_pe_id="'.$p.'"' . $filtreTypeSql; 
  $result_no=queryPDO($requete_no);
  $num_rows_no=$result_no->rowCount();
  //echo '<div id="notes">';
  if ($num_rows_no > 0):
    if ($_SESSION['debug']==1) echo '<div>'.$debug.'</div>';
    echo $pagination;
    echo '<div class="item entete">';
    if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
    if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa fa-pencil"></i></div>';
    echo '  <div class="nom_note">Nom</div>';
    echo '  <div class="categorie_note">Type</div>';
    echo '  <div class="niveau_note">Niveau</div>';
    echo '</div>'; // entete
    while($dnno = $result_no->fetch(PDO::FETCH_ASSOC)):
      echo '<div class="item data">';
      // on vérifie les droits de lecture
      $accreditation = isset($dnno['pno_dd']) ? (int)$dnno['pno_dd'] : (isset($dnno['pno_niveau']) ? (int)$dnno['pno_niveau'] : 0);
      // Préparation du contenu
      $nom=stripslashes(ucfirst($dnno['no_nom']));
      if ($_SESSION['debug']==1 && $_SESSION['mj']==1) $idno=' ('.$dnno['no_id'].')';
      include('include/insert/'.$_SESSION['rulesetRep'].'/ligneNote.php');
      echo $ligne;
      echo '</div>'; // item data              
    endwhile;
    else:
    if(isset($_GET["type"])):
      echo '<div class="nodata">Aucune note dans la cat&eacute;gorie '.libelle("dd_types_notes","tyno","nom",$_GET["type"]).' !</div>';
      else:
      echo '<div class="nodata">Aucune note disponible !</div>';
    endif;
  endif;
  //echo '</div>'; // #notes          
?>

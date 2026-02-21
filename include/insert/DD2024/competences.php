<?
// Liste des Compétences - DD2024

echo '<div class="item entete">';
if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
if ($_SESSION['mj']==1) echo '	<div class="icone_modif"><i class="fa-solid fa-pen-to-square"></i></div>';  
echo '  <div class="nom_comp">Nom</div>';
echo '  <div class="carac_comp">Caractéristique</div>';
echo '</div>';    
  if ($_GET['msg'] && $_SESSION['debug']==1) echo '<div>'.$_GET['msg'].'</div>';
  $requete="SELECT * FROM dd_competences WHERE comp_ruleset_var_id='".$_SESSION['ruleset']."' ORDER BY comp_nom"; 
  $result=queryPDO($requete);
  $num_rows=$result->rowCount();
  if ($num_rows > 0):
    while($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $click='afficherComp('.$dn['comp_id'].')';
      echo '<div id="comp'.$dn['comp_id'].'" class="item data">';
      if ($_SESSION['mj']==1) echo '  <div class="icone_suppr"><span onClick="suppression(\'dd_competences\',\'comp\','.$don['do_id'].')"><i class="fa fa-trash"></i></span></div>';
      if ($_SESSION['mj']==1) echo '  <div class="icone_modif"><span onclick="modifierCompetence('.$dn['comp_id'].')"><i class="fa-solid fa-pen-to-square"></i></span></div>';        
      echo '  <div id="nomComp'.$dn['comp_id'].'" class="nom_comp" onClick="'.$click.'">'.$dn['comp_nom'].' </div>';
      echo '  <div id="caracComp'.$dn['comp_id'].'" class="carac_comp" onClick="'.$click.'">'.$dn['comp_caracteristique'].'</div>';
      echo '</div>';
    endwhile;
    else:
    echo '<div class="alerte">Aucun compétence dans la base de données</div>';
  endif;
?>
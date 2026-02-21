<?
  $listeJoueurs='';
  $requete="SELECT * FROM dd_joueurs ORDER BY j_pseudo";
  $result=queryPDO($requete);
  $num_rows=$result->rowCount();
  $nbl=0; // nb de sorts dans la ligne      
  if ($num_rows > 0):
    $listeJoueurs.='<div class="item entete">';
    $listeJoueurs.='  <div class="icone_suppr"><i class="fa fa-trash"></i></div>';
    $listeJoueurs.='	<div class="icone_modif"><i class="fa fa-pencil"></i></div>';
    $listeJoueurs.='	<div class="pseudo_joueur gras">Pseudo</div>';		
    $listeJoueurs.='	<div class="nom_joueur gras">Nom</div>';
    $listeJoueurs.='	<div class="prenom_joueur gras">'.utf8_encode("Prénom").'</div>';
    $listeJoueurs.='	<div class="email_joueur gras">Email</div>';
    $listeJoueurs.='</div>';       
    while($dn = $result->fetch(PDO::FETCH_ASSOC)):		  
      $id=$dn['r_id'];
      $listeJoueurs.='<div class="item data">';
      $listeJoueurs.='	<div class="icone_suppr"><i class="fa fa-trash" onClick="supprimerJoueur(\''.$dn['j_id'].'\')"></i></div>';
      $listeJoueurs.='	<div class="icone_modif"><a href="joueur-modifier.php?joueur='.$dn['j_id'].'&retour=joueurs&idretour='.$dn['j_id'].'"><i class="fa fa-pencil"></i></a></div>';		
      $listeJoueurs.='	<div class="pseudo_joueur"><a href="joueur.php?joueur='.$dn['j_id'].'&retour=joueurs&idretour='.$dn['j_id'].'">'.$dn['j_pseudo'].'</i></a></div>';		
      $listeJoueurs.='	<div class="nom_joueur">'.$dn['j_nom'].'</div>';
      $listeJoueurs.='	<div class="prenom_joueur">'.$dn['j_prenom'].'</div>';
      $listeJoueurs.='	<div class="email_joueur"><a href="mailto:'.$dn['j_email'].'"><i class="fa-solid fa-envelope"></i> '.$dn['j_email'].'</a></div>';
      $listeJoueurs.='</div>';
    endwhile;
    else:
    $listeJoueurs.='<div class="nodata">Aucun joueur !</div>';
  endif;
?>
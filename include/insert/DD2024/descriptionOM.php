<?
$description="Description manquante...";
if ($dn['om_res_id']!="") $source=libelle("dd_ressources","res","nom",$dn['om_res_id']);
if ($dn['om_fom_id']==1): // affichage des objets basiques(format = 1)*
  //objets reproduisant des effets de sorts, valable pour les anneaux, sceptres, baguettes...
  if (in_array($dn['om_com_id'], [4,14,15])): //if ($dn['om_com_id']==4 || $dn['om_com_id']==15):
    // recherche du sort incriminé
    $requete_so = 'SELECT * FROM dd_sorts WHERE so_id="'.$dn['om_so_id'].'"';	
    $result_so=queryPDO($requete_so);
    $dnso = $result_so->fetch(PDO::FETCH_ASSOC);
    // calcul du niveau
    $requete_clso = 'SELECT * FROM dd_sortclasse WHERE sc_so_id="'.$dn['om_so_id'].'"';	
    $result_clso=queryPDO($requete_clso);
    $num_rows_clso=$result_clso->rowCount();
    $mage='';$pretre=''; // niveau du sort respectivement pour un mage ou pour un prêtre
    if ($num_rows_clso>0):
      while($dnclso = $result_clso->fetch(PDO::FETCH_ASSOC)):
        if ($dnclso['sc_cla_id']==6) $mage=$dnclso['sc_niveau'];
        if ($dnclso['sc_cla_id']==9) $pretre=$dnclso['sc_niveau'];
      endwhile;
    endif;
    if ($mage!=''):
      $nlsMage=($mage*2)-1;
      if ($dn['om_so_niveau']>$nlsMage):
        $nlsMage=$dn['om_so_niveau'];
      endif;
      else:
      $nlsMage='';
    endif;
    if ($pretre!=''):
      $nlsPretre=($pretre*2)-1;
      if ($dn['om_so_niveau']>$nlsPretre):
        $nlsPretre=$dn['om_so_niveau'];
      endif;
      else: 
      $nlsPretre='';
    endif;
    if ($mage!=''):
      $niveau=$mage;
      elseif($pretre!=''):
      $niveau=$pretre;
      else:
      $niveau=0;
    endif;
    if ($nlsMage!=''):
      $nls=$nlsMage;
      elseif($nlsPretre!=''):
      $nls=$nlsPretre;
      else:
      $nls=0;
    endif;
    // préparation des données
    $college=libelle("dd_colleges","co","nom", $dnso['so_co_id']);
    // gestion des potions
    if ($dn['om_com_id']==15):
      $prix=50*$niveau*$nls;
      $cout=round($prix/2,0);
      $xp=round($prix/25,0);
      $description='<p>Cette potion ou huile reproduit les effets du sort <span class="lien" onClick="afficherSort(\''.$dnso['so_id'].'\')">'.$dnso['so_nom'].'</span>.</p><p>'.$college.' ; NLS '.$nls.' ; Préparation de potions, '.$dnso['so_nom'].' ; Prix '.$prix.' po ; Coût '.$cout.' po, '.$xp.' PX.</p>';
    endif;
    // gestion des baguettes
    if ($dn['om_com_id']==4):
      $prix=750*$niveau*$nls;
      $cout=round($prix/2,0);
      $xp=round($prix/25,0);
      $description='<p>Cette baguette possède 50 charges du sort <span class="lien" onClick="afficherSort(\''.$dnso['so_id'].'\')">'.$dnso['so_nom'].'</span>, lancé au niveau '.$nls.' de lanceur de sorts.</p><p>'.$college.' ; NLS '.$nls.' ; Création de baguettes magiques, '.$dnso['so_nom'].'; Prix '.$prix.' po ; Coût '.$cout.' po, '.$xp.' PX.</p>';
      $debug='NLS mage : '.$nlsMage.', NLS Prêtre : '.$nlsPretre.', Niveau Mage : '.$mage.', Niveau Prêtre : '.$pretre;
    endif;
    // gestion des parchemins
    if ($dn['om_com_id']==14):
      $prix=25*$niveau*$nls;
      $cout=round($prix/2,0);
      $xp=round($prix/25,0);
      $description='<p>Ce parchemin permet de lancer le sort <span class="lien" onClick="afficherSort(\''.$dnso['so_id'].'\')">'.$dnso['so_nom'].'</span> au niveau '.$nls.' de lanceur de sorts.</p><p>'.$college.' ; NLS '.$nls.' ; Écriture de parchemins, '.$dnso['so_nom'].'; Prix '.$prix.' po ; Coût '.$cout.' po, '.$xp.' PX.</p>';
      $debug='NLS mage : '.$nlsMage.', NLS Prêtre : '.$nlsPretre.', Niveau Mage : '.$mage.', Niveau Prêtre : '.$pretre;
    endif;

  endif;
  //*************************************************************************************
  // armes et armures
  if ($dn['om_com_id']==2 || $dn['om_com_id']==3):
    $nls=3*$dn['om_modificateur'];
    // gestion des armes
    if ($dn['om_com_id']==2):
      switch ($dn['om_modiicateur']):
        case 2:
          $modprix=8000;
          break;
        case 3:
          $modprix=18000;
          break;
        case 4:
          $modprix=32000;
          break;
        case 5:
          $modprix=50000;
          break;
        case 1:
        default:
          $modprix=2000;
      endswitch;
      $prix=$modprix;
      $cout=round($prix/2,0);
      $xp=round($prix/25,0);
      $description='<p>Cette arme possède un modificateur au toucher et aux dégâts de +'.$dn['om_modificateur'].'.</p><p>NLS '.$nls.' ; Création d\'armes et armures magiques ; Prix '.$prix.' po ; Coût '.$cout.' po, '.$xp.' PX.</p>';
    endif;
    // gestion des armures et boucliers
    if ($dn['om_com_id']==3):
      $prix=750*$niveau*$nls;
      $cout=round($prix/2,0);
      $xp=round($prix/25,0);
      $description='<p>Cette baguette possède 50 charges du sort '.$dnso['so_nom'].', lancé au niveau '.$nls.' de lanceur de sorts.</p><p>'.$dnso['so_college'].' ; NLS '.$nls.' ; Création de baguettes magiques, '.$dnso['so_nom'].' ; Prix '.$prix.' po ; Coût '.$cout.' po, '.$xp.' PX.</p>';
      $debug='NLS mage : '.$nlsMage.', NLS Prêtre : '.$nlsPretre.', Niveau Mage : '.$mage.', Niveau Prêtre : '.$pretre;
    endif;
  endif;
  else: // affichage spécifique
  $description=stripslashes($dn['om_description']);
endif;
?>
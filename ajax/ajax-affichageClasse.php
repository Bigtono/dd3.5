<?php
include_once("../include/dblib.inc.php");
error_reporting(E_ERROR & ~E_WARNING & ~E_NOTICE);

$c = $_POST['classe'];

if(!empty($c)):
  $requete = "SELECT * FROM dd_classes WHERE cla_id='".$c."'";
  $resultat = queryPDO($requete);
  $dn = $resultat->fetch(PDO::FETCH_ASSOC);
  $classe='
    <div id="classe" class="affichage">
      <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div></div>
      <div class="titreAction">
        <div class="titreA">'.stripslashes($dn['cla_nom']).'</div>
        <div></div>
      </div>     
      <div class="entete">
        <div class="gauche">
          <div class="ligne"><span class="label">D&eacute; de vie : </span>'.stripslashes($dn['cla_dV']).'</div>
          <div class="ligne"><span class="label">Niveau max : </span>'.stripslashes($dn['cla_niveauMax']).'</div>
          <div class="ligne"><span class="label">Points de comp&eacute;tences : </span>'.stripslashes($dn['cla_pointsCompetences']).'</div>
        </div>
        <div class="droite">
          <div class="ligne"><span class="label">Type de magie : </span>'.libelle("dd_typeMagie","mag", "nom",$dn['cla_mag_id']).'</div>
          <div class="ligne"><span class="label">Caract&eacute;ristique de Lanceur de sort : </span>'.libelle("dd_caracteristiques","car","nom",$dn['cla_car_id']).'</div>
          <div class="ligne"><span class="label">Alignement : </span>'.stripslashes($dn['cla_alignement']).'</div>
        </div>
      </div>';
  $competences='';
  $requete='SELECT comp_id, comp_nom FROM `dd_classe_competence` JOIN dd_classes ON cc_cla_id=cla_id JOIN dd_competences ON cc_comp_id=comp_id WHERE cc_cla_id="'.$c.'" ORDER BY cla_nom, comp_nom';
  $resultat_cc=queryPDO($requete);
  $num_rows_cc=$resultat_cc->rowCount();
  if ($num_rows_cc>0):
    while($dncc=$resultat_cc->fetch(PDO::FETCH_ASSOC)):
      if ($critere!=''):
        $competence=tag($critere,$dncc['comp_nom']);
        else:
        $competence=$dncc['comp_nom'];
      endif;  
      if ($competences!='') $competences.=', ';
      $competences.= '<span id="comp'.$dncc['comp_id'].'" class="lien" onClick="afficherComp('.$dncc['comp_id'].')">'.$competence.'</span>';
    endwhile;
    else:
    $competences="aucune";
  endif;
  $classe.='<div class="competences"><span class="label"> Comp&eacute;tences de classe : </span>'.$competences.'</div>';

  if ($dn['cla_clt_id']==2):
    $classe.='<div class="label mt10">Conditions (classe de prestige uniquement) :</div>';
    $classe.='<div class="conditions">'.stripslashes($dn['cla_conditions']).'</div>';
  endif;

  // tableau d'aptitudes
  $result='<table class="progression">';
  $result.='<caption>Table de progression <span onClick="switchCol()" class="switch"><i class="fa fa-repeat"></i></span></caption>';  
  // sorts - si la classe est LS profane ou divin, on génère l'entête spécifique des niveaux de sorts
  $sorts='';
  if (strlen($dn['cla_mag_id'])>0):
    for ($s=0; $s<10; $s++):
      $sorts.='<th class="sn">'.$s.'</th>';
    endfor;
    else:
    $requete='SELECT `cn_cla_id`, sum(`cn_niveauSortArcane`) as sp, sum(`cn_niveauSortDivin`) as sd, sum( `cn_niveauSortEffectif`) as sa FROM `dd_classe_niveau` WHERE cn_cla_id="'.$c.'" GROUP BY `cn_cla_id`';
    $resultat_cps=queryPDO($requete);
    $num_rows_cps=$resultat_cps->rowCount();
    if ($num_rows_cps>0):
      $dncps=$resultat_cps->fetch(PDO::FETCH_ASSOC);
      if ($dncps['sp']>0 || $dncps['sd']>0 || $dncps['sa']>0) $sorts='<th class="sorts">Sorts</th>';
    endif;
  endif;
  // entête du tableau
  $result.='<thead><tr><th colspan="6">&nbsp;</th><th colspan="10">Nombre de sorts par jour</th></tr>';
  $result.='<tr><th class="niveau">Niv.</th><th class="bba">BBA</th><th class="save">Ref.</th><th class="save">Vig.</th><th class="save">Vol.</th><th class="capacites">Capacit&eacute;s</th>'.$sorts.'</tr></thead>';
  // contenu du tableau
  for ($i=1; $i<$dn['cla_niveauMax']+1; $i++):
    // Capacités
    $requete='SELECT cap_id, cc_niveau, cap_nom, cc_precision, cap_description FROM dd_classe_capacite JOIN dd_classes ON cc_cla_id=cla_id JOIN dd_capacites_speciales ON cc_cap_id=cap_id WHERE cla_id='.$c.' AND cc_niveau='.$i.' ORDER BY cc_niveau';
    $resultat_cap=queryPDO($requete);
    $num_rows_cap=$resultat_cap->rowCount();
    $capacites='';
    if ($num_rows_cap>0):
      $j=0;
      while($dnc=$resultat_cap->fetch(PDO::FETCH_ASSOC)):
        if ($j>0) $capacites.=', ';
        $capacites.= '<span id="cap'.$dnc['cap_id'].'" class="lien" onClick="afficherCapacite('.$dnc['cap_id'].')">'.$dnc['cap_nom'];
        if (strlen($dnc['cc_precision'])>0) $capacites.=' ('.$dnc['cc_precision'].')';
        $capacites.= '</span>';
        $j++;
      endwhile;
    endif;
    // niveaux
    $requete='SELECT * FROM dd_classe_niveau WHERE cn_cla_id='.$c.' AND cn_niveau='.$i.' ORDER BY cn_niveau';
    $resultat_niv=queryPDO($requete);
    $num_rows_niv=$resultat_niv->rowCount();
    $sorts='';
    if ($num_rows_niv>0):
      while($dnn=$resultat_niv->fetch(PDO::FETCH_ASSOC)):
        if (strlen($dn['cla_mag_id'])>0):
          for ($s=0; $s<10; $s++):
            $sorts.='<td class="sn">'.$dnn['cn_sort_n'.$s].'</td>';
          endfor;
          else:
          if ($dnn['cn_niveauSortArcane']>0) $sorts='+1 niveau effectif de magie profane';
          if ($dnn['cn_niveauSortDivin']>0):
            if ($sorts!='') $sorts.='/';
            $sorts.='+1 niveau effectif de magie divine';
          endif;
          if ($dnn['cn_niveauSortEffectif']>0) $sorts='+1 niveau effectif';
          $sorts='<td class="sorts">'.$sorts.'</td>';
        endif;
        $bba=$dnn['cn_bba'];
        $reflexes=$dnn['cn_reflexes'];
        $vigueur=$dnn['cn_vigueur'];
        $volonte=$dnn['cn_volonte'];
      endwhile;
    endif;
    $result.='<tr><td>'.$i.'</td><td class="bba">'.$bba.'</td><td class="save">'.$reflexes.'</td><td class="save">'.$vigueur.'</td><td class="save">'.$volonte.'</td><td class="capacites">'.$capacites.'</td class="sorts">'.$sorts.'</tr>';
  endfor;
  $result.='</table>';

  $classe.=$result.'  
  <div class="label mt10">Description :</div> 
  <div class="texte description">'.stripslashes($texte).'</div>';
  $classe.='</div>';

  // On ajoute les données dans un tableau
  echo $dn['do_id']."@".$classe;
	else:
	echo "0@erreur";
endif;
?>
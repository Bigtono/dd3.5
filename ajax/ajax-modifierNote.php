<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");
include_once("../include/diverslib.inc.php");

$n = $_POST['note'];
$p = $_POST['perso'];

if(!empty($n)):
  if ($n!="n"):
    $requete = 'SELECT * FROM dd_notes JOIN dd_types_notes ON no_tyno_id=tyno_id WHERE no_id="'.$n.'"';
    $resultat = queryPDO($requete);
    $dn = $resultat->fetch(PDO::FETCH_ASSOC);
    $no_id=$dn['no_id'];
    $no_nom=$dn['no_nom'];  
    $no_categorie=$dn['no_tyno_id'];
    $no_cumulatif=$dn['no_cumulatif'];
    $no_texte_basique=$dn['no_texte_basique'];
    $no_texte_intermediaire=$dn['no_texte_intermediaire'];
    $no_texte_avance=$dn['no_texte_avance'];
    $no_texte_expert=$dn['no_texte_expert'];
  endif;
  // recherche de la diffusion de la note
  /*
  $requete_pno = 'SELECT * FROM dd_personnages_notes WHERE pno_no_id="'.$n.'"';
  $resultat_pno = queryPDO($requete_pno);
  $num_rows_pno=$resultat_pno->rowCount();
  $pno=array();
  $liste_pe='';
  if ($num_rows_pno>0):
    $i=0;
    while ($dnpno = $resultat_pno->fetch(PDO::FETCH_ASSOC)):
      $pno[$i]=$dnpno['pno_pe_id'];
      if ($liste_pe.='') $liste_pe.=', ';
      $liste_pe.=$dnpno['pno_pe_id'];
      $i++;
    endwhile;
  endif;
  */
  //********************************************************
  // mise en forme du contenu
  if ($n=='n'):
    $libelle='Ajouter';
    else:
    $libelle='Modifier';
  endif;
  // gestion des personnages
  $requete_pe = 'SELECT * FROM dd_personnages ORDER BY pe_nom';
  $resultat_pe = queryPDO($requete_pe);
  $num_rows_pe=$resultat_pe->rowCount();
  $perso='';
  if ($num_rows_pe>0):
    $i=0;
    while ($dnpe = $resultat_pe->fetch(PDO::FETCH_ASSOC)):
      // recherche de la diffusion de la note auprès du personnage
      $requete_pno = 'SELECT * FROM dd_personnages_notes WHERE pno_no_id="'.$n.'" AND pno_pe_id="'.$dnpe['pe_id'].'"';
      $resultat_pno = queryPDO($requete_pno);
      $num_rows_pno=$resultat_pno->rowCount();
      if ($num_rows_pno>0):
        $i++;
        $dnpno = $resultat_pno->fetch(PDO::FETCH_ASSOC);
        $valdif=$dnpno['pno_niveau'];
        else:
        $valdif=0;
      endif;
      //$perso.='<div class="mr20"><input type="checkbox" class="mr5" id="pe'.$dnpe['pe_id'].'" name="pe'.$dnpe['pe_id'].'" value="'.$dnpe['pe_id'].'"'.$checked.'><label for="personnages['.$dnpe['pe_id'].']">'.$dnpe['pe_nom'].'</label></div>';
      $perso.='<div class="mr20"><label for="personnages['.$dnpe['pe_id'].']" class="gras mr10">'.$dnpe['pe_nom'].'</label><select id="pe'.$dnpe['pe_id'].'" name="pe'.$dnpe['pe_id'].'" class="diffusion">'.optionListNiveauNote($valdif).' ('.$valdif.')</select></div>';
    endwhile;
    if ($_SESSION['mj']==1 && $_SESSION['debug']==1):
      $perso.=' ('.$i.')';
    endif;
  endif;
  // catégories
  $categorie='<select id="mp_no_tyno_id">'.optionList("dd_types_notes", "tyno","nom", $no_categorie).'</select>';
  $cumulatif='<select id="mp_no_cumulatif">'.optionListOuiNon($no_cumulatif).'</select>';
 
  //**********************************************************************
  // affichage du contenu
  $result='<div id="note" class="affichage">';
  $result.='  <input  type="hidden" id="mp_no_id" value="'.$n.'">';
  $result.='  <div><input id="mp_no_nom" class="input_nom" value="'.stripslashes($no_nom).'"> ('.$n.')</div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Catégorie</div>'.$categorie.'</div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Diffusion</div>'.$perso.'</div>';
  $result.='  <div class="ligne mt10"><div class="label w90">Cumulatif</div>'.$cumulatif.'</div>';
  $result.='  <div class="label">Basique</div><div><textarea id="mp_no_texte_basique" name="mp_no_texte_basique" class="input_texte">'.stripslashes($no_texte_basique).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_no_texte_basique');</script>";
  $result.='  <div class="label">Intermédiaire</div><div><textarea id="mp_no_texte_intermediaire" name="mp_no_texte_intermediaire" class="input_texte">'.stripslashes($no_texte_intermediaire).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_no_texte_intermediaire');</script>";
  $result.='  <div class="label">Avancé</div><div><textarea id="mp_no_texte_avance" name="mp_no_texte_avance" class="input_texte">'.stripslashes($no_texte_avance).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_no_texte_avance');</script>";
  $result.='  <div class="label">Expert</div><div><textarea id="mp_no_texte_expert" name="mp_no_texte_expert" class="input_texte">'.stripslashes($no_texte_expert).'</textarea></div>';
  $result.="  <script>CKEDITOR.replace('mp_no_texte_expert');</script>";
  $result.='  <input class="bouton" type="button" name="validModifNote" id="validModifNote" value="'.$libelle.'" onClick="validerModifNote('.$p.')">';
  $result.='  <input class="bouton ml15" type="button" name="annuleModifNote" id="annuleModifNote" value="Annuler" onClick="annulerPageModif()"></div>';
  $result.='</div>'; 
  // On ajoute les donnnées dans un tableau
  echo $dn['no_id']."@".$result;
	else:
	echo "0@Erreur";
endif;


?>
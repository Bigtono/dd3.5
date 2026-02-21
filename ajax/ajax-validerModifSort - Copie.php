<?php
include_once("../include/dblib.inc.php");


if(isset($_POST['mp_so_id'])):
  if ($_POST['mp_so_id']=="n"):
    // création d'un sort
    $requete = "INSERT INTO dd_sorts (
    so_nom,
    so_college,
    so_branche,
    so_portee,
    so_cible,
    so_duree_sort,
    so_duree_incantation,
    so_resistance,
    so_jet_sauvegarde,
    so_vocal,
    so_gestuel,
    so_materiel,
    so_focalisateur,
    so_focalisateur_divin,
    so_texte,
    so_resume,
    so_res_id,
    so_page)
    VALUES ('".
      addslashes($_POST['mp_so_nom'])."','".
      addslashes($_POST['mp_so_college'])."','".
      addslashes($_POST['mp_so_branche'])."','".
      addslashes($_POST['mp_so_portee'])."','".
      addslashes($_POST['mp_so_cible'])."','".
      addslashes($_POST['mp_so_duree_sort'])."','".
      addslashes($_POST['mp_so_duree_incantation'])."','".
      $_POST['mp_so_resistance']."','".
      addslashes($_POST['mp_so_jet_sauvegarde'])."','".
      $_POST['mp_so_vocal']."','".
      $_POST['mp_so_gestuel']."','".
      $_POST['mp_so_materiel']."','".
      $_POST['mp_so_focalisateur']."','".
      $_POST['mp_so_focalisateur_divin']."','".
      addslashes($_POST['mp_so_texte'])."','".
      addslashes($_POST['mp_so_resume'])."','".
      $_POST['mp_so_res_id']."','".
      $_POST['mp_so_page']."')";
    $resultat = execPDO($requete);
    $q=lastID("dd_sorts", "so");
    // ajout des données dans la table sortclasse
    $requete_ls='SELECT * FROM dd_classes WHERE cla_mag_id IS NOT NULL';
    $result_ls=queryPDO($requete_ls);
    $num_row_ls=$result_ls->rowCount();
    $msg_ls="";
    if ($num_row_ls>0):
      $msg_ls="* ";
      while($dnls=$result_ls->fetch(PDO::FETCH_ASSOC)):
        $niveau=$_POST['mp_ls'.$dnls['cla_id']];
        $msg_ls.=$dnls['cla_nom'].' => ';
        // l'enregistrement n'existe pas, on vérifie s'il faut le créer
        if ($niveau!=""):
          $requete_maj_ls='INSERT INTO dd_sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ("'.$q.'","'.$dnls['cla_id'].'", "'.$niveau.'")';
          $resultat_maj_ls = execPDO($requete_maj_ls);
          $msg_ls.='INSERT ('.$requete_maj_ls.'). ';
          else:
          $msg_ls.='Aucun niveau. ';
        endif;
      endwhile;
      else:
      $msg_ls.='Pas de classe de LS. ';
    endif;
    else:
    // MAJ du sort
    $requete = "UPDATE dd_sorts
      SET so_nom='".addslashes($_POST['mp_so_nom']).
      "', so_college='".addslashes($_POST['mp_so_college']).
      "', so_branche='".addslashes($_POST['mp_so_branche']).
      "', so_portee='".addslashes($_POST['mp_so_portee']).
      "', so_cible='".addslashes($_POST['mp_so_cible']).
      "', so_duree_sort='".addslashes($_POST['mp_so_duree_sort']).
      "', so_duree_incantation='".addslashes($_POST['mp_so_duree_incantation']).
      "', so_resistance='".$_POST['mp_so_resistance'].
      "', so_jet_sauvegarde='".addslashes($_POST['mp_so_jet_sauvegarde']).    
      "', so_vocal='".$_POST['mp_so_vocal'].
      "', so_gestuel='".$_POST['mp_so_gestuel'].
      "', so_materiel='".$_POST['mp_so_materiel'].
      "', so_focalisateur='".$_POST['mp_so_focalisateur'].
      "', so_focalisateur_divin='".$_POST['mp_so_focalisateur_divin'].
      "', so_texte='".addslashes($_POST['mp_so_texte']).  
      "', so_resume='".addslashes($_POST['mp_so_resume']).
      "', so_res_id='".$_POST['mp_so_res_id'].
      "', so_page='".$_POST['mp_so_page'].      
      "' WHERE so_id='".$_POST['mp_so_id']."'";
    $resultat = execPDO($requete);
    $q=$_POST['mp_so_id'];
    //***********************************************************************************
    // MAJ de la table sortclasse
    $requete_ls='SELECT * FROM dd_classes WHERE cla_mag_id IS NOT NULL';
    $result_ls=queryPDO($requete_ls);
    $num_row_ls=$result_ls->rowCount();
    $msg_ls="";
    if ($num_row_ls>0):
      $msg_ls="* ";
      while($dnls=$result_ls->fetch(PDO::FETCH_ASSOC)):
        $niveau=$_POST['mp_ls'.$dnls['cla_id']];
        $msg_ls.=$dnls['cla_nom'].' => ';
        // recherche d'un enregistrement existant
        $requete_sc='SELECT * FROM dd_sortclasse WHERE sc_so_id='.$_POST['mp_so_id'].' AND sc_cla_id='.$dnls['cla_id'];
        $result_sc=queryPDO($requete_sc);
        $num_rows_sc=$result_sc->rowCount();
        if ($num_rows_sc>0): 
          // l'enregistrement existe
          $dnsc=$result_sc->fetch(PDO::FETCH_ASSOC);
          // on vérifie s'il faut le supprimer (champ niveau vide)
          if ($niveau==""):
            // le niveau est vide, il faut supprimer l'enregistrement
            $requete_maj_ls='DELETE FROM dd_sortclasse WHERE sc_id="'.$dnsc['sc_id'].'"';
            $resultat_maj_ls = execPDO($requete_maj_ls);
            $msg_ls.='DELETE ('.$requete_maj_ls.'). ';
            else:
            // l'enregistrement est à garder, il est mis à jour            
            $requete_maj_ls='UPDATE dd_sortclasse SET sc_niveau="'.$niveau.'" WHERE sc_id="'.$dnsc['sc_id'].'"';
            $resultat_maj_ls = execPDO($requete_maj_ls);
            $msg_ls.='UPDATE ('.$requete_maj_ls.'). ';
          endif;
          else:
          // l'enregistrement n'existe pas
          // on vérifie s'il faut le créer
          if ($niveau!=""):
            $requete_maj_ls='INSERT INTO dd_sortclasse (sc_so_id, sc_cla_id, sc_niveau) VALUES ("'.$_POST['mp_so_id'].'","'.$dnls['cla_id'].'", "'.$niveau.'")';
            $resultat_maj_ls = execPDO($requete_maj_ls);
            $msg_ls.='INSERT ('.$requete_maj_ls.'). ';
            else:
            $msg_ls.='Aucun niveau. ';
          endif;
        endif;
      endwhile;
      else:
      $msg_ls.='Pas de classe de LS. ';
    endif;
  endif;
  // recherche des domaines
  $requete='SELECT * from dd_sortdomaine WHERE sd_so_id="'.$_POST['mp_so_id'].'"';
  $result_do=queryPDO($requete);
  $num_rows_do=$result_do->rowCount();
  $domaines='';
  if ($num_rows_do>0):
    while($dnd=$result_do->fetch(PDO::FETCH_ASSOC)):
      if (strlen($domaines)>0) $domaines.=', ';
      $domaines.=libelle("dd_domaines","do","nom", $dnd['sd_do_id']);
    endwhile;
  endif;
  // On ajoute les donnnées dans un tableau
  echo $q."@".$requete."@".$_POST['mp_so_nom']."@".$_POST['mp_so_college']."@".$domaines."@".$_POST['mp_so_resume']."@".$msg_ls;
	else:
	echo "prout@".$_POST['mp_so_id'];
endif;
?>
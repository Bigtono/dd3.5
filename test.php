<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

?>
<!doctype html>

<HEAD>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-sorts.js'></script>
  <script type='text/javascript' src='js/moncode-dons.js'></script>
  <script type='text/javascript' src='js/moncode-competences.js'></script>
</HEAD>

<body>
  <? include("include/affichageSelectionSources.php"); ?>
  <?php
  $id = 61;
  $sql = 'SELECT re_nom, re_code, scc_abreviation FROM dd_rencontres JOIN dd_scenarios_chapitres ON re_scc_id=scc_id WHERE re_id= :id';
  $stmt = $db->prepare($sql);
  $stmt->execute([':id' => $id]);
  $rencontre = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($rencontre):
    if ($rencontre['scc_abreviation'] != "" && $rencontre['re_code'] != ""):
      $text = stripslashes($rencontre['scc_abreviation']) . stripslashes($rencontre['re_code']) . " : ";
    else:
      $text = "";
    endif;
    $text .= stripslashes($rencontre['re_nom']);
  else:
    $text = "erreur : " . $sql;
  endif;
  echo $text;
  /*
  $var="### ";
  $ligne='### D2 :Essai de chapitre';
  echo '<h1>'.substr($ligne,strlen($var)).'</h1>';
  $chapitre=explode(":",trim(substr($ligne,4)));
  $chapitre=explode(":",trim(substr($ligne,4)));
  echo '<span class="fondrouge">'.trim($chapitre[0]).'</span>';
  echo '<span class="fondrouge">'.trim($chapitre[1]).'</span>';
  
  // recherche des r�f�rences de livres s�lectionn�es
$requete='SELECT res_id, res_nom, res_editeur, res_selection FROM dd_ressources WHERE res_selection="1" ORDER BY res_nom';
$result=queryPDO($requete);
$num_rows=$result->rowCount();
$num_rows=$result->rowCount();
$selection='';
if ($num_rows > 0):
  //while($sort = mysql_fetch_array ($result)):
  while($dn2 = $result->fetch(PDO::FETCH_ASSOC)):
    if ($selection!='') $selection.=',';
    $selection.=$dn2['res_id'];
  endwhile;
endif;
echo '<div>'.$selection.'</div>';  
$requete='SELECT * FROM dd_ressources WHERE res_id IN ('.$selection.') ORDER BY res_nom';
$result=queryPDO($requete);
$num_rows=$result->rowCount();
//$liste='<option value="'.$tout.'">'.$tout.'</option>';
if ($num_rows > 0):
	while($dn = $result->fetch(PDO::FETCH_ASSOC)):
		$liste.='<option value="'.$dn['res_id'].'"';
    if ($id == $dn['res_id']) $liste.=' selected="SELECTED"';
    $liste.='>'.$dn['res_nom'].'</option>';	
	endwhile;
endif;

echo '<div>'.$requete.'</div>';
  
echo '<div>'.$liste.'</div>';
  
  /*
  $_POST['diffusion']="pe1pe5pe2";
  $dif=explode("pe",$_POST['diffusion']);
  $_POST['mp_pno_id']=2;
	foreach($dif as $key=>$value):
    $requete='INSERT INTO dd_personnages_notes (pno_no_id, pno_pe_id) VALUES ('.$_POST['mp_pno_id'].','.$value.')';
    if ($value && $value!='') echo '<div>'.$requete.'</div>';
	endforeach;
  $debug=0;
  
  $filename="monstres.txt";
  $fp = fopen( $filename, "r") or die("Imposible d'ouvrir $filename");
  $sortie='';
  $pouvoirs=0;
  $debut=0;
  $monstre="";
  $enregistrement=0;
  $i=1;
  while(!feof($fp)):
  
    $add=1; // par d�faut, on ajoute la ligne au texte � l'�tape de compilation � la fin du script
  
    $ligne=fgets($fp);

    //*******************************************************************************************************************
    // Chapitre
    $var="### ";
    if (substr($ligne,0,strlen($var))==$var):
      $ligne='<h1>'.substr($ligne,strlen($var)).'</h1>';
      $chapitre=substr($ligne,0,2);
    endif;
    // titre
    $var="## ";
    if (substr($ligne,0,strlen($var))==$var):
      $debut=1;
      $titre=substr($ligne,strlen($var));
      $ligne='<h2>'.substr($ligne,strlen($var)).'</h2>';
      $perso=substr($ligne,strlen($var));
    endif;
  
    //*******************************************************************************************************************  
    // d�but des pouvoirs sp�ciaux
    $var=utf8_encode("...");
    if (substr($ligne,0,strlen($var))==$var):
      $pouvoirs=1;
      $add=0;
    endif;
    if ($pouvoirs==1):
      $pos=strpos($ligne,".");
      if ($pos>0):
        $ligne='<span class="gras">'.substr($ligne,0,$pos).'. </span>'.substr($ligne,$pos+1); 
      endif;
    endif;
    // fin des pouvoirs sp�ciaux et du bloc de stats du monstre/pnj
    $var=utf8_encode("$$");
    if (substr($ligne,0,strlen($var))==$var):
      $debut=2;
      $pouvoirs=0;
      $add=0;
    endif;
  
    //*******************************************************************************************************************
    // sorts
    $var=utf8_encode("Sorts de");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$ligne.' </span>';
    $criteres= array('9�me', '8�me', '7�me', '6�me', '5�me', '4�me', '3�me', '2�me', '1er', '0 - ');
    include('include/insert/'.$_SESSION['rulesetRep'].'/trt_sort.php');
  
    $avant=array('Attaque de base +');
    $apres=array('<span class="gras">Attaque de base</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array('Lutte +');
    $apres=array('<span class="gras">Lutte</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array('Init +');
    $apres=array('<span class="gras">Init</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;

    $avant=array('Vig +');
    $apres=array('<span class="gras">Vig</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;

    $avant=array('R�f +');
    $apres=array('<span class="gras">R�f</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;

    $avant=array('Vol +');
    $apres=array('<span class="gras">Lutte</span> +');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array('; Sens ');
    $apres=array('; <span class="gras">Sens</span> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array('CA ');
    $apres=array('<span class="gras">CA</span> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array(', toucher ');
    $apres=array(', <span class="gras">toucher</span> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
  
    $avant=array(', pris au d�pourvu ');
    $apres=array(', <span class="gras">, pris au d�pourvu </span> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    
    $var=utf8_encode("M�l�e ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Distance ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Vuln�rabilit� ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("RM ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Immunit� ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Langues ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Vitesse ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Espace ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Possessions ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Caract�ristiques");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Options d'attaque");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Carac.");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">Caract&eacute;ristiques </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Actions sp�ciales");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("�quipement de combat");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));
    $var=utf8_encode("Options de combat");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$var.' </span>'.substr($ligne,strlen($var));

  
    //*******************************************************************************************************************
    // Capacit�s de type sort
    $var=utf8_encode("Capacit�s de type sort ");
    if (substr($ligne,0,strlen($var))==$var) $ligne='<span class="gras">'.$ligne.' </span>';
    $criteres= array('A volont�', '1/jour', '2/jour', '3/jour', '1/10 minutes', '1/round', '1/2 rounds', '1/3 rounds');
    include('include/insert/'.$_SESSION['rulesetRep'].'/trt_sort.php');

  
    //*******************************************************************************************************************
    // Particularit�s
    $var=utf8_encode("Particularit�s");
    if (substr($ligne,0,strlen($var))==$var):
      $ligne='<span class="gras">'.$var.'. </span>'.strtolower(substr($ligne,strlen($var)));
      $requete="SELECT * FROM dd_dons";
      $result = queryPDO($requete);
      $num_rows = $result->rowCount();
      if ($num_rows>0):
        while($dn = $result->fetch(PDO::FETCH_ASSOC)):
          $avant=array(htmlentities(strtolower($dn['do_nom'])));
          $apres=array('<span onclick="afficherDon('.$dn['do_id'].')" class="lien text-red">'.$dn['do_nom'].'</span>');
          $substitution=str_replace($avant, $apres, $ligne);
          $ligne=$substitution;
        endwhile;
      endif;
    endif;
  
    //*******************************************************************************************************************
    // Dons
    $var="Dons";
    if (substr($ligne,0,strlen($var))==$var):
      if ($debug==1) echo '<div class="fondbeige"><b>Ligne :</b> '.$ligne.'</div>';  
      $texte=$ligne;
      $texte2=substr($ligne,strlen($var));
      if ($debug==1) echo '<div class="fondbeige"><b>texte2 :</b> '.$texte2.'</div>';
      // recherche des extensions et mentions de noms de domaine
      $avant=array('*');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('FRCS');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('CAr,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="CAr") $texte2=substr($texte2,0,-3);
      $avant=array('MH,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="SC") $texte2=substr($texte2,0,-2);
      $avant=array('SC');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="SC") $texte2=substr($texte2,0,-2);
      $avant=array('D,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('D ');
      $apres=array(' ');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="D") $texte2=substr($texte2,0,-1);
      $avant=array('B,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('B ');
      $apres=array(' ');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('*');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);  
      // recherche et suppression des textes entre parenth�ses
      $p1=strpos($texte2,"(");    
      while($p1>0):
        $p2=strpos($texte2,")");
        if ($p2>0):
          $texte2=substr($texte2,0, $p1-1).substr($texte2,$p2+1); 
          else:
          $texte2=substr($texte2,0, $p1-1);
        endif;
        $p1=strpos($texte2,"(");  
      endwhile;
      if ($debug==1) echo '<div class="fondbeige"><b>Ligne nettoy&eacute;e:</b> '.$texte2.'</div>';
      // extraction des noms de don de la ligne
      $dons=explode(",",$texte2);
      foreach($dons as $key=>$value):
        $value=trim($value);
        $tempo=htmlentities($value);
        if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
          $value=substr(trim($value),0,-1);
          if ($debug==1) echo '<div class="ml20"><b>Nettoyage obele et double obele:</b></div>';
        endif;
        if (substr(trim($value),-1)=="B"):
          $value=substr($value,0,-1);
          if ($debug==1) echo '<div class="ml20"><b>Nettoyage :</b> '.substr($value,-1).'</div>';
        endif;  
        if ($debug==1) echo '<div class="ml20"><b>Don :</b> '.$value.'</div>';
        $requete='SELECT * FROM dd_dons WHERE do_res_id IN '.$selection.' AND lower(do_nom)="'.trim(strtolower(stripslashes($value))).'"';
        if ($debug==1) echo '<div class="ml20">'.$requete.'</div>';
        $result = queryPDO($requete);
        $num_rows = $result->rowCount();
        if ($num_rows>0):
          if ($debug==1) echo '<div class="ml20"><b>Don dans la base</b> '.$value.'</div>';
          $dn = $result->fetch(PDO::FETCH_ASSOC);
          $critere=strtolower($dn['do_nom']);
          $pos=strpos(strtolower($texte),$critere);
          if ($debug==1) echo '<div class="ml20"><b>Crit&egrave;re :</b> '.$critere.' (Pos : '.$pos.')</div>';
          if ($pos>0):
            $lg=strlen($dn['do_nom']);
            $remplacement='<span onClick="afficherDon('.$dn['do_id'].')" class="lien text-red">'.$dn['do_nom'].'</span>';
            $lg2=strlen($remplacement);
            if ($debug==1) echo '<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
            $pos2=$pos+$lg;
            $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
            $texte=$substitution;
            else:
            if ($debug==1) echo '<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';
            
          endif;     
          else:
          echo '<div class="ml20">Don "'.$value.'" introuvable dans la base : ligne '.$i.'</div>';
          writeLog("debug_dons.txt", trim($value));
        endif;
      endforeach; 
      $ligne=$texte;
      $ligne='<span class="gras">'.$var.' </span>'.strtolower(substr($ligne,strlen($var)));
    endif;

    //*******************************************************************************************************************
    // Comp�tences  
    $var=utf8_encode("Comp�tences");
    if (substr($ligne,0,strlen($var))==$var):
      if ($debug==3) echo '<div class="fondbeige"><b>Ligne :</b> '.$ligne.'</div>';  
      $texte=$ligne;
      $texte2=substr($ligne,strlen($var));
      if ($debug==3) echo '<div class="fondbeige"><b>texte2 :</b> '.$texte2.'</div>';
      // recherche des extensions et mentions de noms de domaine
      $avant=array('*');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('FRCS');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('CAr,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="CAr") $texte2=substr($texte2,0,-3);
      $avant=array('MH,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="SC") $texte2=substr($texte2,0,-2);
      $avant=array('SC');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="SC") $texte2=substr($texte2,0,-2);
      $avant=array('D,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('D ');
      $apres=array(' ');
      $texte2=str_replace($avant, $apres, $texte2);
      if (substr($texte2,-1)=="D") $texte2=substr($texte2,0,-1);
      $avant=array('B,');
      $apres=array(',');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('B ');
      $apres=array(' ');
      $texte2=str_replace($avant, $apres, $texte2);
      $avant=array('*');
      $apres=array('');
      $texte2=str_replace($avant, $apres, $texte2);  
      // recherche et suppression des textes entre parenth�ses
      $p1=strpos($texte2,"(");    
      while($p1>0):
        $p2=strpos($texte2,")");
        if ($p2>0):
          $texte2=substr($texte2,0, $p1-1).substr($texte2,$p2+1); 
          else:
          $texte2=substr($texte2,0, $p1-1);
        endif;
        $p1=strpos($texte2,"(");  
      endwhile;
      if ($debug==3) echo '<div class="fondbeige"><b>Ligne nettoy&eacute;e:</b> '.$texte2.'</div>';
      // extraction des noms de don de la ligne
      $comp=explode(",",$texte2);
      foreach($comp as $key=>$value):
        $pos=strpos($value,"+");
        if ($pos==0) $pos=strpos($value,"-");
        if ($pos>0) $value=substr($value,0,$pos-1);
        $value=trim($value);
  
        $tempo=htmlentities($value);
        if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
          $value=substr(trim($value),0,-1);
          if ($debug==3) echo '<div class="ml20"><b>Nettoyage obele et double obele:</b></div>';
        endif;
        if (substr(trim($value),-1)=="B"):
          $value=substr($value,0,-1);
          if ($debug==3) echo '<div class="ml20"><b>Nettoyage :</b> '.substr($value,-1).'</div>';
        endif;  
        if ($debug==3) echo '<div class="ml20"><b>Comp&eacute;tence :</b> '.$value.'</div>';
        $requete='SELECT * FROM dd_competences WHERE lower(comp_nom)="'.trim(strtolower(stripslashes($value))).'"';
        if ($debug==3) echo '<div class="ml20">'.$requete.'</div>';
        $result = queryPDO($requete);
        $num_rows = $result->rowCount();
        if ($num_rows>0):
          if ($debug==3) echo '<div class="ml20"><b>Comp&eacute;tence dans la base</b> : '.$value.'</div>';
          $dn = $result->fetch(PDO::FETCH_ASSOC);
          $critere=strtolower($dn['comp_nom']);
          $pos=strpos(strtolower($texte),$critere);
          if ($debug==3) echo '<div class="ml20"><b>Crit&egrave;re :</b> '.$critere.' (Pos : '.$pos.')</div>';
          if ($pos>0):
            $lg=strlen($dn['comp_nom']);
            $remplacement='<span onClick="afficherComp('.$dn['comp_id'].')" class="lien text-red">'.$dn['comp_nom'].'</span>';
            $lg2=strlen($remplacement);
            if ($debug==3) echo '<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
            $pos2=$pos+$lg;
            $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
            $texte=$substitution;
            else:
            if ($debug==3) echo '<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';
            
          endif;     
          else:
          echo '<div class="ml20">Comp&eacute;tence "'.$value.'" introuvable dans la base : ligne '.$i.'</div>';
          //writeLog("debug_dons.txt", trim($value));
        endif;
      endforeach; 
      $ligne=$texte;
      $ligne='<span class="gras">'.$var.' </span>'.strtolower(substr($ligne,strlen($var)));
    endif;
  
    $avant=array('D,FRCS ');
    $apres=array('<sup>D,FRCS</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('D,FRCS,');
    $apres=array('<sup>FRCS</sup>, ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('FRCS ');
    $apres=array('<sup>FRCS</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('FRCS,');
    $apres=array('<sup>FRCS</sup>,');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('SC, ');
    $apres=array('<sup>SC</sup>, ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('SC ');
    $apres=array('<sup>SC</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $avant=array('MH');
    $apres=array('<sup>MH</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);  
    $ligne=$substitution;
    $avant=array('CAr');
    $apres=array('<sup>CAr</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);  
    $ligne=$substitution;
    $avant=array('*');
    $apres=array('<sup>*</sup> ');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;  
  
    // cas particulier des ob�les
    $tempo=htmlentities($ligne);
    $avant=array('&dagger;');
    $apres=array('<sup>&dagger;</sup>');
    $substitution=str_replace($avant, $apres, $tempo);
    $ligne=html_entity_decode($substitution); 
    $tempo=htmlentities($ligne);
    $avant=array('&Dagger;');
    $apres=array('<sup>&Dagger;</sup>');
    $substitution=str_replace($avant, $apres, $tempo);
    $ligne=html_entity_decode($substitution); 
  
    $avant=array('D,');
    $apres=array('<sup>D</sup>,');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    $avant=array('D (');
    $apres=array('<sup>D</sup> (');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;  
    $var="D";
    if (substr($ligne,-1)==$var) $ligne=substr($ligne,0,-1).'<sup>'.$var.'</sup>';
    $avant=array('* voir page');
    $apres=array('<sup>*</sup> voir page');
    $substitution=str_replace($avant, $apres, $ligne);
    $ligne=$substitution;
    
    // compilation du r�sultat
    if ($debut==1) $monstre.='<div>'.$ligne.'</div>';
    if ($debut==2 && $enregistrement==1):
      $requete='INSERT INTO dd_monstres (mo_nom, mo_stats, mo_chapitre) VALUES ("'.addslashes($titre).'","'.addslashes($monstre).'","'.addslashes($chapitre).'")';
      $resultat=execPDO($requete);
      if ($debug==4) echo '<div class="ml20">Insertion du bloc de stats "'.$titre.'" ('.$i.') dans la base</div>';
      $monstre="";
      $debut=0;
    endif;
    if ($add==1) $sortie.='<div>'.$ligne.'</div>';
    
    $i++;
  endwhile;
  
  fclose($fp);
  
  echo '<div class="monstres ml30">'.$sortie.'</div>';
*/
  ?>

</body>
<div id="detail-pp"></div>
<div id="modification"></div>

</html>
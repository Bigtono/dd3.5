<?
//****************************************************************
// traitement de la description
//****************************************************************
$info_trt='';


//*******************************************************************************************************************  
// Description
// cette séquence doit être l'avant dernière du bloc de description du monstre
$var="***";
if (substr($ligne,0,strlen($var))==$var):
  $info_trt.='<div class="ml20">Description détectée à la ligne '.$i.'</div>';
  $ligne='<hr>';
endif;

//*******************************************************************************************************************  
// Pouvoirs spéciaux
// cette séquence doit être la dernière du bloc de description du monstre
$var="...";
if (substr($ligne,0,strlen($var))==$var):
  $info_trt.='<div class="ml20">Pouvoirs spéciaux détectés à la ligne '.$i.'</div>';
  $pouvoirs=1;
  $ligne='<hr>';
endif;
if ($pouvoirs==1):
  $pos=strpos($ligne,".");
  if ($pos>0):
    $ligne='<strong>'.substr($ligne,0,$pos).'. </strong>'.substr($ligne,$pos+1); 
  endif;
endif;

//*******************************************************************************************************************
// sorts
$var="Sorts de ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$ligne.' </strong>';
$criteres= array('9ème', '8ème', '7ème', '6ème', '5ème', '4ème', '3ème', '2ème', '2ème', '9e', '8e', '7e', '6e', '5e', '4e', '3e', '2e', '1er', '0 ');
include('trt_sort.php');

//*******************************************************************************************************************
// Capacités de type sort
$var="Capacités de type sort ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$ligne.' </strong>';
$criteres= array('A volonté', 'À volonté', '1/jour', '2/jour', '3/jour', '1/10 minutes', '1/round', '1/2 rounds', '1/3 rounds');
include('trt_sort.php');

//*******************************************************************************************************************
// Livre de sorts
$var="Livre de sorts";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$ligne.' </strong>';
$criteres= array('9ème', '8ème', '7ème', '6ème', '5ème', '4ème', '3ème', '2ème', '2ème', '9e', '8e', '7e', '6e', '5e', '4e', '3e', '2e', '1er', '0 ');
include('trt_sort.php');

//*******************************************************************************************************************
// recherche de segments spécifiques
$avant=array('Attaque de base +');
$apres=array('<strong>Attaque de base</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('Attaques de base +');
$apres=array('<strong>Attaques de base</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;    

$avant=array('Lutte +');
$apres=array('<strong>Lutte</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('Init ');
$apres=array('<strong>Init</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('Vig +');
$apres=array('<strong>Vig</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('Vig+');
$apres=array('<strong>Vig</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('Réf +');
$apres=array('<strong>Réf</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('Ref +');
$apres=array('<strong>Réf</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('Réf+');
$apres=array('<strong>Réf</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('Ref+');
$apres=array('<strong>Réf</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('Vol +');
$apres=array('<strong>Vol</strong> +');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('; Sens ');
$apres=array('; <strong>Sens</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('CA ');
$apres=array('<strong>CA</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('PV ');
$apres=array('<strong>PV</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;      

$avant=array('RD ');
$apres=array('<strong>RD</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array(', contact ');
$apres=array(', <strong>contact</strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array(', pris au dépourvu ');
$apres=array(', <strong>pris au dépourvu </strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$avant=array('; Allonge ');
$apres=array('; <strong>Allonge </strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('; Portée ');
$apres=array('; <strong>Allonge </strong> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;  

$avant=array('CL ');
$apres=array('NLS ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;

$var="Mêlée ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Grimoire ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Aura ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Immunité ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));          
$var="Immunités ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Immunisé ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));                
$var="Chance d'échapper à une attaque ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));                
$var="Distance ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="À distance ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>Distance </strong>'.substr($ligne,strlen($var));  
$var="Vulnérabilité ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Vulnérabilités ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Faiblesse ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Faiblesses ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="SR ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>RM </strong>'.substr($ligne,strlen($var));
$var="RM ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="RD ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Langues ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Vitesse ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Espace ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Possessions ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Caractéristiques";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Carac.";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>Caract&eacute;ristiques </strong>'.substr($ligne,strlen($var));
$var="Options d'attaque";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Actions spéciales";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Équipement de combat";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="Options de combat";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var="PV ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
$var=" Réf ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));
$var="Capacités magiques ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));
$var="Résistances ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Résistance ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> Résistances </strong>'.substr($ligne,strlen($var));
$var="Livre de sorts ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
// format de monstre classique
$var="Espace occupé/allonge ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Jets de sauvegarde ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Particularités ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Attaque de base/lutte ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Attaque ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Attaque à outrance  ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Dés de vie ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Initiative ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Vitesse de déplacement ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
$var="Classe d’armure  ";
if (substr($ligne,0,strlen($var))==$var) $ligne='<strong> '.$var.' </strong>'.substr($ligne,strlen($var));      
    
// correction des erreurs de traductions spécifiques à des traitements ultérieurs (on n'ajoute pas le style gras)
$var="Talents ";
if (substr($ligne,0,strlen($var))==$var) $ligne='Dons '.substr($ligne,strlen($var));
$var="SQ ";
if (substr($ligne,0,strlen($var))==$var) $ligne='Particularités '.substr($ligne,strlen($var));


//*******************************************************************************************************************
// Particularités
//$var=utf8_encode("Particularités ");
$var="Particularités ";
if (substr($ligne,0,strlen($var))==$var):
  $ligne='<strong>'.$var.'. </strong>'.strtolower(substr($ligne,strlen($var)));
  $requete="SELECT * FROM dd_dons";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows>0):
    while($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $avant=array(htmlentities(strtolower($dn['do_nom'])));
      $apres=array('<span onclick="afficherDon('.$dn['do_id'].')" class="lien">'.$dn['do_nom'].'</span>');
      $substitution=str_replace($avant, $apres, $ligne);
      $ligne=$substitution;
    endwhile;
  endif;
endif;

$var="Options d'attaque ";
if (substr($ligne,0,strlen($var))==$var):
  $ligne='<strong>'.$var.'. </strong>'.strtolower(substr($ligne,strlen($var)));
  $requete="SELECT * FROM dd_dons";
  $result = queryPDO($requete);
  $num_rows = $result->rowCount();
  if ($num_rows>0):
    while($dn = $result->fetch(PDO::FETCH_ASSOC)):
      $avant=array(htmlentities(strtolower($dn['do_nom'])));
      $apres=array('<span onclick="afficherDon('.$dn['do_id'].')" class="lien">'.$dn['do_nom'].'</span>');
      $substitution=str_replace($avant, $apres, $ligne);
      $ligne=$substitution;
    endwhile;
  endif;
endif;


//*******************************************************************************************************************
// Dons
if (substr($ligne,0,strlen($var))=="Dons "):
  if ($debug==2) $info_trt.='<div class="fondbeige"><b>'.$monstre_nom.'</b><br><b>Ligne :</b> '.$ligne.'</div>';  
  $texte=$ligne;
  $texte2=substr($ligne,strlen($var));
  if ($debug==2) $info_trt.='<div class="fondbeige"><b>texte2 :</b> '.$texte2.'</div>';
  // Nettoyage de la ligne
  include('include/insert/trt-extensions.php');
  // recherche et suppression des textes entre parenthèses
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
  if ($debug==2) $info_trt.='<div class="fondbeige"><b>Ligne nettoy&eacute;e:</b> '.$texte2.'</div>';
  // extraction des noms de don de la ligne
  $dons=explode(",",$texte2);
  foreach($dons as $key=>$value):
    $value=trim($value);
    $tempo=htmlentities($value);
    if ($debug==2) $info_trt.='<div class="ml20"><b>value :</b>'.$value.' / <b>tempo :</b>'.$tempo.'</div>';
    if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
      $value=substr(trim($value),0,-1);
      if ($debug==2) $info_trt.='<div class="ml20"><b>Nettoyage obele et double obele:</b></div>';
    endif;
    if (substr(trim($value),-1)=="B"):
      $value=substr($value,0,-1);
      if ($debug==2) $info_trt.='<div class="ml20"><b>Nettoyage :</b> '.substr($value,-1).'</div>';
    endif;  
    if ($debug==2) $info_trt.='<div class="ml20"><b>Don :</b> '.$value.'</div>';
    $requete='SELECT * FROM dd_dons WHERE do_res_id IN '.$selection.' AND lower(do_nom)="'.trim(strtolower(stripslashes($value))).'"';
    if ($debug==2) $info_trt.='<div class="ml20">'.$requete.'</div>';
    $result = queryPDO($requete);
    $num_rows = $result->rowCount();
    if ($num_rows>0):
      $dn = $result->fetch(PDO::FETCH_ASSOC);
      if ($debug==2) $info_trt.='<div class="ml20"><b>Don dans la base</b> '.$dn['do_nom'].'</div>';
      $critere=strtolower($dn['do_nom']);
      $pos=strpos(strtolower($texte),$critere);
      if ($debug==2) $info_trt.='<div class="ml20"><b>Crit&egrave;re :</b> '.$critere.' (Pos : '.$pos.')</div>';
      if ($pos>0):
        $lg=strlen($dn['do_nom']);
        $remplacement='<span onClick="afficherDon('.$dn['do_id'].')" class="lien">'.$dn['do_nom'].'</span>';
        $lg2=strlen($remplacement);
        if ($debug==2) $info_trt.='<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
        $pos2=$pos+$lg;
        $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
        $texte=$substitution;
        else:
        if ($debug==2) $info_trt.='<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';

      endif;     
      else:
      $info_trt.='<div class="ml20">Don "'.$value.'" introuvable dans la base : ligne '.$i.'</div>';
      writeLog("debug_dons.txt", trim($value));
    endif;
  endforeach; 
  $ligne=$texte;
  $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
endif;

//*******************************************************************************************************************
// traitement des sources
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
$avant=array('B, ');
$apres=array('<sup>B</sup>, ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('B ');
$apres=array('<sup>B</sup> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('PG ');
$apres=array('<sup>PG</sup> ');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution; 
$avant=array('D,');
$apres=array('<sup>D</sup>,');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;
$avant=array('D (');
$apres=array('<sup>D</sup> (');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;  
// cas particulier des obèles
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

$avant=array('* voir page');
$apres=array('<sup>*</sup> voir page');
$substitution=str_replace($avant, $apres, $ligne);
$ligne=$substitution;      

//*******************************************************************************************************************
// Compétences  
//$var=utf8_encode("Compétences ");
$var="Compétences ";
if (substr($ligne,0,strlen($var))==$var):
  if ($debug==3) $info_trt.='<div class="fondbeige"><b>Ligne :</b> '.$ligne.'</div>';  
  $texte=$ligne;
  $texte2=substr($ligne,strlen($var));
  if ($debug==3) $info_trt.='<div class="fondbeige"><b>texte2 :</b> '.$texte2.'</div>';
  // recherche des extensions et mentions de noms de domaine
  include('include/insert/trt-extensions.php');
  // recherche et suppression des textes entre parenthèses
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
  if ($debug==3) $info_trt.='<div class="fondbeige"><b>Ligne nettoy&eacute;e:</b> '.$texte2.'</div>';
  // extraction des noms de compétence de la ligne
  $comp=explode(",",$texte2);
  foreach($comp as $key=>$value):
    $pos=strpos($value,"+");
    if ($pos==0) $pos=strpos($value,"-");
    if ($pos>0) $value=substr($value,0,$pos-1);
    $value=trim($value);
    $tempo=htmlentities($value);
    if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
      $value=substr(trim($value),0,-1);
      if ($debug==3) $info_trt.='<div class="ml20"><b>Nettoyage obele et double obele:</b></div>';
    endif;
    if (substr(trim($value),-1)=="B"):
      $value=substr($value,0,-1);
      if ($debug==3) $info_trt.='<div class="ml20"><b>Nettoyage :</b> '.substr($value,-1).'</div>';
    endif;  
    if ($debug==3) $info_trt.='<div class="ml20"><b>Comp&eacute;tence :</b> '.$value.'</div>';
    $requete='SELECT * FROM dd_competences WHERE lower(comp_nom)="'.trim(strtolower(stripslashes($value))).'"';
    if ($debug==3) $info_trt.='<div class="ml20">'.$requete.'</div>';
    $result = queryPDO($requete);
    $num_rows = $result->rowCount();
    if ($num_rows>0):
      if ($debug==3) $info_trt.='<div class="ml20"><b>Comp&eacute;tence dans la base</b> : '.$value.'</div>';
      $dn = $result->fetch(PDO::FETCH_ASSOC);
      $critere=strtolower($dn['comp_nom']);
      $pos=strpos(strtolower($texte),$critere);
      if ($debug==3) $info_trt.='<div class="ml20"><b>Crit&egrave;re :</b> '.$critere.' (Pos : '.$pos.')</div>';
      if ($pos>0):
        $lg=strlen($dn['comp_nom']);
        $remplacement='<span onClick="afficherComp('.$dn['comp_id'].')" class="lien">'.$dn['comp_nom'].'</span>';
        $lg2=strlen($remplacement);
        if ($debug==3) $info_trt.='<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
        $pos2=$pos+$lg;
        $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
        $texte=$substitution;
        else:
        if ($debug==3) $info_trt.='<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';

      endif;     
      else:
      $info_trt.='<div class="ml20">Comp&eacute;tence "'.$value.'" introuvable dans la base : ligne '.$i.'</div>';
      //writeLog("debug_dons.txt", trim($value));
    endif;
  endforeach; 
  $ligne=$texte;
  $ligne='<strong>'.$var.' </strong>'.substr($ligne,strlen($var));
endif;



// on ajoute la ligne traitée à la variable de stockage de la description
if (!empty($ligne)):
  $monstre.='<div>'.$ligne.'</div>';
  // on gère l'affichage du résultat
  $sortie.='<div>'.$ligne.'</div>';
endif;

?>
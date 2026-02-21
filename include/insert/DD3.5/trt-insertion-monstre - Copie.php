<?

$ligne=''; // on reset la variable ligne. Si aucune séquence de script ne vient remplir $ligne, la séquence d'ajout de ligne à la variable $monstre sera échappé 
$info_trt='';
$add=1; // par défaut, on ajoute la ligne au texte à l'étape de compilation à la fin du script

$ligne=fgets($fp);

//*******************************************************************************************************************
// Rencontre
$var="### ";
if (substr($ligne,0,strlen($var))==$var):
  $rencontre=explode(":",trim(substr($ligne,4)));
  $abreviation_rencontre=trim($rencontre[0]);
  $titre_rencontre=trim($rencontre[1]);
  $scenario_rencontre=trim($rencontre[2]);
  $scenario_chapitre=trim($rencontre[3]);
  $sortie.='<h1 class="fondrouge">'.$abreviation_rencontre.' : '.$titre_rencontre.' : '.$scenario_rencontre.' : '.$scenario_chapitre.'</h1>';
endif;

// titre
$var="## ";
if (substr($ligne,0,strlen($var))==$var):
  $etape=1;
  $rencontre_monstre=explode(":",trim(substr($ligne,3)));
  $effectif=trim($rencontre_monstre[0]);
  $titre_monstre=trim($rencontre_monstre[1]);
  $sortie.='<h2>'.$effectif.' x '.$titre_monstre.'</h2>';
  $ligne='';  //on n'inscrit plus le nom de la créature dans le bloc stats
endif;

//*******************************************************************************************************************  
// début des pouvoirs spéciaux
$var=utf8_encode("...");
if (substr($ligne,0,strlen($var))==$var):
  $pouvoirs=1;
  $add=0;
endif;
if ($pouvoirs==1):
  $pos=strpos($ligne,".");
  if ($pos>0):
    $ligne='<strong>'.substr($ligne,0,$pos).'. </strong>'.substr($ligne,$pos+1); 
  endif;
endif;
// fin des pouvoirs spéciaux mais suite du bloc de stats du monstre/pnj
$var=utf8_encode("---");
if (substr($ligne,0,strlen($var))==$var):
  $pouvoirs=0;
  $add=0;
endif;    
// fin des pouvoirs spéciaux et du bloc de stats du monstre/pnj
$var=utf8_encode("$$");
if (substr($ligne,0,strlen($var))==$var):
  $etape=2;
  $pouvoirs=0;
  $add=0;
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
$var="Dons ";
if (substr($ligne,0,strlen($var))==$var):
  if ($debug==2) $info_trt.='<div class="fondbeige"><b>'.$titre_monstre.'</b><br><b>Ligne :</b> '.$ligne.'</div>';  
  $texte=$ligne;
  $texte2=substr($ligne,strlen($var));
  if ($debug==2) $info_trt.='<div class="fondbeige"><b>texte2 :</b> '.$texte2.'</div>';
  // Nettoyage de la ligne
  include('include/insert/'.$_SESSION['rulesetRep'].'/trt-extensions.php');
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
  include('include/insert/'.$_SESSION['rulesetRep'].'/trt-extensions.php');
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

//***********************************************************************************************
// compilation du résultat
//***********************************************************************************************
//$info_trt.='<div class="ml20 gras">Compilation des résultats</div>';
if ($etape==1):
  if ($ligne!=''): // on n'ajoute pas le nom du monstre dans le corps de la decription
    $monstre.='<div>'.utf8_decode($ligne).'</div>';
  endif;
endif;
if ($etape==2):
  // Gestion du profil du monstre
  $requete='SELECT * FROM dd_monstres WHERE mo_nom="'.$titre_monstre.'"';
  $result_m=queryPDO($requete);
  $num_rows_m=$result_m->rowCount();
  if ($num_rows_m > 0): // le bloc de stats existe déjà
    $dnm = $result_m->fetch(PDO::FETCH_ASSOC);
    $mo_id=$dnm['mo_id'];
    $info_trt.='<div class="ml20">Bloc de stats "'.$dnm['mo_nom'].'" d&eacute;j&agrave; pr&eacute;sent ('.$dnm['mo_id'].')</div>';
    else: // le bloc de stats n'existe pas, on le crée
    $info_trt.='<div class="ml20"><b>Bloc de stats "'.$titre_monstre.'" absent</b></div>';
    if ($debug==0):
      // création du profil du monstre
      $requete='INSERT INTO dd_monstres (mo_nom, mo_stats) VALUES ("'.addslashes($titre_monstre).'","'.addslashes(utf8_encode($monstre)).'")';
      $resultat=execPDO($requete);
      $mo_id=lastID("dd_monstres","mo");
      $info_trt.='<div class="ml20 gras">Insertion du bloc de stats "'.$titre_monstre.'"</div>';
      else:
      $info_trt.='<div class="ml20 gras">MODE DEBUG : pas d\'insertion du bloc de stats "'.$titre_monstre.'" ('.$abreviation_rencontre.' : '.$re_id.')</div>';
    endif;
  endif;
  // Gestion de la rencontre et de l'effectif de la rencontre
  if ($titre_rencontre!=''):
    // Gestion de la rencontre
    if ($debug==0):
      $requete='SELECT * FROM dd_rencontres WHERE re_abreviation="'.$abreviation_rencontre.'" AND re_sc_id="'.$scenario_rencontre.'"';
      $info_trt.='<div class="ml20 fondgris">### '.$requete.'</div>';
      $result_r=queryPDO($requete);
      $num_rows_r=$result_r->rowCount();
      if ($num_rows_r > 0): // la rencontre existe, on récupère l'ID
        $dnr = $result_r->fetch(PDO::FETCH_ASSOC);
        $re_id=$dnr['re_id'];
        $info_trt.='<div class="ml20">Rencontre "'.$dnr['re_nom'].'" d&eacute;j&agrave; pr&eacute;sente ('.$re_id.')</div>';
        else: // la rencontre n'existe pas, on la crée
        $info_trt.='<div class="ml20 gras">Cr&eacute;ation de la rencontre "'.$abreviation_rencontre.' : '.$titre_rencontre.'"</div>'; 
        $requete='INSERT INTO dd_rencontres (re_nom, re_abreviation, re_sc_id, re_scc_id) VALUES ("'.addslashes($titre_rencontre).'","'.addslashes($abreviation_rencontre).'","'.addslashes($scenario_rencontre).'", "'.addslashes($scenario_chapitre).'")';
        $resultat=execPDO($requete);
        $re_id=lastID("dd_rencontres","re");
      endif;
    endif;
    // Gestion de effectifs de la rencontre
    // vérification des effectifs déjà en base
    $requete='SELECT rem_id, rem_effectif FROM dd_rencontres_monstres WHERE rem_re_id="'.$re_id.'" AND rem_mo_id="'.$mo_id.'"';
    //$info_trt.='<div class="ml20 fondgris">'.$requete.'</div>';
    $result_rem=queryPDO($requete);
    $num_rows_rem=$result_rem->rowCount();
    if ($num_rows_rem > 0): // un effectif existe déjà
      $dnrem = $result_rem->fetch(PDO::FETCH_ASSOC);
      if ($effectif!=$dnrem['rem_effectif']):
        $requete='UPDATE dd_rencontres_monstres SET rem_effectif="'.$effectif.'" WHERE rem_id="'.$dnrem['rem_id'].'"';
        $resultat=execPDO($requete);
        $info_trt.='<div class="ml20 gras">MAJ de l\'effectif de la rencontre</div>';
        else:
        $info_trt.='<div class="ml20 gras">Aucun changement dans l\'effectif de la rencontre</div>';
      endif;
      else:
      $requete='INSERT INTO dd_rencontres_monstres (rem_re_id, rem_mo_id, rem_effectif) VALUES ("'.$re_id.'","'.$mo_id.'","'.$effectif.'")';
      $resultat=execPDO($requete);
      $info_trt.='<div class="ml20 gras">Insertion de '.$effectif.' '.$titre_monstre.' dans la rencontre</div>';
    endif;
    else:
    $info_trt.='<div class="ml20 gras">Aucune rencontre ('.$titre_rencontre.') !</div>';
  endif;
  $monstre="";
  $etape=0;
endif;
if ($add==1) $sortie.='<div>'.$ligne.'</div>';
$i++;
?>
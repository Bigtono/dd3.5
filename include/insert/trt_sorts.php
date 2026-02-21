<?
    foreach($criteres as $key => $value): 
      if (substr(trim($ligne),-1)==".") $ligne=substr($ligne,0,-1); // suppression du point en fin de ligne
      $var=utf8_encode($value);
      if (substr($ligne,0,strlen($var))==$var):
        if ($debug==2) echo '<div class="fondbeige"><b>Ligne :</b> '.$ligne.'</div>';
        $texte=$ligne;
        $pos=strpos($texte,"-");
        $texte2=substr($texte,$pos+1);
        if ($debug==2) echo '<div class="fondbeige"><b>Texte2 :</b> '.$texte2.'</div>';
        // recherche des extensions et mentions de noms de domaine
        $avant=array('D,FRCS');
        $apres=array('');
        $texte2=str_replace($avant, $apres, $texte2);  
        $avant=array('FRCS');
        $apres=array('');
        $texte2=str_replace($avant, $apres, $texte2);
        $avant=array('SC');
        $apres=array('');
        $texte2=str_replace($avant, $apres, $texte2);
        $avant=array('†');
        $apres=array('');
        $texte2=str_replace($avant, $apres, $texte2);
        $avant=array('D,');
        $apres=array(',');
        $texte2=str_replace($avant, $apres, $texte2);
        $avant=array('D (');
        $apres=array(' (');
        $texte2=str_replace($avant, $apres, $texte2);
        if (substr(trim($texte2),-1)=="D"):
          $texte2=substr(trim($texte2),0,-1);
          if ($debug==2) echo '<div class="ml20"><b>Nettoyage :</b> '.substr(trim($texte2),-1).'</div>';
        endif;
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
        if ($debug==2) echo '<div class="fondbeige"><b>Ligne nettoy&eacute;e :</b> '.$texte2.'</div>';
        // extraction des noms de sorts de la ligne
        $sorts=explode(",",$texte2);
        foreach($sorts as $key=>$value):
          $tempo=htmlentities($value);
          if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
            $value=substr(trim($value),0,-1);
            if ($debug==2) echo '<div class="ml20"><b>Nettoyage obele et double obele:</b></div>';
          endif;
          if ($debug==2) echo '<div class="ml20"><b>Sort :</b> '.$value.'</div>';
          $requete='SELECT * FROM dd_sorts WHERE so_res_id IN '.$selection.' AND so_nom="'.trim(stripslashes($value)).'"';
          $result = queryPDO($requete);
          $num_rows = $result->rowCount();
          if ($num_rows>0):
            $dn = $result->fetch(PDO::FETCH_ASSOC);
            if ($debug==2) echo '<div class="ml20"><b>Sort dans la base :</b> '.$dn['so_nom'].'</div>';
            $critere=strtolower($dn['so_nom']);
            if ($debug==2) echo '<div class="ml20"><b>Critere :</b> '.$critere.'</div>';
            $pos=strpos(strtolower($texte),$critere);
            if ($pos>0):
              $lg=strlen($dn['so_nom']);
              $remplacement='<span onClick="afficherSort('.$dn['so_id'].')" class="lien text-red">'.$dn['so_nom'].'</span>';
              if ($debug==2) echo '<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
              $lg2=strlen($remplacement);
              $pos2=$pos+$lg;
              $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
              $texte=$substitution;
              else:
              if ($debug==2) echo '<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';    
            endif;
            else:
            echo '<div class="ml20">Sort "'.$value.'" introuvable dans la base : ligne '.$i.'</div>';
            writeLog("debug_sorts.txt", trim($value));
          endif;
        endforeach; 
        $ligne=$texte;
        $ligne='<span class="gras ml20">'.$var.' </span>'.substr($ligne,strlen($var));  
      endif;
    endforeach;  

?>
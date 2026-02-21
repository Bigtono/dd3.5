<?
    foreach($criteres as $key => $value): 
      if (substr(trim($ligne),-1)==".") $ligne=substr($ligne,0,-1); // suppression du point en fin de ligne
      //$var=utf8_encode($value);
      $var=$value;
      if (substr($ligne,0,strlen($var))==$var):
        if ($debug==3) $info_trt.='<div class="fondbeige"><b>'.$titre.'</b><br><b>Ligne :</b> '.$ligne.'</div>';
        $texte=$ligne; // variable contenant la liste des sorts avant nettoyage
        $texte=str_replace('—','-',$texte); // remplacement du tiret long par un tiret court
        $pos=strpos($texte,"-");
        $texte2=substr($texte,$pos+1); // variable qui contient la liste nettoyée des sorts. La variable sera transformée en tableau ultérieurement

        include('include/insert/trt-extensions.php');
        if ($debug==3) $info_trt.='<div class="fondbeige"><b>Texte2 :</b> '.$texte2.'</div>'; 
        // gestion du caractère de fin
        $carFin=substr(trim($texte2),-1);
        if ($carFin=="D" || $carFin=="B"):
          $texte2=substr(trim($texte2),0,-1);
          if ($debug==3) $info_trt.='<div class="ml20"><b>* Nettoyage :</b> '.$carFin.' dans <span class="fongris">'.$texte2.'</span></div>';
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
        // recherche et nettoyage des termes liées aux dons de métagie
        $criteres_meta= array(' silencieux', ' silencieuse', ' augmentée', ' augmenté', ' accélérée', ' accéléré', ' renforcée', ' renforcé', ' statique', ' à durée étendue', ' à portée étendue', ' à zone d\'effet étendue');
        foreach($criteres_meta as $key_meta => $value_meta): 
          $texte2=str_replace($value_meta,"",$texte2);
        endforeach;    
        if ($debug==3) $info_trt.='<div class="fondbeige"><b>Ligne nettoy&eacute;e :</b> '.$texte2.'</div>';
        // extraction des noms de sorts de la ligne
        $sorts=explode(",",$texte2);
        foreach($sorts as $key=>$value):
          $tempo=htmlentities($value);
          //$info_trt.='<div class="ml20"><b>'.$tempo.' / '.substr(trim($tempo),0,-8).'</b></div>';
          if (substr(trim($tempo),-16)=="&dagger;&dagger;"):
            $value=substr(trim($tempo),0,-16);
            if ($debug==3):
              $info_trt.='<div class="ml20"><b>** Nettoyage obele x2</b></div>';
            endif;
            else:
            if (substr(trim($tempo),-8)=="&dagger;" || substr(trim($tempo),-8)=="&Dagger;"):
              $value=substr(trim($tempo),0,-8);
              if ($debug==3) $info_trt.='<div class="ml20"><b>** Nettoyage obele et double obele:</b></div>';
            endif;
          endif;
          if ($debug==3) $info_trt.='<div class="ml20"><b>Sort :</b> '.$value.'</div>';
          $requete='SELECT * FROM dd_sorts WHERE so_res_id IN '.$selection.' AND so_nom="'.trim(stripslashes($value)).'"';
          $result = queryPDO($requete);
          $num_rows = $result->rowCount();
          if ($num_rows>0):
            $dn = $result->fetch(PDO::FETCH_ASSOC);
            if ($debug==3) $info_trt.='<div class="ml20"><b>Sort dans la base :</b> '.$dn['so_nom'].'</div>';
            $critere=strtolower($dn['so_nom']);
            if ($debug==3) $info_trt.='<div class="ml20"><b>Critere :</b> '.$critere.'</div>';
            $pos=strpos(strtolower($texte),$critere);
            if ($pos>0):
              $lg=strlen($dn['so_nom']);
              $remplacement='<span onClick="afficherSort('.$dn['so_id'].')" class="lien">'.$dn['so_nom'].'</span>';
              if ($debug==3) $info_trt.='<div class="ml20"><b>Remplacement :</b> '.$remplacement.'</div>';
              $lg2=strlen($remplacement);
              $pos2=$pos+$lg;
              $substitution=substr($texte,0, $pos).$remplacement.substr($texte,$pos2); 
              $texte=$substitution;
              else:
              if ($debug==3) $info_trt.='<div class="ml20"><b>Crit&egrave;re non trouv&eacute; :</b> '.$critere.' => '.$texte.')</div>';    
            endif;
            else:
            $info_trt.='<div class="ml20">Sort "'.trim($value).'" introuvable dans la base : ligne '.$i.'</div>';
            //writeLog("debug_sorts.txt", trim($value));
          endif;
        endforeach; 
        $ligne=$texte;
        // gestion du bug des lignes de sorts (D majuscule non traité)
        if (substr(trim($ligne),-1)=="D"):
          if ($debug==3) $info_trt.='<div class="fondbleu"><b>* Recherche Bug D :</b> '.$ligne.' / '.substr(trim($ligne),0,-1).' / '.substr(trim($ligne),-1).'</div>';
          $ligne=substr(trim($ligne),0,-1).'<sup>D</sup>';
        endif;
        if ($debug==3) $info_trt.='<div class="fondrouge"><b>Ligne trait&eacute;e :</b> '.$ligne.'</div>';
        $ligne='<span class="gras ml20">'.$var.' </span>'.substr($ligne,strlen($var));
      endif;
    endforeach;
?>
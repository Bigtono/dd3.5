<?
  // recherche et nettoyage des extensions et mentions de noms de domaine
  
  $avant=array('*');
  $apres=array('');
  $texte2=str_replace($avant, $apres, $texte2);

  $avant=array('FRCS');
  $apres=array('');
  $texte2=str_replace($avant, $apres, $texte2);

  $avant=array('CAr,');
  $apres=array(',');
  $texte2=str_replace($avant, $apres, $texte2);
  if (substr($texte2,-3)=="CAr") $texte2=substr($texte2,0,-3);

  $avant=array('MH,');
  $apres=array(',');
  $texte2=str_replace($avant, $apres, $texte2);
  if (substr($texte2,-2)=="MH") $texte2=substr($texte2,0,-2);

  $avant=array('SC ');
  $apres=array('');
  $texte2=str_replace($avant, $apres, $texte2);
  if (substr($texte2,-2)=="SC") $texte2=substr($texte2,0,-2);

  $avant=array('PG ');
  $apres=array('');
  $texte2=str_replace($avant, $apres, $texte2);
  if (substr($texte2,-2)=="PG") $texte2=substr($texte2,0,-2);

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
  if (substr($texte2,-1)=="B") $texte2=substr($texte2,0,-1);
  
  $avant=array('*');
  $apres=array('');
  $texte2=str_replace($avant, $apres, $texte2); 

?>
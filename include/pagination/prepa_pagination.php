<?
// nombre d'item par page
if (isset($page_source)):
  if ($page_source>0):
    $nbp=$page_source;
    else:
    $nbp=15;
  endif;
  else:
  $nbp=15;
endif;
// numéro de la page appelée
if (isset($_GET['page'])):
  if ($_GET['page']>0):
    $page=$_GET['page'];
    else:
    $page=1;
  endif;
  else:
  $page=1;
endif;
?>
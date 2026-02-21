<?php
include_once("../include/dblib.inc.php");
include_once("../include/session.php");

$n=$_POST['note'];
$a=$_POST['accreditation']; 

if(!empty($n)):
  $requete = "SELECT * FROM dd_notes WHERE no_id='".$n."'";	
  $result=queryPDO($requete);
	$num_rows=$result->rowCount();
  $dn = $result->fetch(PDO::FETCH_ASSOC);
  // mise en forme du contenu
  $niveau=niveauNote($n);
  if ($dn['no_cumulatif']==0):
    $texte=$dn['no_texte_basique'];
    if (strlen($dn['no_texte_intermediaire'])>0 && $a>1) $texte=stripslashes($dn['no_texte_intermediaire']);
    if (strlen($dn['no_texte_avance'])>0 && $a>2) $texte=stripslashes($dn['no_texte_avance']);
    if (strlen($dn['no_texte_expert'])>0 && $a>3):
      $texte=stripslashes($dn['no_texte_expert']);
    endif;
    else:
    $texte=$dn['no_texte_basique'];
    if (strlen($dn['no_texte_intermediaire'])>0 && $a>1) $texte.=$dn['no_texte_intermediaire'];
    if (strlen($dn['no_texte_avance'])>0 && $a>2) $texte.=$dn['no_texte_avance'];
    if (strlen($dn['no_texte_expert'])>0 && $a>3) $texte.=$dn['no_texte_expert'];
  endif;

  /** Génère TOC + HTML modifié */
  // exemple : [$tocHtml, $htmlWithIds] = buildTocFromHtml($texte);
  // exemple : [$tocHtml, $htmlWithIds] = buildTocFromHtml($texte, ['h2','h3']);
  [$tocHtml, $htmlWithIds] = buildTocFromHtml($texte, ['h2']);
  /** Affichage (à adapter à ton template) */
  $texte2='<aside>'.$tocHtml.'</aside><article class="article">'.$htmlWithIds.'</article>';

  // affichage du contenu
  $result='<div id="note" class="affichage">';
  $result.='  <div class="menu2"><div  class="ga lien" onClick="fermerDetail()"><i class="fa fa-close"></i></div><div class="ce"></div><div class="dr lien" onclick="modifierNote('.$dn['no_id'].')"><i class="fa fa-pencil"></i></div></div>';
  $result.='  <div class="nom_objet">'.stripslashes($dn['no_nom']).'</div>';
  $result.='  <div class="description">';
  $result.='    <div class="texte"><span class="label">Catégorie : </span>'.libelle("dd_types_notes", "tyno", "nom",$dn['no_tyno_id']).'</div>';
  $result.='    <div class="texte"><span class="label">Niveau : </span>'.libelle("dd_niveaux_notes", "nino", "nom",$a).'</div>';
  $result.='    <div class="texte">'.stripslashes($texte2).'</div>';
  $result.='  </div>';
  $result.='</div>';
  // On ajoute les données dans un tableau
  echo $dn['no_id']."@".$result;
	else:
	echo "Erreur affichage Note";
endif;
?>
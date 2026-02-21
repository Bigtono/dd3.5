<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");


if(isset($_GET['regle'])):
  $regle=$_GET['regle'];
endif;
if(isset($_GET['sup'])):
  $sup=$_GET['sup'];
  else:
  $sup=0;
endif;

?>
<!doctype html>
<html>
<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-regles.js'></script>
</head>
<body>
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper">
    <?
      if(isset($regle)):
        if ($regle=="n"):
          $libelle='Ajouter';
          else:
          $libelle='Modifier';
          $requete = "SELECT * FROM dd_regles WHERE re_id='".$regle."'";
          $resultat = queryPDO($requete);
          $dn = $resultat->fetch(PDO::FETCH_ASSOC);
        endif;
        // mise en forme du contenu
        $categorie='<select id="mp_re_cr_id" name="mp_re_cr_id">'.optionList("dd_categorie_regle", "cr","nom", $dn['re_cr_id']).'</select>';
        if ($regle=="n"):
          $id_parent=$sup;
          else:
          $id_parent=$dn['re_re_id'];
        endif;
        $parent='<select id="mp_re_re_id" name="mp_re_re_id">'.optionList("dd_regles", "re","nom", $id_parent).'</select>';
        // affichage du contenu 
        ?>
        <form action="regle-enregistrement.php?regle=<? echo $regle; ?>" id="regle" method="post" class="mt10">
          <input type="hidden" id="mp_re_id" name="mp_re_id" value="<? echo $regle; ?>">
          <div><input id="mp_re_nom" name="mp_re_nom" class="input_nom" value="<? echo stripslashes($dn['re_nom']); ?>" ></div>
          <div class="ligne mt10"><div class="label w90">Parent</div><? echo $parent; ?></div>
          <div class="label">Description</div><div><textarea id="mp_re_texte" name="mp_re_texte" class="input_texte" rows="15"><? echo stripslashes($dn['re_texte']); ?></textarea></div>
          <script>
            CKEDITOR.replace('mp_re_texte', {
              allowedContent: true, // désactive le filtre de contenu
              // facultatif : garde un minimum de sécurité en autorisant explicitement certaines balises et attributs
              extraAllowedContent: 'span[*]; div[*]; strong; em; b; i; p[*]; br;',
              contentsCss: 'include/_styles_.css'
            });
          </script>          
                    
          <div class="ligne mt10"><div class="label w90">Ordre</div><input id="mp_re_ordre" name="mp_re_ordre" class="input_ordre" value="<? echo stripslashes($dn['re_ordre']); ?>" ></div>
          <div class="ligne mt10">
            <div class="label w90">Affichage &eacute;cran</div><input id="mp_re_ecran" name="mp_re_ecran" class="input_ordre" value="<? echo stripslashes($dn['re_ecran']); ?>" >
          </div>
          
          <input class="bouton" type="submit" name="ok" id="ok" value="<? echo $libelle; ?>">
          <input class="bouton ml15" type="submit" name="nok" id="nok" value="Annuler"></div>
        </form>'; 
        
        <?
        else:
        echo "<div>Erreur</div>";
      endif;    
    ?>
    </div><!-- wrapper --->
	</div><!-- page --->
</body>
</html>
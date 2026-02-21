<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$re_id=$_POST["mp_rencontre"];
$description=$_POST["mp_description"];
$monstre_categorie=$_POST["mp_monstre_categorie"];

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
	<div id="page">
		<? include("include/header.php"); ?>
		<? include("include/menu.php"); ?>
	  <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA">Imports de cr&eacute;atures V2</div>
        <div></div>
      </div>
      <?
      // Préparation des données
      $rencontres='<select id="mp_rencontre" class="rencontre_nom">'.OptionListeRencontre().'</select>';
      $categories='<select id="mp_monstre_categorie" class="mp_monstre_categorie">'.OptionList("dd_monstres_categories", "mocat","nom", $monstre_categorie).'</select>';      

      if (empty($_POST['import'])):
      ?>
        <h2>Saisie du monstre</h2>
        <form action="insertion-monstres.php" method="post" enctype="multipart/form-data" class="mb20">
          <input type="hidden" id="import" name="import" value="1">
          <div class="ligne"><div class="gras w100">Nom</div><input type="text" id="mp_monstre_nom" name="mp_monstre_nom" value="" class="monstre_nom"></div>
          <div class="ligne"><div class="gras w100">Catégorie</div><? echo $categories; ?></div>
          <div class="ligne"><div class="gras w100">Rencontre</div><? echo $rencontres; ?></div>
          <div class="ligne"><div class="gras w100">Effectif</div><input type="text" id="mp_effectif" name="mp_effectif" value="" class="rencontre_effectif"></div>  
          <div class="ligne"><div class="gras w100">Description</div><textarea name="mp_description" rows="20" class="monstre_description"></textarea></div>
          <button class="btNoir" type="submit">Envoyer</button>
        </form>
      <?
      endif;
      ?>
      <h2>Description du monstre</h2>
    <?
      // Vérifie si un textarea a été envoyé
      if (!empty($description)):
        // Récupère les données brutes
        $contenu = trim($description);

        // Normaliser les apostrophes « Word » en apostrophes simples
        $recherche = [
            "’",  // U+2019 typographique
            "‘",  // U+2018 ouverture
            "\xC2\x92",    // variante possible CP1252 ? UTF-8
        ];
        $remplace = ["'", "'", "'"];
        $contenu = str_replace($recherche, $remplace, $contenu);
        $contenu = str_replace(["“","”"], '"', $contenu);

        // Transforme en tableau de lignes comme pour un fichier
        $lignes = explode("\n", $contenu);
        // Variables utilisées pendant le traitement
        $debug = $_SESSION['debug'];
        $sortie = '';
        $pouvoirs = 0;
        $etape=0;
        $abreviation_rencontre = '';
        $titre_rencontre = '';
        $scenario_rencontre = '';
        $scenario_chapitre = '';
        $monstre = "";
        $enregistrement = 1;
        $i = 1;
        static $mode_pouvoirs = false; // Mode "Pouvoirs" activé après une ligne "..."
      
        $re_id=$_POST["mp_rencontre"];

        // Traitement du nom et de la catégorie du monstre
        $rencontre_abreviation=libelle("dd_rencontres","re","abreviation",$re_id);
        $rencontre_nom=libelle("dd_rencontres","re","nom",$re_id);
        $scenario_chapitre_id=libelle("dd_rencontres","re","scc_id",$re_id);
        $chapitre_nom=libelle("dd_scenarios_chapitres","scc","nom",$scenario_chapitre_id);
        $scenario_id=libelle("dd_scenarios_chapitres","scc","sc_id",$scenario_chapitre_id);
        $scenario_nom=libelle("dd_scenarios","sc","nom",$scenario_id);
        if (!empty($_GET["mp_rencontre"])) $sortie.='<h1 class="fondrouge">'.$rencontre_abreviation.' : '.$rencontre_nom.' : '.$scenario_nom.' : '.$chapitre_nom.'</h1>';
        $monstre_nom=$_POST["mp_monstre_nom"];
        $categorie=libelle("dd_monstres_categories","mocat","nom",$monstre_categorie);
        $effectif=$_POST["mp_effectif"];
        $sortie.='<h2>'.$effectif.' x '.$monstre_nom.'</h2>';

        // Gestion du bloc de stats du monstre (champ mo_stats)
        // Le code trt-insertion-monstre.php recoit une ligne et lui applique un formatage selon son contenu
        // la ligne formatée est stockées dans $monstre. la variable monstre est ensuite stockée dans le champ mo_stats de la table dd_monstres
        // la variable $sortie affiche le résultat du traitement à l'écran
        
        $resultat_trt='';
    
        foreach ($lignes as $ligne):
          $info_trt = '';
          include('include/insert/'.$_SESSION['rulesetRep'].'/trt-insertion-monstre-2.php');
          if (!empty($info_trt)) $resultat_trt.=$info_trt;
          $i++;
        endforeach;
        // affichage des infos de traitement
        echo '<div class="gras mr10" onCLick="togglePlus(\'traitement\')"><h3><i id="toggle-traitement" class="fas fa-chevron-right"></i> Traitement</h3></div>';
        echo '<div id="traitement" class="box-data accordion-content noDisplay">'.$resultat_trt.'</div>';
    
        $sortie='<h3 class="mt30">'.$monstre_nom.'</h3>';
        $sortie.='<div><strong>'.$categorie.'</strong></div>';
        $sortie.='<div class="mt20">'.$monstre.'</div>';
        echo $sortie;

        //*******************************************************************
        // compilation du résultat
        //*******************************************************************
        // 1 - Gestion de la fiche du monstre
        // enregistrement des données dans la table dd_monstres
        $requete='SELECT * FROM dd_monstres WHERE mo_nom="'.$monstre_nom.'"';
        $result_m=queryPDO($requete);
        $num_rows_m=$result_m->rowCount();
        if ($num_rows_m > 0): // le monstre existe déjà
          $dnm = $result_m->fetch(PDO::FETCH_ASSOC);
          $mo_id=$dnm['mo_id'];
          $info_trt.='<div class="ml20">Bloc de stats "'.$dnm['mo_nom'].'" d&eacute;j&agrave; pr&eacute;sent ('.$dnm['mo_id'].')</div>';
          else: // le bloc de stats n'existe pas, on le crée
          $info_trt.='<div class="ml20"><b>Bloc de stats "'.$monstre_nom.'" absent</b></div>';
          if ($debug==0):
            // création du profil du monstre
            $requete='INSERT INTO dd_monstres (mo_nom, mo_mocat_id, mo_stats) VALUES ("'.addslashes($monstre_nom).'","'.addslashes($monstre_categorie).'","'.addslashes($monstre).'")';
            $resultat=execPDO($requete);
            $mo_id=lastID("dd_monstres","mo");
            $info_trt.='<div class="ml20 gras">Insertion du monstre "'.$monstre_nom.'"</div>';
            else:
            $info_trt.='<div class="ml20 gras">MODE DEBUG : pas d\'insertion du monstre "'.$monstre_nom.'" ('.$rencontre_abreviation.' : '.$re_id.')</div>';
          endif;
        endif;
        // 2 - Gestion de la rencontre et de l'effectif de la rencontre
        // si une rencontre ($mp_rencontre) et un effectif ($mp_effectif) ont été sélectionnés, on ajoute le monstre à la rencontre dans la table dd_rencontres_monstres
        if (!empty($re_id) && !empty($effectif)):
          if ($debug==0):      
            $requete='INSERT INTO dd_rencontres_monstres (rem_re_id, rem_mo_id, rem_effectif) VALUES ("'.$re_id.'","'.$mo_id.'","'.$effectif.'")';
            $resultat=execPDO($requete);
            $info_trt.='<div class="ml20 gras">Insertion de '.$effectif.' '.$monstre_nom.' dans la rencontre '.$recontre_nom.'</div>';
            else:
            $info_trt.='<div class="ml20 gras">Visualisation de '.$effectif.' '.$monstre_nom.' dans la rencontre '.$recontre_nom.'</div>';
          endif;
        endif;
        else:
        // Si textarea vide mais que l'action d'import est demandée
        if (isset($_POST['import']) && $_POST['import'] == 1):
            echo '<div class="nodata">Aucune donnée reçue dans le textarea.</div>';
        endif;
      endif;
      ?>
      <p class="mb50">&nbsp;</p> <!--- marge pour éviter le chevauchement du texte et du bouton de retour en haut de page --->
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>          
    </div><!-- wrapper --->
	</div><!-- page --->
</body>
<div id="detail-pp"></div>  
<div id="modification"></div>
</html>
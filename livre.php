<?
session_start();
include("include/dblib.inc.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$mess="";
if ($_GET['actionflag']==2):
	// vidage de l'ancienne sélection dans le caddie 
	supprCaddie($_GET['idperso']);
	// remplissage du caddie avec la nouvelle sélecrion
	$mess=0;
	$aSort['id_perso']=$_GET['idperso'];
	$aSort['date_demande']=mktime();
	$aSort['commentaire']=date("Y-m-d");
	$nbsi=0;
	foreach($_GET[s] as $key=>$value):
		$aSort['id_sort']=$key;
		$aSort['nom_sort']=writeSort($key);
		$mess=addData($aSort, 'caddie_impression');
		$nbsi++;
	endforeach;
endif;

?>
<!doctype html>
<HEAD>
<? include("include/head.php"); ?>
<!--- scripts --->
<script type='text/javascript' src='js/moncode-sorts.js'></script>
<script type='text/javascript'>
	$(document).ready(function(){
		$('#resolution').html("Resolution : "+screen.width+'x'+screen.height);
	});
</script>
</HEAD>

<BODY>

<div id="page">
	<? include("include/header.php"); ?>
	<? include("include/menu.php"); ?>
  <div class="wrapper">
<?
//************************************************************************************************************************
// sélection de la classe et du personnage
//************************************************************************************************************************

if ($_GET['actionflag']<1):
?>
	<div class="action">Etape 1 : S&eacute;lectionner un personnage</div>
  
	<form action="<? echo $prm_url; ?>livre.php" method="get" name="selections" id="selections">
		<input type="hidden" name="actionflag" value="1" /> 
    <div class="selection">
        Personnage : 
        <select name="personnage" size="1">
						<? echo optionList("personnages", "pe", "nom"); ?>
        </select>
        <input type="submit" name="Submit" value="Selectionner" />
		</div>
     <div class="selection">
      	<b>si nouveau personnage :</b><br>
        Indiquer son nom <input name="nouveau_personnage" type="text" id="nouveau_personnage" size="30" /><br />
				Indiquer sa classe <select name="classe"><? echo OptionListeClassesLS(); ?></select>
		</div>
    <input type="submit" name="Submit" value="Selectionner" />
		</form>
<?
endif;

//************************************************************************************************************************
// Remplissage du caddie
//************************************************************************************************************************
if ($_GET['actionflag']==1):
	if (strlen($_GET['nouveau_personnage'])>0):
		$nomperso=$_GET['nouveau_personnage'];
		// ajout du personnage dans la liste
		$aPerso['pe_nom']=$_GET['nouveau_personnage'];
		$aPerso['pe_cla_id']=$_GET['classe'];
		$idperso=addData($aPerso, 'personnages');
		$classe=libelle( "classes", "cla", "nom", $_GET['classe']);
		else:
		$idperso=$_GET['personnage'];
		$nomperso=libelle("personnages", "pe", "nom",$idperso);
		$idclasse=libelle("personnages", "pe", "cla_id",$idperso);
	endif;
?>

<div class="action">Etape 2 : S&eacute;lection des	sorts</div>

<div id="listesort">
<?
	// création du formulaire
	echo '<form name="select_sort" action="'.$prm_url.'livre.php" method="get">';
	echo '<input type="hidden" name="actionflag" value="2">';
	echo '<input type="hidden" name="nomperso" value="'.$nomperso.'">';
	echo '<input type="hidden" name="idperso" value="'.$idperso.'">';
	echo '<input type="hidden" name="classe" value="'.$classe.'">';
	
	// menu onglets pour choisir le niveau de sort
 	echo '<div id="tabsort">';
		for($i=0;$i<10;$i++):
			if ($i==0):
  		   echo '<div id="niveau'.$i.'" class="tab red" onclick="changeOnglet('.$i.')">Niveau '.$i.'</div>';
				 else:
				 echo '<div id="niveau'.$i.'" class="tab" onclick="changeOnglet('.$i.')">Niveau '.$i.'</div>';
			endif;
		endfor;
	echo '</div>';
	
	// boucle de création des niveaux de sorts
	for($i=0;$i<10;$i++):

		if ($i==0):
			echo '<div id="contenuOnglet'.$i.'" class="niveausort" style="display:block;">';
			else:
			echo '<div id="contenuOnglet'.$i.'" class="niveausort" style="display:none;">';
		endif;

		echo '<div class="titre-niveausort">Sort de niveau '.$i.'</div>';
		// Sélection des classes de lanceurs de sorts
		$requete="SELECT id_sort, nom_sort, college_sort, description_courte, selection FROM sorts LEFT JOIN ressources ON so_res_id=res_id WHERE so_res_id IN ".$selection." AND ".$idclasse."=\"$i\" ORDER BY nom_sort";
    echo '<div class="titre-niveausort">'.$requete.'</div>';
    $result=queryPDO($requete);
    $num_rows=$result->rowCount();
		if ($num_rows > 0):
			// formatage du tableau de données
			echo '<div id="liste-sorts">';;
			while($sort = $result->fetch(PDO::FETCH_ASSOC)):
				// vérification de la sélection. on contrôle si le sort est déjà dans le caddie du personnage sélectionné
        /*
				if (strlen($nomperso)>0):
					$sql='SELECT id_sort FROM caddie_impression WHERE id_perso="'.$idperso.'" AND id_sort="'.$sort['id_sort'].'"';
					$result_caddie= getRowSpec($sql);
					$num_rows_caddie = mysql_num_rows( $result_caddie );
					if ($num_rows_caddie > 0):
						$check=" checked";
						$value="c";
						else:
						$check="";
						$value="";
					endif;
				endif;
        */
				// création de la ligne de sort
        include("include/insert/'.$_SESSION['rulesetRep'].'/ligneSort.php");
			endwhile;
			echo '<div><input type="submit" name="Submit" value="Selectionner"></div>';
			echo '</div>';
		endif;
		echo '</div>'; // fin du bloc affichant les sorts
	endfor;
	
	// fin du formulaire
	echo '</form>';
endif;

//************************************************************************************************************************
// Compilation du livre de sort
//************************************************************************************************************************

if ($_GET['actionflag']==2):
	$nomperso=$_GET["nomperso"];
	$classe=$_GET["classe"];

?>
	<div class="action">Etape 3 : Impression des sorts effectuée</div>
	<div class="selection">
		<? echo $nbsi ?> sort(s) ajouté(s) dans le livre <a href="<? echo $prm_url."fiche_sortilege.php?v=C&classe=".$classe."&idperso=".$_GET["idperso"]; ?>" target="_new">[Imprimer]</a><br />
    <?
    	echo "ID perso : ".$aSort['id_perso']."<br>";
			echo "Timer : ".$aSort['date_demande']."<br>";
			echo "Date : ".$aSort['commentaire'];
		?>
	</div>
<?
endif;
?>	
	</div>
</div>
<div id="parametres">
	<div id="resolution" class="perso">toto</div>
	<div class="perso">
  	<? if (isset($nomperso)) echo '<strong>Personnage : </strong>'.$nomperso.' ('.$classe.')'; ?>
  </div>
	<div class="perso"><? echo $selectionAffichage; ?></div>
</div>
<div id="detail"></div>
</body>
</html>
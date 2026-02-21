<?php

include_once("../include/dblib.inc.php");

	// collège
	$college='<select name="college_sort" id="college_sort">';
	$college.='<option value=""></option>';
	$college.='<option value="Abjuration">Abjuration</option>';
	$college.='<option value="Divination">Divination</option>';
	$college.='<option value="Enchantement">Enchantement</option>';
	$college.='<option value="Evocation">Evocation</option>';
	$college.='<option value="Illusion">Illusion</option>';
	$college.='<option value="Invocation">Invocation</option>';
	$college.='<option value="Necromancie">Necromancie</option>';
	$college.='<option value="Transmutation">Transmutation</option>';
	$college.='<option value="Universel">Universel</option>';	
	$college.='</select>';
	// catégorie de lanceur de sorts
	$lanceur="";
	$lanceur .= "P <input type ='text' size='2' id='pretre' value=''> ";
	$lanceur .= "M <input type ='text' size='2' id='mage' value=''> ";
	$lanceur .= "Pa <input type ='text' size='2' id='paladin' value=''> ";
	$lanceur .= "R <input type ='text' size='2' id='rodeur' value=''> ";
	$lanceur .= "B <input type ='text' size='2' id='barde' value=''> ";
	$lanceur .= "D <input type ='text' size='2' id='druide' value=''> ";
	// composante
	$composante="";
	$composante .= 'V <input type="checkbox" name="vocal_sort" id="vocal_sort" value=""> '; 
	$composante .= 'G <input type="checkbox" name="gestuel_sort" id="gestuel_sort" value=""> ';
	$composante .= 'M <input type="checkbox" name="materiel_sort" id="materiel_sort" value=""> '; 
	$composante .= 'F <input type="checkbox" name="focalisateur" id="focalisateur" value=""> ';
	$composante .= 'FD <input type="checkbox" name="focalisateur_divin" id="focalisateur_divin" value="">'; 
	// RM
	$rm='<select name="resistance_sort" id="resistance_sort">';
	$rm.='<option value="0">Non</option>';
	$rm.='<option value="1">Oui</option>';
	$rm.='</select>';	
		
		// mise en forme du contenu
		$result='<form id="modifSort" method="post">';
		
		$result.='<div id="sort" class="affichage">';
		
		$result.='<input id="id_sort" value="'.$q.'" type="hidden">';

		$result.='<div id="nom"><input id="nom_sort" value="" size="50"></div>';

	  $result.='<div id="college">Collège '.$college.' &nbsp;';
		$result.=' Branche <input id="branche_sort" value=""></div>';
		
		$result.='<div id="lanceur"><span>Lanceur :</span> '.stripslashes(utf8_encode($lanceur)).'</div>';
		
		$result.='<div class="entete-sort">';
		
	  $result.='<div class="gauche">';
		$result.='<span>Portée :</span> <input id="portee_sort" value=""><br>';
		$result.='<span>Cible :</span> <input id="cible_sort" value=""><br>';
		$result.='<span>Durée :</span> <input id="duree_sort" value=""><br>';
		$result.='</div>';
		
		$result.='<div class="droite">';
		$result.='<span>Composantes :</span> '.stripslashes(utf8_encode($composante)).'<br>';
		$result.='<span>Incantation :</span> <input id="duree_incantation" value=""><br>';
		$result.='<span>RM :</span> '.$rm.'<br>';
		$result.='<span>JS :</span> <input id="jet_sauv_sort" value=""><br>';
		$result.='</div>';
		
		$result.='</div>';
		
		$result.='<div id="texte"><span>Description du sort :</span><br><textarea id="text_sort" cols="72" rows="10"></textarea></div>';
		
		$result.='<div id="effet"><span>Résumé du sort:</span><br><input id="description_courte" size="96" value=""></div>';
		
		$result.='<div id="source"><span>Source du sort:</span><select id="source_sort">'.OptionListeLivres().'</select> <input id="page_sort" value="" size="5"></div>';
			
		$result.='<div><input class="bouton" type="button" id="validNouveauSort" value="Créer" onClick="validerNouveauSort()"> &nbsp; <input class="bouton" type="button" id="annuleNouveauSort" value="Annuler" onClick="annulePageModif()"></div><br>';
		 
		$result.='</div>'; // fin #sort
		
		$result.='</form>'; 
		
  // On ajoute les donnnées dans un tableau
  echo $dn['id_sort']."@".$result;

?>
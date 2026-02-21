// JavaScript Document
//##############################MOTEUR AJAX 1############################################
var nombreOnglets = 9;

function changeOnglet(numero)
{
  // On commence par tout masquer
  for (var i = 0; i < nombreOnglets+1; i++) {
    $("#contenuOnglet" + i).css('display','none');
		$("#niveau" + i).css('color','white');
	}
  // Puis on affiche celui qui a été sélectionné
  $("#contenuOnglet" + numero).css('display','block');
	$("#niveau" + numero).css('color','red');
}

function afficherSort(idSort) {   
	//alert(idSort);
  console.log('Affichage sort #'+idSort);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageSort.php',
    data: "sort="+ idSort,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur serveur');}
	}); 
}

function modifierSort(idSort) {
  //alert('ID '+idSort);
  console.log('Modification sort #'+idSort);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierSort.php',
    data: "sort="+ idSort,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierSort');}
	}); 
}

function validerModifSort() {
  var mp_so_texte = CKEDITOR.instances.mp_so_texte.getData(); // traitement du champ textarea modifié par CKEDITOR
	var vocal="0";
	var gestuel="0";
	var materiel="0";
	var focalisateur="0";
	var focalisateur_divin="0";
	if ($('#mp_so_vocal').is(':checked')) vocal="1";
	if ($('#mp_so_gestuel').is(':checked')) gestuel="1";
	if ($('#mp_so_materiel').is(':checked')) materiel="1";
	if ($('#mp_so_focalisateur').is(':checked')) focalisateur="1";
	if ($('#mp_so_focalisateur_divin').is(':checked')) focalisateur_divin="1";
  
  var ls=$('input.input_niveau');
  var lsParam="";
  console.log('Gestion des parametre url LS : ');
  $.each(ls, function(index) {
    var cla=$(this).attr("id");
    var claId=cla.split("-");
    lsParam = lsParam+"&ls"+claId[1]+"="+$(this).val();
    console.log(index + ": " + claId[1] + ": " + $(this).val());
  });
  console.log(lsParam);
  
  var ls=$('input.input_niveau_domaine');
  var lsParamDom="";
  console.log('Gestion des parametre url Dom : ');
  $.each(ls, function(index) {
    var cla=$(this).attr("id");
    var claId=cla.split("-");
    lsParamDom = lsParamDom+"&ds"+claId[1]+"="+$(this).val();
    console.log(index + ": " + claId[1] + ": " + $(this).val());
  });
  console.log(lsParamDom);
  
    //actualisation des champs de modification du sort
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifSort.php',
    data: "mp_so_id="+$('#mp_so_id').val()+
		"&mp_so_nom="+encodeURIComponent($('#mp_so_nom').val())+
		"&mp_so_co_id="+$('#mp_so_co_id').val()+
		"&mp_so_branche="+encodeURIComponent($('#mp_so_branche').val())+
		"&mp_so_portee="+encodeURIComponent($('#mp_so_portee').val())+
		"&mp_so_cible="+encodeURIComponent($('#mp_so_cible').val())+
    "&mp_so_zone_effet="+encodeURIComponent($('#mp_so_zone_effet').val())+
		"&mp_so_duree_sort="+encodeURIComponent($('#mp_so_duree_sort').val())+
		"&mp_so_duree_incantation="+encodeURIComponent($('#mp_so_duree_incantation').val())+    
		"&mp_so_vocal="+vocal+
		"&mp_so_gestuel="+gestuel+
		"&mp_so_materiel="+materiel+
		"&mp_so_focalisateur="+focalisateur+
		"&mp_so_focalisateur_divin="+focalisateur_divin+
		"&mp_so_resistance="+$('#mp_so_resistance').val()+
		"&mp_so_jet_sauvegarde="+encodeURIComponent($('#mp_so_jet_sauvegarde').val())+
		"&mp_so_texte="+encodeURIComponent(mp_so_texte)+
		"&mp_so_resume="+encodeURIComponent($('#mp_so_resume').val())+    
		"&mp_so_res_id="+$('#mp_so_res_id').val()+
    lsParam+
    lsParamDom,
		dataType:'text',
    success: actualiserPageSorts,
    error: function() {alert('Erreur validerModifSort()');}
	});		
}

function actualiserPageSorts(reponse) {
	var resultat = reponse.split("@");
  console.log('Actualisation page sort #'+resultat[0]);
  console.log('Modification : '+resultat[6]);
	//alert('ID : '+resultat[0]+', Résultat : '+resultat[1]+', resume : '+resultat[4]);
  /*actualisation des champs de modification du sort */
	$("#nomSort"+resultat[0]).html(resultat[2]); 
	$("#catSort"+resultat[0]).html(resultat[3]);
  $("#domSort"+resultat[0]).html(resultat[4]);
	$("#shortDescSort"+resultat[0]).html(resultat[5]);
	/*actualisation des champs de visualisation du sort */
	afficherSort(resultat[0]);
	$("#modification").hide();
}

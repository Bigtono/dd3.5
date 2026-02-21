// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function afficherRegle(idRegle) {
  //alert('ID '+idRegle);
  console.log('traitement regle #'+idRegle);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageRegle.php',
    data: "regle="+ idRegle,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherRegle');}
	}); 
}

function modifierRegle(idRegle) {
  //alert('ID '+idRegle);
  console.log('Modification regle #'+idRegle);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierRegle.php',
    data: "regle="+ idRegle,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierRegle');}
	}); 
}

function validerModifRegle() {
  console.log('Nom : '+$('#mp_re_nom').val());
  var mp_re_texte = CKEDITOR.instances.mp_re_texte.getData(); // traitement du champ textarea modifié par CKEDITOR
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifRegle.php',
    data: "mp_re_id="+$('#mp_re_id').val()+
    "&mp_re_cr_id="+$('#mp_re_cr_id').val()+
    "&mp_re_re_id="+$('#mp_re_re_id').val()+
		"&mp_re_nom="+encodeURIComponent($('#mp_re_nom').val())+
		"&mp_re_texte="+encodeURIComponent(mp_re_texte),	
		dataType:'text',
    success: actualiserPageRegles,
    error: function() {alert('Erreur validerModifRegle()');}
	});		
}

function actualiserPageRegles(reponse) {
	var resultat = reponse.split("@");
  console.log('Affichage regle modifiée #'+resultat[0]);
  console.log('Requete : '+resultat[1]);
	$("#nomRegle"+resultat[0]).html(resultat[2]); 
	$("#catRegle"+resultat[0]).html(resultat[3]);
	afficherRegle(resultat[0]);
	$("#modification").hide();
}
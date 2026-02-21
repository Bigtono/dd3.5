// JavaScript Document
// DD3.5
//##############################MOTEUR AJAX 1############################################

function masquerLateral() {
	$('#panneau_lateral').hide();
}
function afficherLateral() {
	$('#panneau_lateral').show();
}
function toggle(panneau){
	$('#'+panneau).toggle();
}
function afficherContenu(content) {
  $('.contenuMain').hide();
  $('.contenuMainV').hide();
  $('#'+content).toggle();
}

function togglePlus(panneau) {
  $('.accordion-content').not($(this).next()).slideUp();
  // Toggle le contenu correspondant
  $('#'+panneau).toggle();
}

/******************************************************************************************/
/* affichage/modification des données dans les fenêtres detail-pp et modification */

function actualiserPage(reponse) {
  var resultat = reponse.split("@");
  console.log('Affichage #'+resultat[0]);  
	$("#detail-pp").html(resultat[1]); 
	$("#detail-pp").show('fast');
}

function annulerPageModif() {
	$("#modification").hide('fast');
}

function fermerDetail() {
	$("#detail-pp").hide('fast');
}

function actualiserPageModif(reponse) {
  var resultat = reponse.split("@");
  console.log('Affichage formulaire #'+resultat[0]);
  $("#detail-pp").hide('fast');
	$("#modification").html(resultat[1]); 
	$("#modification").show('fast');
}

/******************************************************************************************/

function suppression(table, prefixe, id) {
	if (confirm('Voulez-vous supprimer l\'enregistrement '+id+' ?')) {
		$.ajax({
			type: 'POST',
			url: 'ajax/ajax-suppression.php',
			data: '&table='+table+'&prefixe='+prefixe+'&id='+id,
			dataType:'text',
			success: afficherSuppression,
			error: function() {alert('Erreur suppression');}
		}); 		
	}	
}
 
function copier(table, prefixe, id) {
	if (confirm('Voulez-vous copier l\'enregistrement '+id+' ?')) {
		//alert('Copie de '+table+', id '+id);
		$.ajax({
			type: 'POST',
			url: 'ajax/ajax-copierPersonnage.php',
			data: '&table='+table+'&prefixe='+prefixe+'&id='+id,
			dataType:'text',
			success: afficherCopier,
			error: function() {alert('Erreur copier');}
		}); 		
	}	
}

function afficherSuppression(reponse) {
	var resultat = reponse.split("@");
  console.log('Suppression #'+resultat[0]);
	$('#'+resultat[0]).hide();
}

function afficherCopier(reponse) {
	var resultat = reponse.split("@");
	//alert('cat =>  : '+resultat[0]+', liste='+resultat[1]);
	$('#message').html(resultat[1]);
}

/********************************************************************************
Joueurs
*********************************************************************************/
// Gestion de la suppression d'un joueur
function supprimerJoueur(id) {
  console.log('Supprimer Joueur #'+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-supprimerJoueur.php',
    data: '&j='+id,
    dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur supprimerJoueur()');}
  });
}
function validerSupprimerJoueur() {
  console.log('Valider suppression Joueur #'+$('#mp_j_id').val());
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerSupprJoueur.php',
    data: "j_id="+$('#mp_j_id').val(),
		dataType:'text',
    success: function(reponse){
      var resultat = reponse.split("@");
      console.log('Liste joueur mise à jour');
      $("#listeJoueurs").html(resultat[1]);
      $("#modification").hide();    
    },
    error: function() {alert('Erreur validerSuppressionJoueur()');}
	});
}

/********************************************************************************
Variables
*********************************************************************************/
// gérer une variable
function modifierVariable(id) {
  //alert('ID '+idSort);
  console.log('Modification Variable #'+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierVariable.php',
    data: "var="+id,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierVariable()');}
	}); 
}

function validerModifVariable() {
  //actualisation des champs de modification du sort
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifVariable.php',
    data: "mp_var_id="+$('#mp_var_id').val()+
    "&mp_var_valeur="+encodeURIComponent($('#mp_var_valeur').val())+
		"&mp_var_cat="+encodeURIComponent($('#mp_var_cat').val())+
    "&mp_var_description="+encodeURIComponent($('#mp_var_description').val()),
		dataType:'text',
    success: function(reponse){
      var resultat = reponse.split("@");
      console.log('Actualisation page variable #'+resultat[0]+', LISTE : '+resultat[1]);
      $("#variables").html(resultat[1]);
      $("#modification").hide();      
    },
    error: function() {alert('Erreur validerModifVariable()');}
	});		
}


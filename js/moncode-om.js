// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function afficherOM(id) {
  //alert('ID '+id);
  console.log('Traitement OM #'+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageOM.php',
    data: "om="+ id,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherOM');}
	}); 
}

function modifierOM(id, typeOM) {
  console.log('Modification OM #'+id+', Catégorie '+typeOM);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierOM.php',
    data: "om="+ id+"&type="+typeOM,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierOM');}
	}); 
}

function validerModifOM() {
  var mp_om_description = CKEDITOR.instances.mp_om_description.getData(); // traitement du champ textarea modifié par CKEDITOR
  //actualisation des champs de modification du sort
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifOM.php',
    data: "mp_om_id="+$('#mp_om_id').val()+
		"&mp_om_nom="+encodeURIComponent($('#mp_om_nom').val())+
		"&mp_om_com_id="+$('#mp_om_com_id').val()+
    "&mp_om_fom_id="+$('#mp_om_fom_id').val()+
    "&mp_om_so_id="+$('#mp_om_so_id').val()+
    "&mp_om_so_niveau="+$('#mp_om_so_niveau').val()+
    "&mp_om_modificateurs="+$('#mp_om_modificateurs').val()+
    "&mp_om_variantes="+$('#mp_om_variantes').val()+
    "&mp_om_visible="+$('#mp_om_visible').val()+
		"&mp_om_description="+encodeURIComponent(mp_om_description)+
		"&mp_om_res_id="+$('#mp_om_res_id').val()+
		"&mp_om_page_source="+encodeURIComponent($('#mp_om_page_source').val()),	
		dataType:'text',
    success: actualiserPageOM,
    error: function() {alert('Erreur validerModifOM()');}
	});		

}

function actualiserPageOM(reponse) {
	var resultat = reponse.split("@");
  console.log('Affichage Modif OM #'+resultat[0]+', Requete '+resultat[1]+', niveau : '+resultat[5]+', Visible : '+resultat[6]);  
	$("#nomOM"+resultat[0]).html(resultat[2]); 
	$("#catOM"+resultat[0]).html(resultat[3]);
  $("#sourceOM"+resultat[0]).html(resultat[4]);
	afficherOM(resultat[0]);
	$("#modification").hide();
}

// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function afficherComp(idComp) {   
	//alert(idComp);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageComp.php',
    data: "comp="+ idComp,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherComp');}
	}); 
}
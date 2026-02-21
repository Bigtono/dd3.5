// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function afficherClasse(id) {
  //alert('ID '+idDon);
  console.log('Affichage Classe #'+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageClasse.php',
    data: "classe="+id,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherClasse()');}
	}); 
}

function afficherCapacite(idcap) {   
	//alert(idcap);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageCapacite.php',
    data: "cap="+ idcap,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherCapacite');}
	}); 
}

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

function modifierCapacite(id) {
  //alert('Cap ID '+id);
  console.log('id : '+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierCapacite.php',
    data: "cap="+id,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierCapacite');}
	});
}

function validerModifCapacite() {
  //alert('Cap '+$('#mp_cap_nom').val());
  console.log('Cap : '+$('#mp_cap_nom').val());  
  var mp_cap_description = CKEDITOR.instances.mp_cap_description.getData(); // traitement du champ textarea modifié par CKEDITOR
  //actualisation des champs de modification du sort
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifCapacite.php',
    data: "mp_cap_id="+$('#mp_cap_id').val()+
		"&mp_cap_nom="+encodeURIComponent($('#mp_cap_nom').val())+
		"&mp_cap_description="+encodeURIComponent(mp_cap_description),	
		dataType:'text',
    success: actualiserCapacite,
    error: function() {alert('Erreur validerModifCapacite()');}
	});		
}

function actualiserCapacite(reponse) {
  //recup du résultat > tableau 
	var resultat = reponse.split("@");
  afficherCapacite(resultat[0]);
  $("#capNom"+resultat[0]).html(resultat[1]);
  $("#capDesc"+resultat[0]).html(resultat[2]);
	$("#modification").hide();
}

function switchCol(zone) {
  if (zone==1) {
    console.log('switch #1');
    $("#avant").hide();
    $("#apres").html('<i class="fa-solid fa-right-long" onClick="switchCol(2)"></i>');
    $(".bba").show();
    $(".save").show();
    $(".capacites").hide();
    $(".sorts").hide(); 
    $(".sn").hide();    
  };
  if (zone==2) {
    console.log('switch #2');
    $("#avant").show();
    $("#avant").html('<i class="fa-solid fa-left-long" onClick="switchCol(1)"></i>');
    $("#apres").show();    
    $("#apres").html('<i class="fa-solid fa-right-long" onClick="switchCol(3)"></i>');
    $(".bba").hide();
    $(".save").hide();
    $(".capacites").show();
    $(".sorts").hide();
    $(".sn").hide();    
  };
  if (zone==3) {
    console.log('switch #3');
    $("#avant").show();
    $("#avant").html('<i class="fa-solid fa-left-long" onClick="switchCol(2)"></i>');
    $("#apres").hide();
    $(".bba").hide();
    $(".save").hide();
    $(".capacites").hide();
    $(".sorts").show();
    $(".sn").show();
  };
 
}
// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function ajouterClassePerso(id) {
  console.log('formulaire nouvelle classe Perso #'+id);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-ajouterClassePerso.php',
    data: "perso="+id,
		dataType:'text',
    success: actualiserDivNouvelleClasse,
    error: function() {alert('Erreur ajouterClassePerso()');}
	}); 
}

function actualiserDivNouvelleClasse(reponse) {
  var resultat = reponse.split("@");
  console.log('Affichage formulaire : '+resultat[0]);
	$("#nouvelleClasse").html(resultat[1]); 
}

function validerAjoutClasse(id) {
  console.log('Ajout Classe : '+$('#mp_cla_id').val()+' Niveau '+$('#mp_cp_niveau').val()+' au perso '+id);  
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutClassePerso.php',
    data: "perso="+id+"&classe="+$('#mp_cla_id').val()+"&niveau="+$('#mp_cp_niveau').val(),	
		dataType:'text',
    success: actualiserDivClassesPerso,
    error: function() {alert('Erreur validerAjoutClasse()');}
	});		
}

function supprimerClassePerso(perso,classe)
{
  console.log('Supprimer Classe #'+classe+" du perso "+perso);  
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-supprimerClassePerso.php',
    data: "perso="+perso+"&classe="+classe,	
		dataType:'text',
    success: actualiserDivClassesPerso,
    error: function() {alert('Erreur supprimerClassePerso()');}
	}); 
}

function actualiserDivClassesPerso(reponse) {
	var resultat = reponse.split("@");
  console.log('Réaffichage des classes du perso #'+resultat[0]+', REQUETE : '+resultat[2]);
  $("#classes").html(resultat[1]); 
}

function majNiveauClassePerso(reponse) {
  var niveau=document.getElementById(reponse).value;
	var classe = reponse;
  console.log('Classe '+classe+', Niveau : '+niveau);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-majNiveauClassePerso.php',
    data: "niveau="+niveau+"&classe="+classe,	
		dataType:'text',
    success: actualiserNiveauClassePerso,
    error: function() {alert('Erreur majNiveauClassePerso()');}
	});   
}

function actualiserNiveauClassePerso(reponse) {
	var resultat = reponse.split("@");
  console.log('MAJ du niveau de classe #'+resultat[0]);
}

// nouvelle fonction
// passage de la fonction jquery accordion dans l'appel ajax
// la fonction n'utilise plus actualiserPageModif dans success:
function gererEquipement(perso) {
  console.log('gererEquipement Personnage #'+perso);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-gererEquipement.php',
    data: "perso="+perso,
		dataType:'text',
    success: function(result) {
      var resultat = result.split("@");
      console.log('Personnage : '+resultat[0]);
      $("#modification").html(resultat[1]);
      $('#accordion').accordion({
        heightStyle: "content",
        active: false,
        collapsible: true
      });
      console.log('Affichage formulaire #');
      $("#modification").show('fast');
    },
    error: function() {alert('Erreur gererEquipement');}
	}); 
}
function ajouterEqt(eqt,perso) {
  const liste = document.getElementById('mod' + eqt);
  let mod = '';
  if (liste) {
    mod = liste.value;
  } else {
    console.warn("Élément 'mod" + eqt + "' introuvable. Valeur mod laissée vide.");
  } 
  const liste2 = document.getElementById('so' + eqt);
  let sort = '';
  if (liste2) {
    sort = liste2.value;
  } else {
    console.warn("Élément 'sort" + eqt + "' introuvable. Valeur sort laissée vide.");
  } 
  console.log('Ajouter Objet magique #'+eqt+' du personnage #'+perso+', mod : '+mod+', sort #'+sort);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutEqt.php',
    data: "perso="+perso+"&eqt="+eqt+"&mod="+mod+"&sort="+sort,
		dataType:'text',
    success: actualiserEqt,
    error: function() {alert('Erreur ajouterEqt()');}
	}); 
}

function afficherEqt(eqt) {
  console.log('Affichage Eqt #'+eqt);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageEqt.php',
    data: "eqt="+eqt,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherEqt()');}
	}); 
}

function modifierEqt(eqt) {
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

function validerModifEqt() {
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
    success: function(reponse) {
      var resultat = reponse.split("@");
      console.log('Affichage regle modifiée #'+resultat[0]);
      console.log('Requete : '+resultat[1]);
      $("#nomRegle"+resultat[0]).html(resultat[2]); 
      $("#catRegle"+resultat[0]).html(resultat[3]);
      afficherRegle(resultat[0]);
      $("#modification").hide();
    },
    error: function() {alert('Erreur validerModifRegle()');}
	});		
}

function supprimerEqt(eqt,perso) {
  console.log('Supprimer Objet magique #'+eqt+' du perso #'+perso);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerSupprEqt.php',
    data: "eqt="+eqt+"&perso="+perso,
		dataType:'text',
    success: actualiserEqt,
    error: function() {alert('Erreur supprimerEqt()');}
	}); 
}

function actualiserEqt(reponse) {
  var resultat = reponse.split("@");
  console.log('Perso : '+resultat[2]+', mod : '+resultat[3]+', sort #'+resultat[4]+', Objet : '+resultat[0]);
  $("#listeOM").html(resultat[1]);      
}




function afficherNote(note, accreditation) {
  console.log('Affichage Note #'+note+', accreditation : '+accreditation);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageNote.php',
    data: "note="+note+"&accreditation="+accreditation,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherNote');}
	}); 
}

function modifierNote(note,perso) {
  console.log('Ajouter Note #'+note);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierNote.php',
    data: "note="+note+"&perso="+perso,
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur modifierNote');}
	}); 
}

function supprimerNote(note, perso) {
  console.log('Supprimer Note #'+note);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerSupprNote.php',
    data: "perso="+perso+"&note="+note,
		dataType:'text',
    success: actualiserNote,
    error: function() {alert('Erreur supprimerNote');}
	}); 
}

function validerModifNote(perso) {
  let diffusion="" ;
  //$("input[type='checkbox']:checked").each(function() {
  $(".diffusion").each(function() {
      if ($(this).val()>0) {
        //diffusion=diffusion+$(this).attr('id');
        diffusion=diffusion+$(this).attr('id')+'a'+$(this).val();
      }
    }
  ); 
  console.log('Diffusion : '+diffusion+' Cumulatif : '+$('#mp_no_cumulatif').val()); 
  var mp_no_texte_basique = CKEDITOR.instances.mp_no_texte_basique.getData(); // traitement du champ textarea modifié par CKEDITOR
  var mp_no_texte_intermediaire = CKEDITOR.instances.mp_no_texte_intermediaire.getData(); // traitement du champ textarea modifié par CKEDITOR
  var mp_no_texte_avance = CKEDITOR.instances.mp_no_texte_avance.getData(); // traitement du champ textarea modifié par CKEDITOR
  var mp_no_texte_expert = CKEDITOR.instances.mp_no_texte_expert.getData(); // traitement du champ textarea modifié par CKEDITOR
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifNote.php',
    data: "mp_no_id="+$('#mp_no_id').val()+
		"&mp_no_nom="+encodeURIComponent($('#mp_no_nom').val())+
		"&mp_no_tyno_id="+$('#mp_no_tyno_id').val()+
    "&mp_no_cumulatif="+$('#mp_no_cumulatif').val()+
		"&mp_no_texte_basique="+encodeURIComponent(mp_no_texte_basique)+
    "&mp_no_texte_intermediaire="+encodeURIComponent(mp_no_texte_intermediaire)+
    "&mp_no_texte_avance="+encodeURIComponent(mp_no_texte_avance)+
    "&mp_no_texte_expert="+encodeURIComponent(mp_no_texte_expert)+
    "&diffusion="+diffusion,
		dataType:'text',
    success: function(reponse) {
      var resultat = reponse.split("@");
      console.log('Actualiser Note #'+resultat[0]+', notes : '+resultat[1]);
      //console.log('debug : '+resultat[3]);
      $("#no"+resultat[0]).html(resultat[1]);
      $("#modification").hide();
      afficherNote(resultat[0]);      
    },
    error: function() {alert('Erreur validerModifNote()');}
	});		
}

function diffuser(note, perso) {
  let diffusion="" ;
  $("input[type='checkbox']:checked").each(function() {
      diffusion=diffusion+"@"+$(this).attr('id');
    }
  );          
  console.log(diffusion); 
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-diffusionNote.php',
    data: "diffusion="+diffusion,	
		dataType:'text',
    success: actualiserNote,
    error: function() {alert('Erreur diffuser()');}
	});		
}

function ajouterGrimoire() {
  //alert('ID '+idDon);
  console.log('Ajout Grimoire');
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-ajouterGrimoire.php',
    data:'grimoire=n',
		dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur ajouterGrimoire()');}
	}); 
}

function validerAjoutGrimoire() {
  console.log('Valider Ajout Grimoire. Format #'+$('#mp_gr_grf_id').val()+', Classe #'+$('#mp_gr_cla_id').val()+', Perso #'+$('#mp_gr_pe_id').val());
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutGrimoire.php',
    data: "mp_gr_nom="+encodeURIComponent($('#mp_gr_nom').val())+
    "&mp_gr_grf_id="+$('#mp_gr_grf_id').val()+
		"&mp_gr_pe_id="+$('#mp_gr_pe_id').val()+
    "&mp_gr_cla_id="+$('#mp_gr_cla_id').val(),	
		dataType:'text',
    success: actualiserPageGrimoire,
    error: function() {alert('Erreur validerPageGrimoire()');}
	});		
}

function actualiserPageGrimoire(reponse) {
  //recup du résultat > tableau 
	var resultat = reponse.split("@");
	$("#ListeGrimoires").html(resultat[1]); 
	$("#modification").hide();
}
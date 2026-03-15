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
  console.log('RÃ©affichage des classes du perso #'+resultat[0]+', REQUETE : '+resultat[2]);
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
    console.warn("Ã‰lÃ©ment 'mod" + eqt + "' introuvable. Valeur mod laissÃ©e vide.");
  } 
  const liste2 = document.getElementById('so' + eqt);
  let sort = '';
  if (liste2) {
    sort = liste2.value;
  } else {
    console.warn("Ã‰lÃ©ment 'sort" + eqt + "' introuvable. Valeur sort laissÃ©e vide.");
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
  var mp_re_texte = CKEDITOR.instances.mp_re_texte.getData(); // traitement du champ textarea modifiÃ© par CKEDITOR
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
      console.log('Affichage regle modifiÃ©e #'+resultat[0]);
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
  if (window.NoteActions && typeof window.NoteActions.afficherNote === 'function') return window.NoteActions.afficherNote(note, accreditation, 0);
  alert('Module notes indisponible');
}

function toggleAttributionNoteCampagne(noteId, checkboxEl, event) {
  if (event) event.stopPropagation();
  if (!checkboxEl) return;

  var checked = checkboxEl.checked ? 1 : 0;
  checkboxEl.disabled = true;

  $.ajax({
    type: 'POST',
    url: 'ajax/note_campagne_toggle.php',
    data: "note_id=" + noteId + "&checked=" + checked,
    dataType: 'json',
    success: function(reponse) {
      if (reponse && reponse.success) {
        checkboxEl.checked = parseInt(reponse.checked, 10) === 1;
      } else {
        checkboxEl.checked = (checked === 1) ? false : true;
        alert((reponse && reponse.message) ? reponse.message : 'Erreur attribution note/campagne.');
      }
    },
    error: function() {
      checkboxEl.checked = (checked === 1) ? false : true;
      alert('Erreur toggleAttributionNoteCampagne()');
    },
    complete: function() {
      checkboxEl.disabled = false;
    }
  });
}

function modifierNote(note,perso) {
  if (window.NoteActions && typeof window.NoteActions.modifierNote === 'function') return window.NoteActions.modifierNote(note, perso);
  alert('Module notes indisponible');
}

function supprimerNote(note, perso) {
  if (window.NoteActions && typeof window.NoteActions.supprimerNote === 'function') return window.NoteActions.supprimerNote(note, perso);
  alert('Module notes indisponible');
}

function validerModifNote(perso) {
  if (window.NoteActions && typeof window.NoteActions.validerModifNote === 'function') return window.NoteActions.validerModifNote(perso);
  alert('Module notes indisponible');
}

function actualiserNote(reponse) {
  if (window.NoteActions && typeof window.NoteActions.actualiserNote === 'function') return window.NoteActions.actualiserNote(reponse);
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
  //recup du rÃ©sultat > tableau 
	var resultat = reponse.split("@");
	$("#ListeGrimoires").html(resultat[1]); 
	$("#modification").hide();
}

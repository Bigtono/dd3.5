// JavaScript Document
//##############################MOTEUR AJAX 1############################################

// gestion de l'affichage des onglets de monstres dans la page rencontre
function initDetailRencontre() {
    const container = document.getElementById('detailRencontre');
    if (!container) return;

    const monsters = Array.from(container.querySelectorAll('.monstre'));

    if (monsters.length === 0) return;

    if (monsters.length === 1) {
      monsters[0].classList.add('is-active');
      monsters[0].removeAttribute('hidden');
      return;
    }

    const menu = container.querySelector('.menu_main');
    const buttons = menu ? Array.from(menu.querySelectorAll('.btMain')) : [];

    if (!menu || buttons.length === 0) {
      monsters.forEach(m => m.classList.remove('is-active'));
      monsters[0].classList.add('is-active');
      monsters[0].removeAttribute('hidden');
      return;
    }

    let select = container.querySelector('#menuDropdown');
    if (!select) {
      select = document.createElement('select');
      select.id = 'menuDropdown';
      menu.parentNode.insertBefore(select, menu);
    }

    const enableSelect = buttons.length > 1;

    if (enableSelect) {
      select.innerHTML = '';
      const optDefault = document.createElement('option');
      optDefault.value = '';
      optDefault.textContent = 'Choisissez un monstre…';
      select.appendChild(optDefault);

      buttons.forEach(btn => {
        const opt = document.createElement('option');
        opt.value = btn.getAttribute('data-key');
        opt.textContent = btn.textContent.trim();
        select.appendChild(opt);
      });
    } else {
      select.style.display = 'none';
    }

    function showMonster(key) {
      monsters.forEach(m => m.classList.remove('is-active'));
      buttons.forEach(b => b.classList.remove('is-active'));

      const target = container.querySelector('#' + CSS.escape(key));
      if (target) {
        target.classList.add('is-active');
        target.removeAttribute('hidden');
      }

      const btn = buttons.find(b => b.getAttribute('data-key') === key);
      if (btn) btn.classList.add('is-active');

      if (enableSelect) select.value = key;
    }

    const firstKey = buttons[0].getAttribute('data-key');
    monsters.forEach(m => m.removeAttribute('hidden'));
    showMonster(firstKey);

    menu.addEventListener('click', function (e) {
      const btn = e.target.closest('.btMain');
      if (!btn) return;
      const key = btn.getAttribute('data-key');
      if (key) showMonster(key);
    });

    if (enableSelect) {
      select.addEventListener('change', function () {
        if (this.value) showMonster(this.value);
      });
    }
  }

function afficherDetailRencontre(idRencontre) {
  //alert('ID '+idRegle);
  console.log('traitement rencontre #'+idRencontre);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageDetailRencontre.php',
    data: "rencontre="+ idRencontre,
		dataType:'text',
    success: actualiserPage,
    error: function() {alert('Erreur afficherDetailRencontre()');}
	}); 
}

function majChapitre(value) {
  var scenario=encodeURIComponent(value);
  console.log('Scénario #'+scenario);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-majChapitre.php',
    data: "scenario="+scenario,
		dataType:'text',
    success: function(reponse){
      var resultat = reponse.split("@");
      console.log('Scenario #'+resultat[0]+', LISTE : '+resultat[1]);
      $("#listeChapitres").html(resultat[1]);
      $("#chapitres").show('fast');
    },
    error: function() {alert('Erreur majChapitre()');}
	});   
}

function ajoutMonstreRencontre(rencontre) {
  var monstre=$('#mp_nouveau_monstre').val();
  var nb=$('#mp_nb_monstre').val();
  console.log('Selection Monstre #'+monstre+', nb : '+nb+' dans Rencontre #'+rencontre);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerAjoutMontreRencontre.php',
    data: 'rencontre='+rencontre+'&monstre='+monstre+'&nb='+nb,
		dataType:'text',
    success: function(reponse){
      var resultat = reponse.split("@");
      console.log('Ajout Monstre #'+resultat[1]+' nb : '+resultat[2]+' dans rencontre #'+resultat[0]);
      $("#detailRencontre").html(resultat[3]);
      initDetailRencontre();
    },
    error: function() {alert('Erreur ajoutMonstreRencontre()');}
	});   
}

// Gestion de la suppression d'un combattant
function supprimerMonstreRencontre(rem) {
  var monstre=$('#mp_rem_mo_id').val();
  var rencontre=$('#mp_rem_re_id').val();
  console.log('Supprimer MonstreRencontre #'+rem+' Monstre #'+monstre+', rencontre #'+rencontre);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-supprimerMonstreRencontre.php',
    data: '&rem='+rem,
    dataType:'text',
    success: actualiserPageModif,
    error: function() {alert('Erreur supprimerCbt');}
  });
}
function validerSupprimerMonstreRencontre() {
  var rem=$('#mp_rem_id').val();
  var monstre=$('#mp_rem_mo_id').val();
  var rencontre=$('#mp_rem_re_id').val();  
  
  console.log('Valider suppression Monstre #'+monstre+', rencontre #'+rencontre+', rem #'+rem);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerSupprMonstreRencontre.php',
    data: 'rem='+rem+'&rencontre='+rencontre+'&monstre='+monstre,
		dataType:'text',
    success: function(reponse){
      var resultat = reponse.split("@");
      console.log('Suppression Monstre #'+resultat[1]+' dans rencontre #'+resultat[0]);
      $("#detailRencontre").html(resultat[2]);
      initDetailRencontre();
      $("#modification").hide();
    },
    error: function() {alert('Erreur validerSupprimerMonstreRencontre()');}
	});
}




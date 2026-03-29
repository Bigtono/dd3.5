// JavaScript Document
//##############################MOTEUR AJAX 1############################################
function afficherDon(idDon) {
  //alert('ID '+idDon);
  console.log('Affichage don #' + idDon);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-affichageDon.php',
    data: "don=" + idDon,
    dataType: 'text',
    success: actualiserPage,
    error: function () { alert('Erreur afficherDon'); }
  });
}
function modifierDon(idDon) {
  //alert('ID '+idDon);
  console.log('Modification don #' + idDon);
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-modifierDon.php',
    data: "don=" + idDon,
    dataType: 'text',
    success: actualiserPageModif,
    error: function () { alert('Erreur modifierDon'); }
  });
}

function validerModifDon() {
  var mp_do_texte = '';
  if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.mp_do_texte) {
    mp_do_texte = CKEDITOR.instances.mp_do_texte.getData();
  } else {
    mp_do_texte = $('#mp_do_texte').val() || '';
  } // traitement du champ textarea modifie

  //actualisation des champs de modification du sort
  $.ajax({
    type: 'POST',
    url: 'ajax/ajax-validerModifDon.php',
    data: "mp_do_id=" + $('#mp_do_id').val() +
      "&mp_do_nom=" + encodeURIComponent($('#mp_do_nom').val()) +
      "&mp_do_dado_id=" + $('#mp_do_dado_id').val() +
      "&mp_do_texte=" + encodeURIComponent(mp_do_texte) +
      "&mp_do_resume=" + encodeURIComponent($('#mp_do_resume').val()) +
      "&mp_do_res_id=" + $('#mp_do_res_id').val(),
    dataType: 'text',
    success: actualiserPageDons,
    error: function () { alert('Erreur validerModifDon()'); }
  });
}

function actualiserPageDons(reponse) {
  //recup du résultat > tableau 
  var resultat = reponse.split("@");
  //alert('ID : '+resultat[0]+', Résultat : '+resultat[1]+', resume : '+resultat[4]);
  //alert('LS : '+resultat[5]);
  $("#nomDon" + resultat[0]).html(resultat[2]);
  $("#catDon" + resultat[0]).html(resultat[3]);
  $("#shortDescSort" + resultat[0]).html(resultat[4]);
  $("#sourceDon" + resultat[0]).html(resultat[5]);
  afficherDon(resultat[0]);
  $("#modification").hide();
}

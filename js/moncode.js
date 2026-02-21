// JavaScript Document
//##############################MOTEUR AJAX 1############################################

function onOff(param) {
  console.log('Paramétrage #'+param);
	$.ajax({
    type: 'POST',
    url: 'ajax/ajax-parametrage.php',
    data: "param="+param,
		dataType:'text',
    success: function(result) {
      var resultat = result.split("@");
      console.log('Modif Paramétrage #'+resultat[0]+', valeur : '+resultat[1]);
      $("#"+resultat[0]).html(resultat[1]);
    },
    error: function() {alert('Erreur onOff()');}
	});  
}

//**************************************************************************************
// Fonction pour activer un bouton de retour en haut de page quand on arrive en base de page
// Execute a function when the window is being scrolled
window.onscroll = function () { scrollFunction() };

// When the user scrolls down 20px from the top of the document, show the button
function scrollFunction() {
  // Get the button
    var mybutton = document.getElementById("scrollToTopButton");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        mybutton.style.opacity = 1;
        mybutton.style.visibility = "visible";
    } else {
        mybutton.style.opacity = 0;
        mybutton.style.visibility = "hidden";
    }
}
// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
//**************************************************************************************
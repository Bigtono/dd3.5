// JavaScript Document
$(document).ready(function() {
 
    // Sélectionner tous les liens ayant l'attribut rel valant tooltip
    $('a[rel=tooltip]').mouseover(function(e) {
 
        // Récupérer la valeur de l'attribut title et l'assigner a une variable
        var tip = $(this).attr('title');   
 
        // Supprimer la valeur de l'attribut title pour éviter l'infobulle native
        $(this).attr('title','');
 
        // Insérer notre infobulle avec son texte dans la page
        $(this).append('<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">' + tip + '</div><div class="tipFooter"></div></div>');    
 
        // Ajuster les coordonnées de l'infobulle
        $('#tooltip').css('top', e.pageY + 10 );
        $('#tooltip').css('left', e.pageX + 20 );
 
        // Faire apparaitre l'infobulle avec un effet fadeIn
        $('#tooltip').fadeIn('500');
        $('#tooltip').fadeTo('10',0.8);
 
    }).mousemove(function(e) {
 
        // Ajuster la position de l'infobulle au déplacement de la souris
        $('#tooltip').css('top', e.pageY + 10 );
        $('#tooltip').css('left', e.pageX + 20 );
 
    }).mouseout(function() {
 
        // Réaffecter la valeur de l'attribut title
        $(this).attr('title',$('.tipBody').html());
 
        // Supprimer notre infobulle
        $(this).children('div#tooltip').remove();
 
    });
 
});
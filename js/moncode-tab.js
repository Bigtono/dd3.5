$(function() {
	$('#onglets').css('display', 'block');
	$('#onglets').click(function(event) {
		var actuel = event.target;
		if (!/li/i.test(actuel.nodeName) || actuel.className.indexOf('actif') > -1) {
			alert(actuel.nodeName)
			return;
		}
		$(actuel).addClass('actif').siblings().removeClass('actif');
		setDisplay();
	});
	function setDisplay() {
		var modeAffichage;
		$('#onglets li').each(function(rang) {
			modeAffichage = $(this).hasClass('actif') ? '' : 'none';
			$('.item').eq(rang).css('display', modeAffichage);
		});
	}
	setDisplay();
});
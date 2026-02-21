document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll('.sort-bulle').forEach(bulle => {

    const compteur = bulle.querySelector('.compteur');
    if (!compteur) return;

    compteur.addEventListener('click', e => {
      e.stopPropagation(); // 🔒 empêche de remonter au parent
      maj(bulle, -1);
    });

    compteur.addEventListener('contextmenu', e => {
      e.preventDefault();
      e.stopPropagation();
      maj(bulle, 1);
    });

  });

});

function maj(b, delta){
  const container = document.querySelector('[data-perso-id]');
  const persoId = container ? container.dataset.persoId : null;

  if (!persoId){
    console.error('ID personnage introuvable');
    return;
  }
  console.log('personnage=' + persoId +
          '&id=' + b.dataset.id +
          '&delta=' + delta);

  fetch('ajax/grimoire_update_lance.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'personnage=' + persoId +
          '&id=' + b.dataset.id +
          '&delta=' + delta
  })
  .then(r => {
    if (!r.ok){
      throw new Error('HTTP ' + r.status);
    }
    return r.json();
  })
  .then(d => {
    console.log('Réponse AJAX', d);

    if (d.success === true){
      const span = b.querySelector('.charges');
      if (span){
        span.textContent = d.val;
      }
    } else {
      console.error('Erreur métier:', d.error);
    }
  })
  .catch(err => {
    console.error('Erreur AJAX:', err);
  });
}



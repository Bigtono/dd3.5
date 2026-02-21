document.addEventListener('DOMContentLoaded', () => {
  console.log('JS chargé');
  
  document.querySelectorAll('.sort-bulle').forEach(b => {
    console.log('bulle détectée', b);
    b.addEventListener('click', () => {
      console.log('CLICK GAUCHE');
      maj(b, -1);
    });
    b.addEventListener('contextmenu', e => {
      e.preventDefault();
      console.log('CLICK DROIT');
      maj(b, 1);
    });
  });

  document.querySelectorAll('.tab-btn').forEach(b => {
    b.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn,.tab-content')
        .forEach(e => e.classList.remove('active'));
      b.classList.add('active');
      document.getElementById(b.dataset.tab).classList.add('active');
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



document.addEventListener('DOMContentLoaded', () => {

  const page = document.querySelector('.page-content');
  if (!page) return;

  const persoId = page.dataset.persoId;
  const menu = document.getElementById('menu-sort');
  let currentBulle = null;

  /* =========================
     ATTACHEMENT DES ÉVÉNEMENTS
     ========================= */

  document.querySelectorAll('.sort-bulle').forEach(bulle => {

    const nom = bulle.querySelector('.nom');
    const compteur = bulle.querySelector('.compteur');

    /* clic gauche sur le nom : afficher le sort */
    nom.addEventListener('click', e => {
      e.stopPropagation();
      afficherSort(bulle.dataset.sortId);
    });

    /* clic droit sur le nom : menu contextuel */
    nom.addEventListener('contextmenu', e => {
      e.preventDefault();
      e.stopPropagation();
      currentBulle = bulle;
      ouvrirMenu(e.pageX, e.pageY, bulle);
    });

    /* clic gauche sur le compteur : décrémenter pes_memorise */
    compteur.addEventListener('click', e => {
      e.stopPropagation();
      if (parseInt(bulle.dataset.memo, 10) > 0){
        decrementMemo(bulle);
      }
    });
    /* clic droit sur le compteur : incrémenter */
    compteur.addEventListener('contextmenu', e => {
      e.preventDefault();
      e.stopPropagation();
      incrementMemo(bulle);
    });    
    

  });

  /* clic ailleurs : fermeture du menu */
  document.addEventListener('click', () => fermerMenu());

  /* =========================
     MENU CONTEXTUEL
     ========================= */

  function ouvrirMenu(x, y, bulle){
    if (!menu) return;

    const ul = menu.querySelector('ul');
    ul.innerHTML = '';

    const connu = parseInt(bulle.dataset.connu, 10);
    const memo  = parseInt(bulle.dataset.memo, 10);

    if (connu === 0){
      ajouterAction('Apprendre le sort', () => toggleConnu(bulle));
    } else {
      ajouterAction('Oublier le sort', () => toggleConnu(bulle));
      if (memo === 0){
        ajouterAction('Mémoriser le sort', () => incrementMemo(bulle));
      }
    }

    ajouterAction('Retirer le sort', () => supprimerSort(bulle));

    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
    menu.classList.remove('hidden');
  }

  function ajouterAction(label, action){
    const li = document.createElement('li');
    li.textContent = label;
    li.addEventListener('click', () => {
      action();
      fermerMenu();
    });
    menu.querySelector('ul').appendChild(li);
  }

  function fermerMenu(){
    if (menu) menu.classList.add('hidden');
  }

  /* =========================
     CONTEXTE COMMUN AJAX
     ========================= */

  function getContext(bulle){
    return {
      perso: persoId,
      sort: bulle.dataset.sortId
    };
  }

  /* =========================
     ACTIONS AJAX
     ========================= */

  function toggleConnu(bulle){
    const c = getContext(bulle);

    fetch('ajax/sort_toggle_connu.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `personnage=${c.perso}&sort=${c.sort}`
    })
    .then(r => r.json())
    .then(d => {
      const connu = parseInt(d.connu, 10);
      bulle.dataset.connu = connu;

      bulle.classList.toggle('connu', connu === 1);
      bulle.classList.toggle('inconnu', connu === 0);

      if (connu === 0){
        bulle.dataset.memo = 0;
        bulle.querySelector('.compteur').textContent = '';
        bulle.classList.remove('memorise');
      }
    });
  }

  function incrementMemo(bulle){
    const c = getContext(bulle);

    fetch('ajax/sort_memo_inc.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `personnage=${c.perso}&sort=${c.sort}`
    })
    .then(r => r.json())
    .then(d => {
      const memo = parseInt(d.memo, 10);
      bulle.dataset.memo = memo;
      bulle.querySelector('.compteur').textContent = memo;
      bulle.classList.add('memorise');
      bulle.classList.add('connu');
      bulle.classList.remove('inconnu');
      bulle.dataset.connu = 1;
    });
  }

  function decrementMemo(bulle){
    const c = getContext(bulle);

    fetch('ajax/sort_memo_dec.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: `personnage=${c.perso}&sort=${c.sort}`
    })
    .then(r => r.json())
    .then(d => {
      const memo = parseInt(d.memo, 10);
      bulle.dataset.memo = memo;
      bulle.querySelector('.compteur').textContent = memo > 0 ? memo : '';
      bulle.classList.toggle('memorise', memo > 0);
    });
  }

  function supprimerSort(bulle){

    var nomEl = bulle.querySelector('.nom');
    var nom = nomEl ? nomEl.textContent : 'ce sort';

    if (!confirm('Retirer définitivement le sort « ' + nom + ' » du grimoire ?')){
      return;
    }

    var c = getContext(bulle);

    fetch('ajax/sort_delete.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'personnage=' + c.perso + '&sort=' + c.sort
    })
    .then(function(r){ return r.json(); })
    .then(function(){
      bulle.remove();
    });
  }

});

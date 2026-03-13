<?
include("include/session.php");
include("include/dblib.inc.php");
include("connexion.php");
include("include/diverslib.inc.php");
include("include/date.inc.php");

$personnagePageKey = 'possessions';
include("include/personnage_bootstrap.php");
?>
<!doctype html>
<html>

<head>
  <? include("include/head.php"); ?>
  <script type='text/javascript' src='js/moncode-om.js'></script>
  <script type='text/javascript' src='js/moncode-personnages.js'></script>
</head>

<body>
  <div id="page">
    <? include("include/header.php"); ?>
    <? include("include/menu.php"); ?>
    <div class="wrapper">
      <? include('include/ariane.php'); ?>
      <div class="titreAction">
        <div class="titreA"><? echo htmlspecialchars($personnageNom); ?></div>
        <div><a class="personnage-retour lien" href="<? echo htmlspecialchars($retourFicheUrl); ?>" title="Retour a la fiche"><i class="fa-solid fa-arrow-left"></i></a></div>
      </div>

      <? include("include/personnage_nav.php"); ?>
      <? include('include/insert/' . $_SESSION['rulesetRep'] . '/personnage_possessions.php'); ?>

      <p class="mb50">&nbsp;</p>
      <button onclick="topFunction()" id="scrollToTopButton" title="Haut de page"><i class="fas fa-chevron-up"></i></button>
    </div>
    <div id="modification"></div>
    <div id="detail-pp"></div>
  </div>
  <script>
    (function() {
      const ENDPOINT = 'ajax/sorts_suggest.php';
      const timers = new WeakMap();

      function getPartsFromWrap(wrap) {
        const input = wrap.querySelector('.js_sort_search');
        const panel = wrap.querySelector('.ac-panel');
        const hidden = wrap.querySelector('.js_so_id');
        return {
          input,
          panel,
          hidden
        };
      }

      function render(wrap, items) {
        const {
          panel
        } = getPartsFromWrap(wrap);
        if (!panel) return;
        panel.innerHTML = '';
        if (!items || !items.length) {
          panel.hidden = true;
          return;
        }
        items.forEach((it, idx) => {
          const div = document.createElement('div');
          div.className = 'ac-item' + (idx === 0 ? ' is-active' : '');
          div.textContent = it.label;
          div.dataset.id = it.id;
          panel.appendChild(div);
        });
        panel.hidden = false;
      }

      function selectItem(wrap, item) {
        const {
          input,
          hidden,
          panel
        } = getPartsFromWrap(wrap);
        if (input) input.value = item.label;
        if (hidden) hidden.value = item.id;
        if (panel) panel.hidden = true;
      }

      async function search(wrap, q) {
        try {
          const url = ENDPOINT + '?q=' + encodeURIComponent(q);
          const res = await fetch(url, {
            headers: {
              'Accept': 'application/json'
            }
          });
          if (!res.ok) throw new Error('HTTP ' + res.status);
          const data = await res.json();
          render(wrap, Array.isArray(data) ? data : (data.results || []));
        } catch (err) {
          console.error('Autocomplete fetch error:', err);
          render(wrap, []);
        }
      }

      document.addEventListener('input', (e) => {
        if (!e.target.matches('.js_sort_search')) return;
        const wrap = e.target.closest('.ac-wrap');
        if (!wrap) return;
        const {
          hidden
        } = getPartsFromWrap(wrap);
        if (hidden) hidden.value = '';

        const q = e.target.value.trim();
        clearTimeout(timers.get(wrap));
        if (q.length < 2) {
          render(wrap, []);
          return;
        }
        const t = setTimeout(() => search(wrap, q), 180);
        timers.set(wrap, t);
      });

      document.addEventListener('keydown', (e) => {
        if (!e.target.matches('.js_sort_search')) return;
        const wrap = e.target.closest('.ac-wrap');
        if (!wrap) return;
        const {
          panel
        } = getPartsFromWrap(wrap);
        if (!panel || panel.hidden) return;

        const items = Array.from(panel.querySelectorAll('.ac-item'));
        if (!items.length) return;

        let idx = items.findIndex(i => i.classList.contains('is-active'));
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          items[idx]?.classList.remove('is-active');
          idx = (idx + 1) % items.length;
          items[idx].classList.add('is-active');
          items[idx].scrollIntoView({
            block: 'nearest'
          });
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          items[idx]?.classList.remove('is-active');
          idx = (idx - 1 + items.length) % items.length;
          items[idx].classList.add('is-active');
          items[idx].scrollIntoView({
            block: 'nearest'
          });
        } else if (e.key === 'Enter') {
          e.preventDefault();
          const it = items[idx];
          if (it) selectItem(wrap, {
            id: it.dataset.id,
            label: it.textContent
          });
        } else if (e.key === 'Escape') {
          panel.hidden = true;
        }
      });

      document.addEventListener('mousedown', (e) => {
        const item = e.target.closest('.ac-item');
        if (!item) return;
        const panel = item.closest('.ac-panel');
        if (!panel) return;
        const wrap = panel.closest('.ac-wrap');
        if (!wrap) return;
        selectItem(wrap, {
          id: item.dataset.id,
          label: item.textContent
        });
      });

      document.addEventListener('click', (e) => {
        document.querySelectorAll('.ac-panel').forEach(p => {
          if (!p.contains(e.target) && !p.closest('.ac-wrap')?.contains(e.target)) {
            p.hidden = true;
          }
        });
      });
    })();
  </script>
</body>

</html>
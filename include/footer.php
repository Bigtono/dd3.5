<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.sortable-list[data-global-sort="1"]').forEach(function (listEl) {
    const headers = listEl.querySelectorAll('.list-header .col[data-sort-field]');

    headers.forEach(function (headerEl) {
      headerEl.addEventListener('click', function () {
        const field = headerEl.dataset.sortField;
        if (!field) return;

        const url = new URL(window.location.href);

        const currentSort  = url.searchParams.get('sort')  || '';
        const currentOrder = url.searchParams.get('order') || 'asc';

        let newOrder = 'asc';
        if (currentSort === field && currentOrder === 'asc') {
          newOrder = 'desc';
        }

        url.searchParams.set('sort', field);
        url.searchParams.set('order', newOrder);

        // On revient à la page 1 dès qu'on change le tri
        url.searchParams.set('page', '1');

        window.location.href = url.toString();
      });
    });
  });
});
</script>
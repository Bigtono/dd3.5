CKEDITOR.plugins.add('insertsort', {
  icons: 'insertsort',
  init: function (editor) {
    editor.addCommand('insertSortCommand', {
      exec: function (editor) {
        if (document.getElementById('sortPopup')) return;

        const popup = document.createElement('div');
        popup.id = 'sortPopup';
        popup.style.position = 'fixed';
        popup.style.top = '20%';
        popup.style.left = '50%';
        popup.style.transform = 'translateX(-50%)';
        popup.style.width = '300px';
        popup.style.background = 'white';
        popup.style.border = '1px solid #ccc';
        popup.style.padding = '20px';
        popup.style.boxShadow = '0 0 10px rgba(0,0,0,0.5)';
        popup.style.zIndex = 9999;

        popup.innerHTML = `
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
          <i class="fa fa-close close-btn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-size:18px;color:#888;" onclick="document.getElementById('sortPopup').remove();"></i>
          <h3>Choisir un sort</h3>
          <select id="listeSorts" style="width:100%;margin-top:10px;"></select>
          <button id="insertSortBtn" style="width:100%;margin-top:10px;">Insérer</button>
        `;
        document.body.appendChild(popup);

        fetch('/dd3.5/ckeditor/plugins/insertsort/sort_selector.php')
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById('listeSorts');
            data.forEach(sort => {
              const option = document.createElement('option');
              option.value = sort.so_id;
              option.textContent = sort.so_nom;
              select.appendChild(option);
            });
          });

        document.getElementById('insertSortBtn').addEventListener('click', () => {
          const select = document.getElementById('listeSorts');
          const so_id = select.value;
          const selectedText = editor.getSelection().getSelectedText() || 'Sort';
          const span = `<span class="lien" onclick="afficherSort(${so_id})">${selectedText}</span>`;
          editor.insertHtml(span);
          document.getElementById('sortPopup').remove();
        });
      }
    });

    editor.ui.addButton('InsertSort', {
      label: 'Insérer un lien vers un sort',
      command: 'insertSortCommand',
      toolbar: 'insert',
      icon: this.path + 'icons/insertsort.png' // ← obligatoire si PNG
    });
    
  }
});

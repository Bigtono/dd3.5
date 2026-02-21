CKEDITOR.plugins.add('insertcapa', {
  icons: 'insertcapa',
  init: function (editor) {
    editor.addCommand('insertCapaCommand', {
      exec: function (editor) {
        if (document.getElementById('capaPopup')) return;

        const popup = document.createElement('div');
        popup.id = 'capaPopup';
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
          <i class="fa fa-close close-btn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-size:18px;color:#888;" onclick="document.getElementById('capaPopup').remove();"></i>
          <h3>Choisir une capacité spéciale</h3>
          <select id="listeCapacites" style="width:100%;margin-top:10px;"></select>
          <button id="insertCapaBtn" style="width:100%;margin-top:10px;">Insérer</button>
        `;
        document.body.appendChild(popup);

        fetch('/dd3.5/ckeditor/plugins/insertcapa/capa_selector.php')
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById('listeCapacites');
            data.forEach(capa => {
              const option = document.createElement('option');
              option.value = capa.cap_id;
              option.textContent = capa.cap_nom;
              select.appendChild(option);
            });
          });

        document.getElementById('insertCapaBtn').addEventListener('click', () => {
          const select = document.getElementById('listeCapacites');
          const cap_id = select.value;
          const selectedText = editor.getSelection().getSelectedText() || 'Capacite';
          const span = `<span class="lien" onclick="afficherCapacite(${cap_id})">${selectedText}</span>`;
          editor.insertHtml(span);
          document.getElementById('capaPopup').remove();
        });
      }
    });

    editor.ui.addButton('InsertCapa', {
      label: 'Insérer un lien vers une capacité spéciale',
      command: 'insertCapaCommand',
      toolbar: 'insert',
      icon: this.path + 'icons/insertcapa.png' // ← obligatoire si PNG
    });
    
  }
});

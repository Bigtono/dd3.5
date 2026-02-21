CKEDITOR.plugins.add('insertom', {
  icons: 'insertom',
  init: function (editor) {
    editor.addCommand('insertomCommand', {
      exec: function (editor) {
        if (document.getElementById('omPopup')) return;

        const popup = document.createElement('div');
        popup.id = 'omPopup';
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
          <i class="fa fa-close close-btn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-size:18px;color:#888;" onclick="document.getElementById('omPopup').remove();"></i>
          <h3>Choisir un om</h3>
          <select id="listeoms" style="width:100%;margin-top:10px;"></select>
          <button id="insertomBtn" style="width:100%;margin-top:10px;">Insérer</button>
        `;
        document.body.appendChild(popup);

        fetch('/dd3.5/ckeditor/plugins/insertom/om_selector.php')
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById('listeoms');
            data.forEach(om => {
              const option = document.createElement('option');
              option.value = om.om_id;
              option.textContent = om.om_nom;
              select.appendChild(option);
            });
          });

        document.getElementById('insertomBtn').addEventListener('click', () => {
          const select = document.getElementById('listeoms');
          const om_id = select.value;
          const selectedText = editor.getSelection().getSelectedText() || 'om';
          const span = `<span class="lien" onclick="afficherOM(${om_id})">${selectedText}</span>`;
          editor.insertHtml(span);
          document.getElementById('omPopup').remove();
        });
      }
    });

    editor.ui.addButton('Insertom', {
      label: 'Insérer un lien vers un objet magique',
      command: 'insertomCommand',
      toolbar: 'insert',
      icon: this.path + 'icons/insertom.png' // ← obligatoire si PNG
    });
    
  }
});

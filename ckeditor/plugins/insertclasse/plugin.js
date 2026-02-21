CKEDITOR.plugins.add('insertclasse', {
  icons: 'insertclasse',
  init: function (editor) {
    editor.addCommand('insertclasseCommand', {
      exec: function (editor) {
        if (document.getElementById('classePopup')) return;

        const popup = document.createElement('div');
        popup.id = 'classePopup';
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
          <i class="fa fa-close close-btn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-size:18px;color:#888;" onclick="document.getElementById('classePopup').remove();"></i>
          <h3>Choisir une classe</h3>
          <select id="listeClasses" style="width:100%;margin-top:10px;"></select>
          <button id="insertclasseBtn" style="width:100%;margin-top:10px;">Insérer</button>
        `;
        document.body.appendChild(popup);

        fetch('/dd3.5/ckeditor/plugins/insertclasse/classe_selector.php')
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById('listeClasses');
            data.forEach(classe => {
              const option = document.createElement('option');
              option.value = classe.cla_id;
              option.textContent = classe.cla_nom;
              select.appendChild(option);
            });
          });

        document.getElementById('insertclasseBtn').addEventListener('click', () => {
          const select = document.getElementById('listeClasses');
          const cla_id = select.value;
          const selectedText = editor.getSelection().getSelectedText() || 'Classe';
          const span = `<span class="lien" onclick="afficherClasse(${cla_id})">${selectedText}</span>`;
          editor.insertHtml(span);
          document.getElementById('classePopup').remove();
        });
      }
    });

    editor.ui.addButton('insertclasse', {
      label: 'Insérer un lien vers une classe',
      command: 'insertclasseCommand',
      toolbar: 'insert',
      icon: this.path + 'icons/insertclasse.png' // ← obligatoire si PNG
    });
    
  }
});

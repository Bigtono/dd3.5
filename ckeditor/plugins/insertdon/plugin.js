CKEDITOR.plugins.add('insertdon', {
  icons: 'insertdon',
  init: function (editor) {
    editor.addCommand('insertdonCommand', {
      exec: function (editor) {
        if (document.getElementById('donPopup')) return;

        const popup = document.createElement('div');
        popup.id = 'donPopup';
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
          <i class="fa fa-close close-btn" style="position:absolute;top:5px;right:10px;cursor:pointer;font-size:18px;color:#888;" onclick="document.getElementById('donPopup').remove();"></i>
          <h3>Choisir un don</h3>
          <select id="listedons" style="width:100%;margin-top:10px;"></select>
          <button id="insertdonBtn" style="width:100%;margin-top:10px;">Insérer</button>
        `;
        document.body.appendChild(popup);

        fetch('/dd3.5/ckeditor/plugins/insertdon/don_selector.php')
          .then(res => res.json())
          .then(data => {
            const select = document.getElementById('listedons');
            data.forEach(don => {
              const option = document.createElement('option');
              option.value = don.do_id;
              option.textContent = don.do_nom;
              select.appendChild(option);
            });
          });

        document.getElementById('insertdonBtn').addEventListener('click', () => {
          const select = document.getElementById('listedons');
          const do_id = select.value;
          const selectedText = editor.getSelection().getSelectedText() || 'don';
          const span = `<span class="lien" onclick="afficherDon(${do_id})">${selectedText}</span>`;
          editor.insertHtml(span);
          document.getElementById('donPopup').remove();
        });
      }
    });

    editor.ui.addButton('insertdon', {
      label: 'Insérer un lien vers un don',
      command: 'insertdonCommand',
      toolbar: 'insert',
      icon: this.path + 'icons/insertdon.png' // ← obligatoire si PNG
    });
    
  }
});

CKEDITOR.editorConfig = function( config ) {
	config.toolbarGroups = [
		{ name: 'document', groups: [ 'document', 'doctools', 'mode' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert', 'insertsort', 'insertclasse', 'insertom', 'insertdon', 'insertcapa' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	// 👇 Boutons supprimés
	config.removeButtons = 'Print,Form,Checkbox,Radio,TextField,Select,Button,ImageButton,Textarea,HiddenField,About,Maximize,ShowBlocks,SelectAll,Scayt,Find,Replace,ExportPdf,NewPage,Flash,Smiley,PageBreak,Iframe,Undo,Redo,TextColor,BGColor,BidiLtr,BidiRtl,Language,Save,Preview,Paste,Copy,Styles,Font,FontSize';

	// Ajout du plugin personnali�
	config.extraPlugins = 'insertsort,insertclasse, insertom, insertdon, insertcapa';

	// ✅ Ajout du bouton dans un groupe de la toolbar
	//    Il sera affiché dans la section "insert"
	config.toolbar = null; // Assure que toolbarGroups est utilisé
};
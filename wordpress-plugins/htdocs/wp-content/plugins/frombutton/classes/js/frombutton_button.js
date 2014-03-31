/***************************************************************
@
@	FromButton WP JS
@	bassem.rabia@hotmail.co.uk
@
/**************************************************************/  

(function() {
	tinymce.create('tinymce.plugins.FromButtonButton', {
		init : function(ed, url){ 
			ed.addButton('frombutton_button', {
				title : 'From Button', 
				image : url+'/FromButton16.png',
				onclick : function() {
					idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;  
					ed.execCommand('mceInsertContent', false, '[FromButtonBuy]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : 'FAQ HTML Page Button',
				author : 'Bassem Rabia'
			};
		}
	});
	tinymce.PluginManager.add('frombutton_button', tinymce.plugins.FromButtonButton);
})();
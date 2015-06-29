(function() {
    tinymce.create('tinymce.plugins.twitt_mce', {
 
        init : function(ed, url){
			
			ed.addCommand('mcetwitt_mce', function() { 
				ed.windowManager.open({
					file : twitt_admin_ajax,
					width : 400 + ed.getLang('twitt_mce.delta_width', 0),
					height : 280 + ed.getLang('twitt_mce.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			
			
			} );
            ed.addButton('twitt_mce', {
            title : 'Insert Widget Twitter',
			cmd : 'mcetwitt_mce',
            image : twitt_plugin_url + '/images/widget-twitter_edit_but.png'			
            });
        }
    });
 
    tinymce.PluginManager.add('twitt_mce', tinymce.plugins.twitt_mce);
 
})();

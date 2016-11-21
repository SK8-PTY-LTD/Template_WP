(function() {
  tinymce.create('tinymce.plugins.fmc_form_mce', {
    init : function(ed, url) {
      ed.addCommand('mcefmc_form_mce', function() {
        ed.windowManager.open({
          file : form_maker_admin_ajax_cfm,
					width : 550 + ed.getLang('fmc_form_mce.delta_width', 0),
					height : 300 + ed.getLang('fmc_form_mce.delta_height', 0),
					inline : 1
				}, {
            fmc_plugin_url : url // Plugin absolute URL
				});
			});
      ed.addButton('fmc_form_mce', {
        title : 'Insert Contact Form',
        cmd : 'mcefmc_form_mce',
        image: url + '/images/form_maker_edit_but.png'
      });
    }
  });
  tinymce.PluginManager.add('fmc_form_mce', tinymce.plugins.fmc_form_mce);
})();
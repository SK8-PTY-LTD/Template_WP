(function() {
    plugin_slug = 'drawit';
    plugin_name = 'DrawIt';
    media_upload_url = drawitFE.mediaupload;

    // To attach new diagram to this post, need to also indicate post ID.
    post_id_get = new RegExp('[\?&amp;]post=([^&amp;#]*)').exec(window.location.href);
    post_id = (post_id_get !== null && post_id_get.length > 1) ? post_id_get[1] : 0;

    tinymce.PluginManager.add(plugin_slug + '_mce_button', function(editor, url) {
        editor.on('init', function() {
            var cssURL = url + '/../css/drawit-mce.css';
            if(document.createStyleSheet){
                document.createStyleSheet(cssURL);
            } else {
                cssLink = editor.dom.create('link', {
                            rel: 'stylesheet',
                            href: cssURL
                          });
                document.getElementsByTagName('head')[0].
                          appendChild(cssLink);
            }
        });

        editor.addButton(plugin_slug + '_mce_button', {
            tooltip: plugin_name + ' Diagram',
            icon: plugin_slug,
            onclick: function() {
                diag_title = "";
                img_id = "";

                selected_code = tinymce.activeEditor.selection.getContent();

                // If this errors out, then just leave the title and id as default values.
                try {
                    // Add <span></span> around this copy of selection for ease of jQuery parsing
                    // (eases matching of both <img> at top level or at nested level of selection).
                    j_code = jQuery('<span>' + selected_code + '</span>').find('img').first();

                    // If editing an existing diagram, then get the title of it (if provided).
                    diag_title = j_code.attr('title');
                    if (typeof diag_title === typeof undefined || diag_title === false) {
                        diag_title = "";
                    } else {
                        diag_title = '&title=' + diag_title;
                    }

                    // If there are multiple classes defined, then find the one
                    // we want that has the attachment id#.
                    img_class = j_code.attr('class');
                    if (typeof img_class !== typeof undefined && img_class !== false) {
                        img_class_split = img_class.split(' ');
                        for(i = 0; i < img_class_split.length; i++) {
                            elem = img_class_split[i];
                            if(elem.indexOf('wp-image-') === 0) {
                                img_class_split = elem.split('-');
                                img_id = '&img_id=' + img_class_split[2];
                            }
                        }
                    }

                } catch(err) {
                    diag_title = "";
                    img_id = "";
                }
                tb_show('Draw a diagram', media_upload_url + '?referer=' + plugin_slug + '&type=' + plugin_slug + '&post_id=' + post_id + diag_title + img_id + '&TB_iframe=true', false);

                jQuery('#TB_window').css({
                    'min-width': '90%',
                    'left': 'calc(-1 * (' + jQuery('#TB_window').css('margin-left') + ') + 5%)',
                    'background': 'url("' + url + '/../img/wpspin-2x.gif") no-repeat center center #fff'
                });
                jQuery('#TB_window > iframe').css({
                    'min-width': '100%'
                });
            }
        });
    });
})();

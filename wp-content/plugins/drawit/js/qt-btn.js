(function() {
    plugin_slug = 'drawit';
    plugin_name = 'DrawIt';
    media_upload_url = drawitFE.mediaupload;

    // Add text-only button.
    QTags.addButton(plugin_slug, plugin_name, function(el, canvas) {
        selected_code = "";
        diag_title = "";
        img_id = "";
        canvas.focus();

        // If editing an existing diagram, then get the path for it.
        if(document.selection) { // IE
			selected_code = document.selection.createRange().text;
        } else { // FF, WebKit, Opera, etc.
            selected_code = canvas.value.substring(canvas.selectionStart, canvas.selectionEnd);
        }

        canvas.focus();

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

            // If there are multiple classes defined, then find the one we want that has the attachment id#.
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

        // To attach new diagram to this post, need to also indicate post ID.
        post_id_get = new RegExp('[\?&amp;]post=([^&amp;#]*)').exec(window.location.href);
        post_id = (post_id_get !== null && post_id_get.length > 1) ? post_id_get[1] : 0;

        // Here is the form to use.
        //tb_show('Draw a diagram', 'media-upload.php?referer=' + plugin_slug + '&type=' + plugin_slug + '&post_id=' + post_id + diag_title + img_id + '&TB_iframe=true', false);
        tb_show('Draw a diagram', media_upload_url + '?referer=' + plugin_slug + '&type=' + plugin_slug + '&post_id=' + post_id + diag_title + img_id + '&TB_iframe=true', false);

        // "thickbox" from tb_show doesn't allow size overriding directly with
        // 'width' and 'margin-left' (these get updated dynamically when the
        // window resizes), so we need to work around this to make our
        // editor's thickbox large enough to show the entire editor.
        jQuery('#TB_window').css({
            'min-width': '90%',
            'left': 'calc(-1 * (' + jQuery('#TB_window').css('margin-left') + ') + 5%)',
            'background': 'url("/wp-content/plugins/' + plugin_slug + '/img/wpspin-2x.gif") no-repeat center center #fff'
        });
        jQuery('#TB_window > iframe').css({
            'min-width': '100%'
        });

        return false;
    });
})();

jQuery(document).ready( function() {
    plugin_slug = 'drawit';
    plugin_name = 'DrawIt';

    var size_info = '';
    var plugin_iframe = document.getElementById(plugin_slug + '-iframe');

    // Sending messages to draw.io server.
    var send_msg = function (data) {
        plugin_iframe.contentWindow.postMessage(JSON.stringify(data), 'https://www.draw.io');
    }

    // User clicked "save", so save the file to media library.
    var save_callback = function(xml) {
        insert_html = '';
        console.log(xml);
        document.getElementById(plugin_slug + '-editor-mask').style.display = "block";
        document.getElementById(plugin_slug + '-xml').value = xml;
        img_msg = {
            'action':       'export',
            'embedImages':  true,
            'format':       document.getElementById(plugin_slug + '-type').value
        };
        send_msg(img_msg);
    }

    // Get png or svg version of image.
    var get_img = function(img_type, img_data) {
        // All done, send everything back to WP.
        var data = {
            'action':   'submit-form-' + plugin_slug,
            'title':    document.getElementById(plugin_slug + '-title').value,
            'nonce':    document.getElementById(plugin_slug + '-nonce').value,
            'post_id':  document.getElementById(plugin_slug + '-post-id').value,
            'xml':      document.getElementById(plugin_slug + '-xml').value,
            'img_type': img_type,
            'img_data': img_data
        };

        // Post the file submission via ajax.
        jQuery.post(ajaxurl, data, function(response) {
            resp = JSON.parse(response);

            // Success, send HTML code to editor.
            if(resp['success']) {
                document.getElementById(plugin_slug + '-editor-mask').style.display = "none";
                plugin_iframe.src = plugin_iframe.src.split('#')[0];
                parent.window.send_to_editor(resp['html']);

            // Fail.
            } else {
                //alert(resp['html']);
                img_msg = {
                    'action':       'dialog',
                    'title':        'Error',
                    'message':      resp['html'],
                    'button':       'OK',
                    'modified':     true
                };
                send_msg(img_msg);
            }

            document.getElementById(plugin_slug + '-editor-mask').style.display = "none";

        });
    }

    // User clicked "exit", close media window.
    var exit_callback = function() {
        plugin_iframe.src = plugin_iframe.src.split('#')[0];
        parent.window.tb_remove();
    }

    // Wait for messages from draw.io iframe.
    var receive = function(evt) {
        if(evt.origin == 'https://www.draw.io') {
            resp = JSON.parse(evt.data);

            switch(resp['event']) {
                // Initialization is done, send message saying to create new doc.
                case 'init':
                    orig_xml = document.getElementById(plugin_slug + '-xml');

                    if(orig_xml === null) {
                        alert('null!');
                        orig_xml = '';
                    } else {
                        orig_xml = orig_xml.value;
                    }

                    load_msg = {
                        'action':   'load',
                        'xml':      orig_xml
                    };

                    send_msg(load_msg);
                    break;

                // Diagram (or blank canvas) has now loaded.
                case 'load':
                    size_info = evt.data;
                    //alert(size_info);
                    break;

                // User has clicked 'save'
                case 'save':
                    save_callback(resp['xml']);
                    break;

                // Message that comes after clicking 'save' and this script requests the png/svg.
                case 'export':
                    get_img(resp['format'], resp['data']);
                    break;

                // User has clicked 'exit'.
                case 'exit':
                    exit_callback();
                    break;

                default:
                    alert('ERROR: Unrecognized message');
                    break;
            }
        }
    };
    window.addEventListener('message', receive);
});

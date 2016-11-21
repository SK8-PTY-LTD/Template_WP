// This prevents the behavior that can occur where an iframe causes the parent
// to scroll to the iframe when the iframe has finished loading.
jQuery(window).load( function() {
    jQuery("iframe.drawit-iframe").each(function() {
        jQuery(this).addClass('drawit-iframe-displayed');
    });
});

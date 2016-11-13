jQuery(window).load(function () {
    jQuery('#subscribe_thickbox').trigger('click');
    jQuery("#TB_closeWindowButton").click(function () {
        jQuery.post(ajaxurl,
                {
                    'action': 'close_tab'
                });
    });
});
jQuery(document).ready(function() {
    jQuery('script').each(function () {
        var src = jQuery(this).attr('src');
        if (typeof src !== typeof undefined && src !== false) {
            if (src.search('bootstrap.js') !== -1 || src.search('bootstrap.min.js') !== -1) {
                var bootstrapButton = jQuery.fn.button.noConflict();
                jQuery.fn.bootstrapBtn = bootstrapButton;
            }
        }
    });
    if (jQuery('form.sol-form input[name="emailEnable"]:checked').val() == 0) {
        jQuery('form.sol-form .ui-button.ui-corner-right').addClass('active');
        jQuery('form.sol-form .ui-button.ui-corner-left').removeClass('active');
    } else {
        jQuery('form.sol-form .ui-button.ui-corner-left').addClass('active');
        jQuery('form.sol-form .ui-button.ui-corner-right').removeClass('active');
    }
    jQuery('form.sol-form input[name="emailEnable"]').click(function() {
        if (jQuery('form.sol-form input[name="emailEnable"]:checked').val() == 0) {
            jQuery('form.sol-form .ui-button.ui-corner-right').addClass('active');
            jQuery('form.sol-form .ui-button.ui-corner-left').removeClass('active');
        } else {
            jQuery('form.sol-form .ui-button.ui-corner-left').addClass('active');
            jQuery('form.sol-form .ui-button.ui-corner-right').removeClass('active');
        }
    });
    
});
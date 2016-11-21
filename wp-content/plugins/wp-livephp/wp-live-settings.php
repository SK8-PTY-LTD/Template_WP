<style>
.wp-livephp-description {
    color: #777777;
    font-size: 12px;
    font-weight: normal;
    line-height: 14px;
}
.wp-livephp-error {
    color: crimson;
    line-height: 13px;
}
</style>
<div class="wrap">
    <?php screen_icon( 'options-general' ); ?>
    <h2><?php echo get_admin_page_title(); ?></h2>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" class="postbox-container" style="width:60%;">
            <div class="meta-box-sortables">
                <div class="postbox">
                    <h3 class="hndle"><span><?php echo get_admin_page_title(); ?> Settings</span></h3>
                    <div class="inside">
                        <table class="form-table"><tbody><tr valign="top">
                        <th scope="row"><label>Frontend Monitoring:</label></th><td>
                            <div id="enable_frontend"></div>
                            <p class="wp-livephp-description">
                                Turn this on to enable autorefresh on your blogs visitor side. <br>
                                After changing this setting, you will need to manually refresh your browser (for one last time)
                                for the script to load / unload on your site.
                            </p>
                        </td></tr>
                      <tr valign="top">
                        <th scope="row"><label>Backend Monitoring:</label></th><td>
                            <div id="enable_backend"></div>
                            <p class="wp-livephp-description">
                                If you work on the wp-admin panels, you can enable autorefresh with this switch.
                                <br><br>
                            </p>
                        </td></tr>
                        <tr valign="top">
                        <th scope="row"><label>Content Monitoring:</label></th><td>
                            <div id="enable_content"></div>
                            <p class="wp-livephp-description">
                                If you want autorefresh on content updates, you can enable it here. When you save a post or page in your wp-admin, 
                                the visitor side will refresh itself, showing the new content immediately. 
                            </p>
                            <div id="wp-livephp_contenterror" class="wp-livephp-error"></div>
                        </td></tr>
                        <tr valign="top">
                        <th scope="row"><label>Admin bar button:</label></th><td>
                            <input type="checkbox" id="livephp-adminbar" name="livephp-adminbar" value="1" <?php echo $adminbar ?>>
                            <label for="livephp-adminbar">Enable admin bar integration</label>
                            <p class="wp-livephp-description">
                                Add a button to your admin bar to enable or disable frontend or backend monitoring.
                            </p>
                        </td></tr>
                        </table>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle"><span>Description</span></h3>
                    <div class="inside">
    <p>
    This plugin was written to make Wordpress theme and plugin developers' life easier.<br>
    Inspired by the brilliant live.js script (written by Martin Kool),
    this plugin will auto refresh your browser if you change any files in your wp-content/themes
    or plugins directories. No need for Alt-Tab and manual refresh anymore.
    </p>
    <p>
    If you activate the WP Live Php plugin, it adds a small javascript file to your blog.
    It will monitor your directories by calling wp-live.php every second. If any file changed
    (i.e. has a newer filemtime), the browser will be refreshed.
    </p>
    <p>
    With this plugin, it is also very easy to check your work in many browsers simultaneously.
    Just enable Frontend Monitoring, load the site in all your browsers and the rest goes automatically.
    </p>
    <p>
    Starting from v1.3 there is an option to enable admin bar integration, to conveniently enable or
    disable Live.php monitoring directly on your frontend or backend with just one click.
    </p>
    <p style="color:darkred">
    WARNING!<br>
    You should never activate this plugin on a live server! It is meant for developer environment only!
    </p>
    <br>
                    </div>
                </div>
            </div>
        </div>
        <div id="postbox-container-2" class="postbox-container" style="width:20%;">
            <div class="meta-box-sortables">
                <div class="postbox">
                    <h3 class="hndle"><span>Support the developer!</span></h3>
                    <div class="inside" style="text-align:center;">
                        <br>
                        If you think Live.php made your life easier and find it useful, please consider making a donation!<br><br>
                        <a href="http://flattr.com/thing/451308/WP-Live-php" target="_blank">
                        <img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a>
                        <br><br>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form>
                        <br>
                        <p>
                        You could also <a href="http://wordpress.org/extend/plugins/wp-livephp/" target="_blank">give it a 5 star rating</a>,<br>
                        or tell others that <a href="http://wordpress.org/extend/plugins/wp-livephp/" target="_blank">it works with your WordPress version</a>.
                        </p>
                    </div>
                </div>

                <div class="postbox">
                    <h3 class="hndle"><span>Problems? Bugs? Requests?</span></h3>
                    <div class="inside">
                        If you have any questions or requests regarding Live.php or if you have found a bug, please visit the
                        <a href="http://wordpress.org/support/plugin/wp-livephp" target="_blank">Live.php support forum</a> on wordpress.org!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
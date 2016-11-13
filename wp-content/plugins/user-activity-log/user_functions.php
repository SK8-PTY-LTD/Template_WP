<?php
/*
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/*
 * Create table "wp_user_activity" when activate plugin
 */
if (!function_exists('ual_user_activity_table_create')) {

    function ual_user_activity_table_create() {
        global $wpdb;
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/user-activity-log/user_activity_log.php', $markup = true, $translate = true);
        $current_version = $plugin_data['Version'];
        $old_table_name = $wpdb->prefix . "user_activity";
        $table_name = $wpdb->prefix . "ualp_user_activity";
        //table is not created. you may create the table here.
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $old_table_exist = ual_checkif_table_rename();
            if (!$old_table_exist) {
                $create_table_query = "CREATE TABLE $table_name (uactid bigint(20) unsigned NOT NULL auto_increment,post_id int(20) unsigned NOT NULL,post_title varchar(250) NOT NULL,user_id bigint(20) unsigned NOT NULL default '0',user_name varchar(50) NOT NULL,user_role varchar(50) NOT NULL,user_email varchar(50) NOT NULL,ip_address varchar(50) NOT NULL,modified_date datetime NOT NULL default '0000-00-00 00:00:00',object_type varchar(50) NOT NULL default 'post',action varchar(50) NOT NULL,PRIMARY KEY (uactid))";
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta($create_table_query);
            } else {
                $wpdb->query("RENAME TABLE " . $old_table_name . " TO " . $table_name);
            }
        }
        update_option('ual_version', $current_version);
    }

}
add_action('activate_plugin', 'ual_user_activity_table_create');

/*
 * Rename table "user_activity" to "ualp"
 */
if (!function_exists('ual_checkif_table_rename')) {

    function ual_checkif_table_rename() {
        global $wpdb;
        $old_table_name = $wpdb->prefix . "user_activity";
        $table_name = $wpdb->prefix . "ualp_user_activity";
        if ($wpdb->get_var("SHOW TABLES LIKE '$old_table_name'") == $old_table_name) {
            if ($wpdb->get_var("SHOW COLUMNS FROM " . $old_table_name . " LIKE 'uactid'") == 'uactid') {
                return true;
            }
            return false;
        }
        return false;
    }

}

/*
 * Run database updater when plugin updated
 */
if (!function_exists('ual_database_upgrade')) {

    function ual_database_upgrade() {
        global $wpdb;
        $old_table_name = $wpdb->prefix . "user_activity";
        $table_name = $wpdb->prefix . "ualp_user_activity";
        $old_table_exist = ual_checkif_table_rename();
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/user-activity-log/user_activity_log.php', $markup = true, $translate = true);
        $current_version = $plugin_data['Version'];
        if ($old_table_exist) {
            ?>
            <div class="updated">
                <p>
                    <strong>
                        <?php _e('User Activity Log Data Update', 'wp_user_log'); ?>
                    </strong> &#8211; <?php _e('We need to update your database to the latest version.', 'wp_user_log'); ?>
                </p>
                <p class="submit">
                    <a href="<?php echo esc_url(add_query_arg('do_update_ual', 'do', admin_url('admin.php?page=general_settings_menu'))); ?>" class="ual-update-now button-primary">
                        <?php _e('Run the updater', 'wp_user_log'); ?>
                    </a>
                </p>
            </div>
            <?php
            if (isset($_GET['do_update_ual']) && $_GET['do_update_ual'] == 'do') {
                update_option('ual_version', $current_version);
                $wpdb->query("RENAME TABLE " . $old_table_name . " TO " . $table_name);
            }
            ?>
            <script type="text/javascript">
                jQuery('.ual-update-now').click('click', function () {
                    return window.confirm('<?php echo esc_js(__('It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wp_user_log')); ?>');
                });
            </script>
            <?php
        }
    }

}
add_action('admin_notices', 'ual_database_upgrade');

/*
 * Insert record into wp_user_activity table
 *
 * @param int $post_id Post ID.
 * @param string $post_title Post Title.
 * @param string $obj_type Object Type (Plugin, Post, User etc.).
 * @param int $current_user_id current user id.
 * @param string $current_user current user name.
 * @param string $user_role current user Role.
 * @param string $user_mail current user Email address.
 * @param datetime $modified_date current user's modified time.
 * @param string $ip current user's IP address.
 * @param string $action current user's activity name.
 *
 */
if (!function_exists('ual_user_activity_add')) {

    function ual_user_activity_add($post_id, $post_title, $obj_type, $current_user_id, $current_user, $user_role, $user_mail, $modified_date, $ip, $action) {
        global $wpdb;
        $table_name = $wpdb->prefix . "ualp_user_activity";
        $post_title = addslashes($post_title);
        $insert_query = $wpdb->query("INSERT INTO $table_name (post_id,post_title,user_id, user_name, user_role, user_email, ip_address, modified_date, object_type, action) VALUES ('$post_id','$post_title','$current_user_id', '$current_user', '$user_role','$user_mail', '$ip', '$modified_date', '$obj_type', '$action')");
    }

}

/*
 * Get activity
 *
 * @param string $action current user's activity name.
 * @param string $obj_type Object Type (Plugin, Post, User etc.).
 * @param int $post_id Post ID.
 * @param string $post_title Post Title.
 *
 */
if (!function_exists('ual_get_activity_function')) {

    function ual_get_activity_function($action, $obj_type, $post_id, $post_title) {
        $modified_date = current_time('mysql');
        $ip = $_SERVER['REMOTE_ADDR'];
        $current_user_id = get_current_user_id();
        $current_user1 = wp_get_current_user();
        $current_user = $current_user1->user_login;
        $user = new WP_User($current_user_id);
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $get_emails = "SELECT * from $table_name where user_login='$current_user'";
        $mails = $wpdb->get_results($get_emails);
        foreach ($mails as $k => $v) {
            $user_mail = $v->user_email;
        }
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role)
                $user_role = $role;
        }
        $current_user_display_name = $user->display_name;
        ual_user_activity_add($post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action);
    }

}

/*
 * Add activity for the current user when login
 *
 * @param string $user_login current user's login name.
 *
 */
if (!function_exists('ual_shook_wp_login')):

    function ual_shook_wp_login($user_login) {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $action = "logged in";
        $obj_type = "user";
        $current_user = $user_login;
        $get_uid = "SELECT * from $table_name where user_login='$current_user'";
        $c_uid = $wpdb->get_results($get_uid);
        foreach ($c_uid as $k => $v) {
            $user_idis = $v->ID;
            $user_mail = $v->user_email;
        }
        $current_user_id = $user_idis;
        $user = new WP_User($current_user_id);
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role)
                $user_role = $role;
        }
        $post_id = $current_user_id;
        $post_title = $current_user;
        $modified_date = current_time('mysql');
        $ip = $_SERVER['REMOTE_ADDR'];
        $current_user_display_name = $user->display_name;
        ual_user_activity_add($post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action);
    }

endif;

/*
 * Get activity for the current user when logout
 */
if (!function_exists('ual_shook_wp_logout')):

    function ual_shook_wp_logout() {
        $action = "logged out";
        $obj_type = "user";
        $post_id = get_current_user_id();
        $user_nm = get_user_by('id', $post_id);
        $post_title = $user_nm->user_login;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the delete user
 *
 * @param int $user Post ID
 *
 */
if (!function_exists('ual_shook_delete_user')):

    function ual_shook_delete_user($user) {
        $action = "delete user";
        $obj_type = "user";
        $post_id = $user;
        $user_nm = get_user_by('id', $post_id);
        $post_title = $user_nm->user_login;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the registered user
 *
 * @param int $user Post ID
 *
 */
if (!function_exists('ual_shook_user_register')):

    function ual_shook_user_register($user) {
        $action = "user register";
        $obj_type = "user";
        $post_id = $user;
        $user_nm = get_user_by('id', $post_id);
        $post_title = $user_nm->user_login;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - update profile
 *
 * @param int $user Post ID
 *
 */
if (!function_exists('ual_shook_profile_update')):

    function ual_shook_profile_update($user) {
        $action = "profile update";
        $obj_type = "user";
        $post_id = $user;
        $user_nm = get_user_by('id', $post_id);
        $post_title = $user_nm->user_login;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - add attach media file
 *
 * @param int $attach Post ID
 *
 */
if (!function_exists('ual_shook_add_attachment')):

    function ual_shook_add_attachment($attach) {
        $action = "add attachment";
        $obj_type = "attachment";
        $post_id = $attach;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - edit attach media file
 *
 * @param int $attach Post ID
 *
 */
if (!function_exists('ual_shook_edit_attachment')):

    function ual_shook_edit_attachment($attach) {
        $post_id = $attach;
        $post_title = get_the_title($post_id);
        $action = "edit attachment";
        $obj_type = "attachment";
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - delete attach media file
 *
 * @param int $attach Post ID
 *
 */
if (!function_exists('ual_shook_delete_attachment')):

    function ual_shook_delete_attachment($attach) {
        $post_id = $attach;
        $post_title = get_the_title($post_id);
        $action = "delete attachment";
        $obj_type = "attachment";
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Insert Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_wp_insert_comment')):

    function ual_shook_wp_insert_comment($comment) {
        $action = "insert comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Edit Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_edit_comment')):

    function ual_shook_edit_comment($comment) {
        $action = "edit comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Trash Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_trash_comment')):

    function ual_shook_trash_comment($comment) {
        $action = "trash comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Spam Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_spam_comment')):

    function ual_shook_spam_comment($comment) {
        $action = "spam comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Unspam Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_unspam_comment')):

    function ual_shook_unspam_comment($comment) {
        $action = "unspam comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Delete Comment
 *
 * @param int $comment Comment ID
 *
 */
if (!function_exists('ual_shook_delete_comment')):

    function ual_shook_delete_comment($comment) {
        $action = "delete comment";
        $obj_type = "comment";
        $comment_id = $comment;
        $com = get_comment($comment_id);
        $post_id = $com->comment_post_ID;
        $post_title = get_the_title($post_id);
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Create Terms
 *
 * @param int $term Post ID
 * @param string $taxonomy taxonomy name
 *
 */
if (!function_exists('ual_shook_created_term')):

    function ual_shook_created_term($term, $taxonomy) {
        $action = "created term";
        $obj_type = "term";
        if ('nav_menu' === $taxonomy)
            return;
        global $wpdb;
        $post_id = $term;
        $tab_nm = $wpdb->prefix . "terms";
        $get_term_name = "SELECT * from $tab_nm where term_id=$post_id";
        $terms_nm = $wpdb->get_results($get_term_name);
        foreach ($terms_nm as $k => $v) {
            $post_title = $v->name;
        }
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Edit Terms
 *
 * @param int $term Post ID
 * @param string $taxonomy taxonomy name
 *
 */
if (!function_exists('ual_shook_edited_term')):

    function ual_shook_edited_term($term, $taxonomy) {
        $action = "edited term";
        $obj_type = "term";
        if ('nav_menu' === $taxonomy)
            return;
        global $wpdb;
        $post_id = $term;
        $tab_nm = $wpdb->prefix . "terms";
        $get_term_name = "SELECT * from $tab_nm where term_id=$post_id";
        $terms_nm = $wpdb->get_results($get_term_name);
        foreach ($terms_nm as $k => $v) {
            $post_title = $v->name;
        }
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Delete Terms
 *
 * @param int $term_id Post ID
 * @param string $taxonomy taxonomy name
 * @param string $deleted_term = null
 *
 */
if (!function_exists('ual_shook_delete_term')):

    function ual_shook_delete_term($term_id, $taxonomy_name, $deleted_term = null) {
        if ('nav_menu' === $taxonomy_name)
            return;
        $term = $deleted_term;
        if ($term && !is_wp_error($term)) {
            global $wpdb;
            $action = 'delete term';
            $obj_type = 'Term';
            ual_get_activity_function($action, $obj_type, $term_id, $term->name);
        }
    }

endif;

/*
 * Get activity for the user - Update navigation menu
 *
 * @param int $menu Post ID
 *
 */
if (!function_exists('ual_shook_wp_update_nav_menu')):

    function ual_shook_wp_update_nav_menu($menu) {
        $action = "update nav menu";
        $obj_type = "menu";
        $post_id = $menu;
        $menu_object = wp_get_nav_menu_object($post_id);
        $post_title = $menu_object->name;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Create navigation menu
 *
 * @param int $menu Post ID
 *
 */
if (!function_exists('ual_shook_wp_create_nav_menu')):

    function ual_shook_wp_create_nav_menu($menu) {
        $action = "create nav menu";
        $obj_type = "menu";
        $post_id = $menu;
        $menu_object = wp_get_nav_menu_object($post_id);
        $post_title = $menu_object->name;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Delete navigation menu
 *
 * @param int $tt_id Post ID
 * @param string $deleted_term Post Title
 *
 */
if (!function_exists('ual_shook_delete_nav_menu')):

    function ual_shook_delete_nav_menu($tt_id, $deleted_term) {
        $action = "delete nav menu";
        $obj_type = "menu";
        $post_id = $tt_id;
        $post_title = $deleted_term->name;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Switch Theme
 *
 * @param string $theme Post Title
 *
 */
if (!function_exists('ual_shook_switch_theme')):

    function ual_shook_switch_theme($theme) {
        $action = "switch theme";
        $obj_type = "theme";
        $post_id = "";
        $post_title = $theme;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Update Theme
 *
 */
if (!function_exists('shook_delete_site_transient_update_themes')):

    function shook_delete_site_transient_update_themes() {
        $action = "delete_site_transient_update_themes";
        $obj_type = "theme";
        $post_id = "";
        $post_title = $theme;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Customize Theme
 *
 */
if (!function_exists('ual_shook_customize_save')):

    function ual_shook_customize_save() {
        $action = "customize save";
        $obj_type = "theme";
        $post_id = "";
        $post_title = "Theme Customizer";
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Activate Plugin
 *
 * @param string $plugin Post Title
 *
 */
if (!function_exists('ual_shook_activated_plugin')):

    function ual_shook_activated_plugin($plugin) {
        $action = "activated plugin";
        $obj_type = "plugin";
        $post_id = "";
        $post_title = $plugin;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Activate Plugin
 *
 * @param string $new_status new posts status
 * @param string $old_status old posts status
 * @param object $post posts
 *
 */
if (!function_exists('ual_shook_transition_post_status')):

    function ual_shook_transition_post_status($new_status, $old_status, $post) {
        $action = '';
        $obj_type = $post->post_type;
        $post_id = $post->ID;
        $post_title = $post->post_title;
        if ('auto-draft' === $old_status && ( 'auto-draft' !== $new_status && 'inherit' !== $new_status )) {
            $action = $obj_type . ' created';
        } elseif ('auto-draft' === $new_status || ( 'new' === $old_status && 'inherit' === $new_status )) {
            return;
        } elseif ('trash' === $new_status) {
            $action = $obj_type . ' deleted';
        } else {
            $action = $obj_type . ' updated';
        }
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Deactivate Plugin
 *
 * @param string $plugin Post Title
 *
 */
if (!function_exists('ual_shook_deactivated_plugin')):

    function ual_shook_deactivated_plugin($plugin) {
        $action = "deactivated plugin";
        $obj_type = "plugin";
        $post_id = "";
        $post_title = $plugin;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Core file updated successfully
 *
 */
if (!function_exists('shook_core_updated_successfully')):

    function shook_core_updated_successfully() {
        $action = "core updated successfully";
        $obj_type = "update";
        $post_id = "";
        $post_title = $obj_type;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Export wordpress data
 *
 */
if (!function_exists('ual_shook_export_wp')):

    function ual_shook_export_wp() {
        $action = "export wp";
        $obj_type = "export";
        $post_id = "";
        $post_title = $obj_type;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Upgrader process complete
 *
 */
if (!function_exists('shook_upgrader_process_complete')):

    function shook_upgrader_process_complete() {
        $action = "upgrade process complete";
        $obj_type = "upgrade";
        $post_id = "";
        $post_title = $obj_type;
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

/*
 * Get activity for the user - Delete theme
 *
 */
if (!function_exists('ual_shook_theme_deleted')):

    function ual_shook_theme_deleted() {
        $backtrace_history = debug_backtrace();
        $delete_theme_call = null;
        foreach ($backtrace_history as $call) {
            if (isset($call['function']) && 'delete_theme' === $call['function']) {
                $delete_theme_call = $call;
                break;
            }
        }
        if (empty($delete_theme_call))
            return;
        $name = $delete_theme_call['args'][0];
        $action = 'Theme deleted';
        $obj_type = 'Theme';
        $post_title = $name;
        $post_id = "";
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
    }

endif;

add_action('wp_login', 'ual_shook_wp_login');
add_action('wp_logout', 'ual_shook_wp_logout');
add_action('delete_user', 'ual_shook_delete_user');
add_action('user_register', 'ual_shook_user_register');
add_action('profile_update', 'ual_shook_profile_update');
add_action('add_attachment', 'ual_shook_add_attachment');
add_action('edit_attachment', 'ual_shook_edit_attachment');
add_action('delete_attachment', 'ual_shook_delete_attachment');
add_action('wp_insert_comment', 'ual_shook_wp_insert_comment');
add_action('edit_comment', 'ual_shook_edit_comment');
add_action('trash_comment', 'ual_shook_trash_comment');
add_action('spam_comment', 'ual_shook_spam_comment');
add_action('unspam_comment', 'ual_shook_unspam_comment');
add_action('delete_comment', 'ual_shook_delete_comment');
add_action('wp_update_nav_menu', 'ual_shook_wp_update_nav_menu');
add_action('wp_create_nav_menu', 'ual_shook_wp_create_nav_menu');
add_action('delete_nav_menu', 'ual_shook_delete_nav_menu', 10, 2);
add_action('activated_plugin', 'ual_shook_activated_plugin');
add_action('deactivated_plugin', 'ual_shook_deactivated_plugin');
add_action('created_term', 'ual_shook_created_term', 10, 2);
add_action('edited_term', 'ual_shook_edited_term', 10, 2);
add_action('delete_term', 'ual_shook_delete_term', 10, 3);
add_action('switch_theme', 'ual_shook_switch_theme');
add_action('customize_save', 'ual_shook_customize_save');
add_action('export_wp', 'ual_shook_export_wp');
add_action('transition_post_status', 'ual_shook_transition_post_status', 10, 3);
add_action('delete_site_transient_update_themes', 'ual_shook_theme_deleted');

/*
 * Get activity for the user - Login fail
 *
 * @param string $user username
 */
if (!function_exists('ual_shook_wp_login_failed')):

    function ual_shook_wp_login_failed($user) {
        $action = "login failed";
        $obj_type = "user";
        $post_id = "";
        $post_title = $user;
        $current_user = $user;
        $modified_date = current_time('mysql');
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = get_user_by('login', $current_user);
        $current_user_id = $user->ID;
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role)
                $user_role = $role;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $get_emails = "SELECT * from $table_name where user_login='$current_user'";
        $mails = $wpdb->get_results($get_emails);
        foreach ($mails as $k => $v) {
            $user_mail = $v->user_email;
        }
        $current_user_display_name = $user->display_name;
        ual_user_activity_add($post_id, $post_title, $obj_type, $current_user_id, $current_user_display_name, $user_role, $user_mail, $modified_date, $ip, $action);
    }

endif;
add_filter('wp_login_failed', 'ual_shook_wp_login_failed');

/*
 * Get activity for the user - Widget update
 *
 * @param string $widget widget data
 */
if (!function_exists('ual_shook_widget_update_callback')):

    function ual_shook_widget_update_callback($widget) {
        $action = "widget updated";
        $obj_type = "widget";
        $post_id = "";
        $post_title = "Sidebar Widget";
        ual_get_activity_function($action, $obj_type, $post_id, $post_title);
        return $widget;
    }

endif;
add_filter('widget_update_callback', 'ual_shook_widget_update_callback');

/*
 * Input validation function
 *
 * @param string $data input data
 */
if (!function_exists('ual_test_input')) {

    function ual_test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}

if (!function_exists('admin_notice_message')) {

    /**
     * Display success or error message
     *
     * @param string $class
     * @param string $message
     * @return string message with HTML
     */
    function admin_notice_message($class, $message) {
        ?>
        <div class="<?php _e($class, 'wp_user_log'); ?> is-dismissible notice settings-error">
            <p><?php _e($message, 'wp_user_log'); ?></p>
        </div>
        <?php
    }

}

/**
 *
 * @param $actions for take a action for redirection setting
 * @param $plugin_file give path of plugin file
 * @return action for setting link
 */
if (!function_exists('ual_settings_link')) {

    function ual_settings_link($actions, $plugin_file) {
        static $plugin;
        if (empty($plugin))
            $plugin = dirname(plugin_basename(__FILE__)) . '/user_activity_log.php';
        if ($plugin_file == $plugin) {
            $settings_link = '<a href="' . admin_url('admin.php?page=general_settings_menu') . '">' . __('Settings', 'wp_user_log') . '</a>';
            array_unshift($actions, $settings_link);
        }
        return $actions;
    }

}
add_filter('plugin_action_links', 'ual_settings_link', 10, 2);

/*
 * add notice at admin side
 * @global object $current_user
 */
if (!function_exists('ual_plugin_upgrade_notice')) {

    function ual_plugin_upgrade_notice() {
        global $current_user;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
        if (!get_user_meta($user_id, 'ual_plugin_upgrade_notice') && current_user_can('manage_options')) {
            ?>
            <div class="updated notice is-dismissible"><?php
                $genre_url = add_query_arg('ual_plugin_upgrade_notice', 0, get_permalink());
                ?>
                <p><?php _e('User Activity Log Plugin : ', 'wp_user_log'); ?>
                    <a href="http://solwininfotech.com/documents/wordpress/user-activity-log-pro/" target="_blank" style="text-decoration: underline">
                        <strong><?php _e('Live Documentation', 'wp_user_log'); ?></strong>
                    </a>
                </p>
                <p>
                    <?php _e('Want more user activity log features?', 'wp_user_log'); ?>
                    <a href="https://codecanyon.net/item/user-activity-log-pro-for-wordpress/18201203?ref=solwin" target="_blank" style="text-decoration: underline">
                        <strong><?php _e('Upgrade to PRO', 'wp_user_log'); ?></strong>
                    </a>
                </p>
            </div>
            <?php
        }
    }

}
add_action('admin_notices', 'ual_plugin_upgrade_notice');

/**
 * add user meta for ignore notice
 * @global object $current_user
 */
if (!function_exists('ual_ignore_upgrade_notice')) {

    function ual_ignore_upgrade_notice() {
        global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if (isset($_GET['ual_plugin_upgrade_notice']) && '0' == $_GET['ual_plugin_upgrade_notice']) {
            add_user_meta($user_id, 'ual_plugin_upgrade_notice', 'true', true);
        }
    }

}
add_action('admin_init', 'ual_ignore_upgrade_notice');

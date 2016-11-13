<?php
/*
  Plugin Name: User Activity Log
  Plugin URI: https://wordpress.org/plugins/user-activity-log/
  Description: Log the activity of users and roles to monitor your site with actions
  Author: Solwin Infotech
  Author URI: https://www.solwininfotech.com/
  Version: 1.2.6
  Requires at least: 4.0
  Tested up to: 4.6.1
  Copyright: Solwin Infotech
  License: GPLv2 or later
 */

/*
  Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/*
  Define variables
 */
define('UAL_PLUGIN_DIR', plugin_dir_path(__FILE__));
include(UAL_PLUGIN_DIR . 'user_functions.php');
include(UAL_PLUGIN_DIR . 'user_settings_menu.php');
add_action('init', 'ual_filter_data');
add_action('plugins_loaded', 'latest_news_solwin_feed');
add_action('current_screen', 'ual_footer');
add_filter('set-screen-option', 'ual_set_screen_option', 10, 3);

/**
 * Load plugin text domain (wp_user_log)
 */
add_action('plugins_loaded', 'load_text_domain_user_activity_log');

if (!function_exists('load_text_domain_user_activity_log')) {

    function load_text_domain_user_activity_log() {
        load_plugin_textdomain('wp_user_log', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

}

/**
 * Add Admin Dashboard Widget - News from Solwin Infotech
 */
if (!function_exists('latest_news_solwin_feed')) {

    function latest_news_solwin_feed() {
        // Register the new dashboard widget with the 'wp_dashboard_setup' action
        add_action('wp_dashboard_setup', 'solwin_latest_news_with_product_details');
        if (!function_exists('solwin_latest_news_with_product_details')) {

            function solwin_latest_news_with_product_details() {
                add_screen_option('layout_columns', array('max' => 3, 'default' => 2));
                add_meta_box('wp_user_log_dashboard_widget', __('News From Solwin Infotech', 'wp_user_log'), 'solwin_dashboard_widget_news', 'dashboard', 'normal', 'high');
            }

        }
        if (!function_exists('solwin_dashboard_widget_news')) {

            function solwin_dashboard_widget_news() {
                echo '<div class="rss-widget">'
                . '<div class="solwin-news"><p><strong>Solwin Infotech News</strong></p>';
                wp_widget_rss_output(array(
                    'url' => 'https://www.solwininfotech.com/feed/',
                    'title' => __('News From Solwin Infotech', 'wp_user_log'),
                    'items' => 5,
                    'show_summary' => 0,
                    'show_author' => 0,
                    'show_date' => 1
                ));
                echo '</div>';
                $title = $link = $thumbnail = "";
                //get Latest product detail from xml file

                $file = 'https://www.solwininfotech.com/documents/assets/latest_product.xml';
                echo '<div class="display-product">'
                . '<div class="product-detail"><p><strong>' . __('Latest Product', 'wp_user_log') . '</strong></p>';
                $response = wp_remote_post($file);
                if (is_wp_error($response)) {
                    $error_message = $response->get_error_message();
                    echo "<p>" . __('Something went wrong', 'wp_user_log') . " : $error_message" . "</p>";
                } else {
                    $body = wp_remote_retrieve_body($response);
                    $xml = simplexml_load_string($body);
                    $title = $xml->item->name;
                    $thumbnail = $xml->item->img;
                    $link = $xml->item->link;

                    $allProducttext = $xml->item->viewalltext;
                    $allProductlink = $xml->item->viewalllink;
                    $moretext = $xml->item->moretext;
                    $needsupporttext = $xml->item->needsupporttext;
                    $needsupportlink = $xml->item->needsupportlink;
                    $customservicetext = $xml->item->customservicetext;
                    $customservicelink = $xml->item->customservicelink;
                    $joinproductclubtext = $xml->item->joinproductclubtext;
                    $joinproductclublink = $xml->item->joinproductclublink;

                    echo '<div class="product-name"><a href="' . $link . '" target="_blank">'
                    . '<img alt="' . $title . '" src="' . $thumbnail . '"> </a>'
                    . '<a href="' . $link . '" target="_blank">' . $title . '</a>'
                    . '<p><a href="' . $allProductlink . '" target="_blank" class="button button-default">' . $allProducttext . ' &RightArrow;</a></p>'
                    . '<hr>'
                    . '<p><strong>' . $moretext . '</strong></p>'
                    . '<ul>'
                    . '<li><a href="' . $needsupportlink . '" target="_blank">' . $needsupporttext . '</a></li>'
                    . '<li><a href="' . $customservicelink . '" target="_blank">' . $customservicetext . '</a></li>'
                    . '<li><a href="' . $joinproductclublink . '" target="_blank">' . $joinproductclubtext . '</a></li>'
                    . '</ul>'
                    . '</div>';
                }
                echo '</div></div><div class="clear"></div>'
                . '</div>';
            }

        }
    }

}

/**
 * Add Footer link
 */
if (!function_exists('ual_footer')) {

    function ual_footer() {
        $screen = get_current_screen();
        if ($screen->id == "toplevel_page_user_action_log" || $screen->id == "user-action-log_page_general_settings_menu" || $screen->id == "admin_page_email_settings_menu" || $screen->id == "admin_page_user_settings_menu") {
            add_filter('admin_footer_text', 'ual_remove_footer_admin'); //change admin footer text
        }
    }

}

/**
 * Add rating html at footer of admin
 * @return html rating
 */
if (!function_exists('ual_remove_footer_admin')) {

    function ual_remove_footer_admin() {
        ob_start();
        ?>
        <p id="footer-left" class="alignleft">
            <?php _e('If you like ', 'wp_user_log'); ?>
            <a href="https://www.solwininfotech.com/product/wordpress-plugins/user-activity-log/" target="_blank"><strong><?php _e('User Activity Log Plugin', 'wp_user_log'); ?></strong></a>
            <?php _e('please leave us a', 'wp_user_log'); ?>
            <a class="bdp-rating-link" data-rated="Thanks :)" target="_blank" href="https://wordpress.org/support/plugin/user-activity-log/reviews/?filter=5#new-post">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</a>
            <?php _e('rating. A heartly thank you from Solwin Infotech in advance!', 'wp_user_log'); ?>
        </p>
        <?php
        return ob_get_clean();
    }

}

/**
 * function for set the value in header
 */
if (!function_exists('ual_filter_data')):

    function ual_filter_data() {

        wp_register_style('ual-style-css', plugins_url('css/style.css', __FILE__));
        wp_enqueue_style('ual-style-css');
        $admin_url = get_admin_url();
        $paged = 1;
        $u_role = $u_name = $o_type = $txtSearch = "";

        // For filtering data
        if (isset($_POST['btn_filter'])) {
            if (isset($_POST['role']) && $_POST['role'] != '0') {
                $u_role = ual_test_input($_POST['role']);
                $where.=" and user_role='$u_role'";
            }
            if (isset($_POST['user']) && $_POST['user'] != '0') {
                $u_name = ual_test_input($_POST['user']);
                $where.=" and user_name='$u_name'";
            }
            if (isset($_POST['post_type']) && $_POST['post_type'] != '0') {
                $o_type = ual_test_input($_POST['post_type']);
                $where.=" and object_type='$o_type'";
            }
            header("Location: $admin_url?page=user_action_log&paged=$paged&userrole=$u_role&username=$u_name&type=$o_type&txtsearch=$txtSearch", true);
            exit();
        }
        if (isset($_POST['btnSearch']) && $_POST['btnSearch']) {
            $txtSearch = ual_test_input($_POST['txtSearchinput']);
            header("Location: $admin_url?page=user_action_log&paged=$paged&userrole=$u_role&username=$u_name&type=$o_type&txtsearch=$txtSearch", true);
            exit();
        }
    }

endif;
add_action('admin_menu', 'ual_user_activity');

/*
 * for creating admin side pages
 */
if (!function_exists('ual_user_activity')):

    function ual_user_activity() {
        global $screen_option_page;
        $screen_option_page = add_menu_page(__('User Action Log', 'wp_user_log'), __('User Action Log', 'wp_user_log'), 'manage_options', 'user_action_log', 'ual_user_activity_function', 'dashicons-admin-users');
        add_action("load-$screen_option_page", 'ual_screen_options');
        add_submenu_page('user_action_log', __('General Settings', 'wp_user_log'), __('Settings', 'wp_user_log'), 'manage_options', 'general_settings_menu', 'ual_general_settings', 'dashicons-admin-users');
        $generalpage = add_submenu_page(__('Notification Settings', 'wp_user_log'), __('User Action Log', 'wp_user_log'), __('General', 'wp_user_log'), 'manage_options', 'user_settings_menu', 'ual_user_activity_setting_function', 'dashicons-admin-users');
        $emailpage = add_submenu_page(__('Email Settings', 'wp_user_log'), __('Email Settings', 'wp_user_log'), __('Email', 'wp_user_log'), 'manage_options', 'email_settings_menu', 'ual_email_settings', 'dashicons-admin-users');
    }

endif;


/**
 * add per page option in screen option in single post templates list
 * @global string $bdp_screen_option_page
 */
if (!function_exists('ual_screen_options')) {

    function ual_screen_options() {
        global $screen_option_page;
        $screen = get_current_screen();

        // get out of here if we are not on our settings page
        if (!is_object($screen) || $screen->id != $screen_option_page)
            return;

        $args = array(
            'label' => __('Number of items per page:', 'wp_user_log'),
            'default' => 10,
            'option' => 'ual_per_page'
        );
        add_screen_option('per_page', $args);
    }

}

/**
 *
 * @param type $status
 * @param type $option
 * @param type $value
 * @return type
 */
if (!function_exists('ual_set_screen_option')) {

    function ual_set_screen_option($status, $option, $value) {
        if ('ual_per_page' == $option)
            return $value;
    }

}

/*
 * Display all the user activity log data
 */

if (!function_exists('ual_user_activity_function')):

    function ual_user_activity_function() {
        global $wpdb;
        $paged = $total_pages = 1;
        $srno = 0;
        $user = get_current_user_id();
        $screen = get_current_screen();
        $screen_option = $screen->get_option('per_page', 'option');
        $limit = get_user_meta($user, $screen_option, true);
        $recordperpage = 10;
        if (isset($_GET['page']) && absint($_GET['page'])) {
            $recordperpage = absint($_GET['page']);
        } elseif (isset($limit)) {
            $recordperpage = $limit;
        } else {
            $recordperpage = get_option('posts_per_page');
        }
        if (!isset($recordperpage) || empty($recordperpage)) {
            $recordperpage = 10;
        }
        if (!isset($limit) || empty($limit)) {
            $limit = 10;
        }
        $table_name = $wpdb->prefix . "ualp_user_activity";
        $where = "where 1=1";
        $u_role = $u_name = $o_type = "";

        if (isset($_GET['paged']))
            $paged = ual_test_input($_GET['paged']);

        if (isset($_POST['paged']))
            $paged = $_POST['paged'];

        $offset = ($paged - 1) * $recordperpage;
        $us_role = $us_name = $ob_type = $searchtxt = "";
        if (isset($_GET['userrole']) && $_GET['userrole'] != "") {
            $us_role = ual_test_input($_GET['userrole']);
            $where.=" and user_role='$us_role'";
        }
        if (isset($_GET['username']) && $_GET['username'] != "") {
            $us_name = ual_test_input($_GET['username']);
            $where.=" and user_name='$us_name'";
        }
        if (isset($_GET['type']) && $_GET['type'] != "") {
            $ob_type = ual_test_input($_GET['type']);
            $where.=" and object_type='$ob_type'";
        }
        if (isset($_GET['txtsearch']) && $_GET['txtsearch'] != "") {
            $searchtxt = ual_test_input($_GET['txtsearch']);
            $where.=" and user_name like '$searchtxt' or user_role like '$searchtxt' or object_type like '$searchtxt' or action like '$searchtxt'";
        }

        // query for display all the user activity data start
        $select_query = $get_data = $total_items_query = $total_items = "";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) {
            $select_query = "SELECT * from $table_name $where ORDER BY modified_date desc LIMIT $offset,$recordperpage";
            $get_data = $wpdb->get_results($select_query);
            $total_items_query = "SELECT count(*) FROM $table_name $where";
            $total_items = $wpdb->get_var($total_items_query, 0, 0);
        }

        // query for display all the user activity data end
        // for pagination
        $total_pages = ceil($total_items / $recordperpage);
        $next_page = (int) $paged + 1;
        if ($next_page > $total_pages)
            $next_page = $total_pages;
        $prev_page = (int) $paged - 1;
        if ($prev_page < 1)
            $prev_page = 1;
        ?>
        <div class="wrap">
            <h2><?php _e('User Activity Log', 'wp_user_log'); ?></h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?" . $_SERVER['QUERY_STRING']); ?>" class="frm-user-activity">
                <div class="tablenav top">
                    <div class="wp-filter">

                        <!-- Drop down menu for Role Start -->
                        <div class="alignleft actions">
                            <select name="role">
                                <option selected value="0"><?php _e('All Role', 'wp_user_log'); ?></option>
                                <?php
                                $role_query = "SELECT distinct user_role from $table_name";
                                $get_roles = $wpdb->get_results($role_query);
                                foreach ($get_roles as $role) {
                                    $user_role = $role->user_role;
                                    if ($user_role != "") {
                                        ?>
                                        <option value="<?php echo $user_role; ?>" <?php echo selected($us_role, $user_role); ?>><?php printf(__('%s', 'wp_user_log'), ucfirst($user_role)); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Drop down menu for Role end -->
                        <!-- Drop down menu for User Start -->
                        <div class="alignleft actions">
                            <select name="user" class="sol-dropdown">
                                <option selected value="0"><?php _e('All User', 'wp_user_log'); ?></option>
                                <?php
                                $username_query = "SELECT distinct user_name from $table_name";
                                $get_username = $wpdb->get_results($username_query);
                                foreach ($get_username as $username) {
                                    $user_name = $username->user_name;
                                    if ($user_name != "") {
                                        ?>
                                        <option value="<?php echo $user_name; ?>" <?php echo selected($us_name, $user_name); ?>><?php printf(__('%s', 'wp_user_log'), ucfirst($user_name)); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Drop down menu for User end -->
                        <!-- Drop down menu for Post type Start -->
                        <div class="alignleft actions">
                            <select name="post_type">
                                <option selected value="0"><?php _e('All Type', 'wp_user_log'); ?></option>
                                <?php
                                $object_type_query = "SELECT distinct object_type from $table_name";
                                $get_type = $wpdb->get_results($object_type_query);
                                foreach ($get_type as $type) {
                                    $object_type = $type->object_type;
                                    if ($object_type != "") {
                                        ?>
                                        <option value="<?php echo $object_type; ?>" <?php echo selected($ob_type, $object_type); ?>><?php printf(__('%s', 'wp_user_log'), ucfirst($object_type)); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <input class="button-secondary action sol-filter-btn" type="submit" value="Filter" name="btn_filter">
                        </div>
                        <!-- Drop down menu for Post type end -->

                        <!-- Search Box start -->
                        <div class="sol-search-div">
                            <p class="search-box">
                                <label class="screen-reader-text" for="search-input"><?php _e('Search', 'wp_user_log'); ?> :</label>
                                <input id="user-search-input" type="search" placeholder="User, Role, Action" value="<?php printf(__('%s', 'wp_user_log'), $searchtxt); ?>" name="txtSearchinput">
                                <input id="search-submit" class="button" type="submit" value="<?php esc_attr_e('Search', 'wp_user_log'); ?>" name="btnSearch">
                            </p>
                        </div>
                        <!-- Search Box end -->

                    </div>
                    <!-- Top pagination start -->
                    <div class="tablenav-pages">
                        <?php $items = sprintf(_n('%s item', '%s items', $total_items, 'wp_user_log'), $total_items); ?>
                        <span class="displaying-num"><?php echo $items; ?></span>
                        <div class="tablenav-pages" <?php
                        if ((int) $total_pages <= 1) {
                            echo 'style="display:none;"';
                        }
                        ?>>
                            <span class="pagination-links">
                                <?php if ($paged == '1') { ?>
                                    <span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>
                                    <span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>
                                <?php } else {
                                    ?>
                                    <a class="first-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=1&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the first page">&laquo;</a>
                                    <a class="prev-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $prev_page . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the previous page">&lsaquo;</a>
                                <?php } ?>
                                <span class="paging-input">
                                    <input class="current-page" type="text" size="1" value="<?php echo $paged; ?>" name="paged" title="Current page"> of
                                    <span class="total-pages"><?php echo $total_pages; ?></span>
                                </span>
                                <a class="next-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $next_page . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the next page">&rsaquo;</a>
                                <a class="last-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $total_pages . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the last page">&raquo;</a>
                            </span>
                        </div>
                    </div>
                    <!-- Top pagination end -->
                </div>
                <!-- Table for display user action start -->
                <table class="widefat post fixed striped" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 25px" scope="col" class="manage-column column-check"><?php _e('No.', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Date', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Author', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('IP Address', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Type', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Action', 'wp_user_log'); ?></th>
                            <th scope="col" colspan="2"><?php _e('Description', 'wp_user_log'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th style="width: 25px" scope="col" class="manage-column column-check"><?php _e('No.', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Date', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Author', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('IP Address', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Type', 'wp_user_log'); ?></th>
                            <th scope="col"><?php _e('Action', 'wp_user_log'); ?></th>
                            <th scope="col" colspan="2"><?php _e('Description', 'wp_user_log'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($get_data) {
                            $srno = 1 + $offset;
                            foreach ($get_data as $data) {
                                ?>
                                <tr>
                                    <td class="check column-check"><?php
                                        echo $srno;
                                        $srno++;
                                        ?></td>
                                    <td><?php echo $data->modified_date; ?></td>
                                    <td class="user_id column-user_id" data-colname="Author">
                                        <a href="<?php echo get_edit_user_link($data->user_id); ?>">
                                            <?php echo get_avatar($data->user_id, 40); ?>
                                            <span><?php echo ucfirst($data->user_name); ?></span>
                                        </a><br>
                                        <small><?php echo ucfirst($data->user_role); ?></small><br>
                                        <?php echo $data->user_email; ?>
                                    </td>
                                    <td><?php echo $data->ip_address; ?></td>
                                    <td><?php echo ucfirst($data->object_type); ?></td>
                                    <td><?php echo ucfirst($data->action); ?></td>
                                    <td class="column-description" colspan="2">
                                        <?php if (($data->object_type == "post" || $data->object_type == "page") && $data->action != 'post deleted' && $data->action != 'page deleted') { ?>
                                            <a href="<?php echo get_permalink($data->post_id); ?>">
                                                <?php echo ucfirst($data->post_title); ?>
                                            </a><?php
                                        } else {
                                            echo ucfirst($data->post_title);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr class="no-items">';
                            echo '<td class="colspanchange" colspan="8">' . __('No record found.', 'wp_user_log') . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Table for display user action end -->
                <!-- Bottom pagination start -->
                <div class="tablenav top">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo $items; ?></span>
                        <div class="tablenav-pages" <?php
                        if ((int) $total_pages <= 1) {
                            echo 'style="display:none;"';
                        }
                        ?>>
                            <span class="pagination-links">
                                <?php if ($paged == '1') { ?>
                                    <span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>
                                    <span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>
                                <?php } else {
                                    ?>
                                    <a class="first-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=1&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the first page">&laquo;</a>
                                    <a class="prev-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $prev_page . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the previous page">&lsaquo;</a>
                                <?php } ?>
                                <span class="paging-input">
                                    <span class="current-page" title="Current page"><?php echo $paged; ?></span> <?php _e('of', 'wp_user_log'); ?>
                                    <!--<input class="current-page" type="text" size="1" value="<?php // echo $paged;                      ?>" name="paged" title="Current page"> of-->
                                    <span class="total-pages"><?php echo $total_pages; ?></span>
                                </span>
                                <a class="next-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $next_page . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the next page">&rsaquo;</a>
                                <a class="last-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=user_action_log&paged=' . $total_pages . '&userrole=' . $us_role . '&username=' . $us_name . '&type=' . $ob_type . '&txtsearch=' . $searchtxt; ?>" title="Go to the last page">&raquo;</a>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Bottom pagination end -->
            </form>

        </div>
        <?php
    }

endif;

if (!function_exists('ual_advertisment_sidebar')) {

    function ual_advertisment_sidebar() {
        ?>
        <div class="user-activity-ad-block">
            <div class="ual-help">
                <h2><?php _e('Help to improve this plugin!', 'wp_user_log'); ?></h2>
                <div class="help-wrapper">
                    <span><?php _e('Enjoyed this plugin?', 'wp_user_log'); ?></span>
                    <span><?php _e('You can help by', 'wp_user_log'); ?>
                        <a href="https://wordpress.org/support/plugin/user-activity-log/reviews/?filter=5#new-post" target="_blank">
                            <?php _e(' rating this plugin on wordpress.org', 'wp_user_log'); ?>
                        </a>
                    </span>
                    <div class="ual-total-download">
                        <?php _e('Downloads:', 'wp_user_log'); ?><?php get_total_downloads_user_activity_log_plugin(); ?>
                        <?php
                        $wp_version = get_bloginfo('version');
                        if ($wp_version > 3.8) {
                            wp_custom_star_rating_user_activity_log();
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="useful_plugins">
                <h2><?php _e('User Activity Log Pro', 'wp_user_log'); ?></h2>
                <div class="help-wrapper">
                    <div class="pro-content">
                        <ul class="advertisementContent">
                            <li><?php _e("Export logs in CSV Format", 'wp_user_log') ?></li>
                            <li><?php _e("View Detail logs(Old/New comparison)", 'wp_user_log') ?></li>
                            <li><?php _e("Delete Logs", 'wp_user_log') ?></li>
                            <li><?php _e("Favorite/Unfavorite Logs", 'wp_user_log') ?></li>
                            <li><?php _e("Password Security", 'wp_user_log') ?></li>
                            <li><?php _e("Role selection option for display logs", 'wp_user_log') ?></li>
                            <li><?php _e("Hook Selection option to monitor activity", 'wp_user_log') ?></li>
                            <li><?php _e("Add Custom event to track the logs", 'wp_user_log') ?></li>
                            <li><?php _e("Sort and Filter logs", 'wp_user_log') ?></li>
                        </ul>
                        <p class="pricing_change"><?php _e("Buy Now only at ", 'wp_user_log') ?><ins><?php _e("$24", 'wp_user_log') ?></ins></p>
                    </div>
                    <div class="pre-book-pro">
                        <a href="https://codecanyon.net/item/user-activity-log-pro-for-wordpress/18201203?ref=solwin" target="_blank">
                            <?php _e('Buy Now', 'wp_user_log'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="ual-support">
                <h3><?php _e('Need Support?', 'wp_user_log'); ?></h3>
                <div class="help-wrapper">
                    <span><?php _e('Check out the', 'wp_user_log') ?>
                        <a href="https://wordpress.org/plugins/user-activity-log/faq/" target="_blank"><?php _e('FAQs', 'wp_user_log'); ?></a>
                        <?php _e('and', 'wp_user_log') ?>
                        <a href="https://wordpress.org/support/plugin/user-activity-log" target="_blank"><?php _e('Support Forums', 'wp_user_log') ?></a>
                    </span>
                </div>
            </div>
            <div class="ual-support">
                <h3><?php _e('Share & Follow Us', 'wp_user_log'); ?></h3>
                <div class="help-wrapper">
                    <!-- Twitter -->
                    <div style='display:block;margin-bottom:8px;'>
                        <a href="https://twitter.com/solwininfotech" class="twitter-follow-button" data-show-count="true" data-show-screen-name="true" data-dnt="true">Follow @solwininfotech</a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = p + '://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, 'script', 'twitter-wjs');</script>
                    </div>
                    <!-- Facebook -->
                    <div style='display:block;margin-bottom: 10px;'>
                        <div id="fb-root"></div>
                        <script>(function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id))
                                    return;
                                js = d.createElement(s);
                                js.id = id;
                                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>
                        <div class="fb-share-button" data-href="https://wordpress.org/plugins/user-activity-log/" data-layout="button_count"></div>
                    </div>
                    <!-- Google Plus -->
                    <div style='display:block;margin-bottom: 8px;'>
                        <!-- Place this tag where you want the +1 button to render. -->
                        <div class="g-plusone" data-href="https://wordpress.org/plugins/user-activity-log/"></div>
                        <!-- Place this tag after the last +1 button tag. -->
                        <script type="text/javascript">
                            (function () {
                                var po = document.createElement('script');
                                po.type = 'text/javascript';
                                po.async = true;
                                po.src = 'https://apis.google.com/js/platform.js';
                                var s = document.getElementsByTagName('script')[0];
                                s.parentNode.insertBefore(po, s);
                            })();
                        </script>
                    </div>
                    <div style='display:block;margin-bottom: 8px;'>
                        <script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
                        <script type="IN/Share" data-url="https://wordpress.org/plugins/user-activity-log/" data-counter="right" data-showzero="true"></script>
                    </div>
                </div>
            </div>
        </div><?php
    }

}
// Deactivate user activity pro plugin when user activity lite is activate
register_activation_hook(__FILE__, 'ualDeactivateUalp');
if (!function_exists('ualDeactivateUalp')) {

    function ualDeactivateUalp() {
        if (is_plugin_active('user-activity-log-pro/user_activity_log_pro.php')) {
            deactivate_plugins('user-activity-log-pro/user_activity_log_pro.php');
        }
    }

}
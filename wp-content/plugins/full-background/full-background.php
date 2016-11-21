<?php
/*
Plugin Name: Full Background
Plugin URI: http://wp-plugins.in/wordpress-background-image
Description: Add responsive full background to your website easily, random background support and unlimited backgrounds, compatible with all major browsers and with phone and tablet.
Version: 1.0.2
Author: Alobaidi
Author URI: http://wp-plugins.in
License: GPLv2 or later
*/

/*  Copyright 2015 Alobaidi (email: wp-plugins@outlook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
    

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function alobaidi_full_background_plugin_row_meta( $links, $file ) {

	if ( strpos( $file, 'full-background.php' ) !== false ) {
		
		$new_links = array(
						'<a href="http://wp-plugins.in/wordpress-background-image" target="_blank">Explanation of Use</a>',
						'<a href="https://profiles.wordpress.org/alobaidi#content-plugins" target="_blank">More Plugins</a>',
						'<a href="http://j.mp/ET_WPTime_ref_pl" target="_blank">Elegant Themes</a>'
					);
		
		$links = array_merge( $links, $new_links );
		
	}
	
	return $links;
	
}
add_filter( 'plugin_row_meta', 'alobaidi_full_background_plugin_row_meta', 10, 2 );


function alobaidi_full_background_plugin_action_links( $actions, $plugin_file ){
	
	static $plugin;

	if ( !isset($plugin) ){
		$plugin = plugin_basename(__FILE__);
	}
		
	if ($plugin == $plugin_file) {
		
		if ( is_ssl() ) {
			$settings_link = '<a href="'.admin_url( 'plugins.php?page=alobaidi_full_background', 'https' ).'">Settings</a>';
		}else{
			$settings_link = '<a href="'.admin_url( 'plugins.php?page=alobaidi_full_background', 'http' ).'">Settings</a>';
		}
		
		$settings = array($settings_link);
		
		$actions = array_merge($settings, $actions);
			
	}
	
	return $actions;
	
}
add_filter( 'plugin_action_links', 'alobaidi_full_background_plugin_action_links', 10, 5 );


	function alobaidi_full_background() {
		add_plugins_page( 'Full Background Settings', 'Full Background', 'manage_options', 'alobaidi_full_background', 'alobaidi_full_background_settings' );
	}
	add_action( 'admin_menu', 'alobaidi_full_background' );


	function alobaidi_full_background_register_setting() {
		register_setting( 'alobaidi_setting_background_link', 'alobaidi_full_background_random' );
	}
	add_action( 'admin_init', 'alobaidi_full_background_register_setting' );
	
		
	function alobaidi_full_background_settings(){
		?>
			<div class="wrap">
				<h2>Full Background Settings</h2>

				<?php if( isset($_GET['settings-updated']) && $_GET['settings-updated'] ){ ?>
					<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
						<p><strong>Settings saved.</strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
					</div>
				<?php } ?>
                
            	<form method="post" action="options.php">
                	<?php settings_fields( 'alobaidi_setting_background_link' ); ?>
                	<table class="form-table">
                		<tbody>

                            <tr>
                                <th><label for="alobaidi_full_background_random">Backgrounds Links</label></th>
                                <td>
                                    <textarea id="alobaidi_full_background_random" name="alobaidi_full_background_random" rows="10" cols="50" class="large-text code" style="white-space:nowrap !important;"><?php echo esc_textarea( get_option('alobaidi_full_background_random') ); ?></textarea>
                                    <p class="description">Enter list of backgrounds links, one link per line, will be display random background, but if you want one background only, enter one link only. <?php if ( is_ssl() ) {echo '<a href="'.admin_url( 'media-new.php', 'https' ).'" target="_blank">Upload Backgrounds</a>';}else{echo '<a href="'.admin_url( 'media-new.php', 'http' ).'" target="_blank">Upload Backgrounds</a>';}?></p>
                                </td>
                            </tr>

                    	</tbody>
                    </table>
                    <p class="submit"><input id="submit" class="button button-primary" type="submit" name="submit" value="Save Changes"></p>
                </form>
            	<div class="tool-box">
					<h3 class="title">Recommended Links</h3>
					<p>Get collection of 87 WordPress themes for $69 only, a lot of features and free support! <a href="http://j.mp/ET_WPTime_ref_pl" target="_blank">Get it now</a>.</p>
					<p>See also:</p>
						<ul>
							<li><a href="http://j.mp/CM_WPTime" target="_blank">Premium WordPress themes on CreativeMarket.</a></li>
							<li><a href="http://j.mp/TF_WPTime" target="_blank">Premium WordPress themes on Themeforest.</a></li>
							<li><a href="http://j.mp/CC_WPTime" target="_blank">Premium WordPress plugins on Codecanyon.</a></li>
						</ul>
					<p><a href="http://j.mp/ET_WPTime_ref_pl" target="_blank"><img src="<?php echo plugins_url( '/banner/570x100.jpg', __FILE__ ); ?>"></a></p>
				</div>
            </div>
		<?php 
	}


function alobaidi_full_background_css(){
	?>

		<?php if( get_option('alobaidi_full_background_random') )  : ?>

    		<?php
    			$get_links 			= 	str_replace(' ', '', get_option('alobaidi_full_background_random') );
    			$preg_replace 		= 	preg_replace( "/\s+/", "\n", $get_links );
    			$explode 			= 	explode("\n", $preg_replace);
    			$make_array 		= 	(array) $explode;
    			$array 				=	$make_array;
    			$count				=	count($array) - 1;
    			$random 			=	rand(0, $count);
    			$background_link 	= 	$array[$random];
    		?>

			<style type="text/css">
				/* Alobaidi Full Background Plugin */
				html{
					background-image:none !important;
					background:none !important;
				}
				
				body{
					background-image:none !important;
					background:url(<?php echo $background_link; ?>) 0 0 fixed no-repeat !important;
					background-size:100% 100% !important;
					-webkit-background-size:100% 100% !important;
					-moz-background-size:100% 100% !important;
					-ms-background-size:100% 100% !important;
					-o-background-size:100% 100% !important;
				}
				/*
				body.logged-in{
					background-position: 0 -32px !important;
				}
				*/
			</style>
		<?php endif; ?>
    
	<?php
}
add_action( 'wp_head', 'alobaidi_full_background_css', 999 );


?>
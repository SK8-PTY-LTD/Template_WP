<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class wpsm_team_b {
	private static $instance;
    public static function forge() {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }
	
	private function __construct() {
		add_action('admin_enqueue_scripts', array(&$this, 'wpsm_team_b_admin_scripts'));
        if (is_admin()) {
			add_action('init', array(&$this, 'team_b_register_cpt'), 1);
			add_action('add_meta_boxes', array(&$this, 'wpsm_team_b_meta_boxes_group'));
			add_action('admin_init', array(&$this, 'wpsm_team_b_meta_boxes_group'), 1);
			add_action('save_post', array(&$this, 'add_team_b_save_meta_box_save'), 9, 1);
			add_action('save_post', array(&$this, 'team_b_settings_meta_box_save'), 9, 1);
		}
    }
	// admin scripts
	public function wpsm_team_b_admin_scripts(){
		if(get_post_type()=="team_builder"){
			
			wp_enqueue_script('theme-preview');
			wp_enqueue_media();
			wp_enqueue_script('jquery-ui-datepicker');
			//color-picker css n js
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style('thickbox');
			wp_enqueue_script( 'wpsm_team_b-color-pic', wpshopmart_team_b_directory_url.'assets/js/color-picker.js', array( 'wp-color-picker' ), false, true );
			wp_enqueue_style('wpsm_team_b-panel-style', wpshopmart_team_b_directory_url.'assets/css/panel-style.css');
			 wp_enqueue_script('wpsm_team_b-media-uploads',wpshopmart_team_b_directory_url.'assets/js/media-upload-script.js',array('media-upload','thickbox','jquery')); 
			//font awesome css
			wp_enqueue_style('wpsm_team_b-font-awesome', wpshopmart_team_b_directory_url.'assets/css/font-awesome/css/font-awesome.min.css');
			wp_enqueue_style('wpsm_team_b_bootstrap', wpshopmart_team_b_directory_url.'assets/css/bootstrap.css');
			wp_enqueue_style('wpsm_team_b_jquery-css', wpshopmart_team_b_directory_url .'assets/css/ac_jquery-ui.css');
			
			//css line editor
			wp_enqueue_style('wpsm_team_b_line-edtor', wpshopmart_team_b_directory_url.'assets/css/jquery-linedtextarea.css');
			wp_enqueue_script( 'wpsm_team_b-line-edit-js', wpshopmart_team_b_directory_url.'assets/js/jquery-linedtextarea.js');
			
			wp_enqueue_script( 'wpsm_tabs_bootstrap-js', wpshopmart_team_b_directory_url.'assets/js/bootstrap.js');
			
			//tooltip
			wp_enqueue_style('wpsm_team_b_tooltip', wpshopmart_team_b_directory_url.'assets/tooltip/darktooltip.css');
			wp_enqueue_script( 'wpsm_team_b-tooltip-js', wpshopmart_team_b_directory_url.'assets/tooltip/jquery.darktooltip.js');
			
			// tab settings
			wp_enqueue_style('wpsm_team_b_settings-css', wpshopmart_team_b_directory_url.'assets/css/settings.css');
			
			
		}
	}
	public function team_b_register_cpt(){
		require_once('cpt-reg.php');
		add_filter( 'manage_edit-team_builder_columns', array(&$this, 'team_builder_columns' )) ;
		add_action( 'manage_team_builder_posts_custom_column', array(&$this, 'team_builder_manage_columns' ), 10, 2 );
	}
	function team_builder_columns( $columns ){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Teams' ),
            'count' => __( 'Team Count' ),
            'shortcode' => __( 'Teams Shortcode' ),
            'date' => __( 'Date' )
        );
        return $columns;
    }

    function team_builder_manage_columns( $column, $post_id ){
        global $post;
		$TotalCount =  get_post_meta( $post_id, 'wpsm_team_b_count', true );
		if(!$TotalCount || $TotalCount==-1){
		$TotalCount =0;
		}
        switch( $column ) {
          case 'shortcode' :
            echo '<input style="width:225px" type="text" value="[TEAM_B id='.$post_id.']" readonly="readonly" />';
            break;
			case 'count' :
            echo $TotalCount;
            break;
          default :
            break;
        }
    }
	// metaboxes
	public function wpsm_team_b_meta_boxes_group(){
		add_meta_box('team_b_add', __('Add Team Panel', wpshopmart_team_b_text_domain), array(&$this, 'wpsm_add_team_b_meta_box_function'), 'team_builder', 'normal', 'low' );
		add_meta_box ('team_b_shortcode', __('Team Shortcode', wpshopmart_team_b_text_domain), array(&$this, 'wpsm_pic_team_b_shortcode'), 'team_builder', 'normal', 'low');
		add_meta_box ('team_b_more_pro', __('More Pro Plugin From Wpshopmart', wpshopmart_team_b_text_domain), array(&$this, 'wpsm_team_pic_more_pro'), 'team_builder', 'normal', 'low');
		add_meta_box('team_b_rateus', __('Rate Us If You Like This Plugin', wpshopmart_team_b_text_domain), array(&$this, 'wpsm_team_b_rateus_meta_box_function'), 'team_builder', 'side', 'low');
		add_meta_box('team_b_setting', __('Team Settings', wpshopmart_team_b_text_domain), array(&$this, 'wpsm_add_team_b_setting_function'), 'team_builder', 'side', 'low');
	}
	
	public function wpsm_add_team_b_meta_box_function($post){
		require_once('add-team.php');
	}
	public function add_team_b_save_meta_box_save($PostID){
		require('data-post/team-save-data.php');
	}
	public function team_b_settings_meta_box_save($PostID){
		require('data-post/team-settings-save-data.php');
	}
	public function wpsm_pic_team_b_shortcode(){
		require('team-shortcode-css.php');
	}
	public function wpsm_team_b_rateus_meta_box_function(){
		?>
		<style>
		#team_b_rateus{
			background:#dd3333;
			text-align:center
			}
			#team_b_rateus .hndle , #team_b_rateus .handlediv{
			display:none;
			}
			#team_b_rateus h1{
			    color: #fff;
				border-bottom: 1px dashed rgba(255, 255, 255,0.9);
				padding-bottom: 10px;
			}
			 #team_b_rateus h3 {
			color:#fff;
			font-size:15px;
			}
			#team_b_rateus .button-hero{
			display:block;
			text-align:center;
			margin-bottom:15px;
			background:#fff !important;
			color:#000 !important;
			box-shadow:none;
			text-shadow:none;
			font-weight:600;
			font-size:18px;
			border:0px;
			}
			.wpsm-rate-us{
			text-align:center;
			}
			.wpsm-rate-us span.dashicons {
				width: 40px;
				height: 40px;
				font-size:20px;
				color:#fff !important;
			}
			.wpsm-rate-us span.dashicons-star-filled:before {
				content: "\f155";
				font-size: 40px;
			}
		</style>
		   <h1>Rate Us </h1>
			<h3>Show us some love, If you like our product then please give us some valuable feedback on wordpress</h3>
			<a href="https://wordpress.org/plugins/team-builder/" target="_blank" class="button button-primary button-hero ">RATE HERE</a>
			<a class="wpsm-rate-us" style=" text-decoration: none; height: 40px; width: 40px;" href="https://wordpress.org/plugins/team-builder/" target="_blank">
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
			</a>
			<?php
	}
	public function wpsm_add_team_b_setting_function($post){
		require_once('settings.php');
	}
	
	public function wpsm_team_pic_more_pro(){
		require_once('more-pro.php');
	}
	
}
global $wpsm_team_b;
$wpsm_team_b = wpsm_team_b::forge();
	

?>
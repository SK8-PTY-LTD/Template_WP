<?php
require_once( SGMB_CLASSES .'SgmbWidget.php');
require_once( SGMB_CLASSES .'SgmbButton.php');

class SGMB
{
	public function init()
	{
		require_once(SGMB_CLASSES .'Installer.php');
		register_activation_hook(SGMB_PATH.'sgmb-buttons.php',  array('SGMB', 'activate'));
		register_uninstall_hook(SGMB_PATH.'sgmb-buttons.php',  array('SGMB','deactivate'));
		add_action('admin_menu', array( $this, 'addMenu'));
		add_shortcode('sgmb', array($this, 'showShortCode'));
		add_action( 'widgets_init', array($this, 'load_widget') );
		add_action('media_buttons', array($this, 'mediaButtons'), 11);
		add_action('admin_enqueue_scripts',  array($this, 'wptuts_add_color_picker'));
		add_filter('the_content', array($this, 'buttonsShowOnEveryPost'));

		$sgmbButton = new SGMBButton();
		$sgmbButton->init();
	}

	public function buttonsShowOnEveryPost($content)
	{
		$postID = get_the_ID();
		$id = get_option('SGMB_SHARE_BUTTON_ID');
		$obj = SGMBButton::findById($id);
		if($obj) {
			$data = json_decode($obj->getOptions(), true);
			$textBeforeSocialMedia = @$data['textOnEveryPost'];
			$postsTitleInData = @$data['sgmbSelectedPosts'];
			switch (@$data['sgmbPostionOnEveryPost']) {
				case 'Left':
					$sgmbPosition = 'sgmb-left';
					break;
				case 'Center':
					$sgmbPosition = 'sgmb-center';
					break;
				case 'Right':
					$sgmbPosition = 'sgmb-right';
					break;
			}
			if(@$data['showButtonsOnEveryPost'] == 'on' ) {
				if(@$data['showOnAllPost'] == 'on' && !is_page()) {
					$content .= "<div class = 'socialMediaOnEveryPost'>";
					$content .= @$textBeforeSocialMedia;
					$content .= do_shortcode( "[sgmb id=$id]" );
					$content .= "</div>";
					$content .= '<script> jQuery(".socialMediaOnEveryPost").addClass("'.$sgmbPosition.'") </script>';
				}
				else {
					foreach ($postsTitleInData as  $postTitleInData) {
						if(!is_home() && $postID == $postTitleInData) {
							$content .= "<div class = 'socialMediaOnEveryPost'>";
							$content .= @$textBeforeSocialMedia;
							$content .= do_shortcode( "[sgmb id=$id]" );
							$content .= "</div>";
							$content .= '<script> jQuery(".socialMediaOnEveryPost").addClass("'.$sgmbPosition.'") </script>';
						}
					}
				}
			}
		}
		return $content;
	}


	public function wptuts_add_color_picker( $hook )
	{
		if( is_admin() ) {

			// Add the color picker css file
			wp_enqueue_style( 'wp-color-picker' );

			// Include our custom jQuery file with WordPress Color Picker dependency
			wp_enqueue_script( 'custom-script-handle', SGMB_URL.'js/addNewSection/SGMBLivePreview.js', array( 'wp-color-picker' ), false, true );
		}
	}

	public function mediaButtons()
	{
		wp_register_script('sgmb-classWidget-scripts', SGMB_URL.'js/addNewSection/SGMBWidget.js', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-accordion', 'jquery-ui-dialog'),null);
		wp_enqueue_script('sgmb-classWidget-scripts');
		wp_register_style('sgmb_tab_theme_style', SGMB_URL.'css/themes/pepper-grinder/jquery-ui.css');
		wp_enqueue_style('sgmb_tab_theme_style');
		$buttonTitle = 'Insert Social Media Buttons';
		$output = '';
		$img = '<span class="dashicons dashicons-share" id="" style="padding: 3px 2px 0px 0px"></span>';
		$output = '<a href="javascript:void(0);" onclick="jQuery(\'#sgmb-thickbox\').dialog({ width: 450, modal: true });" class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';
		echo $output;
		add_action('admin_footer', array($this, 'mediaButtonThickboxs'));
	}

	public function mediaButtonThickboxs()
	{
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('#sgmb-insert').on('click', function () {
					var id = $('#sgmb-buttons-id').val();
					if ('' === id) {
						alert('Select your social media buttons');
						return;
					}
					selectionText = '';
					if (typeof(tinyMCE.editors.content) != "undefined") {
						selectionText = (tinyMCE.activeEditor.selection.getContent()) ? tinyMCE.activeEditor.selection.getContent() : '';
					}
					window.send_to_editor('[sgmb id="' + id + '"]');
					$('#sgmb-thickbox').dialog( "close" );
				});
			});
		</script>
		<div id="sgmb-thickbox" style="display: none;">
			<div class="wrap">
				<p>Insert the shortcode for showing a Social Media.</p>
				<div>
					<select id="sgmb-buttons-id" style="width:150px; margin-bottom:20px;">
						<option value="">Please select...</option>
						<?php
							global $wpdb;
							$proposedTypes = array();
							$orderBy = 'id DESC';
							$allButtons = SGMBButton::findAll($orderBy);
							foreach ($allButtons as $allButton) : ?>
								<option value="<?php echo esc_attr($allButton->getId());?>"><?php echo esc_html($allButton->getTitle());?></option>;
							<?php endforeach; ?>
					</select>
				</div>
				<div class="sgmb-image-uploader-wrapper">
					<input id="sgmb-upload-image" type="text" style="width:150px;" name="ad_image" value=""
						<?php if(SGMB_PRO != 1): ?> disabled <?php endif; ?>
					>
					<input id="sgmb-upload-image-button" class="button" type="button" value="Select custom image"
						<?php if(SGMB_PRO != 1): ?> disabled <?php endif; ?>
					>
					<?php if(SGMB_PRO != 1): ?>
						<a href="<?php echo SGMB_PRO_URL; ?>" target="_blank" style="font-size: 24px; font-family: Cursive; font-style: oblique; margin-left: 24px; color: red !important; margin-bottom: 14px !important;">PRO</a>
					<?php endif; ?>
				</div>
				<p class="submit">
					<input type="button" id="sgmb-insert" class="button-primary dashicons-share" value="Insert"/>
					<a class="button-secondary" onclick="jQuery('#sgmb-thickbox').dialog('close')"; title="Cancel">Cancel</a>
				</p>
			</div>
		</div>
	<?php
	}

	public static function activate()
	{
		SGMBInstaller::install();
	}

	public static function deactivate()
	{
		SGMBInstaller::uninstall();
	}

	public function load_widget()
	{
		register_widget('SgmbWidget');
	}

	public function addMenu()
	{
		add_menu_page("button", "Social Media", "manage_options", "socialmediabuilder", array($this, 'showAllButtons'), "dashicons-share");
		add_submenu_page("socialmediabuilder", "All Buttons", "All Buttons", 'manage_options', "socialmediabuilder", array($this, 'showAllButtons'));
		add_submenu_page("socialmediabuilder", "Add New", "Add New", 'manage_options', "create-button", array($this, 'createButtons'));
		add_submenu_page("socialmediabuilder", "More plugins", "More plugins", 'manage_options', "show-more-plugins", array($this, 'showMorePlugins'));
	}

	public function showAllButtons()
	{
		require_once(SGMB_CLASSES .'pages/SgmbAllButtonsSection.php');
		$sgmb = new SgmbAllButtonsSection();
		$sgmb->init();
	}

	public function createButtons()
	{
		require_once(SGMB_CLASSES .'pages/SgmbAddNewSection.php');
		$sgmb = new SgmbAddNewSection();
		$sgmb->init();
	}

	public function showMorePlugins()
	{
		require_once(SGMB_CLASSES .'pages/SgmbShowMorePlugins.php');
		$sgmb = new SgmbShowMorePlugins();
		$sgmb->init();
	}

	public function showShortCode($args)
	{
		$widget = new SgmbWidget();
		return $widget->init($args);
	}
}

<?php
class SgmbWidget extends WP_Widget
{
	static $widgetCounter;
	function __construct()
	{
		parent::__construct(
			'SgmbWidget',
			__('Social Media Builder', 'sgmb_widget_domain'),
			array('description' => __( 'Most important social buttons for your site', 'sgmb_widget_domain' ),)
		);
	}

 	public function widget($args, $instance)
 	{
 		global $post;
		$postImage = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
		if($postImage == false) {
			$postImage = $this->getPostImage();
		}
		$postUrl = get_permalink();
 		$data = array();
		$data = $this->getData($instance['id']);
		if(!empty($data)) {
			$html = $this->prepareWidget($data);
			$html .= $this->showWidget(json_encode($data), self::$widgetCounter, $postImage, $postUrl);
			echo  $html;
		}
	}

	public function prepareWidget($data)
	{
		self::$widgetCounter += 1 ;
		if(@$data['options']['socialTheme'] == '') {
			$themeType = 'classic';
		}
		else {
			$themeType = @$data['options']['socialTheme'];
		}
		if(@$data['button'][0] != '') {
			$html = '<div id="sgmbShare'.@$data['id'] .'-'.self::$widgetCounter.'" class="sgmbShare jssocials-theme-'.$themeType.' sgmbWidget'.@$data['id'].'-'.self::$widgetCounter.'"></div>';
			$html .='<div class="dropdownWrapper dropdownWrapper'.@$data['id'] .' dropdownWrapper-for-widget " id="dropdownWrapper-for-widget">
						<div class="dropdownLabel" id="dropdownLabel-share-list"><span class="sgmbButtonListLabel'.@$data['id'].'">Share List</span></div>
						<div class="dropdownPanel dropdownPanel'.@$data['id'] .'-'.self::$widgetCounter.'">
						</div>
					</div>';
			$html .= '<script>  SGMB_URL = "'.SGMB_URL.'"; jQuery(".dropdownWrapper").hide();</script>';
		}

		self::renderScripts($themeType);
		return @$html;
	}

	public function getData($id)
	{
		$data = array();
	 	$result = SGMBButton::findById($id);
	 	if ($result) {
	 		$data['id'] = $result->getId();
			$data['title'] = $result->getTitle();
			$data['options'] = json_decode($result->getOptions(), true);
			$data['buttonOptions'] = json_decode($data['options']['buttons'],true);
			foreach ($data['buttonOptions'] as $key => $value) {
				$data['button'][] = $key;
			}
		}
		return $data;
	}

	public function init($args)
	{
		global $post;
		$postImage = wp_get_attachment_url( get_post_thumbnail_id($post->ID));
		if($postImage == false) {
			$postImage = $this->getPostImage();
		}
		$postUrl = get_permalink();
		$data = $this->getData($args['id']);
		$data['options']['shareText'] = htmlspecialchars_decode($data['options']['shareText'], ENT_QUOTES);
		if(!empty($data)) {
			$html = $this->prepareWidget($data);
			$html .=  $this->showWidget(json_encode($data), self::$widgetCounter, $postImage, $postUrl);
			return $html;
		}
	}
	public function getPostImage() {
		global $post, $posts;
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if(isset($matches[1][0])){
			$first_img = $matches[1][0];
		}

		if(empty($first_img)) {
			$first_img = SGMB_URL.'/img/no-image.png';
		}
		return $first_img;
	}

	public function form( $instance )
	{
		$data = SGMBButton::getDataList();
	?>

	<p>
	<label for="<?php echo $this->get_field_id( 'id' ); ?>"><?php _e( 'Title:' ); ?></label>
	<select  class ="widefat" id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>">
	<?php for ($i=0; $i < count($data ) ; $i++): ?>
		<option value="<?php echo  @$data[$i]['id'] ?>"
	 	<?php if(@$data[$i]['id']== @$instance['id']): @$title = @$data[$i]['title']; ?>
		selected
	    <?php endif;?>
	 	> <?php echo  @$data[$i]['title'] ?>
		</option>
	<?php endfor; ?>
	</select>
	<input  id="<?php echo $this->get_field_id( 'title' ); ?>" type="hidden" value="<?php echo esc_attr(@$title);?>" />
	</p>

	<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['id'] = ( ! empty( $new_instance['id'] ) ) ? strip_tags( $new_instance['id'] ) : '';
		return $instance;
	}

	public static function renderScripts($themeType)
	{
		if($themeType== null) {
			$themeType = SGMB_DEFAULT_THEME;
		}
		wp_register_script('sgmb-class-sgmb',SGMB_URL.'js/addNewSection/SGMB.js', array('jquery'),null);
		wp_enqueue_script('sgmb-class-sgmb');
		wp_register_script('sgmb-class-sgmbWidget',SGMB_URL.'js/addNewSection/SGMBWidget.js', array('jquery'),null);
		wp_enqueue_script('sgmb-class-sgmbWidget');
		wp_register_style('sgmb_socialFont_style',SGMB_URL.'css/jssocial/font-awesome.min.css');
		wp_enqueue_style('sgmb_socialFont_style');
		wp_register_script('sgmb-jssocial1-scripts', SGMB_URL.'js/jssocials.min.js', array('jquery'),null);
		wp_enqueue_script('sgmb-jssocial1-scripts');
		wp_register_script('sgmb-jssocial2-scripts', SGMB_URL.'js/jssocials.shares.js', array('jquery'),null);
		wp_enqueue_script('sgmb-jssocial2-scripts');
		wp_register_script('sgmb-drop_down-scripts',SGMB_URL.'js/simple.dropdown.js', array('jquery'),null);
		wp_enqueue_script('sgmb-drop_down-scripts');
		wp_register_style('sgmb_social2_style',SGMB_URL.'css/jssocial/jssocials.css');
		wp_enqueue_style('sgmb_social2_style');
		wp_register_style('jssocials_theme_'.$themeType,SGMB_URL.'css/jssocial/jssocials-theme-'.$themeType.'.css');
		wp_enqueue_style('jssocials_theme_'.$themeType);
		wp_register_style('sgmb_widget_style',SGMB_URL.'css/widget/widget-style.css');
		wp_enqueue_style('sgmb_widget_style');
		wp_register_style('sgmb_buttons_animate',SGMB_URL.'css/animate.css');
		wp_enqueue_style('sgmb_buttons_animate');
		wp_register_style('sgmb_drop_down_style',SGMB_URL.'css/widget/simple.dropdown.css');
		wp_enqueue_style('sgmb_drop_down_style');
	}

	public  function showWidget($data, $widgetCounter, $postImage, $postUrl)
	{
		$content = "<script type=\"text/javascript\">
		jQuery(document).ready(function($){";
		$content .= "var widget = new SGMBWidget();";
		$content .= "widget.show($data, $widgetCounter, '', '$postImage', '$postUrl');";
		$content .= " });</script>";
		echo $content;
	}
}

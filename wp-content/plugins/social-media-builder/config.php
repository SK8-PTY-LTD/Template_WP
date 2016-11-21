<?php
define('SGMB_PATH', dirname(__FILE__).'/');
define('SGMB_URL', plugins_url('', __FILE__).'/');
define('SGMB_ADMIN_URL', admin_url());
define('SGMB_VIEW', SGMB_PATH.'view/');
define('SGMB_FILES', SGMB_PATH.'files/');
define('SGMB_CLASSES', SGMB_PATH.'classes/');
define('SGMB_LIBRARY', SGMB_PATH.'library/');
define('SGMB_TABLE_LIMIT', 15);
define('SGMB_SHARE_BUTTON_VERSION', 1.47);
define('SGMB_DEFAULT_SHARE_URL', "http://google.com");
define('SGMB_DEFAULT_THEME', "classic");
define('SGMB_PRO', 0); //  0 -> free, 1 -> pro
define('SGMB_PRO_URL', 'http://plugins.sygnoos.com/wordpress-social-buttons/');

global $SGMB_BUTTON_FONT_SIZE;
global $SGMB_WIDGET_THEMES;
global $SGMB_SOCIAL_BUTTONS;
global $SGMB_WIDGET_EFFECTS;

$SGMB_BUTTON_FONT_SIZE = array('8', '10', '12', '14', '16', '18', '20', '22', '24', '26');
$SGMB_FONT_SIZE_FOR_SHARE_LIST = array('8', '10', '12', '14', '16', '18');
$SGMB_WIDGET_EFFECTS = array(
	'Free' => array(
		'No Effect',
		'flip'
	),
	'Pro' => array(
		'shake',
		'wobble',
		'swing',
		'flash',
		'bounce',
		'pulse',
		'rubberBand',
		'tada',
		'jello',
		'rotateIn',
		'fadeIn'
	)
);
$SGMB_SOCIAL_BUTTONS = array(
	'facebook',
	'twitter',
	'linkedin',
	'googleplus',
	'email',
	'pinterest',
	'mewe',
	'fbLike',
	'twitterFollow',
	'whatsapp',
	'tumblr',
	'reddit',
	'line',
	'vk',
	'stumbleupon'
);
$SGMB_ADVANCED_NAME_SOCIAL_BUTTONS = array(
	'facebook' => 'Facebook',
	'twitter' => 'Twitter',
	'linkedin' => 'Linked In',
	'googleplus' => 'Google Plus',
	'email' => 'E-mail',
	'pinterest' => 'Pinterest',
	'fbLike' => 'Facebook Like',
	'mewe' => 'MeWe',
	'twitterFollow' => 'Twitter Follow',
	'tumblr' => 'Tumblr',
	'reddit' => 'Reddit',
	'line' => 'Line',
	'vk' => 'VK',
	'stumbleupon' => 'StumbleUpon'
);
$SGMB_WIDGET_THEMES = array(
	'classic' => 0, //  0 -> free
	'cloud' => 0,
	'wood' => 0,
	'toy' => 0,
	'box' => 0,
	'round' => 0,
	'flat' => 1, //  1 -> pro
	'silverround' => 1,
	'goodstaff' => 1,
	'heart' => 1,
	'round-dot' => 1,
	'hex' => 1,
	'cork' => 1,
	'pen' => 1,
	'black' => 1,
	'multi' => 1
);

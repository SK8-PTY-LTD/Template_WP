<?php
/*
Plugin Name: Smart Slider 3
Plugin URI: http://smartslider3.com/
Description: The perfect all-in-one responsive slider solution for WordPress.
Version: 3.0.37
Author: Nextend
Author URI: http://nextendweb.com
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/*  Copyright 2015  Roland Soos - Nextendweb  (email : roland@nextendweb.com)

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

if (!class_exists('N2_SMARTSLIDER_3')) {

    define('N2PRO', 0);
    define('N2SSPRO', 0);
    define('N2GSAP', 0);

    define('NEXTEND_SMARTSLIDER_3__FILE__', __FILE__);
    define('NEXTEND_SMARTSLIDER_3', dirname(__FILE__) . DIRECTORY_SEPARATOR);
    define('NEXTEND_SMARTSLIDER_3_URL_PATH', 'smart-slider-3');
    define('NEXTEND_SMARTSLIDER_3_BASENAME', plugin_basename(__FILE__));

    require_once dirname(NEXTEND_SMARTSLIDER_3__FILE__) . DIRECTORY_SEPARATOR . 'includes/smartslider3.php';

    add_action('activated_plugin', 'N2_SMARTSLIDER_3_DEACTIVATE_ON_PRO');
    function N2_SMARTSLIDER_3_DEACTIVATE_ON_PRO($plugin) {
        if (strstr($plugin, 'nextend-smart-slider3-pro.php') !== false) {
            deactivate_plugins(__FILE__);
        }
    }

    add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'N2_SMARTSLIDER_3_UPGRADE_TO_PRO');
    function N2_SMARTSLIDER_3_UPGRADE_TO_PRO($links) {

        if (function_exists('is_plugin_active') && !is_plugin_active('ml-slider-pro/ml-slider-pro.php')) {
            $links[] = '<a href="' . N2SS3::getProUrlPricing() . '" target="_blank">' . "Go Pro" . '</a>';
        }
        return $links;
    }

    N2Pluggable::addAction('animationFramework', 'N2AssetsPredefined::custom_animation_framework');
}
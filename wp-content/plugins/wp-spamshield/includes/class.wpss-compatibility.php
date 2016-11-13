<?php
/**
 *  WP-SpamShield Compatibility
 *  File Version 1.9.9.5
 */

if( !defined( 'ABSPATH' ) || !defined( 'WPSS_VERSION' ) ) {
	if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); @header('X-Robots-Tag: noindex',TRUE); }
	die( 'ERROR: Direct access to this file is not allowed.' );
}

class WPSS_Compatibility {

	/**
	 *  WP-SpamShield Compatibility Class
	 *  Plugin detection
	 *  Compatibility deconfliction for some of the plugins listed in the Known Issues and Plugin Conflicts ( http://www.redsandmarketing.com/plugins/wp-spamshield/known-conflicts/ )
	 *  Where possible, apply compatibility fixes or workarounds
	 */

	function __construct() {
		/**
		 *  Do nothing...for now
		 */
	}

	static public function is_plugin_active( $plug_bn, $check_network = TRUE ) {
		/**
		 *  Using this because WordPress native' 'is_plugin_active()' function only works in Admin
		 *  ex. $plug_bn = 'folder/filename.php'; // Plugin Basename
		 */
		if( empty( $plug_bn ) ){ return FALSE; }
		global $wpss_conf_active_plugins,$wpss_active_plugins,$wpss_active_network_plugins;
		/* Quick Check */
		if( !empty( $wpss_conf_active_plugins[$plug_bn] ) ) { return TRUE; }
		if( TRUE === $check_network && is_multisite() ) { if( !empty( $wpss_conf_active_network_plugins[$plug_bn] ) ) { return TRUE; } }
		$wpss_conf_active_plugins = array();
		$wpss_conf_active_network_plugins = array();
		/* Check known plugin constants and classes */
		$plug_cncl = array(
			/* Compatibility Fixes */
			'autoptimize/autoptimize.php' => array( 'cn' => 'AUTOPTIMIZE_WP_CONTENT_NAME', 'cl' => 'autoptimizeConfig' ), 'commentluv/commentluv.php' => array( 'cn' => '', 'cl' => 'commentluv' ), 'si-contact-form/si-contact-form.php' => array( 'cn' => 'FSCF_VERSION', 'cl' => 'FSCF_Util' ), 'jetpack/jetpack.php' => array( 'cn' => 'JETPACK__VERSION', 'cl' => 'Jetpack' ), 'wp-spamfree/wp-spamfree.php' => array( 'cn' => '', 'cl' => 'wpSpamFree' ),
			/* 3rd Party Forms, Membership & Registration */
			'bbpress/bbpress.php' => array( 'cn' => '', 'cl' => 'bbPress' ), 'buddypress/bp-loader.php' => array( 'cn' => 'BP_PLUGIN_DIR', 'cl' => 'BuddyPress' ), 'contact-form-7/wp-contact-form-7.php' => array( 'cn' => 'WPCF7_VERSION', 'cl' => '' ), 'gravityforms/gravityforms.php' => array( 'cn' => 'GF_MIN_WP_VERSION', 'cl' => 'GFForms' ), 'mailchimp-for-wp/mailchimp-for-wp.php' => array( 'cn' => 'MC4WP_LITE_VERSION', 'cl' => 'MC4WP_Lite' ), 'ninja-forms/ninja-forms.php' => array( 'cn' => 'NF_PLUGIN_VERSION', 'cl' => 'Ninja_Forms' ),
			/* Cache Plugins */
			'w3-total-cache/w3-total-cache.php' => array( 'cn' => 'W3TC_VERSION', 'cl' => '' ), 'wp-fastest-cache/wpFastestCache.php' => array( 'cn' => 'WPFC_WP_PLUGIN_DIR', 'cl' => 'WpFastestCache' ), 'wp-fastest-cache-premium/wpFastestCachePremium.php' => array( 'cn' => '', 'cl' => '' ), 'wp-rocket/wp-rocket.php' => array( 'cn' => 'WP_ROCKET_VERSION', 'cl' => '' ),
			/* Ecommerce Plugins */
			'affiliates/affiliates.php' => array( 'cn' => 'AFFILIATES_CORE_VERSION', 'cl' => '' ), 'caldera-forms/caldera-core.php' => array( 'cn' => 'CFCORE_VER', 'cl' => '' ), 'download-manager/download-manager.php' => array( 'cn' => 'WPDM_Version', 'cl' => '' ), 'easy-digital-downloads/easy-digital-downloads.php' => array( 'cn' => 'EDD_VERSION', 'cl' => 'Easy_Digital_Downloads' ), 'ecommerce-product-catalog/ecommerce-product-catalog.php' => array( 'cn' => 'AL_BASE_PATH', 'cl' => 'eCommerce_Product_Catalog' ), 'ecwid-shopping-cart/ecwid-shopping-cart.php' => array( 'cn' => 'ECWID_PLUGIN_DIR', 'cl' => '' ), 'eshop/eshop.php' => array( 'cn' => 'ESHOP_VERSION', 'cl' => '' ), 'events-made-easy/events-manager.php' => array( 'cn' => 'EME_DB_VERSION', 'cl' => '' ), 'events-manager/events-manager.php' => array( 'cn' => '', 'cl' => '' ), 'formidable-paypal/formidable-paypal.php' => array( 'cn' => '', 'cl' => '' ), 'give/give.php' => array( 'cn' => 'GIVE_VERSION', 'cl' => 'Give' ), 'gravity-forms-stripe/gravity-forms-stripe.php' => array( 'cn' => 'GFP_STRIPE_FILE', 'cl' => '' ), 'gravityformsauthorizenet/authorizenet.php' => array( 'cn' => 'GF_AUTHORIZENET_VERSION', 'cl' => 'GF_AuthorizeNet_Bootstrap' ), 'gravityformspayfast/payfast.php' => array( 'cn' => 'GF_PAYFAST_VERSION', 'cl' => 'GF_PayFast_Bootstrap' ), 'gravityformsstripe/stripe.php' => array( 'cn' => 'GF_STRIPE_VERSION', 'cl' => 'GF_Stripe_Bootstrap' ), 'gravityformspaypal/paypal.php' => array( 'cn' => 'GF_PAYPAL_VERSION', 'cl' => 'GF_PayPal_Bootstrap' ), 'ithemes-exchange/init.php' => array( 'cn' => '', 'cl' => 'IT_Exchange' ), 'jigoshop/jigoshop.php' => array( 'cn' => 'JIGOSHOP_VERSION', 'cl' => '' ), 'memberpress/memberpress.php' => array( 'cn' => 'MEPR_VERSION', 'cl' => '' ), 'paid-memberships-pro/paid-memberships-pro.php' => array( 'cn' => 'PMPRO_VERSION', 'cl' => '' ), 's2member/s2member-o.php' => array( 'cn' => 'WS_PLUGIN__S2MEMBER_VERSION', 'cl' => '' ), 'shopp/Shopp.php' => array( 'cn' => '', 'cl' => 'ShoppLoader' ), 'simple-membership/simple-wp-membership.php' => array( 'cn' => 'SIMPLE_WP_MEMBERSHIP_VER', 'cl' => '' ), 'stripe/stripe-checkout.php' => array( 'cn' => 'SIMPAY_VERSION', 'cl' => '' ), 'ultimate-product-catalogue/UPCP_Main.php' => array( 'cn' => 'UPCP_CD_PLUGIN_PATH', 'cl' => '' ), 'usc-e-shop/usc-e-shop.php' => array( 'cn' => 'USCES_VERSION', 'cl' => '' ), 'users-ultra/xoousers.php' => array( 'cn' => 'xoousers_url', 'cl' => '' ), 'wc-vendors/class-wc-vendors.php' => array( 'cn' => 'wcv_plugin_dir', 'cl' => 'WC_Vendors' ), 'woocommerce-paypal-pro-payment-gateway/woo-paypal-pro.php' => array( 'cn' => 'WC_PP_PRO_ADDON_VERSION', 'cl' => 'WC_Paypal_Pro_Gateway_Addon' ), 'woocommerce/woocommerce.php' => array( 'cn' => 'WOOCOMMERCE_VERSION', 'cl' => 'WooCommerce' ), 'wordpress-ecommerce/marketpress.php' => array( 'cn' => 'MP_LITE', 'cl' => 'MarketPress' ), 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php' => array( 'cn' => 'WP_CART_VERSION', 'cl' => '' ), 'wp-e-commerce/wp-shopping-cart.php' => array( 'cn' => 'WPSC_VERSION', 'cl' => 'WP_eCommerce' ), 'wp-easycart/wpeasycart.php' => array( 'cn' => 'EC_CURRENT_VERSION', 'cl' => '' ), 'wp-shop-original/wp-shop.php' => array( 'cn' => 'WPSHOP_DIR', 'cl' => '' ), 'wp-ultra-simple-paypal-shopping-cart/wp_ultra_simple_shopping_cart.php' => array( 'cn' => 'WUSPSC_VERSION', 'cl' => '' ), 'wppizza/wppizza.php' => array( 'cn' => 'WPPIZZA_VERSION', 'cl' => '' ), 'yith-woocommerce-stripe/init.php' => array( 'cn' => 'YITH_WCSTRIPE_VERSION', 'cl' => '' ),
			/* Page Builder Plugins */
			'beaver-builder-lite-version/fl-builder.php' => array( 'cn' => 'FL_BUILDER_VERSION', 'cl' => 'FLBuilder' ), 'bb-plugin/fl-builder.php' => array( 'cn' => 'FL_BUILDER_VERSION', 'cl' => 'FLBuilder' ),
			/* All others */
			'wordfence/wordfence.php' => array( 'cn' => 'WORDFENCE_VERSION', 'cl' => 'wordfence' ),
		);
		if( ( !empty( $plug_cncl[$plug_bn]['cn'] ) && defined( $plug_cncl[$plug_bn]['cn'] ) ) || ( !empty( $plug_cncl[$plug_bn]['cl'] ) && class_exists( $plug_cncl[$plug_bn]['cl'] ) ) ) { $wpss_conf_active_plugins[$plug_bn] = TRUE; return TRUE; }
		/* No match yet, so now do standard check */
		if( empty( $wpss_active_plugins ) ) { $wpss_active_plugins = rs_wpss_get_active_plugins(); }
		if( in_array( $plug_bn, $wpss_active_plugins, TRUE ) ) { $wpss_conf_active_plugins[$plug_bn] = TRUE; return TRUE; }
		if( TRUE === $check_network && is_multisite() ) {
			if( empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins = rs_wpss_get_active_network_plugins(); }
			if( in_array( $plug_bn, $wpss_active_network_plugins, TRUE ) ) { $wpss_conf_active_network_plugins[$plug_bn] = TRUE; return TRUE; }
		}
		return FALSE;
	}

	static public function upgrade_conflict_check() {
		/**
		 *  When it is detected that the plugin has been upgraded, fire, and run these checks
		 */

		/* Plugin Organizer Plugin ( https://wordpress.org/plugins/plugin-organizer/ ) */
		if( self::is_plugin_active( 'plugin-organizer/plugin-organizer.php' ) ) {
			self::deconflict_po_01();
		}
	}

	static public function supported() {
		/**
		 *  Check if supported 3rd party plugins are active that require exceptions
		 *  hook@ 'plugins_loaded':-100
		 */

		/**
		 *  Turn on Soft Compat Mode for the following plugins: ( TO DO: array() / foreach() )
		 *  Gravity Forms	- http://www.gravityforms.com/
		 *  W3 Total Cache	- https://wordpress.org/plugins/w3-total-cache/ - https://www.w3-edge.com/products/w3-total-cache/
		 */
		if( self::is_plugin_active( 'gravityforms/gravityforms.php' ) || self::is_plugin_active( 'w3-total-cache/w3-total-cache.php' ) ) {
			if( !defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { define( 'WPSS_SOFT_COMPAT_MODE', TRUE ); }
		}

	}

	static public function conflict_check() {
		/**
		 *  Check if plugins with known issues are active, then deconflict
		 *  hook@ 'plugins_loaded':100
		 */

		/* New User Approve Plugin ( https://wordpress.org/plugins/new-user-approve/ ) */
		if( class_exists( 'pw_new_user_approve' ) ) {
			add_action( 'register_post', array('WPSS_Compatibility','deconflict_nua_01'), -10 );
			add_action( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_02'), -10 );
		}

		/* Affiliates Plugin ( https://wordpress.org/plugins/affiliates/ ) */
		if( defined( 'AFFILIATES_CORE_VERSION' ) || class_exists( 'Affiliates_Registration' ) ) {
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'update_affiliate_user' ) ) {
				add_filter( 'user_registration_email', 'rs_wpss_sanitize_new_user_email' );
			}
			if( class_exists( 'Affiliates_Registration' ) && method_exists( 'Affiliates_Registration', 'render_form' ) ) {
				add_filter( 'affiliates_registration_after_fields', 'rs_wpss_register_form_append' );
			}
		}

	}

	static public function deconflict_nua_01() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) && has_filter( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ) ) ) {
			remove_action( 'register_post', array( pw_new_user_approve::instance(), 'create_new_user' ), 10 );
			add_action( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_01_01'), 9998, 3 );
		}
	}

	static public function deconflict_nua_01_01( $errors, $user_login, $user_email ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'create_new_user' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->create_new_user( $user_login, $user_email, $errors );
		}
		return $errors;
	}

	static public function deconflict_nua_02() {
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) && has_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ) ) ) {
			remove_filter( 'registration_errors', array( pw_new_user_approve::instance(), 'show_user_pending_message' ), 10 );
			if( function_exists( 'login_header' ) && function_exists( 'login_footer' ) ) {
				add_filter( 'registration_errors', array('WPSS_Compatibility','deconflict_nua_02_01'), 9999 );
			}
		}
	}

	static public function deconflict_nua_02_01( $errors ) {
		if( !empty( $errors ) && is_object( $errors ) && $errors->get_error_code() ) { return $errors; }
		if( class_exists( 'pw_new_user_approve' ) && method_exists( 'pw_new_user_approve', 'show_user_pending_message' ) ) {
			if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }
			pw_new_user_approve::instance()->show_user_pending_message( $errors );
		}
		return $errors;
	}

	static public function deconflict_po_01() {
		/* Make sure WP-SpamShield does not get disabled or hindered */
		$all_options = wp_load_alloptions();
		foreach( $all_options  as $option => $value ) {
			if( 0 === strpos( $option, 'PO_disabled_' ) && is_array( $value ) ) {
				foreach( $value  as $k => $v ) {
					if( 0 === strpos( $v, WPSS_PLUGIN_NAME ) ) { unset( $value[$k] ); }
				}
				$value = array_values($value); update_option( $option, $value );
			}
		}
		update_option( 'PO_plugin_order', array() );
	}

	static public function comment_form() {
		/**
		 *  Comment Form Compatibility
		 */

		if( rs_wpss_is_admin_sproc() ) { return; }

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage; if( !empty( $wpss_theme_vantage ) ) { return TRUE; } elseif( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) { $wpss_theme_vantage = TRUE; return TRUE; }
		else {
			$wpss_theme = wp_get_theme();
			if( is_object( $wpss_theme ) && !empty( $wpss_theme ) ) {
				$theme_name = $wpss_theme->get( 'Name' );
				$theme_author = $wpss_theme->get( 'Author' );
				if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) { $wpss_theme_vantage = TRUE; return TRUE; }
			}
		}

		/* Add next here... */

		return FALSE;
	}

	static public function footer_js() {
		/**
		 *  Footer JS Compatibility
		 */

		if( rs_wpss_is_admin_sproc() ) { return; }

		$js = '';

		/* Vantage Theme by Appthemes ( https://www.appthemes.com/themes/vantage/ ) */
		global $wpss_theme_vantage;
		$v_js = ', #add-review-form';
		if( !empty( $wpss_theme_vantage ) ) { $wpss_theme_vantage = TRUE; $js .= $v_js; }
		elseif( defined( 'APP_FRAMEWORK_DIR_NAME' ) && defined( 'VA_VERSION' ) ) { $wpss_theme_vantage = TRUE; $js .= $v_js; }
		else {
			$wpss_theme = wp_get_theme();
			$theme_name = $wpss_theme->get( 'Name' );
			$theme_author = $wpss_theme->get( 'Author' );
			if( 'Vantage' === $theme_name && 'AppThemes' === $theme_author ) { $wpss_theme_vantage = TRUE; $js .= $v_js; }
		}

		/* Add next here... */

		return $js;
	}

	static public function misc_form_bypass() {
		/**
		 *  Miscellaneous Form Spam Check Bypass
		 */

		/* Setup necessary variables */
		$url		= WPSS_THIS_URL;
		$url_lc		= rs_wpss_casetrans('lower',$url);
		$req_uri	= $_SERVER['REQUEST_URI'];
		$req_uri_lc	= rs_wpss_casetrans('lower',$req_uri);
		$post_count = count( $_POST );
		$ip			= rs_wpss_get_ip_addr();
		$user_agent = rs_wpss_get_user_agent();
		$referer	= rs_wpss_get_referrer();

		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info;
		if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */

		/* GEOLOCATION */
		if( $post_count == 6 && isset( $_POST['updatemylocation'], $_POST['log'], $_POST['lat'], $_POST['country'], $_POST['zip'], $_POST['myaddress'] ) ) { return TRUE; }

		/* WP Remote */
		if( defined( 'WPRP_PLUGIN_SLUG' ) && !empty( $_POST['wpr_verify_key'] ) && preg_match( "~\ WP\-Remote$~", $user_agent ) && preg_match( "~\.amazonaws\.com$~", $reverse_dns ) ) { return TRUE; }

		/* Ecommerce Plugins */
		if( ( rs_wpss_is_ssl() || !empty( $_POST['add-to-cart'] ) || !empty( $_POST['add_to_cart'] ) || !empty( $_POST['addtocart'] ) || !empty( $_POST['product-id'] ) || !empty( $_POST['product_id'] ) || !empty( $_POST['productid'] ) || ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && preg_match( "~(^|\.)paypal\.com$~", $reverse_dns ) && $fcrdns === '[Verified]' ) ) && self::is_ecom_enabled() ) { return TRUE; }
		if( ( rs_wpss_is_ssl() || self::is_ecom_enabled() ) && $fcrdns === '[Verified]' ) {
			/* PayPal, Stripe, Authorize.net, Worldpay, etc */
			if(
				( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && preg_match( "~(^|\.)paypal\.com$~", $reverse_dns ) ) ||
				$reverse_dns === 'api.stripe.com' ||
				preg_match( "~(^|\.)(authorize\.net|worldpay\.com|payfast\.co\.za|api\.mollie\.nl|api\.simplifycommerce\.com|wepayapi\.com|2checkout\.com|paylane\.com)$~", $reverse_dns )
			) { return TRUE; }
		}

		/* WooCommerce Payment Gateways */
		if( self::is_woocom_enabled() ) {
			if( ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $reverse_dns ) ) || strpos( $req_uri, 'WC_Gateway_Paypal' ) !== FALSE ) { return TRUE; }
			if( preg_match( "~(^|\.)payfast\.co\.za$~", $reverse_dns ) || ( strpos( $req_uri, 'wc-api' ) !== FALSE && strpos( $req_uri, 'WC_Gateway_PayFast' ) !== FALSE ) ) { return TRUE; }
			/* Plugin: 'woocommerce-gateway-payfast/gateway-payfast.php' */
			if( preg_match( "~((\?|\&)wc\-api\=WC_(Addons_)?Gateway_|/wc\-api/.*WC_(Addons_)?Gateway_)~", $req_uri ) ) { return TRUE; }
			/* $wc_gateways = array( 'WC_Gateway_BACS', 'WC_Gateway_Cheque', 'WC_Gateway_COD', 'WC_Gateway_Paypal', 'WC_Addons_Gateway_Simplify_Commerce', 'WC_Gateway_Simplify_Commerce' ); */
		}

		/* Easy Digital Downloads Payment Gateways */
		if( defined( 'EDD_VERSION' ) ) {
			if( ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $reverse_dns ) ) || ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'IPN' )  || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'IPN' ) !== FALSE ) ) { return TRUE; }
			if( ( !empty( $_GET['edd-listener'] ) && $_GET['edd-listener'] === 'amazon' ) || ( strpos( $req_uri, 'edd-listener' ) !== FALSE && strpos( $req_uri, 'amazon' ) !== FALSE ) ) { return TRUE; }
			if( !empty( $_GET['edd-listener'] ) || strpos( $req_uri, 'edd-listener' ) !== FALSE ) { return TRUE; }
		}

		/* Gravity Forms PayPal Payments Standard Add-On ( http://www.gravityforms.com/add-ons/paypal/ ) */
		if( ( defined( 'GF_MIN_WP_VERSION' ) && defined( 'GF_PAYPAL_VERSION' ) ) || ( class_exists( 'GFForms' ) && class_exists( 'GF_PayPal_Bootstrap' ) ) ) {
			if( $url === WPSS_SITE_URL.'/?page=gf_paypal_ipn' && isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] ) ) { return TRUE; }
		}

		/* PayPal IPN */
		if(
			isset( $_POST['ipn_track_id'], $_POST['payer_id'], $_POST['payment_type'], $_POST['payment_status'], $_POST['receiver_id'], $_POST['txn_id'], $_POST['txn_type'], $_POST['verify_sign'] )
			&& FALSE !== strpos( $req_uri_lc, 'paypal' )
			&& $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )'
			&& preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $reverse_dns )
			&& $fcrdns === '[Verified]'
		) { return TRUE; }

		/* Clef */
		if( defined( 'CLEF_VERSION' ) ) {
			if( preg_match( "~^Clef/[0-9](\.[0-9]+)+\ \(https\://getclef\.com\)$~", $user_agent ) && preg_match( "~((^|\.)clef\.io|\.amazonaws\.com)$~", $reverse_dns ) ) { return TRUE; }
		}

		/* OA Social Login */
		if( defined( 'OA_SOCIAL_LOGIN_VERSION' ) ) {
			$ref_dom_rev = strrev( rs_wpss_get_domain( $referer ) ); $oa_dom_rev = strrev( 'api.oneall.com' );
			if( $post_count >= 4 && isset( $_GET['oa_social_login_source'], $_POST['oa_action'], $_POST['oa_social_login_token'], $_POST['connection_token'], $_POST['identity_vault_key'] ) && $_POST['oa_action'] === 'social_login' && strpos( $ref_dom_rev, $oa_dom_rev ) === 0 ) { return TRUE; }
		}

		/* IFTTT */
		if( rs_wpss_is_xmlrpc() && $user_agent === 'IFTTT production' && preg_match( "~\.amazonaws\.com$~", $reverse_dns ) && $fcrdns === '[Verified]' ) { return TRUE; }

		/* Amazon SNS - http://docs.aws.amazon.com/sns/latest/dg/json-formats.html */
		if( $user_agent === 'Amazon Simple Notification Service Agent' && isset( $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'], $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'], $_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'], $_SERVER['CONTENT_TYPE'], $_POST['HTTP_RAW_POST_DATA'] ) ) {
			$_POST['HTTP_RAWDS_POST_DATA'] = trim(stripslashes($_POST['HTTP_RAW_POST_DATA']));
			if(
				preg_match( "~text/plain~i", $_SERVER['CONTENT_TYPE'] )
				&& preg_match( "~^(SubscriptionConfirmation|Notification|UnsubscribeConfirmation)$~", $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_TYPE'] )
				&& preg_match( "~\"Type\"\ \:\ \"(SubscriptionConfirmation|Notification|UnsubscribeConfirmation)\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& preg_match( "~^[a-z0-9\-]+$~", $_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'] )
				&& preg_match( "~\"MessageId\"\ \:\ \"".preg_quote($_SERVER['HTTP_X_AMZ_SNS_MESSAGE_ID'])."\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& preg_match( "~^arn\:aws\:sns\:([a-z0-9\-]+)\:[a-z0-9]+\:.+$~", $_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'], $amz_sns_topic_arn_matches )
				&& preg_match( "~\"TopicArn\"\ \:\ \"".preg_quote($_SERVER['HTTP_X_AMZ_SNS_TOPIC_ARN'])."\",~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& preg_match( "~\"SigningCertURL\"\ \:\ \"https\://sns\.".preg_quote($amz_sns_topic_arn_matches[1])."\.amazonaws\.com/SimpleNotificationService\-[a-z0-9]+\.pem\"~", $_POST['HTTP_RAWDS_POST_DATA'] )
				&& $fcrdns === '[Verified]'
			) { unset($_POST['HTTP_RAWDS_POST_DATA']); return TRUE; }
			unset($_POST['HTTP_RAWDS_POST_DATA']);
		}

		/* Nothing was triggered */
		return FALSE;
	}

	static public function is_ecom_enabled() {
		/**
		 *  Detect if ecommerce is enabled
		 */
		global $wpss_ecom_enabled,$wpss_woocom_enabled;
		if( !empty( $wpss_ecom_enabled ) || !empty( $wpss_woocom_enabled ) ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		/**
		 *  Users can manually to TRUE in wp-config.php (For example, if user has a custom or unknown ecommerce package)
		 *  Plugin Developers can use WP-SpamShield filter hook 'wpss_misc_form_spam_check_bypass'
		 */
		if( defined('WPSS_CUSTOM_ECOM') && WPSS_CUSTOM_ECOM ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		/**
		 *  Detect popular e-commerce plugins
		 */
		$ecom_plug_constants = array(
			'AFFILIATES_CORE_VERSION', 'AL_BASE_PATH', 'CFCORE_VER', 'EC_CURRENT_VERSION', 'ECWID_PLUGIN_DIR', 'EDD_VERSION', 'EME_DB_VERSION', 'ESHOP_VERSION', 'GF_AUTHORIZENET_VERSION', 'GF_PAYFAST_VERSION', 'GF_PAYPAL_VERSION', 'GF_STRIPE_VERSION', 'GFP_STRIPE_FILE', 'GIVE_VERSION', 'JIGOSHOP_VERSION', 'MEPR_VERSION', 'MP_LITE', 'PMPRO_VERSION', 'SIMPAY_VERSION', 'SIMPLE_WP_MEMBERSHIP_VER', 'UPCP_CD_PLUGIN_PATH', 'USCES_VERSION', 'WC_PP_PRO_ADDON_VERSION', 'wcv_plugin_dir', 'WOOCOMMERCE_VERSION', 'WC_VERSION', 'WP_CART_VERSION', 'WPDM_Version', 'WPPIZZA_VERSION', 'WPSC_VERSION', 'WPSHOP_DIR', 'WS_PLUGIN__S2MEMBER_VERSION', 'WUSPSC_VERSION', 'xoousers_url', 'uultraxoousers_pro_url', 'YITH_WCSTRIPE_VERSION',
			);
		foreach( $ecom_plug_constants as $k => $p ) { if( defined( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		$ecom_plug_classes = array(
			'Easy_Digital_Downloads', 'eCommerce_Product_Catalog', 'GF_AuthorizeNet_Bootstrap', 'GF_PayFast_Bootstrap', 'GF_PayPal_Bootstrap', 'GF_Stripe_Bootstrap', 'Give', 'IT_Exchange', 'MarketPress', 'ShoppLoader', 'WC_Paypal_Pro_Gateway_Addon', 'WC_Vendors', 'WooCommerce', 'WP_eCommerce',
		);
		foreach( $ecom_plug_classes as $k => $p ) { if( class_exists( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		$ecom_plugs = array(
			'affiliates/affiliates.php', 'caldera-forms/caldera-core.php', 'download-manager/download-manager.php', 'easy-digital-downloads/easy-digital-downloads.php', 'ecommerce-product-catalog/ecommerce-product-catalog.php', 'ecwid-shopping-cart/ecwid-shopping-cart.php', 'eshop/eshop.php', 'events-made-easy/events-manager.php', 'events-manager/events-manager.php', 'formidable-paypal/formidable-paypal.php', 'give/give.php', 'gravity-forms-stripe/gravity-forms-stripe.php', 'gravityformsauthorizenet/authorizenet.php', 'gravityformspayfast/payfast.php', 'gravityformsstripe/stripe.php', 'gravityformspaypal/paypal.php', 'ithemes-exchange/init.php', 'jigoshop/jigoshop.php', 'memberpress/memberpress.php', 'paid-memberships-pro/paid-memberships-pro.php', 's2member/s2member-o.php', 'shopp/Shopp.php', 'simple-membership/simple-wp-membership.php', 'stripe/stripe-checkout.php', 'ultimate-product-catalogue/UPCP_Main.php', 'usc-e-shop/usc-e-shop.php', 'users-ultra/xoousers.php', 'wc-vendors/class-wc-vendors.php', 'woocommerce-paypal-pro-payment-gateway/woo-paypal-pro.php', 'woocommerce/woocommerce.php', 'wordpress-ecommerce/marketpress.php', 'wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php', 'wp-e-commerce/wp-shopping-cart.php', 'wp-easycart/wpeasycart.php', 'wp-shop-original/wp-shop.php', 'wp-ultra-simple-paypal-shopping-cart/wp_ultra_simple_shopping_cart.php', 'wppizza/wppizza.php', 'yith-woocommerce-stripe/init.php',
		);
		foreach( $ecom_plugs as $k => $p ) { if( self::is_plugin_active( $p ) ) { $wpss_ecom_enabled = TRUE; return TRUE; } }
		/**
		 *  $ecom_plug_str = array();
		 *  $ecom_plug_regex = array();
		 */
		return FALSE;
	}

	static public function is_woocom_enabled() {
		/**
		 *  Detect WooCommerce plugin
		 */
		global $wpss_ecom_enabled,$wpss_woocom_enabled;
		if( !empty( $wpss_woocom_enabled ) ) { $wpss_ecom_enabled = TRUE; return TRUE; }
		$ecom_plug_constants = array( 'WC_VERSION', 'WOOCOMMERCE_VERSION' );
		foreach( $ecom_plug_constants as $k => $p ) { if( defined( $p ) ) { $wpss_ecom_enabled = TRUE; $wpss_woocom_enabled = TRUE; return TRUE; } }
		$ecom_plugs = array( 'woocommerce/woocommerce.php' );
		foreach( $ecom_plugs as $k => $p ) { if( self::is_plugin_active( $p ) ) { $wpss_ecom_enabled = TRUE; $wpss_woocom_enabled = TRUE; return TRUE; } }
		return FALSE;
	}

	static public function is_builder_active() {
		/**
		 *  Detect if conflicting page builder plugins are active
		 */
		global $wpss_builder_active; if( !empty( $wpss_builder_active ) ) { return TRUE; }
		$builder_plugs = array( 'beaver-builder-lite-version/fl-builder.php', 'bb-plugin/fl-builder.php' );
		foreach( $builder_plugs as $k => $p ) {
			if( self::is_plugin_active( $p ) ) {
				if( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) { $wpss_builder_active = TRUE; return TRUE; }
			}
		}
		return FALSE;
	}

	static public function is_surrogate() {
		/**
		 *  Check for Surrogates
		 *  - Server Caching, Reverse Poxies, WAFS: Varnish, CloudFlare (Rocket Loader), Sucuri WAF, Incapsula, etc.
		 *	- Specific web hosts that use Varnish: WP Engine, Dreamhost, SiteGround, Bluehost...
		 *  @since 1.9.9.5
		 */
		global $wpss_surrogate; if( isset( $wpss_surrogate ) && is_bool( $wpss_surrogate ) ) { return TRUE; }
		$wpss_surrogate = FALSE; $web_host = rs_wpss_get_web_host();
		if( !empty( $web_host ) && ( $web_host === 'WP Engine' || $web_host === 'Dreamhost' || $web_host === 'SiteGround' || $web_host === 'Bluehost' ) ) { $wpss_surrogate = TRUE; WP_SpamShield::update_wpss_option( array( 'surrogate' => $wpss_surrogate ) ); return TRUE; }
		if( self::is_varnish_active() ) { $wpss_surrogate = TRUE; WP_SpamShield::update_wpss_option( array( 'surrogate' => $wpss_surrogate ) ); return TRUE; }
		if( empty( $wpss_surrogate ) ) { $wpss_surrogate = WP_SpamShield::get_wpss_option( 'surrogate' ); }
		if( empty( $wpss_surrogate ) ) { $wpss_surrogate = FALSE; }
		WP_SpamShield::update_wpss_option( array( 'surrogate' => $wpss_surrogate, 'web_host' => $web_host ) );
		return $wpss_surrogate;
	}

	static public function is_varnish_active() {
		/**
		 *  Secondary Varnish detection
		 *  @since 1.9.9.5
		 */
		global $wpss_varnish_active,$wpss_surrogate; if( isset( $wpss_varnish_active ) && is_bool( $wpss_varnish_active ) ) { $wpss_surrogate = TRUE; return TRUE; }
		if( function_exists( 'get_loaded_extensions' ) ) { $ext_loaded_zend = @get_loaded_extensions(); }
		$ext_loaded_zend	= ( !empty( $ext_loaded_zend ) && is_array( $ext_loaded_zend ) ) ? $ext_loaded_zend : array();
		$varnish_loaded		= $wpss_varnish_active = $wpss_surrogate = in_array( 'varnish', $ext_loaded_zend, TRUE ); /* Zend extensions only */
		if( TRUE === $varnish_loaded ) { WP_SpamShield::update_wpss_option( array( 'surrogate' => $wpss_surrogate ) ); return TRUE; }
		$varnish_php_const	= array( 'VARNISH_COMPAT_2', 'VARNISH_COMPAT_3', 'VARNISH_CONFIG_COMPAT', 'VARNISH_CONFIG_HOST', 'VARNISH_CONFIG_IDENT', 'VARNISH_CONFIG_PORT', 'VARNISH_CONFIG_SECRET', 'VARNISH_CONFIG_TIMEOUT', 'VARNISH_STATUS_AUTH', 'VARNISH_STATUS_CANT', 'VARNISH_STATUS_CLOSE', 'VARNISH_STATUS_COMMS', 'VARNISH_STATUS_OK', 'VARNISH_STATUS_PARAM', 'VARNISH_STATUS_SYNTAX', 'VARNISH_STATUS_TOOFEW', 'VARNISH_STATUS_TOOMANY', 'VARNISH_STATUS_UNIMPL', 'VARNISH_STATUS_UNKNOWN', );
		$varnish_plug_const	= array( 'DHDO', 'DHDO_PLUGIN_DIR', 'DREAMSPEED_VERSION', 'VHP_VARNISH_IP', );
		$varnish_constants	= array_merge( $varnish_php_const, $varnish_plug_const );
		foreach( $varnish_constants as $i => $c ) {
			if( defined( $c ) ) { $wpss_varnish_active = $wpss_surrogate = TRUE; WP_SpamShield::update_wpss_option( array( 'surrogate' => $wpss_surrogate ) ); return TRUE; }
		}
		$varnish_plugs		= array( 'dreamobjects/dreamobjects.php', 'dreamspeed-cdn/dreamspeed-cdn.php', 'varnish-http-purge/varnish-http-purge.php', );
		foreach( $varnish_plugs as $i => $p ) {
			if( self::is_plugin_active( $p ) ) { $wpss_varnish_active = $wpss_surrogate = TRUE; return TRUE; }
		}
		$wpss_varnish_active = FALSE;
		return FALSE;
	}

	/* Add more... */

}

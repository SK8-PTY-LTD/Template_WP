<?php

class FMC_Admin {

	public static $instance = null;

	protected $notices = null;
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	private function __construct() {
		$this->notices = new FMC_Notices();

		add_action( 'admin_init', array( $this, 'admin_notice_ignore' ) );
		add_action( 'admin_notices', array($this, 'fmc_admin_notices') );
	}

	
	
	function fmc_admin_notices( ) {
		// Notices filter and run the notices function.

		$admin_notices = apply_filters( 'fmc_admin_notices', array() );
		$this->notices->admin_notice( $admin_notices );

	}
	
	// Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
	public function admin_notice_ignore() {

		$slug = ( isset( $_GET['fmc_admin_notice_ignore'] ) ) ? $_GET['fmc_admin_notice_ignore'] : '';
		// If user clicks to ignore the notice, run this action
		if ( isset($_GET['fmc_admin_notice_ignore']) && current_user_can( 'manage_options'  ) ) {

			$admin_notices_option = get_option( 'fmc_admin_notice', array() );
			$admin_notices_option[ $_GET[ 'fmc_admin_notice_ignore' ] ][ 'dismissed' ] = 1;
			update_option( 'fmc_admin_notice', $admin_notices_option );
			$query_str = remove_query_arg( 'fmc_admin_notice_ignore' );
			wp_redirect( $query_str );
			exit;
		}
	}
}

?>
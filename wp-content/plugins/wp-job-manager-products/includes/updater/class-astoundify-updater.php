<?php

class Astoundify_Updater_Products {

	public $file;
	public $api_url;
	public $item;
	public $slug;
	public $version;

	public function __construct( $file ) {
		$this->plugin_data = get_plugin_data( $file );
		$this->file = $file;

		$this->api_url = 'https://astoundify.com';
		$this->item = $this->plugin_data[ 'Name' ];
		$this->slug = str_replace( '.php', '', basename( $this->file ) );
		$this->version = $this->plugin_data[ 'Version' ];

		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
		}

		add_action( 'job_manager_settings', array( $this, 'license_field'), 11 );

		add_action( 'admin_init', array( $this, 'init_updater' ) );
		add_action( 'admin_init', array( $this, 'deactive_license' ) );

		add_action( 'update_option_' . $this->slug, array( $this, 'sanitize_license' ), 10, 2 );

		if ( ! has_action( 'wp_job_manager_admin_field_license' ) ) {
			add_action( 'wp_job_manager_admin_field_license', array( $this, 'output_license_field' ), 10, 4 );
		}
	}

	public function license_field( $fields ) {
		$fields[ 'wpjmp_settings' ][1][] = array(
			'name'			=> $this->slug,
			'type'          => 'license',
			'std'			=> '',
			'placeholder'	=> '',
			'label'			=> __( 'License Key', 'wp-job-manager-reviews' ),
			'desc'			=> __( 'Enter the license key you received with your purchase receipt.', 'wp-job-manager-reviews' ),
			'attributes'	=> array()
		);

		return $fields;
	}

	public function init_updater() {
		$license_key = trim( get_option( $this->slug ) );

		$edd_updater = new EDD_SL_Plugin_Updater( $this->api_url, $this->file, array( 
			'version' 	=> $this->version,
			'license' 	=> $license_key,
			'item_name' => $this->item,
			'author' 	=> 'Astoundify'
		) );
	}

	public function output_license_field( $option, $attributes, $value, $placeholder ) {
		$status  = get_option( $this->slug . '_status' );
		?>
			<input id="setting-<?php echo $option['name']; ?>" class="regular-text" type="text" name="<?php echo $option['name']; ?>" value="<?php esc_attr_e( $value ); ?>" <?php echo implode( ' ', $attributes ); ?> <?php echo $placeholder; ?> />

			<?php
			if ( $option['desc'] ) {
				echo ' <p class="description">' . $option['desc'] . '</p>';
			}
		?>
			<p>
			<?php if( $status !== false && $status == 'valid' ) { ?>
				<?php
					$args = array(
						'astoundify-action' => 'deactivate-license'
					);
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, wp_nonce_url( admin_url( '/edit.php?post_type=job_listing&page=job-manager-settings' ), 'astoundify_license_deactivate' ) ) ); ?>" class="button-secondary"><?php _e( 'Deactivate', 'wp-job-manager-reviews' ); ?></a>
			<?php  } ?>
			</p>
		<?php
	}

	public function sanitize_license( $old, $new ) {
		$old = get_option( $this->slug );
		
		if( $old && $old != $new ) {
			delete_option( $this->slug . '_status' );
		}

		$this->activate_license( $new );
		
		return $new;
	}

	public function activate_license( $license ) {
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->item ), // the name of our product in EDD
			'url'       => home_url()
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		update_option( $this->slug . '_status', $license_data->license );	
	}
	
	public function deactive_license() {
		if ( ! isset( $_GET[ 'astoundify-action' ] ) ) {
			return;
		}

		if ( 'deactivate-license' != $_GET[ 'astoundify-action' ] ) {
			return;
		}

		if ( isset( $_GET[ '_wpnonce' ] ) && ! wp_verify_nonce( $_GET[ '_wpnonce' ], 'astoundify_license_deactivate' ) ) {
			return;
		}

		$license = get_option( $this->slug );

		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( $this->item ), // the name of our product in EDD
			'url'       => home_url()
		);

		$response = wp_remote_get( add_query_arg( $api_params, $this->api_url ), array( 'timeout' => 15, 'sslverify' => false ) );
		
		if ( is_wp_error( $response ) )
			return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		if( $license_data->license == 'deactivated' ) {
			delete_option( $this->slug . '_status' );
			delete_option( $this->slug );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=job_listing&page=job-manager-settings' ) );

		exit();
	}

}

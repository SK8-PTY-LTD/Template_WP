<?php

/**
 * Admin page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$upd = FM_Admin::get_instance();
$upd->check_for_update();
$fm_plugins=$upd->fm_plugins;
$updates=$upd->updates;
?>
<link href="<?php echo plugins_url( 'admin.css', __FILE__ )?>" rel="stylesheet"/>

<div class="wrap">
	<?php settings_errors(); ?>
	<div id="fm-settings">
		<div id="fm-settings-content">
			<h2 id="add_on_title"><?php echo esc_html( get_admin_page_title() ); ?></h2>


			<div class="main-plugin_desc-cont">
				You can download the latest version of your plugins from your  <a href="https://web-dorado.com" target="_blank"> Web-Dorado.com</a>  account.
				After deactivate and
				delete the current version.
				Install the downloaded latest version of the plugin.
			</div>

			<br/>
			<br/>

			<?php
			if ( $fm_plugins ) {
				$update = 0;
				if ( isset( $fm_plugins[31] ) ) {

					$project = $fm_plugins[31];
					unset( $fm_plugins[31] );
					if ( isset( $updates[31] ) ) {
						$update = 1;
					}
					?>
					<div class="main-plugin">
						<div class="fm-add-on">
								<?php if ( $project['fm_data']['image'] ) { ?>
									<div class="fm-figure-img">
										<a href="<?php echo $project['fm_data']['url'] ?>" target="_blank">
											<img src="<?php echo $project['fm_data']['image'] ?>"/>
										</a>
									</div>
								<?php } ?>

						</div>
						<div class="main-plugin-info">
							<h2>
								<a href="<?php echo $project['fm_data']['url'] ?>" target="_blank"><?php echo $project['Title'] ?></a>
							</h2>
							<div class="main-plugin_desc-cont">
								<div class="main-plugin-desc"><?php echo $project['fm_data']['description'] ?></div>
								<div class="main-plugin-desc main-plugin-desc-info">
									<p><a href="<?php echo $project['fm_data']['url'] ?>" target="_blank">Version <?php echo $project['Version']?></a></p>
								</div>

								<?php if ( isset( $updates[31][0] ) ) { ?>
									<span class="update-info">There is a new  <?php echo $updates[31][0]['version'] ?> version available.</span>
									<p><span>What's new:</span></p>
									<div class="fm_last_update"><?php echo $updates[31][0]['version'] ?>
										- <?php echo strip_tags( str_replace('important', '', $updates[31][0]['note']) ) ?></div>
									<?php unset( $updates[31][0] ); ?>
									<?php if ( count( $updates[31] ) > 0 ) { ?>

											<div class="fm_more_updates">
										<?php foreach ( $updates[31] as $update ) {
											?>
											<div class="fm_update"><?php echo $update['version'] ?>
												- <?php echo strip_tags( str_replace('important', '', $update['note']) ) ?></div>
										<?php
										}
										?>
											</div>
										<a href="#" class="fm_show_more_updates">More updates</a>
									<?php
									}
								} ?>
								
								
						
								

							</div>
						</div>
					</div>
				<?php
				}?>
				<div class="fm-addons_updates">
					<?php
					foreach ( $fm_plugins as $id => $project ) {
						?>
						<div class="fm-add-on">
							<figure class="fm-figure">
								<div class="fm-figure-img">
									<a href="<?php echo $project['fm_data']['url'] ?>" target="_blank">
										<?php if ( $project['fm_data']['image'] ) { ?>
											<img src="<?php echo $project['fm_data']['image'] ?>"/>
										<?php } ?>
									</a>
								</div>
								<figcaption class="fm-addon-descr fm-figcaption">
									<?php if ( isset( $updates[ $id ][0] ) ) { ?>
										<p>What's new:</p>
										<?php echo strip_tags( $updates[ $id ][0]['note'] ) ?>
									<?php } else { ?><?php echo $project['Title'] ?> is up to date
									<?php } ?>
								</figcaption>
							</figure>
							<h2><?php echo $project['Title'] ?></h2>
							<div class="main-plugin-desc-info">
								<p><a href="<?php echo $project['fm_data']['url'] ?>"
								      target="_blank"><?php echo $project['Version'] ?></a> | Web-Dorado</p>
							</div>
							<?php if ( isset( $updates[ $id ] ) ) { ?>
								<div class="fm-addon-descr-update">
									<span
										class="update-info">There is an new  <?php echo $updates[ $id ][0]['version'] ?>
										version</span><br/>
								</div>
							<?php } ?>
						</div>
					
					<?php
					}?>
				</div>
			<?php
			}
			?>

		</div>
		<!-- #fm-settings-content -->
	</div>
	<!-- #fm-settings -->
</div><!-- .wrap -->

<script>
    jQuery('.fm_show_more_updates').click(function(){
        if( jQuery('.fm_more_updates').is(':visible') == false) {
            jQuery(this).text('Show less');
        }else{
            jQuery(this).text('More updates');
        }
       jQuery('.fm_more_updates').slideToggle();
        return false;
    });

</script>

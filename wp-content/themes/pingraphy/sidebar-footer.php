<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package tagazine
 */
?>

				<?php if (( is_active_sidebar( 'footer-sidebar-1' )) || ( is_active_sidebar( 'footer-sidebar-2' )) || ( is_active_sidebar( 'footer-sidebar-3' ))) : ?>
					<div class="footer-widgets">
						<div class="inner clearfix">
							<?php if ( is_active_sidebar( 'footer-sidebar-1' )) : ?>
							<div class="footer-widget footer-column-1">
								<?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
							</div>
							<?php endif; ?>
							<?php if ( is_active_sidebar( 'footer-sidebar-2' )) : ?>
							<div class="footer-widget footer-column-2">
								<?php dynamic_sidebar( 'footer-sidebar-2' ); ?>
							</div>
							<?php endif; ?>
							<?php if ( is_active_sidebar( 'footer-sidebar-3' )) : ?>
							<div class="footer-widget footer-column-3">
								<?php dynamic_sidebar( 'footer-sidebar-3' ); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
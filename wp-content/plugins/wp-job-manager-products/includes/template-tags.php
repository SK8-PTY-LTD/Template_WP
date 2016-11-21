<?php
/**
 * Convenience function.
 */

function wpjmp_get_products( $listing ) {
    if ( is_int( $listing ) ) {
        $listing = get_post( $listing );
    }

    return get_post_meta( $listing->ID, '_products', true );
}

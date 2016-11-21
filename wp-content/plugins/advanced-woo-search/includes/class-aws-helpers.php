<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'AWS_Helpers' ) ) :

    /**
     * Class for plugin help methods
     */
    class AWS_Helpers {

        /*
         * Removes scripts, styles, html tags
         */
        static public function html2txt( $str ) {
            $search = array(
                '@<script[^>]*?>.*?</script>@si',
                '@<[\/\!]*?[^<>]*?>@si',
                '@<style[^>]*?>.*?</style>@siU',
                '@<![\s\S]*?--[ \t\n\r]*>@'
            );
            $str = preg_replace( $search, '', $str );

            $str = esc_attr( $str );
            $str = stripslashes( $str );
            $str = str_replace( array( "\r", "\n" ), ' ', $str );

            $str = str_replace( array(
                "Â·",
                "â€¦",
                "â‚¬",
                "&shy;"
            ), "", $str );

            return $str;
        }

    }

endif;
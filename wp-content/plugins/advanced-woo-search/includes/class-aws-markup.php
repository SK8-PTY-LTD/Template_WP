<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Markup' ) ) :

    /**
     * Class for plugin search action
     */
    class AWS_Markup {

        /*
         * Generate search box markup
         */
        public function markup() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) != $table_name ) {
                echo 'Please go to <a href="' . admin_url( 'admin.php?page=aws-options' ) . '">plugins settings page</a> and click on "Reindex table" button.';
                return;
            }


            $placeholder  = AWS()->get_settings( 'search_field_text' );
            $min_chars    = AWS()->get_settings( 'min_chars' );
            $show_loader  = AWS()->get_settings( 'show_loader' );

            $params_string = '';

            $params = array(
                'data-url'          => admin_url('admin-ajax.php'),
                'data-siteurl'      => site_url(),
                'data-show-loader'  => $show_loader,
                'data-min-chars'    => $min_chars,
            );

            foreach( $params as $key => $value ) {
                $params_string .= $key . '="' . $value . '" ';
            }

            $markup = '';
            $markup .= '<div class="aws-container" ' . $params_string . '>';
            $markup .= '<form class="aws-search-form" action="' . site_url() . '" method="get" role="search" >';
            $markup .= '<input  type="text" name="s" value="' . get_search_query() . '" class="aws-search-field" placeholder="' . $placeholder . '" autocomplete="off" />';
            $markup .= '<input type="hidden" name="post_type" value="product">';
            $markup .= '<div class="aws-search-result" style="display: none;"></div>';
            $markup .= '</form>';
            $markup .= '</div>';

            return apply_filters( 'aws_searchbox_markup', $markup );

        }

    }

endif;
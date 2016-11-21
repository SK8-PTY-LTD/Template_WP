<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Table' ) ) :

    /**
     * Class for plugin index table
     */
    class AWS_Table {

        /**
         * @var AWS_Search ID of current filter $filter_id
         */
        private $table_name;

        /**
         * Constructor
         */
        public function __construct() {

            global $wpdb;

            $this->table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            //add_action( 'wp_loaded', array( $this, 'check_table' ) );

            add_action( 'save_post', array( $this, 'update_table' ), 10, 3 );

            add_action( 'aws_settings_saved', array( $this, 'clear_cache' ) );
            add_action( 'aws_cache_clear', array( $this, 'clear_cache' ) );

            add_action( 'create_term', array( &$this, 'term_changed' ), 10, 3 );
            add_action( 'delete_term', array( &$this, 'term_changed' ), 10, 3 );
            add_action( 'edit_term', array( &$this, 'term_changed' ), 10, 3 );

            add_action( 'wp_ajax_aws-reindex', array( $this, 'reindex_table' ) );
            add_action( 'wp_ajax_nopriv_aws-reindex', array( $this, 'reindex_table' ) );

            add_action( 'wp_ajax_aws-clear-cache', array( &$this, 'clear_cache' ) );
            add_action( 'wp_ajax_nopriv_aws-clear-cache', array( &$this, 'clear_cache' ) );

        }

        /*
         * Reindex plugin table
         */
        public function reindex_table() {

            global $wpdb;

            $wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");

            $this->check_table();

            $this->clear_cache();

        }

        /*
         * Generate table for search terms
         */
        public function check_table() {

            global $wpdb;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) != $this->table_name ) {

                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE {$this->table_name} (
                      id MEDIUMINT(20) NOT NULL DEFAULT 0,
                      term VARCHAR(50) NOT NULL DEFAULT 0,
                      term_source VARCHAR(20) NOT NULL DEFAULT 0,
                      type VARCHAR(50) NOT NULL DEFAULT 0,
                      count MEDIUMINT(20) NOT NULL DEFAULT 0
                ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );

                $this->fill_table();

            }

        }

        /*
         * Insert data into the index table
         */
        private function fill_table( $post_id = 0 ) {

            global $wpdb;

            $posts = get_posts( array(
                'posts_per_page'  => -1,
                'post_type'       => 'product',
                'no_found_rows'   => 1,
                'include'         => $post_id
            ) );


            foreach ( $posts as $post ) {

                $values = array();

                $terms  = array();
                $id     = $post->ID;

                $custom = get_post_custom($id);

                if ( isset( $custom['_visibility'] ) && $custom['_visibility'][0] == 'hidden' ) {
                    continue;
                }

//                if ( isset( $custom['_stock_status'] ) && $custom['_stock_status'][0] == 'outofstock' ) {
//                    continue;
//                }

                $product = new WC_product( $id );

                $sku = $product->get_sku();

                $title = apply_filters( 'the_title', get_the_title( $id ) );
                $content = apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
                $excerpt = apply_filters( 'get_the_excerpt', get_post_field( 'post_excerpt', $id ) );
                $cat_names = $this->get_terms_names_list( $id, 'product_cat' );
                $tag_names = $this->get_terms_names_list( $id, 'product_tag' );


                // WP 4.2 emoji strip
                if ( function_exists( 'wp_encode_emoji' ) ) {
                    $content = wp_encode_emoji( $content );
                }

                $content = strip_shortcodes( $content );


                $terms['title']    = $this->extract_terms( $title );
                $terms['content']  = $this->extract_terms( $content );
                $terms['excerpt']  = $this->extract_terms( $excerpt );
                $terms['sku']      = $this->extract_terms( $sku );
                $terms['category'] = $this->extract_terms( $cat_names );
                $terms['tag']      = $this->extract_terms( $tag_names );


                foreach( $terms as $source => $all_terms ) {

                    foreach ( $all_terms as $term => $count ) {

                        if ( ! $term ) {
                            continue;
                        }

                        $value = $wpdb->prepare(
                            "(%d, %s, %s, %s, %d)",
                            $id, $term, $source, 'product', $count
                        );

                        $values[] = $value;

                    }

                }


                if ( count( $values ) > 0 ) {

                    $values = implode( ', ', $values );

                    $query  = "INSERT IGNORE INTO {$this->table_name}
				              (`id`, `term`, `term_source`, `type`, `count`)
				              VALUES $values
                    ";

                    $wpdb->query( $query );

                }


            }

        }

        /*
         * Update index table
         */
        public function update_table( $post_id, $post, $update ) {

            global $wpdb;

            $this->check_table();

            $slug = 'product';

            if ( $slug != $post->post_type ) {
                return;
            }

            $wpdb->delete( $this->table_name, array( 'id' => $post_id ) );

            $this->fill_table( $post_id );

            $this->clear_cache();

        }

        /*
         * Fires when products terms are changed
         */
        public function term_changed( $term_id, $tt_id, $taxonomy ) {

            if ( $taxonomy === 'product_cat' || $taxonomy === 'product_tag' ) {
                do_action( 'aws_cache_clear' );
            }

        }

        /*
         * Clear search cache
         */
        public function clear_cache() {

            global $wpdb;

            $table_name = "aws_search_term_%";

            $sql = "DELETE FROM $wpdb->options
                WHERE option_name LIKE '{$table_name}'
		    ";

            $wpdb->query( $sql );

        }

        /*
         * Extract terms from content
         */
        private function extract_terms( $str ) {

            $str = AWS_Helpers::html2txt( $str );

            // Avoid single A-Z.
            //$str = preg_replace( '/\b\w{1}\b/i', " ", $str );

            $str = str_replace( array(
                '_',
                '|',
                '+',
                '`',
                '~',
                '!',
                '@',
                '#',
                '$',
                '%',
                '^',
                '&',
                '*',
                '(',
                ')',
                '\\',
                '?',
                ';',
                ':',
                "'",
                '"',
                ".",
                ",",
                "<",
                ">",
                "{",
                "}",
                "/",
                "[",
                "]",
            ), "", $str );

            $str = str_replace( array(
                "Ă‹â€ˇ",
                "Ă‚Â°",
                "Ă‹â€ş",
                "Ă‹ĹĄ",
                "Ă‚Â¸",
                "Ă‚Â§",
                "=",
                "Ă‚Â¨",
                "â€™",
                "â€",
                "â€ť",
                "â€ś",
                "â€ž",
                "Â´",
                "â€”",
                "â€“",
                "Ă—",
                '&#8217;',
                "&nbsp;",
                chr( 194 ) . chr( 160 )
            ), " ", $str );

            $str = str_replace( 'Ăź', 'ss', $str );

            //$str = preg_replace( '/[[:punct:]]+/u', ' ', $str );
            $str = preg_replace( '/[[:space:]]+/', ' ', $str );

            // Most objects except unicode characters
            $str = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $str );

            // Line feeds, carriage returns, tabs
            $str = preg_replace( '/[\x00-\x1F\x80-\x9F]/u', '', $str );

            $str = strtolower( $str );

            $str = preg_replace( '/^[a-z]$/i', "", $str );

            $str = trim( preg_replace( '/\s+/', ' ', $str ) );

            $str_array = array_count_values( explode( ' ', $str ) );

            return $str_array;

        }

        /*
         * Removes scripts, styles, html tags
         */
        private function html2txt( $str ) {
            $search = array(
                '@<script[^>]*?>.*?</script>@si',
                '@<[\/\!]*?[^<>]*?>@si',
                '@<style[^>]*?>.*?</style>@siU',
                '@<![\s\S]*?--[ \t\n\r]*>@'
            );
            $text = preg_replace( $search, '', $str );

            return $text;
        }

        /*
         * Get string with current product terms ids
         *
         * @return string List of terms ids
         */
        private function get_terms_list( $id, $taxonomy ) {

            $terms = get_the_terms( $id, $taxonomy );

            if ( is_wp_error( $terms ) ) {
                return '';
            }

            if ( empty( $terms ) ) {
                return '';
            }

            $cats_array_temp = array();

            foreach ( $terms as $term ) {
                $cats_array_temp[] = $term->term_id;
            }

            return implode( ', ', $cats_array_temp );

        }

        /*
         * Get string with current product terms names
         *
         * @return string List of terms names
         */
        private function get_terms_names_list( $id, $taxonomy ) {

            $terms = get_the_terms( $id, $taxonomy );

            if ( is_wp_error( $terms ) ) {
                return '';
            }

            if ( empty( $terms ) ) {
                return '';
            }

            $cats_array_temp = array();

            foreach ( $terms as $term ) {
                $cats_array_temp[] = $term->name;
            }

            return implode( ', ', $cats_array_temp );

        }

    }

endif;


new AWS_Table();
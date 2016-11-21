<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'AWS_Search' ) ) :

    /**
     * Class for plugin search
     */
    class AWS_Search {

        /**
         * @var AWS_Search Array of all plugin data $data
         */
        private $data = array();

        /**
         * Constructor
         */
        public function __construct() {

            $this->data['settings'] = get_option( 'aws_settings' );

            add_action( 'wp_ajax_aws_action', array( $this, 'action_callback' ) );
            add_action( 'wp_ajax_nopriv_aws_action', array( $this, 'action_callback' ) );

        }

        /*
         * AJAX call action callback
         */
        public function action_callback() {

            global $wpdb;

            $cache = AWS()->get_settings( 'cache' );

            $s = esc_attr( $_POST['keyword'] );
            $s = stripslashes( $s );
            $s = str_replace( array( "\r", "\n" ), '', $s );


            if ( $cache === 'true' ) {

                $cache_option_name = 'aws_search_term_' . $s;

                // Check if value was already cached
                if ($cached_value = get_option($cache_option_name)) {
                    $cached_value['cache'] = 'cached';
                    echo json_encode($cached_value);
                    die;
                }

            }


            $show_cats     = AWS()->get_settings( 'show_cats' );
            $show_tags     = AWS()->get_settings( 'show_tags' );
            $results_num   = AWS()->get_settings( 'results_num' );
            $search_in     = AWS()->get_settings( 'search_in' );

            $search_in_arr = explode( ',',  AWS()->get_settings( 'search_in' ) );

            // Search in title if all options is disabled
            if ( ! $search_in ) {
                $search_in_arr = array( 'title' );
            }

            $categories_array = array();
            $tags_array = array();


            $this->data['s'] = $s;
            $this->data['results_num'] = $results_num;
            $this->data['search_terms'] = array();
            $this->data['search_terms'] = array_unique( explode( ' ', $s ) );
            $this->data['search_in'] = $search_in_arr;


            $posts_ids = $this->query_index_table();
            $products_array = $this->get_products( $posts_ids );


            if ( $show_cats === 'true' ) {
                $categories_array = $this->get_taxonomies( 'product_cat' );
            }

            if ( $show_tags === 'true' ) {
                $tags_array = $this->get_taxonomies( 'product_tag' );
            }

            $result_array = array(
                'cats'     => $categories_array,
                'tags'     => $tags_array,
                'products' => $products_array
            );


            if ( $cache === 'true' ) {
                update_option( $cache_option_name, $result_array );
            }


            echo json_encode( $result_array );

            die;

        }

        /*
         * Query in index table
         */
        private function query_index_table() {

            global $wpdb;

            $table_name = $wpdb->prefix . AWS_INDEX_TABLE_NAME;

            $search_in_arr    = $this->data['search_in'];
            $results_num      = $this->data['results_num'];

            $query = array();

            $query['search'] = '';
            $query['source'] = '';
            $query['relevance'] = '';

            $search_array = array();
            $source_array = array();
            $relevance_array = array();
            $new_relevance_array = array();


            foreach ( $this->data['search_terms'] as $search_term ) {

                $search_term_len = strlen( $search_term );

                $relevance_title        = 200 + 20 * $search_term_len;
                $relevance_content      = 35 + 4 * $search_term_len;
                $relevance_title_like   = 40 + 2 * $search_term_len;
                $relevance_content_like = 35 + 1 * $search_term_len;


                $like = '%' . $wpdb->esc_like( $search_term ) . '%';

                if ( $search_term_len > 1 ) {
                    $search_array[] = $wpdb->prepare( '( term LIKE %s )', $like );
                } else {
                    $search_array[] = $wpdb->prepare( '( term = "%s" )', $search_term );
                }

                foreach ( $search_in_arr as $search_in_term ) {

                    switch ( $search_in_term ) {

                        case 'title':
                            $relevance_array['title'][] = $wpdb->prepare( "( case when ( term_source = 'title' AND term = '%s' ) then {$relevance_title} * count else 0 end )", $search_term );
                            $relevance_array['title'][] = $wpdb->prepare( "( case when ( term_source = 'title' AND term LIKE %s ) then {$relevance_title_like} * count else 0 end )", $like );
                            break;

                        case 'content':
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'content' AND term = '%s' ) then {$relevance_content} * count else 0 end )", $search_term );
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'content' AND term LIKE %s ) then {$relevance_content_like} * count else 0 end )", $like );
                            break;

                        case 'excerpt':
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'excerpt' AND term = '%s' ) then {$relevance_content} * count else 0 end )", $search_term );
                            $relevance_array['content'][] = $wpdb->prepare( "( case when ( term_source = 'excerpt' AND term LIKE %s ) then {$relevance_content_like} * count else 0 end )", $like );
                            break;

                        case 'category':
                            $relevance_array['category'][] = $wpdb->prepare( "( case when ( term_source = 'category' AND term = '%s' ) then 35 else 0 end )", $search_term );
                            $relevance_array['category'][] = $wpdb->prepare( "( case when ( term_source = 'category' AND term LIKE %s ) then 5 else 0 end )", $like );
                            break;

                        case 'tag':
                            $relevance_array['tag'][] = $wpdb->prepare( "( case when ( term_source = 'tag' AND term = '%s' ) then 35 else 0 end )", $search_term );
                            $relevance_array['tag'][] = $wpdb->prepare( "( case when ( term_source = 'tag' AND term LIKE %s ) then 5 else 0 end )", $like );
                            break;

                    }

                }

            }

            // Sort 'relevance' queries in the array by search priority
            foreach ( $search_in_arr as $search_in_item ) {
                if ( isset( $relevance_array[$search_in_item] ) ) {
                    $new_relevance_array[$search_in_item] = implode( ' + ', $relevance_array[$search_in_item] );
                }
            }

            foreach ( $search_in_arr as $search_in_term ) {
                $source_array[] = "term_source = '{$search_in_term}'";
            }

            $query['relevance'] .= sprintf( ' (SUM( %s )) ', implode( ' + ', $new_relevance_array ) );
            $query['search'] .= sprintf( ' AND ( %s )', implode( ' OR ', $search_array ) );
            $query['source'] .= sprintf( ' AND ( %s )', implode( ' OR ', $source_array ) );


            $sql = "SELECT
                    distinct ID,
                    {$query['relevance']} as relevance
                FROM
                    {$table_name}
                WHERE
                    type = 'product'
                {$query['source']}
                {$query['search']}
                GROUP BY ID
                ORDER BY
                    relevance DESC
				LIMIT 0, {$results_num}
		";

            $posts_ids = $this->get_posts_ids( $sql );

            return $posts_ids;

        }

        /*
     * Get array of included to search result posts ids
     */
        private function get_posts_ids( $sql ) {

            global $wpdb;

            $posts_ids = array();

            $search_results = $wpdb->get_results( $sql );


            if ( !empty( $search_results ) && !is_wp_error( $search_results ) && is_array( $search_results ) ) {
                foreach ( $search_results as $search_result ) {
                    $post_id = intval( $search_result->ID );
                    if ( ! in_array( $post_id, $posts_ids ) ) {
                        $posts_ids[] = $post_id;
                    }
                }
            }

            unset( $search_results );

            return $posts_ids;

        }

        /*
         * Get products info
         */
        private function get_products( $posts_ids ) {

            $products_array = array();

            if ( count( $posts_ids ) > 0 ) {

                $show_excerpt      = AWS()->get_settings( 'show_excerpt' );
                $excerpt_source    = AWS()->get_settings( 'desc_source' );
                $excerpt_length    = AWS()->get_settings( 'excerpt_length' );
                $mark_search_words = AWS()->get_settings( 'mark_words' );
                $show_price        = AWS()->get_settings( 'show_price' );
                $show_sale         = AWS()->get_settings( 'show_sale' );
                $show_image        = AWS()->get_settings( 'show_image' );
                $show_sku          = AWS()->get_settings( 'show_sku' );

                foreach ( $posts_ids as $post_id ) {

                    $product = new WC_product( $post_id );

                    $post_data = $product->get_post_data();

                    $title = $product->get_title();
                    $title = AWS_Helpers::html2txt( $title );

                    $excerpt = '';
                    $price   = '';
                    $on_sale = '';
                    $image = '';
                    $sku = '';

                    if ( $show_excerpt === 'true' ) {
                        $excerpt = ( $excerpt_source === 'excerpt' && $post_data->post_excerpt ) ? $post_data->post_excerpt : $post_data->post_content;
                        $excerpt = AWS_Helpers::html2txt( $excerpt );
                        $excerpt = str_replace('"', "'", $excerpt);
                    }

                    if ( $mark_search_words === 'true'  ) {

                        $marked_content = $this->mark_search_words( $title, $excerpt );

                        $title   = $marked_content['title'];
                        $excerpt = $marked_content['content'];

                    } else {
                        $excerpt = wp_trim_words( $excerpt, $excerpt_length, '...' );
                    }

                    if ( $show_price === 'true' ) {
                        $price = $product->get_price_html();
                    }

                    if ( $show_sale === 'true' ) {
                        $on_sale = $product->is_on_sale();
                    }

                    if ( $show_image === 'true' ) {
                        $image_id = $product->get_image_id();
                        $image_attributes = wp_get_attachment_image_src( $image_id );
                        $image = $image_attributes[0];
                    }

                    if ( $show_sku === 'true' ) {
                        $sku = $product->get_sku();
                    }

                    $categories = $product->get_categories( ',' );

                    $tags = $product->get_tags( ',' );

                    $new_result = array(
                        'title'      => $title,
                        'excerpt'    => $excerpt,
                        'link'       => get_permalink( $post_id ),
                        'image'      => $image,
                        'price'      => $price,
                        'categories' => $categories,
                        'tags'       => $tags,
                        'on_sale'    => $on_sale,
                        'sku'        => $sku
                    );

                    $products_array[] = $new_result;
                }

            }

            return $products_array;

        }

        /*
         * Mark search words
         */
        private function mark_search_words( $title, $content ) {

            $pattern = array();
            $exact_pattern = array();
            $exact_words = array();
            $words = array();

            foreach( $this->data['search_terms'] as $search_in ) {

                $exact_words[] = '\b' . $search_in . '\b';
                $exact_pattern[] = '(\b' . $search_in . '\b)+';

                if ( strlen( $search_in ) > 1 ) {
                    $pattern[] = '(' . $search_in . ')+';
                    $words[] = $search_in;
                } else {
                    $pattern[] = '\b[' . $search_in . ']{1}\b';
                    $words[] = '\b' . $search_in . '\b';
                }

            }

            usort( $exact_words, array( $this, 'sort_by_length' ) );
            $exact_words = implode( '|', $exact_words );

            usort( $words, array( $this, 'sort_by_length' ) );
            $words = implode( '|', $words );

            usort( $exact_pattern, array( $this, 'sort_by_length' ) );
            $exact_pattern = implode( '|', $exact_pattern );
            $exact_pattern = sprintf( '/%s/i', $exact_pattern );

            usort( $pattern, array( $this, 'sort_by_length' ) );
            $pattern = implode( '|', $pattern );
            $pattern = sprintf( '/%s/i', $pattern );


            preg_match( '/([^.?!]*?)(' . $exact_words . '){1}(.*?[.!?])/i', $content, $matches );

            if ( ! $matches[0] ) {
                preg_match( '/([^.?!]*?)(' . $words . '){1}(.*?[.!?])/i', $content, $matches );
            }

            if ( ! $matches[0] ) {
                preg_match( '/([^.?!]*?)(.*?)(.*?[.!?])/i', $content, $matches );
            }

            $content = $matches[0];


            // Trim to long content
            if ( str_word_count( strip_tags( $content ) ) > 34 ) {

                if ( str_word_count( strip_tags( $matches[3] ) ) > 34 ) {
                    $matches[3] = wp_trim_words( $matches[3], 30, '...' );
                }

                $content = '...' . $matches[2] . $matches[3];

            }


            $title_has_exact = preg_match( '/(' . $exact_words . '){1}/i', $title );
            $content_has_exact = preg_match( '/(' . $exact_words . '){1}/i', $content );


            if ( $title_has_exact === 1 || $content_has_exact === 1 ) {
                $title = preg_replace($exact_pattern, '<strong>${0}</strong>', $title );
                $content = preg_replace($exact_pattern, '<strong>${0}</strong>', $content );
            } else {
                $title = preg_replace($pattern, '<strong>${0}</strong>', $title );
                $content = preg_replace( $pattern, '<strong>${0}</strong>', $content );
            }


            return array(
                'title'   => $title,
                'content' => $content
            );

        }

        /*
         * Sort array by its values length
         */
        private function sort_by_length( $a, $b ) {
            return strlen( $b ) - strlen( $a );
        }

        /*
         * Query product taxonomies
         */
        private function get_taxonomies( $taxonomy ) {

            global $wpdb;

            $result_array = array();
            $search_array = array();
            $excludes = '';
            $search_query = '';

            foreach ( $this->data['search_terms'] as $search_term ) {
                $like = '%' . $wpdb->esc_like($search_term) . '%';
                $search_array[] = $wpdb->prepare('( name LIKE %s )', $like);
            }

            $search_query .= sprintf( ' AND ( %s )', implode( ' OR ', $search_array ) );


            $sql = "
			SELECT
				distinct($wpdb->terms.name),
				$wpdb->terms.term_id,
				$wpdb->term_taxonomy.taxonomy,
				$wpdb->term_taxonomy.count
			FROM
				$wpdb->terms
				, $wpdb->term_taxonomy
			WHERE 1 = 1
				{$search_query}
				AND $wpdb->term_taxonomy.taxonomy = '{$taxonomy}'
				AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
			$excludes
			LIMIT 0, 10";

            $search_results = $wpdb->get_results( $sql );

            if ( ! empty( $search_results ) && !is_wp_error( $search_results ) ) {

                foreach ( $search_results as $result ) {

                    $term = get_term( $result->term_id, $result->taxonomy );

                    if ( $term != null && !is_wp_error( $term ) ) {
                        $term_link = get_term_link( $term );
                    } else {
                        $term_link = '';
                    }

                    $new_result = array(
                        'name'     => $result->name,
                        'count'    => $result->count,
                        'link'     => $term_link
                    );

                    $result_array[] = $new_result;

                }

            }

            return $result_array;

        }

    }


endif;


new AWS_Search();
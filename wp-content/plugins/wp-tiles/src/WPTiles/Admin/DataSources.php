<?php namespace WPTiles\Admin;

use WPTiles\WPTiles;

// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) )
    exit;

class DataSources
{
    public static function get_grids() {
        $wp_posts = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => WPTiles::GRID_POST_TYPE
        ));

        $result = array();
        foreach ($wp_posts as $post) {
            $result[] = array('value' => $post->ID, 'label' => $post->post_title);
        }
        return $result;
    }

    public static function get_grids_names() {
        $wp_posts = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => WPTiles::GRID_POST_TYPE
        ));

        $result = array();
        foreach ($wp_posts as $post)
        {
            $result[] = array('value' => $post->post_title, 'label' => $post->post_title);
        }
        return $result;
    }

    public static function get_post_types() {
        $result = array();
        foreach( get_post_types( array( 'public' => true ), 'objects' ) as $post_type => $post_type_obj ) {
            $result[] = array( 'value' => $post_type, 'label' => $post_type_obj->labels->name );
        }

        return $result;
    }

    public static function get_taxonomies() {
        $result = array();

        foreach( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy => $tax_object ) {
            $result[] = array( 'value' => $taxonomy, 'label' => $tax_object->labels->name );
        }

        return $result;
    }

    public static function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $result = array();
        foreach( array('thumbnail', 'medium', 'large') as $size ) {
            $width = get_option( "{$size}_size_w" );
            $height = get_option( "{$size}_size_h" );

            if ( $width && $height ) {
                $name = ucfirst( $size );
                $result[] = array( 'value' => $size, 'label' => "$name ({$width}x{$height})" );
            }
        }

        if ( $_wp_additional_image_sizes ) {
            foreach( $_wp_additional_image_sizes as $size => $atts ) {
                $name = ucwords( str_replace( array( '-', '_' ), " ", $size ) );
                $result[] = array( 'value' => $size, 'label' => "$name ({$atts['width']}x{$atts['height']})" );
            }
        }

        $result[] = array( 'value' => 'full', 'label' => __( 'Orgininal Size (Full)', 'wp-tiles' ) );

        return $result;
    }

    public static function get_categories() {
        $wp_cat = get_categories( array(
            'hide_empty' => 0,
            'number' => 100 // Limit the max number of categories for very large sites.
        ) );

        $result = array();
        foreach ( $wp_cat as $cat ) {
            $result[] = array( 'value' => $cat->slug, 'label' => $cat->name );
        }
        return $result;
    }

    public static function get_tags() {
        $tags = get_tags( array(
            'hide_empty' => 0,
            'number' => 100
        ) );

        $result = array();
        foreach ( $tags as $tag ) {
            $result[] = array( 'value' => $tag->slug, 'label' => $tag->name );
        }
        return $result;
    }

    /**
     * Get all the users that have published posts
     * @return array
     * @since 1.1
     */
    public static function get_authors() {
        $wp_users = get_users( array(
            'number' => 100, // Limit the max number of users for large sites
            'has_published_posts' => true
        ) );

        $result   = array();
        foreach ($wp_users as $user)
        {
            if( property_exists( $user, 'data' ) ) {
                $user = $user->data;
            }
            $result[] = array('value' => $user->ID, 'label' => $user->display_name);
        }

        return $result;
    }
}
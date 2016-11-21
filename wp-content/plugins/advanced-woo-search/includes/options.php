<?php
/**
 * Array of plugin options
 */

$options = array();

$options['general'][] = array(
    "name"  => __( "Cache results", "aws" ),
    "desc"  => __( "Turn off if you have old data in the search results after content of products was changed.<br><strong>CAUTION:</strong> can dramatically increase search speed", "aws" ),
    "id"    => "cache",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['general'][] = array(
    "name"  => __( "Search in", "aws" ),
    "desc"  => __( "Source of searching. Set the source of searching by drag&drop needed fields to the right area.", "aws" ),
    "id"    => "search_in",
    "value" => "title,content,sku,excerpt",
    "choices" => array( "title", "content", "sku", "excerpt", "category", "tag" ),
    "type"  => "sortable"
);


// Search Form Settings
$options['form'][] = array(
    "name"  => __( "Text for search field", "aws" ),
    "desc"  => __( "Text for search field placeholder.", "aws" ),
    "id"    => "search_field_text",
    "value" => "Search",
    "type"  => "text"
);

$options['form'][] = array(
    "name"  => __( "Minimum number of characters", "aws" ),
    "desc"  => __( "Minimum number of characters required to run ajax search.", "aws" ),
    "id"    => "min_chars",
    "value" => 1,
    "type"  => "number"
);

$options['form'][] = array(
    "name"  => __( "Show loader", "aws" ),
    "desc"  => __( "Show loader animation while searching.", "aws" ),
    "id"    => "show_loader",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);


// Search Results Settings

$options['results'][] = array(
    "name"  => __( "Show image", "aws" ),
    "desc"  => __( "Show product image for each search result.", "aws" ),
    "id"    => "show_image",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show description", "aws" ),
    "desc"  => __( "Show product description for each search result.", "aws" ),
    "id"    => "show_excerpt",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false'  => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Description source", "aws" ),
    "desc"  => __( "From where to take product description.<br>If first source is empty data will be taken from other sources.", "aws" ),
    "id"    => "desc_source",
    "value" => 'content',
    "type"  => "radio",
    'choices' => array(
        'content'  => __( 'Content', 'aws' ),
        'excerpt'  => __( 'Excerpt', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Description length", "aws" ),
    "desc"  => __( "Maximal allowed number of words for product description.", "aws" ),
    "id"    => "excerpt_length",
    "value" => 20,
    "type"  => "number"
);

$options['results'][] = array(
    "name"  => __( "Description content", "aws" ),
    "desc"  => __( "What to show in product description?", "aws" ),
    "id"    => "mark_words",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( "Smart scrapping sentences with searching terms from product description.", "aws" ),
        'false' => __( "First N words of product description ( number of words that you choose below. )", "aws" ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show price", "aws" ),
    "desc"  => __( "Show product price for each search result.", "aws" ),
    "id"    => "show_price",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show categories", "aws" ),
    "desc"  => __( "Include categories in search result.", "aws" ),
    "id"    => "show_cats",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show tags", "aws" ),
    "desc"  => __( "Include tags in search result.", "aws" ),
    "id"    => "show_tags",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show sale badge", "aws" ),
    "desc"  => __( "Show sale badge for products in search results.", "aws" ),
    "id"    => "show_sale",
    "value" => 'true',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Show product SKU", "aws" ),
    "desc"  => __( "Show product SKU in search results.", "aws" ),
    "id"    => "show_sku",
    "value" => 'false',
    "type"  => "radio",
    'choices' => array(
        'true'  => __( 'On', 'aws' ),
        'false' => __( 'Off', 'aws' ),
    )
);

$options['results'][] = array(
    "name"  => __( "Max number of results", "aws" ),
    "desc"  => __( "Maximum number of displayed search results.", "aws" ),
    "id"    => "results_num",
    "value" => 10,
    "type"  => "number"
);
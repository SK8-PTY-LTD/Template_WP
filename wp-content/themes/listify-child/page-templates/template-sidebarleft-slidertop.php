<?php
/**
 * Template Name: Layout: Sidebar Left-Aquatic Activity
 *
 * @package Listify
 */

get_header(); ?>

    <div <?php echo apply_filters( 'listify_cover', 'page-cover', array( 'size' => 'full' ) ); ?>>
        <?php echo do_shortcode('[smartslider3 slider="11"]');?>
    </div>

    <?php do_action( 'listify_page_before' ); ?>

    <div id="primary" class="container">
        <div class="row content-area">
             
             <?php get_sidebar(); ?>

            <main id="main" class="site-main col-md-8 col-sm-7 col-xs-12" role="main">

                <?php if ( listify_has_integration( 'woocommerce' ) ) : ?>
                    <?php wc_print_notices(); ?>
                <?php endif; ?>

                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', 'page' ); ?>

                    <?php comments_template(); ?>
                <?php endwhile; ?>

            </main>


        </div>
    </div>

<?php get_footer(); ?>

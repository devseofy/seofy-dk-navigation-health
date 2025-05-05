<?php
// Custom template for displaying single custom post type

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="page-header">
                <h1 class="page-title"><?php single_cat_title(); ?></h1>
        </header><!-- .page-header -->
        <?php if (have_posts()) : ?>


       
            <?php while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div><!-- .entry-content -->

                </article><!-- #post-<?php the_ID(); ?> -->
         
            <?php endwhile; ?>

            <?php the_posts_navigation(); ?>


        <?php else : ?>

            <?php get_template_part('template-parts/content', 'none'); ?>

        <?php endif; ?>

        <hr> 
        <?php 
            $cat_id = get_query_var('cat');

            //echo $cat_id;
            $category = get_category($cat_id);
            $shortcode_field_value = get_term_meta($cat_id, 'category_shortcode_field', true); // Replace 'custom_field_key' with the actual key of your custom field
            
            echo do_shortcode( $shortcode_field_value);
            ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>







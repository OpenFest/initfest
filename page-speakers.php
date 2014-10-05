<?php
/*
Template Name: Speakers 
 */

get_header(); ?>
<div class="separator"></div>
<section class="content grid">
    <div class="col-left">
        <h1 class="big">Лектори</h1>
    <?php
        $speakers_args = array( 'post_type' => 'speakers' );
        $speakers = new WP_Query( $speakers_args ); 

        if ( $speakers->have_posts() ) :
            while ( $speakers->have_posts() ) : $speakers->the_post();
    ?>
        <div class="speaker">
    <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail();
                } else {
    ?>
            <img src="img/speakers.jpg">
    <?php
                }
    ?>
            <h3><?php the_title(); ?></h3>
    <?php
                the_content();
    ?>
        </div>
    <?php
            endwhile;
            wp_reset_postdata();
        endif;
    ?>
    </div>
    <div class="col-right"><?php get_sidebar(); ?></div>
</section>

<?php get_footer(); ?>

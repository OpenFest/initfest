<?php
/*
Template Name: Sponsors
 */

get_header(); ?>
<section class="content">
    <h1><?php the_title(); ?></h1>
    <div class="separator"></div>
<?php
    if ( have_posts() ) : while ( have_posts() ) : the_post(); 
        the_content();
    endwhile; endif;
?>
</section>
<div class="separator"></div>
<section class="content grid sponsors-item">
<?php
        $sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand' );
        $sponsors = new WP_Query( $sponsors_args ); 
        $sponsor_count = 0;

        if ( $sponsors->have_posts() ) :
            while ( $sponsors->have_posts() ) : $sponsors->the_post();
?>
    <div class="col2 tac">
<?php
	            $custom = get_post_custom();
				if (!empty ($custom['url'])) {
					echo '<a href="'.$custom['url'][0].'" target=_blank alt="'.get_the_title().'">';
				}

                if ( has_post_thumbnail() ) {
                    the_post_thumbnail();
                } else {
                    the_title();
                }

				if (!empty ($custom['url'])) {
					echo '</a>';
				}
                the_excerpt();
?>
    </div>
<?php

                $sponsor_count++; 
                if ($sponsor_count % 2 == 0) {
?>
</section>
<section class="content grid sponsors-item">
<?php
                }
            endwhile;
            wp_reset_postdata();
        endif;
?>

</section>

<?php get_footer(); ?>

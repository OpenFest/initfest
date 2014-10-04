<?php get_header(); ?>
<section id="content" role="main" class="front-page content grid">
    <?php
         /*
            if ( have_posts() ) : while ( have_posts() ) : the_post(); 
            get_template_part( 'entry' );
            comments_template();
            endwhile; endif;
            get_template_part( 'nav', 'below' );
         */
    ?>
    <div class="col2">
        <h1></h1>
        <p></p>
    </div>
    <div class="col2">
        <h3><?php echo __( 'Sponsors' ); ?></h3>
    <?php
            $args = array( 'post_type' => 'sponsors', 'orderby' => 'rand' );
            $the_query = new WP_Query( $args ); 

            if ( $the_query->have_posts() ) :
                while ( $the_query->have_posts() ) : $the_query->the_post();
                    if ( has_post_thumbnail() ) {
    ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
    <?php
                    } else {
    ?>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    <?php
                    }
                endwhile;
                wp_reset_postdata();
            endif;
    ?>
    </div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>

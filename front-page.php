<?php get_header(); ?>
<section class="content grid">
    <div class="col2">
        <h1><?php echo get_bloginfo('name'); ?> </h1>
        <p><?php echo get_bloginfo('description'); ?></p>
    </div>
    <div class="col2">
        <h3>Спонсори</h3>
        <?php echo do_shortcode( '[sponsors]' ); ?>
    </div>
</section>

<div class="separator"></div>

<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="Новини"]' ); ?>
</section>

<div class="separator"></div>

<section class="content">
    <h3>Място: Интерпред, София, България</h3>
    <?php
        $transport_args = array( 'post_type' => 'transportation' );
        $transport = new WP_Query( $transport_args ); 

        if ( $transport->have_posts() ) :
            while ( $transport->have_posts() ) : $transport->the_post();
    ?>
        <p><?php the_title(); ?> <br /> <?php the_content(); ?></p>
    <?php 
            endwhile;
        endif;
    ?>
</section>


<?php echo do_shortcode( '[ready_google_map id="1" map_language="en" type="HYBRID" align="right"]' ); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>

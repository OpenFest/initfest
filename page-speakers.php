<?php
/*
* Template Name: Speakers
*/

get_header(); 

if ( preg_match('/^(schedule|programa|speakers|halls)/', $pagename) ) {
    wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );
}

?>
<div class="separator"></div>
<section class="content grid">
    <div class="col-left">
        <h1 class="big">Лектори</h1>

<div class="grid members">
    <?php
        $speakers_args = array( 'post_type' => 'speakers', 'nopaging' => 'true', 'order' => 'ASC' );
        $speakers = new WP_Query( $speakers_args ); 

        if ( $speakers->have_posts() ) :
            while ( $speakers->have_posts() ) : $speakers->the_post();
    ?>
<div class="member col4">

<a href="#<?php the_title(); ?>">
    <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail(array(100, 100));
                } else {
    ?>
            <img src="/img/speaker.jpg">
    <?php
                }
?>
		<?php the_title(); ?>

</a>
</div>
    <?php
            endwhile;
            wp_reset_postdata();
        endif;
    ?>

</div>
    <?php
        $speakers_args = array( 'post_type' => 'speakers', 'nopaging' => 'true', 'order' => 'ASC' );
        $speakers = new WP_Query( $speakers_args ); 

        if ( $speakers->have_posts() ) :
            while ( $speakers->have_posts() ) : $speakers->the_post();
    ?>
		<div class="speaker" id="<?php the_title() ?>">
    <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail(array(100, 100));
                } else {
    ?>
            <img src="/img/speaker.jpg">
    <?php
                }
    ?>
            <h3><?php the_title(); ?></h3>
		<div class="icons">
    <?php
		$custom = get_post_custom();
		if (!empty($custom['twitter'])) echo '<a href="https://twitter.com/'.$custom['twitter'][0].'"><i class="fa fa-twitter"></i></a>';
		if (!empty($custom['github'])) echo '<a href="https://github.com/'.$custom['github'][0].'"><i class="fa fa-github"></i></a>';
		if (!empty($custom['public_email'])) echo '<a href="mailto:'.$custom['public_email'][0].'"><i class="fa fa-envelope"></i></a>';
?>
		 </div>      

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
    <?php  get_sidebar(); ?>
</section>

<?php get_footer(); ?>

<?php
get_header(); 

/* Some special pages (the ones describing the event, team, etc. need a special submenu */
if ( preg_match('/^(about|ideas-and-recommendations|feedback|team|history|volunteers)/', $pagename) ) {
    wp_nav_menu( array( 'theme_location' => 'subnav-menu', 'container_class' => 'content subnav cf' ) );
    echo '<div class="separator"></div>';
}

if (openfest_home_page()) {
	$coldiv1='';
	$coldiv2='';
} else {
	$coldiv1='<div class="col-left">';
	$coldiv2='</div>';
}

?>
<section class="content grid">
<?php echo $coldiv1; ?>
    <h1><?php the_title();  ?></h1>
<?php
    if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
?>
<?php echo $coldiv2; ?>
    <?php if (!openfest_home_page()) get_sidebar(); ?>
</section>

<?php get_footer(); ?>

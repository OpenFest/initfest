<?php
get_header(); 

if ( $pagename == 'about' ||
     $pagename == 'about-3' ||
     $pagename == 'ideas-and-recommendations' ||
     $pagename == 'feedback' ||
     $pagename == 'team' ||
     $pagename == 'team-2' ||
	 $pagename == 'history' ||
     $pagename == 'history-2') {
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
<?php echo $coldiv1; ?>
    <?php if (!openfest_home_page()) get_sidebar(); ?>
</section>

<?php get_footer(); ?>

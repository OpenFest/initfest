<?php
/* Template Name: Streaming */
get_header();
//wp_nav_menu( array( 'theme_location' => 'stream-menu', 'container_class' => 'content subnav cf' ) );
?>
<br>
<section class="content grid">
<div class="col-left">
<!-- <h1><?php pll_e('Streaming') ?></h1> -->

<?php

if (!empty($_GET['track'])) {
	$track = $_GET['track'];
} else {
	$track = "stage";
}
?>
<div class="videoWrapper">
	<iframe src="/stream/index.php?track=<?php echo htmlspecialchars($track); ?>" allowfullscreen>
		<p>Your browser does not support iframes</p>
	</iframe>
</div>
<div class="videoWrapper">
    <iframe src="https://webirc.ludost.net/?channels=ofq&uio=d4&nick=ofq<?php echo rand(10000,99999)?>" width="647" height="400"></iframe>
</div>
<br>
<?php
if ( have_posts() ) : 
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
endif;
?>

</div>
<?php
	get_sidebar();
?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

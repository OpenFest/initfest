<?php
/* Template Name: Speakers */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

require("schedule-config.php");

?>
<section class="content grid">
<div class="col-left">

<?php

if((int)$year <= 2024) {
?>
<h1><?php pll_e('Лектори') ?></h1>
<?php

if (!empty($content)) {
	echo $content['gspk'];
?>
<div class="separator"></div>
<?php
	echo $content['fspk'];
} else {
	pll_e('TBA');
}
}
#if((int)$year >= 2025) {
else {
	echo '<h1><a href="'. $config['baseUrl'] .'speaker/">'. pll__('Лектори') .'</a></h1>';

	echo '<meta http-equiv="refresh" content="0; url=' . $config['baseUrl'].'speaker/' . '" />';
}
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

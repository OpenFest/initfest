<?php
/* Template Name: Speakers */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

$content = require __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR . 'parse.php';
//var_dump($data);
?>
<section class="content grid">
<div class="col-left">
<h1><?php pll_e('Лектори') ?></h1>

<?php

	foreach ($content['gspk'] as $line) {
		echo $line, PHP_EOL;
	}

?>
<div class="separator"></div>
<?php
	foreach ($content['fspk'] as $line) {
		echo $line, PHP_EOL;
	}
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

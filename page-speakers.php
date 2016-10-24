<?php
/* Template Name: Speakers */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

$requirePath = __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load.php';
require $requirePath . 'parse.php';
$sched_config = getSchedConfig(date('Y'));
$data = loadData($sched_config);

$content = parseData($sched_config, $data);


?>
<section class="content grid">
<div class="col-left">
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
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

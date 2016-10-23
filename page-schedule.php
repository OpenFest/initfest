<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );


$requirePath = __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR;
require $requirePath . 'class.SmartCurl.php';
require $requirePath . 'config.php';
require $requirePath . 'load.php';
require $requirePath . 'parse.php';
$sched_config = getSchedConfig(date('Y'));
$data = loadData($sched_config);

if ( preg_match('/^workshop/', $pagename) ) {
	$sched_config['filterEventType'] = "workshop";
} else {
	$sched_config['filterEventType'] = "lecture";
}
$content = parseData($sched_config, $data);


?>
<section class="content grid">
<div class="col-left">
<h1><?php pll_e('Програма') ?></h1>

<?php
if (!empty($content)) {
	echo $content['schedule'];
?>


   <div class="separator"></div>
   <table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
     <tbody>
<?php
	echo $content['legend'], PHP_EOL;
?>
      </tbody>
    </table>
   <div class="separator"></div>
<?php
	echo $content['fulltalks'];
	echo $content['gspk'];
	echo $content['fspk'];
}

?>
</div>
<?php
	get_sidebar();
?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

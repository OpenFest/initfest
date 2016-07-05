<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

require("schedule-config.php");

$content = require __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR . 'parse.php';
//var_dump($data);
?>
<section class="content grid">
<div class="col-left">
<h1><?php pll_e('Програма') ?></h1>

<?php
if (!empty($content)) { ?>
	<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
		<thead>
			<tr>
				<td>&nbsp;</td>
<?php
				foreach ($content['halls'] as $hall_name) {
?>
				<td><?php echo htmlspecialchars($hall_name[$CF['lang']]); ?></td>
<?php
				}
?>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($content['lines'] as $line) {
			echo str_replace('SPKURL', $CF['speakers_url'], $line), PHP_EOL;
		}
?>
		</tbody>
	</table>
	<div class="separator"></div>
	<table cellpadding="0" cellspacing="0" class="schedule schedule-legend">
	<tbody>
<?php
		foreach ($content['legend'] as $line) {
			echo $line, PHP_EOL;
		}
?>
	</tbody>
	</table>
<?php
	foreach ($content['fulltalks'] as $line) {
		echo str_replace('SPKURL', $CF['speakers_url'], $line), PHP_EOL;
	}
} else {
	pll_e("TBA");
}
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

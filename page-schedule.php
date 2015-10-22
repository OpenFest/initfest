<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

$lang = pll_current_language('slug');


/* TODO make this read the ids from the proper place, as this breaks other years*/
if ( preg_match('/^workshop/', $pagename) ) {
	$workshop = true;
	$allowedhallids = array(9);
} else {
	$workshop = false;
	$allowedhallids = array(6,7,8);
}


/*
 * There is no better way to get where the speakers are
 */

if ('en' === $lang) {
	$s_slug = 'speakers';
} else {
	$s_slug = 'lektori';
}

$args = array(
	'name'        => $s_slug,
	'post_type'   => 'page',
	'numberposts' => 1
);

$url = '';

$my_posts = get_posts($args);
if( $my_posts ) {
	$url = get_permalink( $my_posts[0]->ID );
}

$content = require __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR . 'parse.php';
//var_dump($data);
?>
<section class="content grid">
<div class="col-left">
<h1><?php pll_e('Програма') ?></h1>


	<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
		<thead>
			<tr>
				<td>&nbsp;</td>
<?php
				foreach ($content['halls'] as $hall_name) {
?>
				<td><?php echo htmlspecialchars($hall_name[$lang]); ?></td>
<?php
				}
?>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($content['lines'] as $line) {
			echo str_replace('SPKURL', $url, $line), PHP_EOL;
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
		echo str_replace('SPKURL', $url, $line), PHP_EOL;
	}
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

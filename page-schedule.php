<?php
/* Template Name: Schedule */
get_header();
$content = require __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR . 'parse.php';
//var_dump($data);
?>
<section class="content grid">
<div class="col-left">
	<h1>Програма</h1>


	<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
		<thead>
			<tr>
				<td>&nbsp;</td>
<?php
				foreach ($content['halls'] as $hall_name) {
?>
				<td><?php echo htmlspecialchars($hall_name); ?></td>
<?php
				}
?>
			</tr>
		</thead>
		<tbody>
<?php
		foreach ($content['lines'] as $line) {
			echo $line, PHP_EOL;
		}
?>
		</tbody>
	</table>
	<div class="separator"></div>
<?php
	foreach ($content['fulltalks'] as $line) {
		echo $line, PHP_EOL;
	}
/*
	foreach ($content['gspk'] as $line) {
		echo $line, PHP_EOL;
	}

	foreach ($content['fspk'] as $line) {
		echo $line, PHP_EOL;
	}*/
?>
	</div>
	<?php  get_sidebar(); ?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

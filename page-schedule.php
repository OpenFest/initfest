<?php
/* Template Name: Schedule */
get_header();
$content = require __DIR__ . DIRECTORY_SEPARATOR . 'schedule' . DIRECTORY_SEPARATOR . 'parse.php';
//var_dump($data);
?>
<section class="content">
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

	foreach ($content['gspk'] as $line) {
		echo $line, PHP_EOL;
	}

	foreach ($content['fspk'] as $line) {
		echo $line, PHP_EOL;
	}
?>
	<div class="separator"></div>
	<div class="col-right sponsors sponsors-frontpage">
	<?php echo do_shortcode( '[sponsors]' ); ?>
	<?php echo do_shortcode( '[partners]' ); ?>
	</div>
	<div class="separator"></div>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

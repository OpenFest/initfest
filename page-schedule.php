<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

require("schedule-config.php");

?>
<section class="content grid">
<?php
function should_show_sidebar() {
	global $year, $pagename;

	if ($year === '2021') {
		return true;
	}

	if (preg_match('/^full/', $pagename)) {
		return false;
	}

	if ($year === '2024' && preg_match('/^workshop/', $pagename)) {
		return false;
	}

	return true;
}
// full schedule is not limited in only one column
if (should_show_sidebar()) {
	echo '<div class="col-left">';
}
?>
<h1><?php pll_e('Програма') ?></h1>


<?php

if($year !== '2025') { # we are using clarion
	if (!empty($content)) {
		echo '<p><a href="https://oldcfp.openfest.org/api/conferences/'.$sched_config['conferenceId'].'/events.ics?locale='.$lang.'">iCalendar</a></p>';
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
	} else {
		echo "TBA";
	}
?>
<?php
} else { # we are using pretalx
	# TODO: actually move this to another file
	if (!empty($result)) {
		# ical export is broken in pretalx for some reason
		# echo '<p><a href="'.$sched_config['baseUrl'].'schedule/export/schedule.ics?lang='.$lang.'">iCalendar</a></p>';
?>


<?php foreach($result as $content) { ?>
<h1><?php echo $content['date']; ?></h1>
   <div class="separator"></div>
<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
	<thead>
		<tr>
		<th></th>
<?php foreach($content['rooms'] as $room) {
echo '<th>' . $room . '</th>';
	}
?>
		</tr>
	</thead>
	<tbody>
<?php foreach($content['slots'] as $slot => $rooms) {
echo '<tr>';

$end = nextTimepoint($content['timepoints'], $slot);

if(--$span < 1)  {
	$span = diffSpan($slot, $end, $content['slotLength']);
	echo '<td class="schedule-card" rowspan="' . $span . '">' . $slot . ' - ' . $end . '</td>';
}

for($i = 0; $i < count($content['rooms']); $i++) {
	if(!array_key_exists($i, $rooms)) continue;
	$talk = $rooms[$i];

	if(empty($talk)) {
		echo '<td></td>';
		continue;
	}

	$persons = [];
	foreach($talk['talk']['persons'] as $person) {
		$persons[] = '<a class="vt-p" href="' . $config['baseUrl'] . 'speaker/' . $person['code'] . '/">' . htmlspecialchars($person['public_name']) . '</a>';
	}
	$speakers = implode(', ', $persons);

	$title = mb_substr($talk['talk']['title'], 0, $config['cut_len']) . (mb_strlen($talk['talk']['title']) > $config['cut_len'] ? '...' : '');

	echo '<td class="schedule-card schedule-' . $talk['talk']['language'] . '" rowspan="'. $talk['span'] .'" style="background-color: ' . getTrackColor($talk['talk']['track'], $content['colors']). '">';
	echo '<a href="' . htmlspecialchars($talk['talk']['url']) . '">' . $title . '</a>';
	echo '<br/>' . $speakers;
	if(time() >= strtotime($talk['talk']['date'])) { // talk has already started
		echo '<p><strong><a href="' . $talk['talk']['url'] . 'feedback' . '">'.pll__('Submit feedback').'</a></strong></p>';
	}
	echo '</td>';

}
echo '</tr>';
	}
?>
	</tbody>
</table>

<?php
	}
?>

<div class="separator"></div>
<table cellpadding="0" cellspacing="0" style="text-align: center;" class="schedule">
	<tbody>
<?php
$colors = array_unique(array_merge(array_map(function($content) { return $content['colors']; }, $result)))[0];
$tracks = array_unique(array_merge(array_map(function($content) { return $content['tracks']; }, $result)))[0];

foreach($tracks as $track) {
	echo '<tr><td style="background-color: ' . getTrackColor($track, $colors). '">' . $track . '</td></tr>';
}
?>
	</tbody>
</table>

<?php
	} else {
		echo "TBA";
	}
?>
<?php echo $coldiv1; ?>
<?php
	if ( have_posts() ) {
		while ( have_posts() ) the_post();
		the_content();
	}
	else echo "TBA";
}
?>
<?php
if (should_show_sidebar()) {
	echo "</div>";
	get_sidebar();
};
?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

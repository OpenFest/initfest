<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

require("schedule-config.php");

?>
<section class="content grid">
<?php
// full schedule is not limited in only one column
if (!preg_match('/^full/', $pagename)) {
	echo '<div class="col-left">';
}
?>
<h1><?php pll_e('Програма') ?></h1>


<?php
if (!empty($content)) {
    echo '<p> <a href="https://cfp.openfest.org/api/conferences/'.$sched_config['conferenceId'].'/events.ics?locale='.$lang.'">iCalendar</a> or <a href="https://calendar.google.com/calendar?cid=b2s3bW9sOWpyczNjODVjbjRrZ3JpYmY5ODRAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ">Google calendar</a></p>';
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
if (!preg_match('/^full/', $pagename)) {
	echo "</div>";
	get_sidebar();
};
?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

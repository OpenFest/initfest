<?php
/* Template Name: Schedule */
get_header();
wp_nav_menu( array( 'theme_location' => 'footer-schedule', 'container_class' => 'content subnav cf' ) );

require("schedule-config.php");

?>
<section class="content grid">
<?php
function should_show_sidebar() {
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
if (!empty($content)) {
    echo '<p><a href="https://cfp.openfest.org/api/conferences/'.$sched_config['conferenceId'].'/events.ics?locale='.$lang.'">iCalendar</a></p>';
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
if (should_show_sidebar()) {
	echo "</div>";
	get_sidebar();
};
?>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

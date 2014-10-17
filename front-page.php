<?php get_header(); ?>
<div class="content front-page-big-title"><h1>OpenFest e единствената по рода си в България конференция, посветена на свободната култура, свободния софтуер и софтуера с отворен код, свободното споделяне на знания – фестивал на свободното творчество. OpenFest е ежегодна среща на всички почитатели, създатели, поддръжници и нови фенове на свободните изкуства и свободния софтуер.</h1></div>
<div class="separator"></div>
<div class="col-right sponsors">
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <?php echo do_shortcode( '[partners]' ); ?>
</div>
<div class="separator"></div>
<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
</section>

<div class="separator"></div>


<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

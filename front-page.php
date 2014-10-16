<?php get_header(); ?>
<section class="content grid">
    <div class="col-left">
	<section class="content">
		<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
	</section>
    </div>
    <div class="col-right sponsors">
        <?php echo do_shortcode( '[sponsors]' ); ?>
        <?php echo do_shortcode( '[partners]' ); ?>
    </div>

</section>

<div class="separator"></div>


<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

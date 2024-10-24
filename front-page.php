<?php get_header(); ?>

<style>
	.ofhr-text-center {
		text-align: center;
	}
</style>

<section class="content subtitle_content">
<?php
e_('about_event');
?>
</section>
<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
<div class="separator"></div>
<div class="col-right sponsors sponsors-frontpage">
    <?php echo do_shortcode( '[sponsors]' ); ?>
    <?php echo do_shortcode( '[partners]' ); ?>
</div>
<div class="separator"></div>
</section>

<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

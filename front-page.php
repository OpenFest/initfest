<?php get_header(); ?>
<section class="content grid">
    <div class="col2 tagline">
        <h1><?php echo get_bloginfo('name'); ?> </h1>
        <p><?php echo get_bloginfo('description'); ?></p>
    </div>
    <div class="col2 sponsors">
        <?php echo do_shortcode( '[sponsors]' ); ?>
        <?php echo do_shortcode( '[partners]' ); ?>
    </div>

</section>

<div class="separator"></div>

<section class="content">
	<?php echo do_shortcode( '[sh-latest-posts cat="news" label="'.pll__('Новини').'"]' ); ?>
</section>

<div class="separator"></div>


<?php echo do_shortcode( '[transport]' ); ?>

<?php get_footer(); ?>

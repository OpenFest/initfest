<?php get_header(); ?>
<section id="content" role="main" class="content grid">
    <div class="col-left">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php get_template_part( 'entry' ); ?>
        <?php endwhile; endif; ?>
        <?php get_template_part( 'nav', 'below' ); ?>
    </div>
    <?php get_sidebar(); ?>
</section>
<?php get_footer(); ?>

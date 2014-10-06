<?php
get_header(); ?>
<section class="content grid">
    <div class="col-left">
    <h1><?php the_title(); ?></h1>
<?php
    if ( have_posts() ) : 
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    endif;
?>
    </div>
    <?php get_sidebar(); ?>
</section>

<?php get_footer(); ?>

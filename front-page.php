<?php get_header(); ?>
<section class="content grid">
	<?php
		 /*
			if ( have_posts() ) : while ( have_posts() ) : the_post(); 
			get_template_part( 'entry' );
			comments_template();
			endwhile; endif;
			get_template_part( 'nav', 'below' );
		 */
	?>
    <div class="col2">
        <h1><?php echo get_bloginfo('name'); ?> </h1>
        <p><?php echo get_bloginfo('description'); ?></p>
    </div>
    <div class="col2">
        <h3>Спонсори</h3>
    <?php
            $sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand' );
            $sponsors = new WP_Query( $sponsors_args ); 

            if ( $sponsors->have_posts() ) :
                while ( $sponsors->have_posts() ) : $sponsors->the_post();
                    if ( has_post_thumbnail() ) {
    ?>
        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
    <?php
                    } else {
    ?>
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    <?php
                    }
                endwhile;
            endif;
    ?>
    </div>
</section>

<div class="separator"></div>

<section class="content">
    <h3>Новини | <small><a href="">виж всички новини</a></small></h3>
    <div class="grid">
    <?php
        $news_args = array( 'cat' => 5, posts_per_page => 3  );
        $news = new WP_Query( $news_args ); 

        if ( $news->have_posts() ) :
            while ( $news->have_posts() ) : $news->the_post();
    ?>
        <div class="col3">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <p class="info">От <?php the_author(); ?> | Публикувано на <?php the_date(); ?> </p>
            <?php the_excerpt(); ?>
        <a class="button" href="<?php the_permalink(); ?>">виж цялата новина</a>
        </div>
    <?php 
            endwhile;
        endif;
    ?>
    </div>
</section>

<div class="separator"></div>

<section class="content grid">
    <h3>Място: Интерпред, София, България</h3>
    <?php
        $transport_args = array( 'post_type' => 'transportation' );
        $transport = new WP_Query( $transport_args ); 

        if ( $transport->have_posts() ) :
            while ( $transport->have_posts() ) : $transport->the_post();
    ?>
        <p><?php the_title(); ?> <br /> <?php the_content(); ?></p>
    <?php 
            endwhile;
        endif;
    ?>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<?php

# Add support for thumbnais 
add_theme_support( 'post-thumbnails' );


register_nav_menus(
	array( 'main-menu' => __( 'Main Menu', 'initfest' ),
           'subnav-menu' => __( 'Sub Navigation', 'initfest'),
		   'footer-openfest' => __('OpenFest', 'initfest'),
		   'footer-openfest' => __('OpenFest', 'initfest'),
		   'footer-schedule' => __('Schedule', 'initfest'),
		   'footer-others' => __('Others', 'initfest'),
		   'footer-followus' => __('Follow us in:', 'initfest') )
);


# Register all shortcodes
function register_shortcodes(){
    add_shortcode('sh-latest-posts', 'sh_latest_posts');
    add_shortcode('sponsors', 'sponsors_shortcode');
    add_shortcode('transport', 'transport_shortcode');
}

add_action( 'init', 'register_shortcodes');


function sh_latest_posts($atts){
	$atts = shortcode_atts( array(
		  'cat' => 'news',
		  'label' => __('News', 'initfest')
	  ), $atts );
	
	$result = '<section class="content"><h3>'.$atts['label'].' | <small><a href="'.esc_url(get_term_link($atts['cat'], 'category')).'">'.__('see all', 'initfest').'</a></small></h3><div class="grid">';
	
	
	$news_args = array( 'catecory_name' => $cat, 'numberposts' => 3  );
	$news = new WP_Query( $news_args ); 

	ob_start();

	if ( $news->have_posts() ) :
		while ( $news->have_posts() ) : $news->the_post();
?>
        <div class="col3">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<p class="info"><?php pll_e('От');?> <?php the_author(); ?> | <?php pll_e('Публикувано на');?> <?php the_date(); ?> </p>
            <?php the_excerpt(); ?>
	<a class="button" href="<?php the_permalink(); ?>"><?php pll_e('виж цялата новина');?></a>
        </div>
<?php 
		endwhile;
	endif;

	$result .= ob_get_contents();
	$result .='</div></section>';
	ob_end_clean();
	
	return $result;
	
}


# Create shortcode for sponsors
function sponsors_shortcode() {
    $result= '<h3>'.pll__('Спонсори').'</h3>';

    
    $sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand' );
    $sponsors = new WP_Query( $sponsors_args ); 

	ob_start();

    if ( $sponsors->have_posts() ) :
        while ( $sponsors->have_posts() ) : $sponsors->the_post();
            if ( has_post_thumbnail() ) {
                the_post_thumbnail();
            } else {
                get_the_title();
            }
        endwhile;
    endif;

	$result .= ob_get_contents();
	ob_end_clean();

    return $result;
}


# Create shortcode for transport methods 
function transport_shortcode() {
    $result= '<section class="content"><h3>'.pll__('Място').': '.pll__('Интерпред, София, България').'</h3>';

    $transport_args = array( 'post_type' => 'transportation' );
    $transport = new WP_Query( $transport_args ); 

	ob_start();

    if ( $transport->have_posts() ) :
        while ( $transport->have_posts() ) : $transport->the_post();
?>
    <h4><?php the_title(); ?></h4>
    <p><?php the_content(); ?></p>
<?php 
        endwhile;
    endif;
?>
</section>
<?php
    echo do_shortcode( '[ready_google_map id="1" map_language="en" type="HYBRID" align="right"]' ); 

    $result .= ob_get_contents();
	ob_end_clean();

    return $result;
}


# Create a custom post type for Sponsors
function create_sponsors_posttype() {

	$labels = array(
		'name' => __( 'Sponsors' ),
		'singular_name' => __( 'Sponsor' ),
		'menu_name' => __( 'Sponsors'),
		'all_items' => __( 'All Sponsors' ),
		'view_item' => __( 'View Sponsor' ),
		'add_new_item' => __( 'Add New Sponsor' ),
		'add_new' => __( 'Add New' ),
		'edit_item' => __( 'Edit Sponsor' ),
		'update_item' => __( 'Update Sponsor' ),
		'search_item' => __( 'Search Sponsor' ),
		'not_found' => __( 'Not Found' ),
		'not_found_in_trash' => __( 'Not Found In Trash' ),
	);

	$args =  array(
		'label' => __( 'sponsors' ),
		'description' => __( 'Sponsors of OpenFest' ),
		'labels' => $labels,
		'supports' => array( 'title', 'excerpt', 'thumbnail', 'custom-fields', ),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 5,
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);

	register_post_type( 'sponsors', $args );
}

add_action( 'init', create_sponsors_posttype );


# Create a custom post type for Speakers 
function create_speakers_posttype() {

	$labels = array(
		'name' => __( 'Speakers' ),
		'singular_name' => __( 'Speaker' ),
		'menu_name' => __( 'Speakers'),
		'all_items' => __( 'All Speakers' ),
		'view_item' => __( 'View Speaker' ),
		'add_new_item' => __( 'Add New Speaker' ),
		'add_new' => __( 'Add New' ),
		'edit_item' => __( 'Edit Speaker' ),
		'update_item' => __( 'Update Speaker' ),
		'search_item' => __( 'Search Speaker' ),
		'not_found' => __( 'Not Found' ),
		'not_found_in_trash' => __( 'Not Found In Trash' ),
	);

	$args =  array(
		'label' => __( 'speakers' ),
		'description' => __( 'Speakers on OpenFest' ),
		'labels' => $labels,
		'supports' => array( 'title', 'excerpt', 'thumbnail', 'custom-fields', ),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_in_admin_bar' => true,
		'menu_position' => 6,
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);

	register_post_type( 'speakers', $args );
}

add_action( 'init', create_speakers_posttype );


# Create a custom post type for Tranportation 
function transportation_posttype() {

        register_post_type( 'transportation',
            array(
                'labels' => array(
                'name' => __( 'Tranportation' ),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'transportation'),
        )
    );
}

function openfest_home_page() {
	return !($wp->query_vars['pagename']=='home' || $wp->query_vars['pagename']=='home-2');
}

add_action( 'init', 'transportation_posttype' );
pll_register_string('Schedule','Програма');
pll_register_string('Others','Други');
pll_register_string('follow','Последвайте ни в:');
pll_register_string('venue','Интерпред, София, България');
pll_register_string('venue_w','Място');
pll_register_string('sponsors_w','Спонсори');
pll_register_string('time','1-ви и 2-ри ноември 2014 г.');
pll_register_string('publishedon','Публикувано на');
pll_register_string('by_w','От');
pll_register_string('see_whole_news','виж цялата новина');
pll_register_string('news','Новини');



	?>

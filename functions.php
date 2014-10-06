<?php

# Add support for thumbnais 
add_theme_support( 'post-thumbnails' );


register_nav_menus(
	array( 'main-menu' => __( 'Main Menu', 'initfest' ),
		   'footer-openfest' => __('OpenFest', 'initfest'),
		   'footer-openfest' => __('OpenFest', 'initfest'),
		   'footer-schedule' => __('Schedule', 'initfest'),
		   'footer-others' => __('Others', 'initfest'),
		   'footer-followus' => __('Follow us in:', 'initfest') )
);

add_shortcode('sh-latest-posts', 'sh_latest_posts');

function sh_latest_posts($atts){
	$atts = shortcode_atts( array(
		  'cat' => 'news',
		  'label' => __('News', 'initfest')
	  ), $atts );
	
	$result = '<section class="content"><h3>'.$atts['label'].' | <small><a href="'.esc_url(get_term_link($atts['cat'], 'category')).'">'.__('see all', 'initfest').'</a></small></h3><div class="grid">';
	
	ob_start();
	
	$news_args = array( 'catecory_name' => $cat, 'numberposts' => 3  );
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

	$result .= ob_get_contents();
	$result .='</div></section>';
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

add_action( 'init', 'transportation_posttype' );


# Create shortcode for sponsors
function sponsors_shortcode() {
    $output = '';

    $sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand' );
    $sponsors = new WP_Query( $sponsors_args ); 

    if ( $sponsors->have_posts() ) :
        while ( $sponsors->have_posts() ) : $sponsors->the_post();
            if ( has_post_thumbnail() ) {
                $output .= the_post_thumbnail();
            } else {
                $output .= get_the_title();
            }
        endwhile;
    endif;

    return $output;

}


# Register all shortcodes
function register_shortcodes(){
   add_shortcode('sponsors', 'sponsors_shortcode');
}

add_action( 'init', 'register_shortcodes');


?>

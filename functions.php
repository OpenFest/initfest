<?php

# Add support for thumbnais
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );

add_filter( 'the_excerpt', 'shortcode_unautop');
add_filter( 'the_excerpt', 'do_shortcode');

function get_blog_slug() {
    if (!is_multisite()) {
        // local development, non-multisite
        return date('Y');
    }

    $blog_details = get_blog_details();
    return trim($blog_details->path, '/');
}

// OpenGraph image for FB/Twitter
function og_image( $tags ) {
    $blog_slug = get_blog_slug();

    $imagePath = __DIR__ . '/img/fb_preview_' . $blog_slug . '.jpg';

    if (file_exists($imagePath)) {
        $imageUrl = esc_url('https://www.openfest.org/wp-content/themes/initfest/img/' . basename($imagePath));
        list($width, $height) = getimagesize($imagePath);

        $tags['og:image'] = $imageUrl;
        $tags['og:image:width'] = $width;
        $tags['og:image:height'] = $height;
        $tags['twitter:image'] = $imageUrl;
    }

	return $tags;
}
add_filter( 'jetpack_open_graph_tags', 'og_image' );


register_nav_menus(
	array( 'main-menu' => __( 'Main Menu', 'initfest' ),
		   'subnav-menu' => __( 'Sub Navigation', 'initfest'),
		   'stream-menu' => __('Stream Menu', 'initfest'),
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
    add_shortcode('partners', 'partners_shortcode');
    add_shortcode('transport', 'transport_shortcode');
    add_shortcode('stream-player', 'stream_player_shortcode');
}

add_action( 'init', 'register_shortcodes');


function sh_latest_posts($atts){
	if (of_get_lang()=='bg')
		$ncat = "news-bg";
	else
		$ncat = "news";
	$atts = array(
		  'cat' => $ncat,
		  'label' => __('News', 'initfest')
	  );

	$result = '<section class="content"><h3>'.$atts['label'].' | <small><a href="'.esc_url(get_term_link($atts['cat'], 'category')).'">'.__('see all', 'initfest').'</a></small></h3><div class="grid">';


	$news_args = array( 'category_name' => 'news,news-bg', 'numberposts' => 6, 'lang' => of_get_lang() );
	$news = new WP_Query( $news_args );

	ob_start();
	if ( $news->have_posts() ) :
		$i=0;
		while ( $news->have_posts() && $i++<6) : $news->the_post();
?>
        <div class="col3">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <?php the_excerpt(); ?>
	<a class="button" href="<?php the_permalink(); ?>"><?php pll_e('виж цялата новина');?></a>
        </div>
<?php
#		if ($i==3)
			echo '</div></section><section class="content"><div class="grid">';
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


    $sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand', 'nopaging' => 'true');
    $sponsors = new WP_Query( $sponsors_args );

	ob_start();

	if ( $sponsors->have_posts() ) :
		while ( $sponsors->have_posts() ) : $sponsors->the_post();
			$custom = get_post_custom();
			if (empty($custom['partner'])) {
				if (!empty ($custom['url'])) {
					echo '<a href="'.$custom['url'][0].'" target=_blank alt="'.get_the_title().'">';
				}
				if ( has_post_thumbnail() ) {
					the_post_thumbnail();
				} else {
					get_the_title();
				}
				if (!empty ($custom['url'])) {
					echo '</a>';
				}
			}
		endwhile;
	endif;

	$result .= ob_get_contents();
	ob_end_clean();

	return $result;
}

# Create shortcode for partners
function partners_shortcode() {
	$result= '<h3>'.pll__('Партньори').'</h3>';


	$sponsors_args = array( 'post_type' => 'sponsors', 'orderby' => 'rand', 'nopaging' => 'true' );
	$sponsors = new WP_Query( $sponsors_args );

	ob_start();

	if ( $sponsors->have_posts() ) :
		while ( $sponsors->have_posts() ) : $sponsors->the_post();
			$custom = get_post_custom();
			if (!empty($custom['partner'])) {
				if (!empty ($custom['url'])) {
					echo '<a href="'.$custom['url'][0].'" target=_blank alt="'.get_the_title().'">';
				}
				if ( has_post_thumbnail() ) {
					the_post_thumbnail();
				} else {
					get_the_title();
				}
				if (!empty ($custom['url'])) {
						echo '</a>';
				}
			}
		endwhile;
	endif;

	$result .= ob_get_contents();
	ob_end_clean();

    return $result;
}


# Create shortcode for transport methods
function transport_shortcode() {
    $result= '<section class="content"><h3>'.pll__('Място').': '.pll__('VENUE_LOCATION').'</h3>';

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
    //echo do_shortcode( '[ready_google_map id="2" map_language="en" align="center"]' );

    $result .= ob_get_contents();
	ob_end_clean();

    return $result;
}


function stream_player_shortcode($params = []) {
    wp_enqueue_style('video.js', 'https://unpkg.com/video.js/dist/video-js.css');
    wp_enqueue_script('video.js', 'https://unpkg.com/video.js/dist/video.min.js');

    $videoJsConfig = [
        'fluid' => true,
    ];

    $params = array_merge([
        'host' => 'stream.openfest.org',
        'track' => 'hall-a',
    ], $params);

    $urlPrefix = 'https://' . $params['host'] . '/';

    ob_start();
?>
<video class="video-js vjs-fill" controls data-setup="<?php echo htmlspecialchars(json_encode($videoJsConfig)); ?>">
    <source type="application/x-mpegURL" src="<?php echo $urlPrefix, 'hls/', $params['track']; ?>.m3u8"></source>
    <source type="application/dash+xml" src="<?php echo $urlPrefix, 'dash/', $params['track']; ?>.mpd"></source>
</video>
<?php

    return ob_get_clean();
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

add_action( 'init', 'create_sponsors_posttype' );


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

add_action( 'init', 'create_speakers_posttype' );


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
	if (empty($wp))  {return is_front_page();} ;
	return !($wp->query_vars['pagename']=='home' || $wp->query_vars['pagename']=='home-2');
}

function of_get_lang() {
	if (function_exists("pll_current_language"))
		return pll_current_language('slug');
	else
		return 'bg';
}

function e_($word) {
	if (function_exists("pll_e"))
		return pll_e($word);
	else
		echo $word;
}

function pn_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $attachment_url )
		return;

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

	}

	return $attachment_id;
}
add_action( 'init', 'transportation_posttype' );

if (function_exists("pll_register_string")) {
	pll_register_string('Schedule','Програма');
	pll_register_string('Others','Други');
	pll_register_string('follow','Последвайте ни в:');
	pll_register_string('venue','VENUE_LOCATION');
	pll_register_string('venue_w','Място');
	pll_register_string('sponsors_w','Спонсори');
	pll_register_string('partners_w','Партньори');
	pll_register_string('speakers_w','Лектори');
	pll_register_string('time','EVENT_TIME');
	pll_register_string('publishedon','Публикувано на');
	pll_register_string('by_w','От');
	pll_register_string('see_whole_news','виж цялата новина');
	pll_register_string('news','Новини');
	pll_register_string('share_freedom','Да споделим свободата');
	pll_register_string('hall1','София');
	pll_register_string('hall2','Варна');
	pll_register_string('hall3','G1');
	pll_register_string('hall4','Пловдив');
	pll_register_string('hall5','Бургас');
	pll_register_string('bulgaria_hall','BULGARIA_HALL');
	pll_register_string('chamber_hall','CHAMBER_HALL');
	pll_register_string('music_hall','MUSIC_HALL');
	pll_register_string('low_quality','LOW_QUALITY');
	pll_register_string('normal_quality','NORMAL_QUALITY');
	pll_register_string('high_quality','HIGH_QUALITY');
	pll_register_string('all_streams','ALL_STREAMS');
	pll_register_string('tba','TBA');
	pll_register_string('feedback','Submit feedback');
	pll_register_string('about_event','about_event');
	pll_register_string('countdown_text_before','countdown_text_before');
	pll_register_string('countdown_text_after','countdown_text_after');
	pll_register_string('countdown_days','countdown_days');
	pll_register_string('countdown_hours','countdown_hours');
	pll_register_string('countdown_minutes','countdown_minutes');
	pll_register_string('after_event','after_event');
	pll_register_string('no_current_streams','no_current_streams');
}

add_filter( 'wp_title', 'wpdocs_hack_wp_title_for_home' );

/**
 * Customize the title for the home page, if one is not set.
 *
 * @param string $title The original title.
 * @return string The title to use.
 */
function wpdocs_hack_wp_title_for_home( $title )
{
  if ( empty( $title ) && ( is_home() || is_front_page() ) ) {
	$title = get_bloginfo('name') . ' | ' . get_bloginfo( 'description' );
  } else {
	$title = $title . get_bloginfo( 'tagline' );
  }
  return $title;
}

<?php
/* basic config for all conferences
 * needs to be included from a WP page, otherwise won't work properly
 * some of this stuff would need to be moved to be taken from Clarion in the future
 */
$CF = array();

$CF['lang']  = pll_current_language('slug');


$hall_defs = array( '2014' => array('lectures' => array(1, 2, 3), 'workshops' => array(4, 5), 'all' => array(1, 2, 3, 4, 5) ),
	'2015' => array('lectures' => array(6, 7, 8), 'workshops' => array(9), 'all' => array(6, 7, 8, 9) )
);

/* clarion conference ids */
$confids = array('2014' => 1, '2015' => 2);


/* get stuff from WP and parse */
$siteurl = get_option('siteurl');
$year = preg_replace('%.*/([0-9]*)$%', '\1', $siteurl);

$CF['confid'] = $confids[$year];

/* TODO make this read the ids from the proper place, as this breaks other years*/
if ( preg_match('/^workshop/', $pagename) ) {
	$CF['allowedhallids'] = $hall_defs[$year]['workshops'];
} else if (preg_match('/^(speakers|lektori)/', $pagename) ) {
	$CF['allowedhallids'] = $hall_defs[$year]['all'];
} else {
	$CF['allowedhallids'] = $hall_defs[$year]['lectures'];
}

/*
 * There is no better way to get where the speakers are
 */

if ('en' === $lang) {
	$CF['s_slug'] = 'speakers';
} else {
	$CF['s_slug'] = 'lektori';
}

$args = array(
	'name'        => $CF['s_slug'],
	'post_type'   => 'page',
	'numberposts' => 1
);

$speakers_url = '';

$my_posts = get_posts($args);
if( $my_posts ) {
	$CF['speakers_url'] = get_permalink( $my_posts[0]->ID );
}


?>

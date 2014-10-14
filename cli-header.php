<?php
    if( php_sapi_name() !== 'cli' ) {
        die("Meant to be run from command line");
    }

    function find_wordpress_base_path() {
        $dir = dirname(__FILE__);
        do {
            //it is possible to check for other files here
            if( file_exists($dir."/wp-config.php") ) {
                return $dir;
            }
        } while( $dir = realpath("$dir/..") );
        return null;
    }



    define( 'BASE_PATH', find_wordpress_base_path()."/" );
	define('WP_USE_THEMES', false);
	global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header, $pgconn;
	require(BASE_PATH . '/wp-content/themes/initfest/pgconn.php');
	$_SERVER['HTTP_HOST'] = "www.openfest.org";
    require(BASE_PATH . 'wp-load.php');
	require_once(BASE_PATH . 'wp-admin/includes/media.php');
	require_once(BASE_PATH . 'wp-admin/includes/file.php');
	require_once(BASE_PATH . 'wp-admin/includes/image.php');

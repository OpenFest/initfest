<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="imagetoolbar" content="no" />
		<meta name="author" content="OpenFest" />
		<meta name="copyright" content="OpenFest" />
		<meta name="robots" content="follow,index" />
		<meta name="title" content="" />
		<meta name="keywords" content="" lang="en-us" />
		<meta name="description" content="" />

		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!--<link rel="icon" type="image/png" href="">-->
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />

		<title><?php is_front_page() ? (bloginfo('name').e_(' | ').e_('Да споделим свободата')) : wp_title( ' | ', true, 'right' ); ?></title>

		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
		<?php wp_head(); ?>

	</head>
	<body>
<?php
	$blog_details = get_blog_details();
	$blog_slug = str_replace('/', '', $blog_details->path);

	if ($blog_slug == '2019' || $blog_slug == '2020')  {
		echo '<nav style="background: url(\''.get_template_directory_uri().'/img/navbg-'.$blog_slug.'.png\'); height: 84px">';
	} else {
		echo "<nav>";
	}
?>
			<div class="content cf">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo"><img src="<?php echo get_template_directory_uri().'/img/logo-'.$blog_slug.'.png'; ?>" alt="OpenFest" /></a>

				<?php wp_nav_menu( array('theme_location' => 'main-menu') ); ?>
			</div>
		</nav>
		<?php
			if(openfest_home_page()){
				if ($blog_slug == '2019') {
					echo '<section class="banner cf" style="background: url(\''.get_template_directory_uri().'/img/banner-back-'.$blog_slug.'.jpg\') top repeat-x; background-size:cover; position:relative; padding: 0.2em 0 0 0;height: 400px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-'. $blog_slug .'.png" alt="" style="position:absolute;top:0;left:50%;margin-left:-550px;height:100%;width:430px" /></section>';
					#echo '<section class="banner cf" style="background: url(\''.get_template_directory_uri().'/img/banner-back-'.$blog_slug.'.jpg\') top repeat-x;padding: 0.2em 0 0 0;height: 400px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-'. $blog_slug .'.png" alt="" style="padding-right: 35%; margin-top: -0.2em;;height:400px" /></section>';
                } else if ($blog_slug == "2021") {
					//echo '<section class="banner cf" style="background: url(\''.get_template_directory_uri().'/img/banner-back-'.$blog_slug.'.jpg\') top repeat-x; background-size:cover; position:relative; padding: 0.2em 0 0 0;height: 400px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-'. $blog_slug .'.png" alt="" style="position:absolute;top:0;left:50%;height:100%;width:608px" /></section>';
					//if (of_get_lang() == "en") {
					//	echo '<section class="banner cf" style="background: url(\''.get_template_directory_uri().'/img/banner-back-'.$blog_slug.'.jpg\') top repeat-x; background-size:cover; position:relative; padding: 0.2em 0 0 0;height: 400px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-'. $blog_slug .'.png" alt="" style="position:absolute;top:0;left:50%;height:100%;width:608px" /></section>';
					//} else {
					//	echo '<section class="banner cf content" style="top: repeat-x; background-size:cover; position:relative; padding: 0.2em 0 0 0;height: 400px; background: none">';
					//	echo '<img src="'.get_template_directory_uri().'/img/banner-back-2021.svg" alt="" style="position:absolute;top:0;right:65%;height:100%;">';
					//	echo '<img src="'.get_template_directory_uri().'/img/banner-bg-2021.svg" alt="" style="position:absolute;top:0;left:50%;height:100%;width:608px">';
					//	echo '</section>';
					//}

					echo '<section class="banner cf content" style="top: repeat-x; background-size:cover; position:relative; padding: 0.2em 0 0 0;height: 400px; background: none">';
					echo '<img src="'.get_template_directory_uri().'/img/banner-back-2021.svg" alt="" style="position:absolute;top:0;right:65%;height:100%;">';
					if (of_get_lang() == "bg") {
						echo '<img src="'.get_template_directory_uri().'/img/banner-bg-2021.svg" alt="" style="position:absolute;top:0;left:50%;height:100%;width:608px">';
					} else {
						echo '<img src="'.get_template_directory_uri().'/img/banner-en-2021.png" alt="" style="position:absolute;top:0;left:50%;height:100%;width:608px">';
					}
					echo '</section>';


                } else if ($blog_slug == '2023') {
					echo '<section class="banner cf" style="background: url('.get_template_directory_uri().'/img/banner-back-2023.png) top center no-repeat;padding: 0.2em 0 0 0;height: 258px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-2023.png" alt="" /></section>';

                } else {
					echo '<section class="banner cf" style="background: url(\''.get_template_directory_uri().'/img/banner-back-'.$blog_slug.'.jpg\') top repeat-x;padding: 0.2em 0 0 0;height: 258px;"><img src="'.get_template_directory_uri().'/img/banner-'.of_get_lang().'-'. $blog_slug .'.png" alt="" /></section>';
				}
			}
		?>

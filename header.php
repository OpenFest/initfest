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
        <link rel="icon" type="image/png" href="">
        
		<title><?php wp_title( ' | ', true, 'right' ); ?></title>

        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_uri(); ?>" />
        <?php wp_head(); ?>
		
	</head>
	<body>

        <nav>
            <div class="content cf">
                <a href="#" class="logo"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="OpenFest" /></a>
                <ul>
                    <li><a href="#">Начало</a></li>
                    <li><a href="#">За събитието</a></li>
                    <li><a href="#">Спонсори</a></li>
                    <li><a href="#">Програма</a></li>
                    <li><a href="#">Контакти</a></li>
                    <li class="separator"></li>
                    <li><a href="#"><i class="fa fa-twitter">https://twitter.com/openfestbg</i></a></li>
                    <li><a href="#"><i class="fa fa-facebook">https://www.facebook.com/groups/6360369433</i></a></li>
                    <li><a href="#"><i class="fa fa-rss"><?php bloginfo('rss2_url'); ?></i></a></li>
                    <li class="separator"></li>
                    <li><a href="#">EN</a></li>
                </ul>
            </div>
        </nav>

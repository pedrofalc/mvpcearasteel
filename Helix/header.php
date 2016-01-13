<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'fabthemes' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,800,700,600' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Lato:400,300,700,900' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->


<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">

	<header id="masthead" class="site-header cf" role="banner">
	
	<div class="top">
		
			<div class="logo">
				
	<?php if (get_theme_mod(FT_scope::tool()->optionsName . '_logo', '') != '') { ?>
				<h1 class="site-title logo"><a class="mylogo" rel="home" href="<?php bloginfo('siteurl');?>/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><img relWidth="<?php echo intval(get_theme_mod(FT_scope::tool()->optionsName . '_maxWidth', 0)); ?>" relHeight="<?php echo intval(get_theme_mod(FT_scope::tool()->optionsName . '_maxHeight', 0)); ?>" id="ft_logo" src="<?php echo get_theme_mod(FT_scope::tool()->optionsName . '_logo', ''); ?>" alt="" /></a></h1>
	<?php } else { ?>
				<h1 class="site-title logo"><a id="blogname" rel="home" href="<?php bloginfo('siteurl');?>/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	<?php } ?>

			</div>
				
			<?php wp_nav_menu( array( 'container_id' => 'submenu','container_class' => 'cf', 'theme_location' => 'primary','menu_id'=>'fabthemes' ,'menu_class'=>'sfmenu sf-vertical','fallback_cb'=> 'fallbackmenu' ) ); ?>
		
			<div class="social-links">
				<ul>
					<li class="twitter"> <a href="http://twitter.com/<?php echo ft_of_get_option('fabthemes_twitter');  ?>"></a> </li>
					<li class="facebook"> <a href="<?php echo ft_of_get_option('fabthemes_facebook');  ?>"></a> </li>
					<li class="gplus"> <a href="<?php echo ft_of_get_option('fabthemes_gplus');  ?>"></a> </li>
					<li class="in"> <a href="<?php echo ft_of_get_option('fabthemes_linkedin');  ?>"></a> </li>
					<li class="feed"> <a href="<?php bloginfo('rss2_url'); ?>"></a> </li>
				</ul>
			</div>
			
		</div>
		
	</header><!-- #masthead .site-header -->


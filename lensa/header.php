<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]--><!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]--><!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]--><!--[if gt IE 8]><!--> 
<html class="no-js" xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>> <!--<![endif]-->
<head> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
  <title><?php colabs_title(); ?></title>       

  <?php 
  wp_head();    
  global $site_title,$site_url;    
  $site_title = get_bloginfo( 'name' );  
  $site_url = home_url( '/' );  
  $site_description = get_bloginfo( 'description' );
  ?> 

  <?php if(get_option('colabs_disable_mobile')!='true'){?>
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, minimal-ui" />
  <?php }?>
  
  <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]--> 
    
</head>

<body <?php body_class(); ?>>
<div class="main-wrapper">
  <header class="header-section container">
  <div class="row">
    <div class="branding column col4">
      <h1 class="logo">
        <a href="<?php echo $site_url;?>">
          <?php     
          if (get_option('colabs_logotitle')=='logo'){
            echo '<img src="' . get_option('colabs_logo') . '" alt="' . $site_title . '" />';
          } else {        
            echo $site_title;
          } // End IF Statement
          ?>      
        </a>    
      </h1>      
      <div class="site-description"><?php echo $site_description; ?></div>    
    </div><!-- .branding -->   

    <a href="#main-menu-slide" class="btn-navbar-wrapper">
      <div class="btn-navbar">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>
    </a>    

    <nav class="nav-collapse column col8">   
      
      <?php 
      if( colabs_check_plugin_active('woocommerce/woocommerce.php') ) : ?>
      <div class="top-menu-block">
        <ul>        
          <li class="cart-block">
            <a href="<?php echo WC()->cart->get_cart_url(); ?>"><i class="icon-shopping-cart"></i></a>
            <?php get_template_part( 'content', 'cart' ); ?>
          </li>
          
          <li class="account">
            <?php
              if ( is_user_logged_in() ) {
                echo '<a href="'.wp_logout_url().'"><i class="icon-signout"></i></a>';
              } else {
                echo '<a href="'.wp_login_url().'"><i class="icon-signin"></i></a>';
              }
            ?>            
          </li>
        </ul>
      </div>
      <?php endif; ?>

      <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'top-nav', 'container' => 'nav', 'menu_class' => '', 'fallback_cb'=>'colabs_nav_fallback') );?><!-- .topnav -->    
    </nav><!-- .nav-collapse -->      
  </div>
  </header><!-- .header-section -->
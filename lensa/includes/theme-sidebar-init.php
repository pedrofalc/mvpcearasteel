<?php
// Register widgetized areas

if (!function_exists('the_widgets_init')) {
	function the_widgets_init() {
	  if ( !function_exists('register_sidebars') )
	      return;
     
      register_sidebar(array(
          'name' => 'Sidebar',
          'id' => 'colabs_right',
          'description' => __( 'This widget will appear in right sidebar area', 'colabsthemes' ),
          'before_widget' => '<aside id="%1$s" class="%2$s block-background block-inner widget sidebar-right-background">',
          'after_widget' => '</aside>',
          'before_title' => '<h4 class="widget-title">',
          'after_title' => '</h4>'));  
      register_sidebar(array(
          'name' => 'Sidebar Shop',
          'id' => 'colabs_shop',
          'description' => __( 'This widget will appear in shop sidebar area', 'colabsthemes' ),
          'before_widget' => '<aside id="%1$s" class="%2$s block-background block-inner widget sidebar-right-background">',
          'after_widget' => '</aside>',
          'before_title' => '<h4 class="widget-title">',
          'after_title' => '</h4>'));
      if('scroller' == get_option('colabs_frontpage_style') && colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php')):    
      register_sidebar(array(
          'name' => 'Footer Widget',
          'id' => 'footer-widget',
          'description' => __( 'This widget will appear footer area', 'colabsthemes' ),
          'before_widget' => '<div id="%1$s" class="%2$s widget column col3">',
          'after_widget' => '</div>',
          'before_title' => '<h4 class="widget-title">',
          'after_title' => '</h4>'));
      endif;    
	}
}

add_action( 'init', 'the_widgets_init' );   
?>
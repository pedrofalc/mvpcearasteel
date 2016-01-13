<?php
if (!is_admin()) add_action( 'wp_enqueue_scripts', 'colabsthemes_add_javascript' );

if (!function_exists('colabsthemes_add_javascript')) {

	function colabsthemes_add_javascript () {
    
    wp_enqueue_style( 'colabs-google-font', 'http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' );
    wp_enqueue_style( 'colabs-framework-css', trailingslashit( get_template_directory_uri() ) . 'includes/css/framework.css' );
    wp_enqueue_style( 'colabs-fancybox-css', trailingslashit( get_template_directory_uri() ) . 'includes/css/fancybox/jquery.fancybox-1.3.4.css' );
    wp_enqueue_style( 'colabs-main-style', get_bloginfo( 'stylesheet_url' ) );
    wp_enqueue_style( 'colabs-woocommerce', get_template_directory_uri() . '/includes/css/woocommerce.css' );
    
    wp_enqueue_script('jquery');
		
		wp_enqueue_script( 'plugins', trailingslashit( get_template_directory_uri() ) . 'includes/js/plugins.js', array('jquery'), '',true );
		wp_enqueue_script( 'zero', trailingslashit( get_template_directory_uri() ) . 'includes/js/scripts.js', array('jquery'), '',true );		
    
		$data = array(
			'theme_url' => get_template_directory_uri()
		);
		wp_localize_script('jquery', 'colabs_settings', $data);

		wp_localize_script('zero','config', array('ajaxurl' => admin_url('admin-ajax.php')));   
		
		/* We add some JavaScript to pages with the comment form to support sites with threaded comments (when in use). */        
  	if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
    

	} /* // End colabsthemes_add_javascript() */
	
} /* // End IF Statement */

/*-----------------------------------------------------------------------------------*/
/* Ajax action for set like
/*-----------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_like', 'like' );
add_action( 'wp_ajax_like', 'like' );

function like() {
	$id = $_POST['id'];
	$votes = 1;
	$response = array();

	create_like( $id, $votes );
	$response['error'] = false;
	echo json_encode($response);
	exit;
}

/*-----------------------------------------------------------------------------------*/
/* Manage Slider Javascript
/*-----------------------------------------------------------------------------------*/
add_action('wp_footer', 'colabs_manage_slider');
function colabs_manage_slider() {
  if(((('big_slider' == get_option('colabs_frontpage_style'))||('scroller' == get_option('colabs_frontpage_style'))) && colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php') ) || (!colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php'))):
    $sliderdata = array( 
							'slideshow'	=> intval(get_option('colabs_slideshow')),
							'autoplay'	=> intval(get_option('colabs_autoplay')),			// Slideshow starts playing automatically
							'stop_loop'	=> intval(get_option('colabs_stop_loop')),			// Pauses slideshow on last slide
							'slide_interval' => intval(get_option('colabs_slide_interval')),		// Length between transitions
							'transition'     => intval(get_option('colabs_transition')), 			// 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
							'transition_speed'=> intval(get_option('colabs_transition_speed')),		// Speed of transition
							'pause_hover'     => intval(get_option('colabs_pause_hover')),			// Pause slideshow on hover
							'keyboard_nav'    => intval(get_option('colabs_keyboard_nav')),			// Keyboard navigation on/off
							'performance'	  => intval(get_option('colabs_performance')),			// 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
							'image_protect'	  => intval(get_option('colabs_image_protect'))			// Disables image dragging and right click with Javascript							
							);
		
		echo '<script type="text/javascript">
		/* <![CDATA[ */
		var slider_config = '. json_encode($sliderdata) .'
		/* ]]> */</script>';
  endif; 
}   
?>
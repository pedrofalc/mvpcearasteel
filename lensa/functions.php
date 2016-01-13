<?php
/*-----------------------------------------------------------------------------------*/
/* Start ColorLabs Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/
error_reporting(0);

if ( ! isset( $content_width ) ) $content_width = 612;

// Set path to ColorLabs Framework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/includes/';

// ColorLabs Admin
require_once ($functions_path . 'admin-init.php');			// Admin Init

// Theme specific functionality
$includes = array(
				'includes/theme-functions.php', 		// Custom theme functions
        'includes/theme-options.php', 			// Options panel settings and custom settings
				'includes/theme-comments.php', 			// Custom comments/pingback loop
				'includes/theme-js.php', 				// Load JavaScript via wp_enqueue_script
				'includes/theme-sidebar-init.php', 			// Initialize widgetized areas
				'includes/theme-widgets.php',			// Theme widgets	
				'includes/theme-custom-type.php',
				'includes/theme-instagram.php',
        'includes/theme-flickr.php',
        'includes/theme-facebook.php',
        'includes/theme-actions.php',
        'includes/theme-woocommerce.php',
				);

// Include the user's custom_functions file, but only if it exists
if (file_exists( COLABS_CUSTOM . 'custom_functions.php'))
  include_once( COLABS_CUSTOM . 'custom_functions.php' );

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'colabs_includes', $includes );
foreach ( $includes as $i ) {
  locate_template( $i, true );
}

/**
 * Custom Login Form
 */
if (!is_admin()) {
  get_template_part('includes/theme-login');
}

function colabs_open_jigoshop_content_wrappers()
{
    echo '<div id="container" class="block-inner">
			<div id="content" role="main">';
}

function colabs_close_jigoshop_content_wrappers()
{
    echo '	</div>
		  </div>';
}

function colabs_prepare_jigoshop_wrappers()
{
    remove_action( 'jigoshop_before_main_content', 'jigoshop_output_content_wrapper', 10 );
    remove_action( 'jigoshop_after_main_content', 'jigoshop_output_content_wrapper_end', 10);

    add_action( 'jigoshop_before_main_content', 'colabs_open_jigoshop_content_wrappers', 10 );
    add_action( 'jigoshop_after_main_content', 'colabs_close_jigoshop_content_wrappers', 10 );
}
add_action( 'wp_head', 'colabs_prepare_jigoshop_wrappers' );

remove_action( 'jigoshop_before_main_content', 'jigoshop_breadcrumb', 20, 0);
remove_action( 'jigoshop_before_single_product_summary', 'jigoshop_show_product_images'    , 20);
add_action( 'jigoshop_before_single_product_summary', 'colabs_jigoshop_show_product_images'    , 20);

if (!function_exists('colabs_jigoshop_show_product_images')) {
	function colabs_jigoshop_show_product_images() {

		global $_product, $post;

		echo '<div class="images gallery-item">';

		do_action( 'jigoshop_before_single_product_summary_thumbnails', $post, $_product );

		$thumb_id = 0;
		if (has_post_thumbnail()) :
			$thumb_id = get_post_thumbnail_id();
			// since there are now user settings for sizes, shouldn't need filters -JAP-
			//$large_thumbnail_size = apply_filters('single_product_large_thumbnail_size', 'shop_large');
			$large_thumbnail_size = jigoshop_get_image_size( 'shop_large' );
			$image_classes = apply_filters( 'jigoshop_product_image_classes', array(), $_product );
			array_unshift( $image_classes, 'zoom' );
			$image_classes = implode( ' ', $image_classes );
			echo '<a href="'.wp_get_attachment_url($thumb_id).'" class="'.$image_classes.'" rel="thumbnails">';
			the_post_thumbnail($large_thumbnail_size);
			echo '</a>';
		else :
			echo jigoshop_get_image_placeholder( 'shop_large' );
		endif;

		do_action('jigoshop_product_thumbnails');

		echo '</div>';

	}
}

if(is_admin()){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
  if( colabs_check_plugin_active('jigoshop/jigoshop.php') ){
		$data = array();
		foreach( get_option('jigoshop_options') as $k => $v) {
			if('jigoshop_shop_small_w' == $k || 'jigoshop_shop_small_h' == $k)
				$data[$k] = '200';
			elseif('jigoshop_catalog_columns' == $k)
				$data[$k] = '4';
			elseif('jigoshop_use_wordpress_catalog_crop' == $k)
				$data[$k] = 'yes';
			else
				$data[$k] = $v;
		} 
		
		update_option('jigoshop_options',$data);
		update_option('jigoshop_catalog_columns','4');
		update_option('jigoshop_use_wordpress_catalog_crop','yes');
		update_option('jigoshop_shop_small_w','200');
		update_option('jigoshop_shop_small_h','200');
	}
}
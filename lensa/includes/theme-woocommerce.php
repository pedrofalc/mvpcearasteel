<?php
// Force size of the shop catalog thumbnail
$shop_catalog = get_option( 'shop_catalog_image_size', array() );
$shop_catalog['width'] 	= 320;
$shop_catalog['height'] = 320;
$shop_catalog['crop'] = 1;
update_option( 'shop_catalog_image_size', $shop_catalog);

// Force size of the single product image
$shop_single = get_option( 'shop_single_image_size', array() );
$shop_single['width'] = 566;
$shop_single['height'] = 99999;
$shop_single['crop'] = false;
update_option( 'shop_single_image_size', $shop_single );

// Disable lightbox
update_option( 'woocommerce_enable_lightbox', 'yes' );
update_option( 'woocommerce_frontend_css', 'no' );

// Add theme support, for WooCommerce 2.1
add_theme_support( 'woocommerce' );

// Disable WooComerce style
add_filter( 'woocommerce_enqueue_styles', '__return_false' );


/* ===================================================================
	WooCommerce Filter and Hooks
=================================================================== */
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);


add_action( 'woocommerce_before_single_product', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_product_meta_open', 9 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 11 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_product_meta_close', 19 );

function woocommerce_product_meta_open() {
  echo '<div class="product-meta clearfix">';
}

function woocommerce_product_meta_close() {
  echo '</div>';
}


remove_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 11 );


/**
 * Override template on WooCommerce Page
 */
function colabs_wc_template_loader( $template ) {
  global $post;
  if( colabs_check_plugin_active('woocommerce/woocommerce.php') ) :
  $myaccount_page_id = wc_get_page_id( 'myaccount' );

  // My Account Page
  if ( $post->ID == $myaccount_page_id ) {
    $template = locate_template( array( 'woocommerce/page-myaccount.php' ) );
  } else if ( is_cart() ) {
    $template = locate_template( array( 'woocommerce/page-cart.php' ) );
  } else if( is_checkout() ) {
    $template = locate_template( array( 'woocommerce/page-checkout.php' ) );
  } else if( is_order_received_page() ) {
    $template = locate_template( array( 'woocommerce/page-thanks.php' ) );
  }
  endif;
  return $template;
}
add_filter( 'template_include', 'colabs_wc_template_loader', 11 );


/**
 * Change Sale Flash Structure
 */
add_filter( 'woocommerce_sale_flash', 'colabs_woocommerce_sale_flash', 10, 3 );
function colabs_woocommerce_sale_flash( $html, $post, $product ) {
  $html = '<span class="onsale"><span>'. __('Sale', 'colabsthemes') .'</span></span>';
  return $html;
}


/**
 * Load WooCommerce Single Product script on My Account Page
 */
add_action( 'woocommerce_init', 'load_woo_scripts' );
function load_woo_scripts() {

  if ( ! is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'single_product_scripts' );
  }

  function single_product_scripts() {
    wp_enqueue_script( 'wc-single-product' );
  }
}

/**
 * Add to Cart Fragments
 */
add_filter( 'add_to_cart_fragments', 'colabs_wc_add_to_cart_fragments' );
function colabs_wc_add_to_cart_fragments( $fragments ) {
  ob_start();

  get_template_part( 'content', 'cart' );

  $cart_content = ob_get_clean();

  $fragments['div.shopping-cart-wrapper'] = $cart_content;

  return $fragments;
}
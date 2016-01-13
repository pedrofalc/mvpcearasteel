<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>
<div class="main-container container">
  <div class="row">
    <header class="page-heading block-background block-inner">
      <h3><?php _e("Single","colabsthemes"); echo ' '.get_post_type(); ?></h3>
      <div class="minimize"></div>
    </header><!-- .page-heading -->
  
    <div class="main-content block-background column col12">
      <?php
  	  do_action('woocommerce_before_main_content');
  	  
  	  if(have_posts()) : while(have_posts()): the_post();
  		  woocommerce_get_template_part( 'content', 'single-product' );
  	  endwhile;	endif;
  	  
  	  do_action('woocommerce_after_main_content');?>
    </div><!-- .main-content -->
    
  </div>
</div>  
<?php get_footer(); ?>
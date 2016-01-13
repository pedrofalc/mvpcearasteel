<?php
/**
 * Single Product Meta
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;
?>

<div class="product-meta-bottom">

  <?php do_action( 'woocommerce_product_meta_start' ); ?>

  <?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) : ?>
    <div itemprop="productID" class="sku_wrapper"><strong><?php _e( 'SKU:', 'woocommerce' ); ?></strong> <span class="sku"><?php echo $product->get_sku(); ?></span>.</div>
  <?php endif; ?>

  <?php
    $size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
    echo $product->get_categories( ', ', '<div class="posted_in">' . '<strong>' . _n( 'Category:', 'Categories:', $size, 'woocommerce' ) . '</strong>' . ' ', '.</div>' );
  ?>

  <?php
    $size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
    echo $product->get_tags( ', ', '<div class="tagged_as">' . '<strong>' . _n( 'Tag:', 'Tags:', $size, 'woocommerce' ) . '</strong>' . ' ', '.</div>' );
  ?>

  <?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
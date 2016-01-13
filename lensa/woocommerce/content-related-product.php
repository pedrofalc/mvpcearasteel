<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;
	
$classes[] = 'gallery-item column col4';
	
?>
<li <?php post_class( $classes ); ?>>
	
	<div class="product-item-inner">
		
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		
		<a href="<?php the_permalink(); ?>">

			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>
			
			<div class="catalog-info">
				<h3><?php the_title(); ?></h3>

				<?php
					/**
					 * woocommerce_after_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_rating - 5
				 	 * @hooked woocommerce_template_loop_price - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
			</div>
			
			<span class="read-details"><span><?php _e('View Details', 'colabsthemes'); ?></span></span>

		</a>

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		
	</div>

</li>
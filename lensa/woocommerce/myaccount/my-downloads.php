<?php
/**
 * My Orders
 *
 * Shows recent orders on the account page
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( $downloads = $woocommerce->customer->get_downloadable_products() ) : ?>

  <h3 class="panel-title module-item-title"><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available downloads', 'woocommerce' ) ); ?></h3>
  
  <div class="digital-downloads-list clearfix">
    
    <?php 
    function download_product($arr) {
      return $arr['product_id'];
    }
    $download_product_ids = array_map('download_product', $downloads); 
    $download_product_ids = array_unique($download_product_ids); 
    $counter = 0; ?>

    <?php foreach( $download_product_ids as $product_id ) : 
      global $post;

      $digital_product = get_product($product_id); 
      $post = $digital_product->post;
      $is_clear = $counter == 0 || $counter % 4 == 0 ? 'alpha' : '';
      
      setup_postdata( $post );?>
      <div class="digital-download-product column col3 <?php echo $is_clear; ?>">
        <div class="digital-download-inner">
          <div class="product-image"><?php echo woocommerce_get_product_thumbnail(); ?></div>
          
          <div class="product-download-content">
            <h5 class="product-title">
              <a href="<?php echo get_permalink();?>">
                <?php echo get_the_title(); ?>
              </a>
            </h5>

            <ul class="digital-downloads">
              <?php foreach ( $downloads as $download ) : 
                if( $download['product_id'] != $product_id ) continue; ?>
                <li>
                  <?php
                    do_action( 'woocommerce_available_download_start', $download );
            
                    echo apply_filters( 'woocommerce_available_download_link', '<a href="' . esc_url( $download['download_url'] ) . '">' . $download['download_name'] . '</a>', $download );

                    if ( is_numeric( $download['downloads_remaining'] ) )
                      echo apply_filters( 'woocommerce_available_download_count', '<span class="count">' . sprintf( _n( '%s download remaining', '%s downloads remaining', $download['downloads_remaining'], 'woocommerce' ), $download['downloads_remaining'] ) . '</span> ', $download );
            
                    do_action( 'woocommerce_available_download_end', $download );
                  ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div><!-- .product-download-content -->
        </div>
      </div>
    <?php $counter++; endforeach; wp_reset_postdata();  ?>

  </div><!-- .digital-downloads-list -->

<?php else : ?>

  <h3 class="panel-title module-item-title"><?php echo apply_filters( 'woocommerce_my_account_my_downloads_title', __( 'Available downloads', 'woocommerce' ) ); ?></h3>
  <p><?php _e("You haven't made any purchase. If you purchase downloadable product, it will appears here.", 'colabsthemes'); ?></p>

<?php endif; ?>
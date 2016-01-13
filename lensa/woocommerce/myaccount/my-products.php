<?php
/**
 * My Products Tab
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$user = get_current_user_id();
$no = 1;
$products_args = array(
  'showposts' => -1,
  'post_type' => 'product',
  'author' => $user,
  'post_status' => array('publish', 'pending')
);
$add_product_permalink = get_permalink( get_option('colabs_submit_product_page_id') );
$edit_product_permalink = get_permalink( get_option('colabs_edit_product_page_id') );

$query_products = new WP_Query($products_args); ?>

<?php if ( $query_products->have_posts() ) : ?>
  
  <h3 class="module-item-title">
    <?php echo apply_filters( 'woocommerce_my_account_my_products_title', __( 'My Products', 'woocommerce' ) ); ?>
    <a class="btn btn-red btn-full-color btn-mini btn-uppercase btn-bold" href="<?php echo $add_product_permalink; ?>"><?php _e('Add Product', 'colabsthemes'); ?></a>
  </h3>
  <h4 class="module-item-subtitle panel-title">
    <?php _e('You have', 'colabsthemes'); ?> <?php echo sprintf( _n( '1 Product', '%s Products', $query_products->found_posts, 'colabsthemes' ), $query_products->found_posts); ?>
  </h4>

  <table class="shop_table my_account_multimedia cart">

    <thead>
      <tr>
        <th class="product-remove">&nbsp;</th>
        <th class="product-thumbnail">&nbsp;</th>
        <th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
        <th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
        <th class="product-status"><?php _e( 'Status', 'colabsthemes' ); ?></th>
        <th class="order-actions">&nbsp;</th>
      </tr>
    </thead>

    <tbody>
      <?php while ( $query_products->have_posts() ) : $query_products->the_post();$product = get_product( get_the_ID() );?> 
      <tr class="cart_table_item">
        <td class="product-remove">
          <a href="#" class="btn-delete" data-id="<?php echo $product->id; ?>" data-action="delete_user_product">&times;</a>
        </td>

        <td class="product-thumbnail">
          <?php colabs_image('width=90&height=90&single=false');?>
        </td>

        <!-- Product Name -->
        <td class="product-name">
          <a href="<?php the_permalink();?>"><?php the_title();?></a>
        </td>
        
        <!-- Product price -->
        <td class="product-price" data-title="<?php _e('Price', 'colabsthemes'); ?>">
          <?php if ( $price_html = $product->get_price_html() ) : ?>
            <?php echo $product->get_price_html(); ?>
          <?php endif; ?>
        </td>
        
        <td class="product-status" data-title="<?php _e('Status', 'colabsthemes'); ?>">
          <em><?php echo $product->post->post_status; ?></em>
        </td>

        <td class="product-edit">
          <a href="<?php echo $edit_product_permalink; ?>?product_id=<?php echo $product->id; ?>" class="btn btn-red btn-full-color btn-uppercase btn-bold btn-mini"><?php _e('Edit', 'colabsthemes'); ?></a>
        </td>
      </tr>
      <?php $no++;endwhile;?>
    </tbody>

  </table>

<?php 
// If user have no product
else : ?>

  <?php if ( Colabs_Users_Commission::is_vendor( get_current_user_id() ) ):?>
    <div class="woocommerce-message">
    
      <?php printf(__('Looks like you doesn\'t have any product, why don\'t you <a href="%1$s">add your product</a> now.'), $add_product_permalink); ?>

    </div>
  
  <?php else : ?>
    <div class="woocommerce-message">
    
      <?php printf(__('%1$s is allow you to sell your own product. To start sell your product, you have to <a href="%2$s">apply as a vendor</a>.'), get_bloginfo('sitename'), $add_product_permalink); ?>

    </div>
  <?php endif; ?>

<?php endif; ?>
<?php
// Cart For WooCommerce
// -------------------------
if( colabs_check_plugin_active('woocommerce/woocommerce.php') ) : ?>
<?php global $woocommerce; ?>
    <div class="shopping-cart-wrapper">
    <?php
    if (sizeof($woocommerce->cart->get_cart())>0) : 

        echo '<h4 class="shopping-cart-title">'. __('Bag Summary','colabsthemes') . "\n";
        echo '<a href="'.$woocommerce->cart->get_cart_url().'">'. __('Edit', 'colabsthemes') .'</a></h4>'."\n";
        
        echo '<ul class="cart_list product_list_widget cart-product-list">';
            foreach ($woocommerce->cart->get_cart() as $cart_item_key => $cart_item) :
                $_product = $cart_item['data'];
                if ($_product->exists() && $cart_item['quantity']>0) :
                    echo '<li class="cart-product">'."\n";
                    echo $_product->get_image( array( 37, 37 ) );
                    echo '<div class="product_list_widget_content">'."\n";
                    echo '<a href="'. get_permalink($cart_item['product_id']). '">'."\n";
                    echo apply_filters('woocommerce_cart_widget_product_title', $_product->get_title(), $_product) .'</a>';

                    echo '<span class="amount">' . woocommerce_price($_product->get_price()) . '</span>'."\n";
                    echo '<dl class="variations">'."\n";
                    echo '<dt>'. __('Quantity','colabsthemes') .':</dt><dd>' .$cart_item['quantity']. '</dd>'."\n";
                    echo '</dl>'."\n";
                    echo '</li>'."\n";
                endif;
            endforeach;
              
        if (sizeof($woocommerce->cart->get_cart())>0) :
            echo '<li class="total">'."\n";
            echo '<div class="total-block">'."\n";
            echo '<strong>'. __('Subtotal:', 'colabsthemes') .'</strong>'. $woocommerce->cart->get_cart_total() ."\n";
            echo '</div>'."\n";
            echo '</li>';
            echo '<li class="buttons">'."\n";
            echo '<a class="checkout" href="'.$woocommerce->cart->get_checkout_url().'"><span class="btn btn-bold btn-red btn-full-color">'. __('Checkout &rarr;', 'colabsthemes') .'<i class="icon-custom-basket"></i></span></a>'."\n";
            echo '</li>'."\n";
        endif;
              
        echo '</ul><!-- .cart_list -->';
        
    else :
    
    // If cart empty
    echo '<ul class="cart_list product_list_widget cart-product-list">';
    echo '<li class="empty">'.__('There’s nothing in the shopping bag yet, why not start adding some?', 'colabsthemes').'</li>';
    echo '</ul>';
    endif;
    ?>
    </div><!-- .shopping-cart-wrapper"> -->
<?php endif; ?>

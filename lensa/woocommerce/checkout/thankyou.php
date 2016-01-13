<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( $order ) : ?>

	<h2 class="page-title float-left"><?php _e('Order Received', 'colabsthemes'); ?></h2>

	<div class="column col6 alpha column-our-details">

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="woocommerce-error">
				<p><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'woocommerce' ); ?></p>
			</div>
			
			<div class="woocommerce-error">
				<p><?php
					if ( is_user_logged_in() )
						_e( 'Please attempt your purchase again or go to your account page.', 'woocommerce' );
					else
						_e( 'Please attempt your purchase again.', 'woocommerce' );
				?></p>
			</div>
			
			<p>
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-primary btn-full-color pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="btn btn-primary btn-full-color pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
			
			<div class="alert-box with-icon icon-custom-map">
				<h4><?php _e('Thank you', 'colabsthemes'); ?></h4>
				<p><?php _e( 'Your order has been received. Please keep your purchase details below.', 'colabsthemes' ); ?></p>
			</div>
			
			<div class="order_details">
				<table>
					<tbody>
						<tr class="order">
							<th><?php _e('Order', 'colabsthemes'); ?></th>
							<td><?php echo $order->get_order_number(); ?></td>
						</tr>
						<tr class="date">
							<th><?php _e( 'Date', 'colabsthemes' ); ?></th>
							<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></td>
						</tr>
						<tr class="total">
							<th><?php _e( 'Total', 'colabsthemes' ); ?></th>
							<td><?php echo $order->get_formatted_order_total(); ?></td>
						</tr>
						<?php if ( $order->payment_method_title ) : ?>
							<tr class="method">
								<th><?php _e( 'Payment method', 'colabsthemes' ); ?></th>
								<td><?php echo $order->payment_method_title; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<div class="stamp"><span><?php _e('Order Received', 'colabsthemes'); ?></span></div>
			</div><!-- .order_details -->

		<?php endif; ?>
		
		<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>

	</div><!-- .column.col6 -->

	<div class="column col6 column-our-details">
		<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
	</div>
	<div class="clear"></div>

<?php else : ?>

	<p><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

<?php endif; ?>
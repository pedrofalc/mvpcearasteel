<?php
/**
 * Login Form
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     2.2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="login-block col5 centered">

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="col2-set" id="customer_login">

  <div class="col-1">

<?php endif; ?>

    <h2><?php _e( 'Login', 'woocommerce' ); ?></h2>

    <?php 
    $login_args = array(  
            'redirect' => home_url(),   
            'id_username' => 'user',  
            'id_password' => 'pass',  
          );   
    
    wp_login_form($login_args);
    ?>
    <p id="nav">

      <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>

    </p>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

  </div>

  <div class="col-2">

    <h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

    <?php colabs_show_register();?>

  </div>

</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

</div>
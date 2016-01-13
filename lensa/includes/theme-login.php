<?php
/**
 *
 * This controls how the login, logout,
 * registration, and forgot your password pages look.
 * It overrides the default WP pages by intercepting the request.
 *
 */
class Colabs_Custom_Login {

  /**
   * Constructor
   */
  function __construct() {
    global $pagenow;

    if( 'wp-login.php' == $pagenow ) {
      add_action( 'colabs_title', array( $this, 'colabs_login_title' ) );
      add_action( 'wp_title', array( $this, 'colabs_login_title' ) );
      add_action( 'wp_head', array( $this, 'custom_login_header' ) );
      add_action( 'wp_head', '_custom_background_cb' );
      add_action( 'login_init', array( $this, 'init' ) );
    }
    
    if(isset($_GET['loggedout'])){
      add_action( 'colabs_title', array( $this, 'colabs_login_title' ) );
    }
  }


  /**
   * Initialization
   */
  function init() {
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';

    if ( isset($_GET['key']) )
      $action = 'resetpass';
    
    // validate action so as to default to the login screen
    //if ( !in_array( $action, array( 'postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register' ), true ) && false === has_filter( 'login_form_' . $action ) )
      //$action = 'login';
    
    // Only change login, register page
    if( in_array( $action , array( 'login', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register' ) ) ) {

      ob_start();
      
      get_header();
      $this->container_open( $action );

        // Check action
        switch ($action) {

          case 'lostpassword':
          case 'retrievepassword':
            $this->action_lostpassword();
          break;

          case 'resetpass':
          case 'rp':
            $this->action_reset_pass();
          break;

          case 'register':
            $this->action_register();
          break;

          case 'login' :
          default:
            $this->action_login();
          break;

        }
        

      $this->container_close();
      get_footer();

      $output = ob_get_contents();
      ob_end_clean();

      echo $output;
      exit;
    }    
  }


  /**
   * Hook into header
   */
  function custom_login_header() {
    /**
     * Enqueue scripts and styles for the login page.
     *
     * @since 3.1.0
     */
    do_action( 'login_enqueue_scripts' );

    /**
     * Fires in the login page header after scripts are enqueued.
     *
     * @since 2.1.0
     */
    do_action( 'login_head' );
  }


  /**
   * Page Title
   */
  function colabs_login_title( $title ) {
    if (isset($_GET['action'])) $action = $_GET['action']; else $action='';

    switch($action) {
      case 'lostpassword':
        $title = __('Retrieve your lost password? ','colabsthemes');
        break;

      case 'login':
      default:
        $title = __('Sign In/Register','colabsthemes');
        break;
    }
    
    return $title;
  }


  /**
   * Container Open
   * @param String $action WP login action
   */
  function container_open( $action ) {}


  /**
   * Container Close
   */
  function container_close() {}


  /**
   * Login hooks
   */
  function action_login() {
    $interim_login = isset($_REQUEST['interim-login']);
    $secure_cookie = '';
    $customize_login = isset( $_REQUEST['customize-login'] );
    if ( $customize_login )
      wp_enqueue_script( 'customize-base' );

    // If the user wants ssl but the session is not ssl, force a secure cookie.
    if ( !empty($_POST['log']) && !force_ssl_admin() ) {
      $user_name = sanitize_user($_POST['log']);
      if ( $user = get_user_by('login', $user_name) ) {
        if ( get_user_option('use_ssl', $user->ID) ) {
          $secure_cookie = true;
          force_ssl_admin(true);
        }
      }
    }

    if ( isset( $_REQUEST['redirect_to'] ) ) {
      $redirect_to = $_REQUEST['redirect_to'];
      // Redirect to https if user wants ssl
      if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
        $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
    } else {
      $redirect_to = admin_url();
    }

    $reauth = empty($_REQUEST['reauth']) ? false : true;

    $user = wp_signon( '', $secure_cookie );

    if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
      if ( headers_sent() ) {
        $user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
          __( 'http://codex.wordpress.org/Cookies' ), __( 'https://wordpress.org/support/' ) ) );
      } elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
        // If cookies are disabled we can't log in even with a valid user+pass
        $user = new WP_Error( 'test_cookie', sprintf( __( '<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.' ),
          __( 'http://codex.wordpress.org/Cookies' ) ) );
      }
    }

    $requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

    /**
     * Filter the login redirect URL.
     *
     * @since 3.0.0
     *
     * @param string           $redirect_to           The redirect destination URL.
     * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
     * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
     */
    $redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );
    
    if ( !is_wp_error($user) && !$reauth ) {
      if( $interim_login ) {
        $message = '<div class="alert alert-success">' . __('You have logged in successfully.') . '</div>';
        $interim_login = 'success';
        echo $message;
      }

      if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
        // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
        if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
          $redirect_to = user_admin_url();
        elseif ( is_multisite() && !$user->has_cap('read') )
          $redirect_to = get_dashboard_url( $user->ID );
        elseif ( !$user->has_cap('edit_posts') )
          $redirect_to = admin_url('profile.php');
      }

      wp_safe_redirect($redirect_to);
      exit();
    }

    $errors = $user;

    // Clear errors if loggedout is set.
    if ( !empty($_GET['loggedout']) || $reauth )
      $errors = new WP_Error();

    if ( $interim_login ) {
      if ( ! $errors->get_error_code() )
        $errors->add('expired', __('Session expired. Please log in again. You will not move away from this page.'), 'message');
    } else {
      // Some parts of this script use the main login form to display a message
      if    ( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
        $errors->add('loggedout', __('You are now logged out.'), 'message');
      elseif  ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
        $errors->add('registerdisabled', __('User registration is currently not allowed.'));
      elseif  ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
        $errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
      elseif  ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
        $errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
      elseif  ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
        $errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
      elseif ( strpos( $redirect_to, 'about.php?updated' ) )
        $errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.' ), 'message' );
    }

    /**
     * Filter the login page errors.
     *
     * @since 3.6.0
     *
     * @param object $errors      WP Error object.
     * @param string $redirect_to Redirect destination URL.
     */
    $errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

    // Clear any stale cookies.
    if ( $reauth )
      wp_clear_auth_cookie();
    
    // Error Messages
    $this->render_messages( $errors );

    $this->login_form( $interim_login, $redirect_to, $errors );
  }


  /**
   * Lost Password hooks
   */
  function action_lostpassword() {
    $errors = new WP_Error();
    $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);

    if ( $http_post ) {
      $errors = retrieve_password();
      if ( !is_wp_error($errors) ) {
        $redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'wp-login.php?checkemail=confirm';
        wp_safe_redirect( $redirect_to );
        exit();
      }
    }

    if ( isset( $_GET['error'] ) ) {
      if ( 'invalidkey' == $_GET['error'] )
        $errors->add( 'invalidkey', __( 'Sorry, that key does not appear to be valid.' ) );
      elseif ( 'expiredkey' == $_GET['error'] )
        $errors->add( 'expiredkey', __( 'Sorry, that key has expired. Please try again.' ) );
    }

    $lostpassword_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

    /**
     * Filter the URL redirected to after submitting the lostpassword/retrievepassword form.
     *
     * @since 3.0.0
     *
     * @param string $lostpassword_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters( 'lostpassword_redirect', $lostpassword_redirect );

    /**
     * Fires before the lost password form.
     *
     * @since 1.5.1
     */
    do_action( 'lost_password' );

    $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';

    // Error Messages
    $this->render_messages( $errors );

    $this->forgot_password_form( $redirect_to );
  }


  /**
   * Reset Password hooks
   */
  function action_reset_pass() {
    list( $rp_path ) = explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) );
    $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
    if ( isset( $_GET['key'] ) ) {
      $value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
      setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      wp_safe_redirect( remove_query_arg( array( 'key', 'login' ) ) );
      exit;
    }

    if ( isset( $_COOKIE[ $rp_cookie ] ) && 0 < strpos( $_COOKIE[ $rp_cookie ], ':' ) ) {
      list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ $rp_cookie ] ), 2 );
      $user = check_password_reset_key( $rp_key, $rp_login );
    } else {
      $user = false;
    }

    if ( ! $user || is_wp_error( $user ) ) {
      setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      if ( $user && $user->get_error_code() === 'expired_key' )
        wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=expiredkey' ) );
      else
        wp_redirect( site_url( 'wp-login.php?action=lostpassword&error=invalidkey' ) );
      exit;
    }

    $errors = new WP_Error();

    if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
      $errors->add( 'password_reset_mismatch', __( 'The passwords do not match.', 'colabsthemes' ) );

    /**
     * Fires before the password reset procedure is validated.
     *
     * @since 3.5.0
     *
     * @param object           $errors WP Error object.
     * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
     */
    do_action( 'validate_password_reset', $errors, $user );

    if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
      reset_password($user, $_POST['pass1']);
      setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
      $message = __( 'Your password has been reset.', 'colabsthemes' ) . ' <a href="' . esc_url( wp_login_url() ) . '">' . __( 'Log in', 'colabsthemes' ) . '</a>';
    }

    wp_enqueue_script('utils');
    wp_enqueue_script('user-profile');
    
    if (isset($message) && !empty($message)) {
      $this->render_messages( $message );
    }

    if (isset($errors) && sizeof($errors)>0 && $errors->get_error_code()) {
      $this->render_messages( $errors );
    }

    $this->reset_pass_form( $rp_key );
  }


  /**
   * Register hooks
   */
  function action_register() {
    $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);

    if ( !get_option('users_can_register') ) {
      wp_redirect( site_url('wp-login.php?registration=disabled') );
      exit();
    }

    $user_login = '';
    $user_email = '';
    $first_name = '';
    $last_name = '';

    if ( $http_post ) {
      $user_login = $_POST['user_login'];
      $user_email = $_POST['user_email'];
      $user_pass = $_POST['your_password'];
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];

      $errors = $this->process_register(  );
    }

    $registration_redirect = ! empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
    /**
     * Filter the registration redirect URL.
     *
     * @since 3.0.0
     *
     * @param string $registration_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters( 'registration_redirect', $registration_redirect );
    
    if (isset($errors) && sizeof($errors)>0 && $errors->get_error_code()) {
      $this->render_messages( $errors );
    }

    $this->register_form( array(
      'user_login' => $user_login, 
      'user_email' => $user_email,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'redirect_to' => $redirect_to
    ) );
  }


  /**
   * Render Messages
   *
   * @param Object $message_data WP_Error Object
   */
  function render_messages( $message_data ) {
    if( is_wp_error( $message_data ) ) {
      if ( $message_data->get_error_code() ) {
        $errors = '';
        $messages = '';
        
        foreach ( $message_data->get_error_codes() as $code ) {
          $severity = $message_data->get_error_data( $code );

          foreach ( $message_data->get_error_messages( $code ) as $error_message ) {
            if ( 'message' == $severity )
              $messages .= '  ' . $error_message . "<br />\n";
            else
              $errors .= '  ' . $error_message . "<br />\n";
          }
        }

        if ( ! empty( $errors ) ) {
          echo '<div class="alert-box errors">'. $errors .'</div>';
        }

        if ( ! empty( $messages ) ) {
          echo '<div class="alert-box">'. $messages .'</div>'; 
        }
        
      }
    }

    else {
      if( $message_data ) {
        echo '<div class="alert-box">'. $message_data .'</div>'; 
      }
    }
  }


  /**
   * Render Login Form
   */
  function login_form( $interim_login, $redirect_to, $errors ) {}


  /**
   * Forgot Password Form
   */
  function forgot_password_form( $redirect_to ) {}


  /**
   * Reset Password Form
   */
  function reset_pass_form( $rp_key ) {}


  /**
   * Register Form
   */
  function register_form( $options = array() ) {}


  /**
   * Register Form Process
   */
  function process_register() {}


  /**
   * email that gets sent out to new users once they register
   */
  function colabs_sent_email($user_id, $user_pass) {

    $user = new WP_User($user_id);

    $user_login = stripslashes($user->user_login);
    $user_email = stripslashes($user->user_email);

    // variables that can be used by admin to dynamically fill in email content
    // $find = array('/%username%/i', '/%password%/i', '/%blogname%/i', '/%siteurl%/i', '/%loginurl%/i', '/%useremail%/i');
    // $replace = array($user_login, $plaintext_pass, get_option('blogname'), get_option('siteurl'), get_option('siteurl').'/wp-login.php', $user_email);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    // send the site admin an email everytime a new user registers
    $message  = sprintf(__('New user registration on your site %s:', 'colabsthemes'), $blogname) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('Username: %s', 'colabsthemes'), $user_login) . PHP_EOL . PHP_EOL;
    $message .= sprintf(__('E-mail: %s', 'colabsthemes'), $user_email) . PHP_EOL;

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'colabsthemes'), $blogname), $message);

    // For user
    $message  = sprintf(__('Username: %s', 'colabsthemes'), $user_login) . PHP_EOL;
    
    if ( get_option('colabs_allow_registration_password') != 'true' ) {
      $message .= sprintf(__('Password: %s', 'colabsthemes'), $user_pass) . PHP_EOL;
    }
    $message .= wp_login_url() . PHP_EOL;

    wp_mail($user_email, sprintf(__('[%s] Your username and password', 'colabsthemes'), $blogname), $message);
  }
}




/**
 * Modify the login page by extending Colabs_Custom_Login class
 */
class Lensa_Colabs_Custom_Login extends Colabs_Custom_Login {

  /**
   * Constructor
   */
  function __construct() {
    global $pagenow;
    parent::__construct();

    if( 'wp-login.php' == $pagenow ) {
      add_filter( 'body_class', array( $this, 'colabs_login_body_class'), 11 );
    }
  }


  /**
   * Change body class
   */
  function colabs_login_body_class( $classes ) {
    $classes[] = 'login-body-style';
    return apply_filters('colabs_login_body_class', $classes);
  }


  /**
   * Filter login redirect
   */
  function custom_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
    /*if( !is_wp_error( $user ) ) {
      if( get_option('colabs_dashboard_page') ) {
        $redirect_to = get_permalink( get_option('colabs_dashboard_page') );
      } else {
        $redirect_to = home_url('/');
      }
    }*/

    return $redirect_to;
  }


  /**
   * Container Open
   */
  function container_open( $action ) {

    switch ($action) {
      case 'lostpassword':
      case 'retrievepassword':
        $title = __('Forgot Your Password?', 'colabsthemes');
        break;

      case 'login':
        $title = __('Sign In or Create An Account', 'colabsthemes');
        break;

      case 'rp':
      case 'resetpass':
        $title = __('Enter your new password', 'colabsthemes');
        break;

      case 'register':
        $title = __('Create An Account', 'colabsthemes');
        break;
    }

    echo '
      <div class="main-container container">
        <div class="row">
          <header class="page-heading block-background block-inner">
            <h3><?php echo $title; ?></h3>
            <div class="minimize"></div>
          </header><!-- .page-heading -->
      
          <div class="main-content block-background column col12">
            <div class="block-inner">
              <div class="login-page">
                <div class="login-block col5 centered">                
    ';
  }


  /**
   * Container Close
   */
  function container_close() {
    echo '
                </div><!-- .login-block -->
              </div><!-- .login-page -->
            </div><!-- .block-inner -->
          </div><!-- .main-content -->
        </div><!-- .row -->
      </div><!-- .main-container -->
    ';
  }


  /**
   * Render Login Form
   */
  function login_form( $interim_login, $redirect_to, $errors ) {
    
    echo '<p>' . __('If you have an account with us, log in using your email address.', 'colabsthemes') . '</p>';

    $login_args = array(  
      'redirect' => home_url(),   
      'id_username' => 'user',  
      'id_password' => 'pass',  
    );   
    
    wp_login_form($login_args); ?>

    <p id="nav">
      <?php 
      if ( ! isset( $_GET['checkemail'] ) || ! in_array( $_GET['checkemail'], array( 'confirm', 'newpass' ) ) ) :
        if ( get_option( 'users_can_register' ) ) :
          $registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register','colabsthemes' ) );
          echo apply_filters( 'register', $registration_url ) . ' | ';
        endif; ?>
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?','colabsthemes' ); ?></a>
      <?php endif; ?>
    </p>

    <?php
  }


  /**
   * Forgot Password Form
   */
  function forgot_password_form( $redirect_to ) {
    ?>
    
    <form name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post'); ?>" method="post" class="lost_reset_password login">
      <p><?php _e('Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'colabsthemes'); ?></p>

      <p class="form-row field-username">
        <label id="user_login" for="user_login"><?php _e('Username or E-mail:', 'colabsthemes'); ?>*</label>
        <input type="text" placeholder="<?php _e( 'Username or Email Address', 'woocommerce' ); ?>" class="text input-text" name="user_login" id="user_login" value="<?php echo esc_attr($user_login); ?>" />
      </p>

      <?php
      /**
       * Fires inside the lostpassword <form> tags, before the hidden fields.
       *
       * @since 2.1.0
       */
      do_action( 'lostpassword_form' ); ?>
      <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
      
      <p class="submit form-row">
        <input type="submit" name="wp-submit" id="wp-submit" class="submit btn btn-uppercase btn-bold btn-red btn-full-color btn-block btn-large"  value="<?php esc_attr_e('Get New Password','colabsthemes'); ?>" />
      </p>
    </form>

    <?php
  }


  /**
   * Reset Password Form
   */
  function reset_pass_form( $rp_key ) {
    ?>
      <form name="resetpassform" class="resetpassform account_form login register-form" id="resetpassform" action="<?php echo esc_url( site_url( 'wp-login.php?action=resetpass', 'login_post' ) ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" value="<?php echo esc_attr( $rp_login ); ?>" autocomplete="off" />
        
        <p><?php _e('Please enter your new password below', 'colabsthemes'); ?></p>

        <p class="form-row">
          <label for="pass1"><?php _e('New password', 'colabsthemes') ?></label>
          <input type="password" name="pass1" id="pass1" class="input input-text" size="20" value="" autocomplete="off" placeholder="<?php _e('New password', 'colabsthemes') ?>" />
        </p>

        <p class="form-row">
          <label for="pass2"><?php _e('Confirm new password', 'colabsthemes') ?></label>
          <input type="password" name="pass2" id="pass2" class="input input-text" size="20" value="" autocomplete="off" placeholder="<?php _e('Confirm new password', 'colabsthemes') ?>" />
        </p>

        <div id="pass-strength-result" class="hide-if-no-js password-strength-result"><?php _e('Strength indicator', 'colabsthemes'); ?></div>
        <div class="clear"></div>

        <p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).'); ?></p>

        <br class="clear" />

        <?php
        /**
         * Fires following the 'Strength indicator' meter in the user password reset form.
         *
         * @since 3.9.0
         *
         * @param WP_User $user User object of the user whose password is being reset.
         */
        do_action( 'resetpass_form', $user );
        ?>
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $rp_key ); ?>" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="btn btn-uppercase btn-bold btn-red btn-full-color btn-block btn-large" value="<?php esc_attr_e('Reset Password'); ?>" /></p>
      </form>
    <?php
  }

  /**
   * Register Form
   */
  function register_form( $options = array() ) {
    extract( $options );

    if ( get_option('users_can_register') ) :
    ?>
      
      <form name="registerform" id="registerform" action="<?php echo esc_url( site_url('wp-login.php?action=register', 'login_post') ); ?>" method="post" novalidate="novalidate" class="account_form login">
        <p class="form-row field-username">
          <label id="user_login" for="user_login"><?php _e('Username', 'colabsthemes'); ?>*</label>
          <input type="text" class="text input-text" name="user_login" id="user_login"  value="<?php echo esc_attr(wp_unslash($user_login)); ?>" placeholder="<?php _e('Username', 'colabsthemes'); ?>" />
        </p>

        <p class="form-row field-email">
          <label id="user_email" for="user_email" ><?php _e('Email Address', 'colabsthemes'); ?>*</label>
          <input type="text" class="text input-text" name="user_email" id="user_email"  value="<?php echo esc_attr(wp_unslash($user_email)); ?>" placeholder="<?php _e('Email Address', 'colabsthemes'); ?>" />
        </p>
        
        <?php if (get_option('colabs_allow_registration_password')=='true') : ?>
          <p class="form-row field-password">
            <i class="icon-custom-locked"></i>
            <label id="your_password" for="your_password" ><?php _e('Password', 'colabsthemes'); ?>*</label>
            <input type="password" class="text input-text" name="your_password" id="your_password"  value="" placeholder="<?php _e('Password', 'colabsthemes'); ?>" />
          </p>

          <p class="form-row field-password">
            <i class="icon-custom-locked"></i>
            <label id="your_password_confirm" for="your_password_confirm" ><?php _e('Password', 'colabsthemes'); ?>*</label>
            <input type="password" class="text input-text" name="your_password_confirm" id="your_password_confirm"  value="" placeholder="<?php _e('Password', 'colabsthemes'); ?>" />
          </p>
        <?php endif; ?>

        <?php
        /**
         * Fires following the 'E-mail' field in the user registration form.
         *
         * @since 2.1.0
         */
        do_action( 'register_form' );
        ?>
        
        <?php if (get_option('colabs_captcha_enable') == 'true') : ?>
          <div class="form-row field-captcha">
            <?php colabsthemes_recaptcha(); ?>
          </div>
        <?php endif; ?>

        <p class="form-row">
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
          <input type="submit" class="button-primary btn btn-red btn-uppercase btn-bold btn-full-color btn-block btn-large" tabindex="7" name="wp-submit" value="<?php esc_attr_e('Register', 'colabsthemes'); ?>" />
        </p>

      </form>

    <?php
    endif;
  }


  static function static_register_form() {
    self::register_form();
  }


  /**
   * Process Register Form
   */
  function process_register() {
    $posted = array();
    $errors = new WP_Error();

    // Get (and clean) data
    $fields = array(
      'user_login',
      'user_email',
      'your_password',
      'your_password_confirm',
      'spam_check'
    );

    foreach ($fields as $field) {
      if (isset($_POST[$field])) {
        $posted[$field] = stripslashes(trim($_POST[$field])); 
      } else {
        $posted[$field] = '';
      }
    }
    extract( $posted );

    // Check the e-mail address
    if ('' == $user_email) {
      $errors->add('empty_email', __('<strong>ERROR</strong>: Please type your e-mail address.', 'colabsthemes'));
    } elseif ( !is_email( $user_email ) ) {
      $errors->add('invalid_email', __('<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'colabsthemes'));
      $user_email = '';
    } elseif ( email_exists( $user_email ) )
      $errors->add('email_exists', __('<strong>ERROR</strong>: This email is already registered, please choose another one.', 'colabsthemes'));

    // Check the username
    if ( '' == $user_login )
      $errors->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'colabsthemes'));
    elseif ( !validate_username( $user_login ) ) {
      $errors->add('invalid_username', __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.', 'colabsthemes'));
      $user_login = '';
    } elseif ( username_exists( $user_login ) )
      $errors->add('username_exists', __('<strong>ERROR</strong>: This username is already registered, please choose another one.', 'colabsthemes'));
    
    if ( '' == $user_login ) {
      $user_login = sanitize_user( $user_login );
    }

    // Check password
    if ( get_option('colabs_allow_registration_password') == 'true' ) {
      $user_pass = $your_password;
      if ('' == $your_password) {
        $errors->add('empty_password', __('<strong>ERROR</strong>: Please enter a password.', 'colabsthemes'));
      } elseif ('' == $your_password_confirm) {
        $errors->add('empty_password', __('<strong>ERROR</strong>: Please enter password twice.', 'colabsthemes'));
      } elseif ($posted['your_password'] !== $your_password_confirm) {
        $errors->add('wrong_password', __('<strong>ERROR</strong>: Passwords do not match.', 'colabsthemes'));
      }
    } else {
      $user_pass = wp_generate_password();
    }

    // process the reCaptcha request if it's been enabled
    if ('true' == get_option('colabs_captcha_enable')) {
      if( colabs_is_captcha_verified() ) {

      } else {
        $errors->add('invalid_captcha', __('<strong>ERROR</strong>: You are a bot', 'colabsthemes'));
      }
    }

    // Spam check
    if( $posted['spam_check'] != '' ) {
      $errors->add('spam_check', __('<strong>ERROR</strong>: You are spam, not human! Shoo!.', 'colabsthemes'));
    }

    do_action('register_post', $user_login, $user_email, $errors);
    $errors = apply_filters( 'registration_errors', $errors, $user_login, $user_email );

    // if there are no errors, let's create the user account
    if ( !$errors->get_error_code() ) {

      $user_id = wp_create_user( $user_login, $user_pass, $user_email );

      if ( !$user_id ) {
        $errors->add('registerfail', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'colabsthemes'), get_option('admin_email')));
      } else {

        // send the user a confirmation and their login details
        $this->colabs_sent_email($user_id, $user_pass);

        // check to see if user set password option is enabled
        if ( get_option('colabs_allow_registration_password') == 'true' ) {
          
          // set the WP login cookie
          $secure_cookie = is_ssl() ? true : false;
          wp_set_auth_cookie($user_id, true, $secure_cookie);

          // redirect
          $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : site_url();
          wp_redirect($redirect_to);
          exit;

        } else {

          //create own password option is turned off so show a message that it's been emailed instead
          $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'wp-login.php?checkemail=newpass';
          wp_safe_redirect( $redirect_to );
          exit;

        }
      }

    }

    return $errors;
  }
}

new Lensa_Colabs_Custom_Login();



/**
 * Output ReCaptcha
 */
function colabs_show_recaptcha() {
  // process the reCaptcha request if it's been enabled
  if (get_option('colabs_captcha_enable') == 'true' && get_option('colabs_captcha_public_key')) :
    wp_enqueue_style( 'colabs-recaptcha-css', trailingslashit( get_template_directory_uri() ) . 'includes/lib/recaptcha.css' );
    ?>
    <script type="text/javascript">
    // <![CDATA[
     var RecaptchaOptions = {
        custom_translations : {
            instructions_visual : "<?php _e('Type the two words:','colabsthemes') ?>",
            instructions_audio : "<?php _e('Type what you hear:','colabsthemes') ?>",
            play_again : "<?php _e('Play sound again','colabsthemes') ?>",
            cant_hear_this : "<?php _e('Download sound as MP3','colabsthemes') ?>",
            visual_challenge : "<?php _e('Visual challenge','colabsthemes') ?>",
            audio_challenge : "<?php _e('Audio challenge','colabsthemes') ?>",
            refresh_btn : "<?php _e('Get two new words','colabsthemes') ?>",
            help_btn : "<?php _e('Help','colabsthemes') ?>",
            incorrect_try_again : "<?php _e('Incorrect. Try again.','colabsthemes') ?>",
        },
        theme: "custom",
        custom_theme_widget: 'recaptcha_widget',
        lang: "en",
        tabindex: 5
     };
    // ]]>
    </script>
    <div id="recaptcha_widget" style="display:none" class="recaptcha_widget">
      <div id="recaptcha_image"></div>
      <div class="recaptcha_only_if_incorrect_sol" style="color:red"><?php _e('Incorrect. Please try again.','colabsthemes');?></div>

      <div class="recaptcha_input">
        <label class="recaptcha_only_if_image" for="recaptcha_response_field"><?php _e('Enter the words above:','colabsthemes');?></label>
        <label class="recaptcha_only_if_audio" for="recaptcha_response_field"><?php _e('Enter the numbers you hear:','colabsthemes');?></label>

        <input type="text" id="recaptcha_response_field" name="recaptcha_response_field">
      </div>

      <ul class="recaptcha_options">
        <li>
          <a href="javascript:Recaptcha.reload()">
            <i class="icon-refresh"></i>
            <span class="captcha_hide"><?php _e('Get another CAPTCHA','colabsthemes');?></span>
          </a>
        </li>
        <li class="recaptcha_only_if_image">
          <a href="javascript:Recaptcha.switch_type('audio')">
            <i class="icon-volume-up"></i><span class="captcha_hide"> <?php _e('Get an audio CAPTCHA','colabsthemes');?></span>
          </a>
        </li>
        <li class="recaptcha_only_if_audio">
          <a href="javascript:Recaptcha.switch_type('image')">
            <i class="icon-picture"></i><span class="captcha_hide"> <?php _e('Get an image CAPTCHA','colabsthemes');?></span>
          </a>
        </li>
        <li>
          <a href="javascript:Recaptcha.showhelp()">
            <i class="icon-question-sign"></i><span class="captcha_hide"> <?php _e('Help','colabsthemes');?></span>
          </a>
        </li>
      </ul>
    </div>
    <p>
    <?php
    // let's call in the big boys. It's captcha time.
    require_once (get_template_directory() . '/includes/lib/recaptchalib.php');
    echo recaptcha_get_html(get_option('colabs_captcha_public_key'));
    ?>
    </p>
<?php
  endif;  // end reCaptcha
}


add_action( 'register_form', 'colabs_show_extra_register_fields', 10 );
function colabs_show_extra_register_fields(){
  if (get_option('colabs_allow_registration_password')=='true') :
  wp_enqueue_script('utils');
  wp_enqueue_script('user-profile');
  ?>
  <p>
    <label for="pass1"><?php _e('Password','colabsthemes') ?><br />
    <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" /></label>
  </p>
  <p>
    <label for="pass2"><?php _e('Repeat password','colabsthemes') ?><br />
    <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" /></label>
  </p>
  <div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator'); ?></div>
  <p class="description indicator-hint"><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).','colabsthemes'); ?></p>
  
  <br class="clear" />
  
  <?php
  colabs_show_recaptcha();
  endif;
}

// Check the form for errors
add_action( 'register_post', 'colabs_check_extra_register_fields', 10, 3 );

function colabs_check_extra_register_fields($login, $email, $errors) {
  if ( $_POST['pass1'] !== $_POST['pass2'] ) {
  $errors->add( 'passwords_not_matched', "<strong>ERROR</strong>: Passwords must match" );
  }
  if ( strlen( $_POST['pass1'] ) < 8 ) {
  $errors->add( 'password_too_short', "<strong>ERROR</strong>: Passwords must be at least eight characters long" );
  }
  if ('true' == get_option('colabs_captcha_enable')) {
    require_once (get_template_directory() . '/includes/lib/recaptchalib.php');
    $resp = null;

    // check and make sure the reCaptcha values match
    $resp = recaptcha_check_answer(
      get_option('colabs_captcha_private_key'),
      $_SERVER['REMOTE_ADDR'],
      $_POST['recaptcha_challenge_field'],
      $_POST['recaptcha_response_field']
    );
    
    if (!$resp->is_valid)
        $errors->add('invalid_captcha', __('<strong>ERROR</strong>: The reCaptcha anti-spam response was incorrect.', 'colabsthemes'));
  }
} 

add_action( 'user_register', 'colabs_register_extra_fields', 100 );
function colabs_register_extra_fields( $user_id ){
  if (get_option('colabs_allow_registration_password')=='true') :
    $userdata = array();
    $userdata['ID'] = $user_id;
    if ( $_POST['pass1'] !== '' ) {
    $userdata['user_pass'] = $_POST['pass1'];
    }
    $new_user_id = wp_update_user( $userdata );
  endif;
} 
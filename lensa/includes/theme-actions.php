<?php 
add_filter( 'body_class','colabs_homepage_body_class', 10 );

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'colabs_homepage_body_class' ) ) {
  function colabs_homepage_body_class( $classes ) {
    if( colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php') ):
      if('classic' == get_option('colabs_frontpage_style')):
        $classes[] = 'frontpage-classic-style';
      elseif('scroller' == get_option('colabs_frontpage_style')):
        $classes[] = 'frontpage-scroller-style';
      elseif('gallery' == get_option('colabs_frontpage_style')):
        $classes[] = 'frontpage-gallery-style';  
      else:
        $classes[] = 'frontpage-big-style';
      endif;
    else:
      $classes[] = 'frontpage-big-style';
    endif;  
        
    return apply_filters('colabs_homepage_body_class', $classes);
  }
}

$colabs_options = get_option( 'colabs_options' );
/*-----------------------------------------------------------------------------------*/
/* CoLabs Advertisement - colabs_ad_gen */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'colabs_ad_gen' ) ) {
  function colabs_ad_gen($post_id = 0) { 
     
    global $colabs_options;
    global $post;
    
    if($post_id == 0)$post_id = $post->ID;
    
    //default
    $colabs_ad_single = isset($colabs_options['colabs_ad_single']) ? $colabs_options['colabs_ad_single'] : '';
    $colabs_ad_single_adsense = isset($colabs_options['colabs_ad_single_adsense']) ? $colabs_options['colabs_ad_single_adsense'] : '';
    $colabs_ad_single_image = isset($colabs_options['colabs_ad_single_image']) ? $colabs_options['colabs_ad_single_image'] : '';
    $colabs_ad_single_url = isset($colabs_options['colabs_ad_single_url']) ? $colabs_options['colabs_ad_single_url'] : '';
    $width = 468;
    $height = 60;
    
    //Single Custom Ad
    $colabs_ad_single_custom = get_post_meta($post_id, 'colabs_ad_single', true); //none, general_ad, custom_ad
    
    if( 'custom_ad' == $colabs_ad_single_custom ){
        $colabs_ad_single = 'true';
        $colabs_ad_single_adsense = get_post_meta($post_id, 'colabs_ad_single_adsense', true);
        $colabs_ad_single_image = get_post_meta($post_id, 'colabs_ad_single_image', true);
        $colabs_ad_single_url = get_post_meta($post_id, 'colabs_ad_single_url', true);
    }
    
    if ( 'true' == $colabs_ad_single && 'none' != $colabs_ad_single_custom && ( '' != $colabs_ad_single_adsense || '' != $colabs_ad_single_image ) ) { ?>
      <div id="singlead" class="ads-section">
        <?php if ("" <> $colabs_ad_single_adsense) { 
          echo stripslashes($colabs_ad_single_adsense);  
        } else { ?>
          <a href="<?php echo $colabs_ad_single_url; ?>"><img src="<?php echo $colabs_ad_single_image; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="advert" /></a>
        <?php } ?>        
      </div><!-- /#topad -->
        
    <?php }
  }
}

add_filter( 'body_class','colabs_layout_body_class', 10 );

/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'colabs_layout_body_class' ) ) {
  function colabs_layout_body_class( $classes ) {
  
    $layout = '';
    // Set main layout
    if ( is_singular() ) {
      global $post;
      $layout = get_post_meta($post->ID, 'layout', true); }
    
        
        //set $colabs_option
        if ( '' != $layout ) {
      global $colabs_options;
            $colabs_options['colabs_layout'] = $layout; 
    } else {
            $layout = get_option( 'colabs_layout_settings' );
      if ( '' == $layout ) $layout = "two-col-left";
        }
                    
    // Add classes to body_class() output 
    $classes[] = $layout;

        
    return apply_filters('colabs_layout_body_class', $classes);
  }
}

?>
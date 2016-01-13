<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- colabs_image - Get Image from custom field
    - vt_resize - Resize post thumbnail
    - getVimeoInfo() - get vimeo video thumbnail
    - get_redirect_url() - Get Redirect Link -- get Daily Motion thumbnail
    - colabs_get_youtube_video_image - Get thumbnail from YouTube
- colabs_get_embed - Get Video
- CoLabs Show Page Menu
- Get the style path currently selected
- Get page ID
- Tidy up the image source url
- Show image in RSS feed
- Show analytics code footer
- Show GoSquared code in footer
- Browser detection body_class() output
- Twitter's Blogger.js output for Twitter widgets
- Template Detector
- CoLabs URL shortener
- SEO - colabs_title()
- SEO - Strip slashes from the display of the website/page title
- SEO - colabs_meta()
      - seo_add_custom() - Add Post Custom Settings
- CoLabs Text Trimmer
- Google Webfonts array
- Google Fonts Stylesheet Generator
- Enable Home link in WP Menus
- Buy Themes page
- Detects the Charset of String and Converts it to UTF-8
- WP Login logo
- colabs_pagination()
- colabs_breadcrumbs()
-- colabs_breadcrumbs_get_parents()
-- colabs_breadcrumbs_get_term_parents()
- WordPress Admin Bar-related
-- Disable WordPress Admin Bar
-- Enhancements to the WordPress Admin Bar
- colabs_prepare_category_ids_from_option()
- Move tracking code from footer to header
- Timthumb Update Page and Functions
	- colabsthemes_timthumb_update_page
	- colabs_check_if_thumbs_are_equal
	- colabs_thumb_new_contents
- colabs_get_dynamic_values()
- colabs_get_posts_by_taxonomy()
- colabs_remove_page_template()
- Open Graph Meta Function
- PressTrends Function
-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* colabs_image - Get Image from custom field  */
/*-----------------------------------------------------------------------------------*/

/*
This function retrieves/resizes the image to be used with the post in this order:

1. Image passed through parameter 'src'
2. WP Post Thumbnail (if option activated)
3. Custom field
4. First attached image in post (if option activated)
5. First inline image in post (if option activated)

Resize options (enabled in options panel):
- vt_resize() is used to natively resize #2 and #4
- Thumb.php is used to resize #1, #3, #4 (only if vt_resize is disabled) and #5

Parameters: 
        $key = Custom field key eg. "image"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
        $class = CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
        $quality = Enter a quality between 80-100. Default is 90
        $id = Assign a custom ID, if alternative is required.
        $link = Echo with anchor ( 'src'), without anchor ( 'img') or original image URL ( 'url').
        $repeat = Auto Img Function. Adjust amount of images to return for the post attachments.
        $offset = Auto Img Function. Offset the $repeat with assigned amount of objects.
        $before = Auto Img Function. Add Syntax before image output.
        $after = Auto Img Function. Add Syntax after image output.
        $single = (true/false) Force thumbnail to link to the post instead of the image.
        $force = Force smaller images to not be effected with image width and height dimentions (proportions fix)
        $return = Return results instead of echoing out.
		$src = A parameter that accepts a img url for resizing. (No anchor)
		$meta = Add a custom meta text to the image and anchor of the image.
		$alignment = Crop alignment for thumb.php (l, r, t, b)
		$size = Custom pre-defined size for WP Thumbnail (string)
    $noheight = Don't output the height on img tag (for responsive designs)
*/

if ( !function_exists('colabs_image') ) {
function colabs_image($args = array()) {

	/* ------------------------------------------------------------------------- */
	/* SET VARIABLES */
	/* ------------------------------------------------------------------------- */

	global $post;
	global $colabs_options;
	
	//Defaults
	if (get_option('colabs_custom_field_image')!=''){
		$key = get_option('colabs_custom_field_image');
	}else{
		$key = 'image';
	}
	$width = null;
	$height = null;
	$class = '';
	$quality = 90;
	$id = null;
	$link = 'src';
	$repeat = 1;
	$offset = 0;
	$before = '';
	$after = '';
	$single = true;
	$force = true;
	$return = false;
	$is_auto_image = false;
	$src = '';
	$meta = '';
	$alignment = '';
	$size = '';	
	$play = false;
  $noheight = '';
  
	$alt = '';
	$img_link = '';
	
	$attachment_id = array();
	$attachment_src = array();
		
	if ( !is_array($args) ) 
		parse_str( $args, $args );
	
	extract($args);
	
	// Set Play Icon
  $playicon = '';
	
  // Set post ID
  if ( empty($id) && isset($post) ) {
		$id = $post->ID;
  }

	$thumb_id = esc_html( get_post_meta($id,'_thumbnail_id',true) );
    
	// Set alignment 
	if ( '' == $alignment) 
		$alignment = esc_html( get_post_meta($id, '_image_alignment', true));

	// Get standard sizes
	if ( !$width && !$height ) {
		$width = '100';
		$height = '100';
	}
  
  // Cast $width and $height to integer
	$width = intval( $width );
	$height = intval( $height );
    
	/* ------------------------------------------------------------------------- */
	/* FIND IMAGE TO USE */
	/* ------------------------------------------------------------------------- */

	// When a custom image is sent through
	if ( $src != '' ) { 
		$custom_field = esc_url( $src );
		$link = ($link) ? $link : 'img';
	
	// WP 2.9 Post Thumbnail support	
	} elseif ( 'true' == get_option( 'colabs_post_image_support') AND !empty($thumb_id) ) {

		if ( get_option( 'colabs_pis_resize') == "true") {
      
      if ( 0 == $height ) {
				$img_data = wp_get_attachment_image_src( $thumb_id, array( intval( $width ), 9999 ) );
				$height = $img_data[2];
			}
      
			// Dynamically resize the post thumbnail 
			$vt_crop = get_option( 'colabs_pis_hard_crop' );
			if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
			$vt_image = vt_resize( $thumb_id, '', $width, $height, $vt_crop );
			
			// Set fields for output
			$custom_field = esc_url( $vt_image['url'] );		
			$width = $vt_image['width'];
			$height = $vt_image['height'];
			
		} else {
			// Use predefined size string
			if ( $size ) 
				$thumb_size = $size;
			else 
				$thumb_size = array($width,$height);
				
			$img_link = get_the_post_thumbnail($id,$thumb_size,array( 'class' => 'colabs-image ' . esc_attr( $class )));
		}		
		
	// Grab the image from custom field
	} else {
    	$custom_field = esc_url( get_post_meta($id, $key, true) );
	} 

	// Automatic Image Thumbs - get first image from post attachment
	if ( empty($custom_field) && 'true' == get_option( 'colabs_auto_img') && empty($img_link) && !(is_singular() AND in_the_loop() AND $link == "src") ) { 
	        
        if( $offset >= 1 ) 
			$repeat = $repeat + $offset;
    
        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => $repeat,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC', 
											'orderby' => 'menu_order date')
											);

		// Search for and get the post attachment
		if ( !empty($attachments) ) {  
       
			$counter = -1;
			$size = 'large';
			foreach ( $attachments as $att_id => $attachment ) {            
				$counter++;
				if ( $counter < $offset ) 
					continue;
			
				if ( 'true' == get_option('colabs_post_image_support') AND get_option( 'colabs_pis_resize') == "true") {
				
					// Dynamically resize the post thumbnail 
					$vt_crop = get_option( 'colabs_pis_hard_crop' );
					if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
					$vt_image = vt_resize( $att_id, '', $width, $height, $vt_crop );
					
					// Set fields for output
					$custom_field = esc_url( $vt_image['url'] );		
					$width = $vt_image['width'];
					$height = $vt_image['height'];
				
				} else {

					$src = wp_get_attachment_image_src($att_id, $size, true);
					$custom_field = esc_url( $src[0] );
					$attachment_id[] = $att_id;
					$src_arr[] = $custom_field;
						
				}
				$thumb_id = $att_id;
				$is_auto_image = true;
			}

		// Get the first img tag from content
		} else { 

			$first_img = '';
			$post = get_post($id); 
			ob_start();
			ob_end_clean();
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if ( !empty($matches[1][0]) ) {
				
				// Save Image URL
				$custom_field = esc_url( $matches[1][0] );
				
				// Search for ALT tag
				$output = preg_match_all( '/<img.+alt=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
				if ( !empty($matches[1][0]) ) {
					$alt = esc_attr( $matches[1][0] );
				}
			}

		}
		
	} 
	
	// Check if there is YouTube embed
	if ( empty($custom_field) && empty($img_link) ) {
		$embed = esc_html(get_post_meta($id, "colabs_embed", true));
		if ( $embed ) {
	    $custom_field = colabs_get_video_image($embed);
            
      // Set Play Icon
      if($play == true){ $playicon = '<span class="playicon">Play</span>'; }
            
		}
	}
				
	// Return if there is no attachment or custom field set
	if ( empty($custom_field) && empty($img_link) ) {
		
		// Check if default placeholder image is uploaded
		$placeholder = get_option( 'framework_colabs_default_image' );
		if ( $placeholder && !(is_singular() AND in_the_loop()) ) {
			$custom_field = esc_url( $placeholder );	

			// Resize the placeholder if
			if ( 'true' == get_option('colabs_post_image_support') AND get_option( 'colabs_pis_resize') == "true") {

				// Dynamically resize the post thumbnail 
				$vt_crop = get_option( 'colabs_pis_hard_crop' );
				if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
				$vt_image = vt_resize( '', $placeholder, $width, $height, $vt_crop );
				
				// Set fields for output
				$custom_field = esc_url( $vt_image['url'] );		
				$width = $vt_image['width'];
				$height = $vt_image['height'];
			
			}			
			
		} else {
	      return;
	  }
	
	}
	
	if(empty($src_arr) && empty($img_link)){ $src_arr[] = $custom_field; }
	
	/* ------------------------------------------------------------------------- */
	/* BEGIN OUTPUT */
	/* ------------------------------------------------------------------------- */

    $output = '';
	
    // Set output height and width
    $set_width = ' width="' . esc_attr( $width ) . '" ';
    $set_height = '';

    if ( ! $noheight && 0 < $height )
    	$set_height = ' height="' . esc_attr( $height ) . '" ';
		
	// Set standard class
	if ( $class ) $class = 'colabs-image ' . esc_attr( $class ); else $class = 'colabs-image';

	// Do check to verify if images are smaller then specified.
	if($force == true){ $set_width = ''; $set_height = ''; }

	// WP Post Thumbnail
	if(!empty($img_link) ){
			
		if( 'img' == $link ) {  // Output the image without anchors
			$output .= wp_kses_post( $before ); 
			$output .= $img_link;
			$output .= wp_kses_post( $after );  
			
		} elseif( 'url' == $link ) {  // Output the large image
      
			$src = wp_get_attachment_image_src($thumb_id, 'large', true);
			$custom_field = esc_url( $src[0] );
			$output .= $custom_field;

		} else {  // Default - output with link				

			if ( ( is_single() OR is_page() ) AND $single == false ) {
				$rel = 'rel="lightbox"';
				$href = false;  
			} else { 
				$href = get_permalink($id);
				$rel = '';
			}
			
			$title = 'title="' . esc_attr( get_the_title( $id ) ) .'"';
		
			$output .= wp_kses_post( $before ); 
			if($href == false){
				$output .= $img_link;
			} else {
				$output .= '<a '.$title.' href="' . esc_url( $href ) .'" '.$rel.'>' . $img_link .  '</a>';
			}
			
			$output .= wp_kses_post( $after );  
		}	
	}
	
	// Use thumb.php to resize. Skip if image has been natively resized with vt_resize.
	elseif ( 'true' == get_option( 'colabs_resize') && empty($vt_image['url']) ) { 
		
		foreach($src_arr as $key => $custom_field){
	
			// Clean the image URL
			$href = esc_url( $custom_field ); 		
			$custom_field = cleanSource( $custom_field );

			// Check if WPMU and set correct path AND that image isn't external
			if ( function_exists( 'get_current_site') && strpos($custom_field,"http://") !== 0 ) {
				
        $blog_id = get_current_blog_id();
        
				if ( isset($blog_id) && $blog_id > 0 ) {
					$imageParts = explode( 'files/', $custom_field );
					if ( isset($imageParts[1]) ) 
						$custom_field = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}
			
			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			//Set custom meta 
			if ($meta) {
        $alt = $meta;
        $title = 'title="' . esc_attr( $meta ) . '"';
      } else {
        if ( ( $alt != '' ) || ! ( $alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) ) {
            $alt = esc_attr( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) );
        } else {
            $alt = esc_attr( get_the_title( $quick_id ) );
        }
        $title = 'title="'. esc_attr( get_the_title( $quick_id ) ) .'"';
      }
			
			// Set alignment parameter
			if ($alignment <> '')
				$alignment = '&amp;a='.$alignment;
			
      $get_attach = colabs_get_img_id_from_url($href);
      
      if($get_attach):
      
        // Dynamically resize the post thumbnail 
        $get_attach_crop = ( "true" == get_option( 'colabs_pis_hard_crop' )) ? array('center','top') : false;
        $vt_image = vt_resize( $get_attach, '', $width, $height, $get_attach_crop );
        
        // Set fields for output	
        $img_link = '<img src="'.esc_url($vt_image['url']).'" alt="'.esc_attr( $alt ).'" class="'. esc_attr( stripslashes( $class ) ) .'" '. $set_width . $set_height . ' />';
        
      else:
      
        $img_url = esc_url( get_template_directory_uri() . '/functions/timthumb.php?src=' . $custom_field . '&amp;w=' . $width . '&amp;h=' . $height . '&amp;zc=1&amp;q=' . $quality . $alignment );  
        $img_link = '<img src="'. $img_url . '" alt="'.esc_attr( $alt ).'" class="'. esc_attr( stripslashes( $class ) ) .'" '. $set_width . $set_height . ' />';
			
      endif;
      
			if( 'img' == $link ) {  // Just output the image
				$output .= wp_kses_post( $before ); 
				$output .= $img_link;
				$output .= $playicon;
				$output .= wp_kses_post( $after ); 
				
			} elseif( 'url' == $link ) {  // Output the image without anchors
        
				if(isset($vt_image)&&( '' != $vt_image['url'])){	
          $custom_field = $vt_image['url'];
        }elseif($is_auto_image == true){
					$src = wp_get_attachment_image_src($thumb_id, 'large', true);
					$custom_field = esc_url( $src[0] );
				}
				$output .= $custom_field;
				
			} else {  // Default - output with link				

				if ( ( is_single() OR is_page() ) AND $single == false ) {
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
			
				$output .= wp_kses_post( $before ); 
				$output .= '<a '.$title.' href="' . esc_url( $href ) .'" '.$rel.'>' . $img_link . $playicon . '</a>';
				$output .= wp_kses_post( $after );  
			}
		}
		
	// No dynamic resizing
	} else { 
		foreach($src_arr as $key => $custom_field){
				
			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true AND isset($attachment_id[$key])){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			//Set custom meta 
			if ($meta) { 
				$alt = esc_attr( $meta );
				$title = 'title="'.  esc_attr( $meta ) .'"';
			} else { 
				if ( empty( $alt ) ) $alt = esc_attr( get_post_meta($thumb_id, '_wp_attachment_image_alt', true) );
				$title = 'title="'. esc_attr( get_the_title( $quick_id ) ) .'"';
			}
      
      if ( empty( $alt ) ) {
			    $alt = esc_attr( get_post( $thumb_id )->post_excerpt ); // If not, Use the Caption
			}

			if ( empty( $alt ) ) {
			    $alt = esc_attr( get_post( $thumb_id )->post_title ); // Finally, use the title
			}
      
			$img_link =  '<img src="'. esc_url( $custom_field ) . '" alt="' . esc_attr( $alt ) . '" ' . $set_width . $set_height . $title . ' class="' . esc_attr( stripslashes( $class ) ) . '" />';
		
			if ( 'img' == $link ) {  // Just output the image 
				$output .= wp_kses_post( $before );
				$output .= $img_link;
				$output .= wp_kses_post( $after );  
				
			} elseif( 'url' == $link ) {  // Output the URL to original image
        
				if ( !$vt_image['url'] && $is_auto_image ) { 
					$src = wp_get_attachment_image_src($thumb_id, 'full', true);
					$custom_field = esc_url( $src[0] );
        }elseif ($size){
          $src = wp_get_attachment_image_src($thumb_id, $size, true);
					$custom_field = esc_url( $src[0] );
				}elseif ( $vt_image['url'] && $is_auto_image ){
          $custom_field = $vt_image['url'];
        }
				$output .= $custom_field;

			} else {  // Default - output with link
			
				if ( ( is_single() OR is_page() ) AND $single == false ) { 

					// Link to the large image if single post
					if ( $vt_image['url'] || $is_auto_image ) { 
						$src = wp_get_attachment_image_src($thumb_id, 'full', true);
						$custom_field = esc_url( $src[0] );
					}
					
					$href = $custom_field;
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
				 
				$output .= wp_kses_post( $before );
				$output .= '<a href="' . esc_url( $href ) . '" ' . $rel . ' ' . $title . '>' . $img_link . '</a>';
				$output .= wp_kses_post( $after );   
			}
		}
	}
  
  // Remove no height attribute - IE fix when no height is set
	$output = str_replace( 'height=""', '', $output );
	$output = str_replace( 'height="0"', '', $output );
  
	// Return or echo the output
	if ( $return == TRUE )
		return $output;
	else 
		echo $output; // Done  

}
}

if ( !function_exists('colabs_get_img_id_from_url') ) {
function colabs_get_img_id_from_url( $attachment_url = '' ) {
 
	global $wpdb;
	$attachment_id = false;
 
	// If there is no url, return.
	if ( '' == $attachment_url )
		return;
 
	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

  if ( is_multisite() ) {
    $blog_id = get_current_blog_id();
    if ( isset($blog_id) && $blog_id > 0 ) {
			$upload_dir_paths['baseurl'] = str_replace("/files", "/", $upload_dir_paths['baseurl']);
    }  
  }  
  
	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
    
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
    
		// Remove the upload path base directory from the attachment URL
    if ( is_multisite() ) {
      $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts WHERE wposts.guid = '%s' AND wposts.post_type = 'attachment'", esc_url($attachment_url) ) );
    }else{
      $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', esc_url($attachment_url) );
      $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
    }
 
	}
 
	return $attachment_id;
}
}

/* Get thumbnail from Video Embed code */

/* // Get Vimeo Thumbnail */
function getVimeoInfo($id, $info = 'thumbnail_large') {
  
		$get_mage = wp_remote_retrieve_body(wp_remote_get("http://vimeo.com/api/v2/video/$id.php"));  
		$image = unserialize($get_mage);
		$output = $image[0][$info];
		return $output;
   
};


if (!function_exists( 'colabs_get_video_image')) { 
	function colabs_get_video_image($embed) { 
		$video_thumb = '';
		// YouTube - get the video code if this is an embed code (old embed)
		preg_match( '/youtube\.com\/v\/([\w\-]+)/', $embed, $old_embed);
		
		
		// YouTube - if old embed returned an empty ID, try capuring the ID from the new iframe embed
		if(!isset($old_embed[1])){
			preg_match( '/youtube\.com\/embed\/([\w\-]+)/', $embed, $iframe);
			if(isset($iframe[1])){
				$youtube_img =  $iframe[1];
			}else{
				// YouTube - if it is not an embed code, get the video code from the youtube URL
				preg_match( '/v\=(.+)&/',$embed ,$youtube_url);
				if(isset($youtube_url[1])){
				$youtube_img =  $youtube_url[1];
				}
			}
			if(isset($youtube_img))
				$video_thumb = "http://img.youtube.com/vi/".$youtube_img."/0.jpg"; 
		}
		
		// Vimeo Thumbnail
		if(isset($video_thumb)){
			preg_match( '/vimeo\.com\/video\/([\w\-]+)/', $embed, $vimeo_src);
			if(isset($vimeo_src[1])){
				$vimeo=getVimeoInfo($vimeo_src[1],'thumbnail_large');
				if(isset($vimeo))
					$video_thumb = $vimeo;
			}
		}
		
		// Metacafe Thumbnail
		if(isset($video_thumb)){
			preg_match( '/metacafe\.com\/fplayer\/([\w\-]+)/', $embed, $metacafe);
			if(isset($metacafe[1])){
			$video_thumb ="http://s3.mcstatic.com/thumb/".$metacafe[1].".jpg";
			}
		}
		
		// Dailymotion
		if(isset($video_thumb)){
			preg_match( '/dailymotion\.com\/embed\/video\/([\w\-]+)/', $embed, $daily);
			if(isset($daily[1])){
			$daily_redirect ="http://www.dailymotion.com/thumbnail/video/".$daily[1];
			$uri_api = wp_remote_retrieve_body(wp_remote_get('https://api.dailymotion.com/video/'.$daily[1].'?fields=thumbnail_url',array('sslverify' => false)));
			$uri_api = json_decode( $uri_api );
			$daily_thumb = $uri_api->thumbnail_url;
			$video_thumb = $daily_thumb;
			}
		}
            
		// return whichever thumbnail image you would like to retrieve
		return $video_thumb;		
	}
}


/*-----------------------------------------------------------------------------------*/
/* vt_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( ! function_exists( 'vt_resize' ) ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// Cast $width and $height to integer
		$width = intval( $width );
		$height = intval( $height );

		// this is an attachment, so we have the ID
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
			$file_path = parse_url( esc_url( $img_url ) );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );
		$file_info = wp_parse_args( $file_info, array(
			'dirname' => '',
			'filename' => '',
			'extension' => '',
		) );

		// check if file exists
		if ( !isset( $file_info['dirname'] ) && !isset( $file_info['filename'] ) && !isset( $file_info['extension'] )  )
			return;
		
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
			return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
				return $vt_image;
			}

			// $crop = false or no height set
			if ( $crop == false OR !$height ) {
				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
					return $vt_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];
			
			// Check if GD Library installed
			if ( ! function_exists ( 'imagecreatetruecolor' ) ) {
			    echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
			    return;
			}

			// no cache files - let's finally resize it
			if ( function_exists( 'wp_get_image_editor' ) ) {
				$image = wp_get_image_editor( $file_path );
				if ( ! is_wp_error( $image ) ) {
					$image->resize( $width, $height, $crop );
					$save_data = $image->save();
					if ( isset( $save_data['path'] ) ) $new_img_path = $save_data['path'];
				}
			} else {
				$new_img_path = image_resize( $file_path, $width, $height, $crop );
			}		
			
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $vt_image;
		}

		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);

		return $vt_image;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Depreciated - colabs_get_image - Get Image from custom field */
/*-----------------------------------------------------------------------------------*/

// Depreciated
function colabs_get_image($key = 'image', $width = null, $height = null, $class = "thumbnail", $quality = 90,$id = null,$link = 'src',$repeat = 1,$offset = 0,$before = '', $after = '',$single = false, $force = false, $return = false) {
	// Run new function
	colabs_image( 'key='.$key.'&width='.$width.'&height='.$height.'&class='.$class.'&quality='.$quality.'&id='.$id.'&link='.$link.'&repeat='.$repeat.'&offset='.$offset.'&before='.$before.'&after='.$after.'&single='.$single.'&force='.$force.'&return='.$return );
	return;

}

/*-----------------------------------------------------------------------------------*/
/* colabs_embed - Get Video embed code from custom field */
/*-----------------------------------------------------------------------------------*/

/*
Get Video
This function gets the embed code from the custom field
Parameters: 
$key = Custom field key eg. "embed"
$width = Set width manually without using $type
$height = Set height manually without using $type
$class = Custom class to apply to wrapping div
$id = ID from post to pull custom field from
*/
if ( ! function_exists( 'colabs_embed' ) ) {
function colabs_embed($args) {

	//Defaults
	$key = 'colabs_embed';
	$width = null;
	$height = null;
	$class = 'video';
	$id = null;
  $preserve_dimensions = false;	
	
	if ( ! is_array( $args ) )
		parse_str( $args, $args );

	extract( $args );

  if( empty( $id ) ) {
    global $post;
    $id = $post->ID;
  }

// Cast $width and $height to integer
$width = intval( $width );
$height = intval( $height );

$custom_field = esc_textarea( get_post_meta( $id, $key, true ) );
if ( $custom_field ) :
	$custom_field = html_entity_decode( $custom_field ); // Decode HTML entities.

	// Only run oEmbed checks if we definitely don't have any HTML tags in the field.
	if ( $custom_field == strip_tags( $custom_field ) ) {
		$custom_field = wp_oembed_get( $custom_field );
	}

	// If we definitely don't have a video, get out.
	if ( '' == $custom_field ) return false;

	// Store dimensions that were passed through the arguments.
    $org_width = $width;
    $org_height = $height;

    // Store the dimensions present in the embed code.
    $embed_width = '';
    $embed_height = '';

	$raw_values = explode( ' ', $custom_field );

	if ( 0 < count( $raw_values ) ) {
		foreach ( $raw_values as $raw ) {
			$embed_params = explode( '=', $raw );
			if ( 'width' == $embed_params[0] ) {
			 	$embed_width = preg_replace( '/[^0-9]/', '', $embed_params[1]);
			} elseif ( 'height' == $embed_params[0] ) {
				$embed_height = preg_replace( '/[^0-9]/', '', $embed_params[1]);
			}
		}
	}

    // If we have a width and no height, calculate the height.
    if ( '' == $org_height && '' != $org_width ) {
    	// Store a calculated height ratio.
   	 	$calculated_height = '';

    	$float_width = floatval( $embed_width );
		$float_height = floatval( $embed_height );
		$float_ratio = floatval( $float_height / $float_width );
		$calculated_height = intval( $float_ratio * $width );

		// Set the height.
		$height = $calculated_height;
    }

    // Custom height check (last minute).
    if ( 0 >= intval( $width ) ) $width = intval( ( get_post_meta( $id, 'width', true ) ) );
    if ( 0 >= intval( $height ) ) $height = intval( get_post_meta( $id, 'height', true ) );

    $atts = array( 'width' => $width, 'height' => $height );
    $styles = array();
	$styles_string = '';

	if ( 0 < count( $atts ) ) {
		foreach ( $atts as $k => $v ) {
			$atts[$k] = $k . '="' . esc_attr( $v ) . '"';
			$styles_string .= $k . ':' . intval( $v ) . 'px;';
		}
	}

	if ( '' != $styles_string ) {
		$styles_string = ' style="' . $styles_string . '"';
	}

	$custom_field = stripslashes( $custom_field );
	if ( true != $preserve_dimensions ) {
		$custom_field = preg_replace( '/width="([0-9]*)"/' , $atts['width'], $custom_field );
		$custom_field = preg_replace( '/height="([0-9]*)"/' , $atts['height'], $custom_field );
		$custom_field = str_replace( ' src="', $styles_string . ' src="', $custom_field );
	}

	// Suckerfish menu hack
	$custom_field = str_replace( '<embed ', '<param name="wmode" value="transparent"></param><embed wmode="transparent" ', $custom_field );
	$custom_field = str_replace( '<iframe ', '<iframe wmode="transparent" ', $custom_field );
	$custom_field = str_replace( '" frameborder="', '?wmode=transparent" frameborder="', $custom_field );

	// Find and sanitize video URL. Add "wmode=transparent" to URL.
	$video_url = preg_match( '/src=["\']?([^"\' ]*)["\' ]/is', $custom_field, $matches );
	if ( isset( $matches[1] ) ) {
		$custom_field = str_replace( $matches[0], 'src="' . esc_url( add_query_arg( 'wmode', 'transparent', $matches[1] ) ) . '"', $custom_field );
	}

	$output = '';
    $output .= '<div class="'. esc_attr( $class ) .'">' . $custom_field . '</div>';

	return apply_filters( 'colabs_embed', $output );
else :
	return false;
endif;
}
}

/*-----------------------------------------------------------------------------------*/
/* Depreciated - colabs_get_embed - Get Video embed code from custom field */
/*-----------------------------------------------------------------------------------*/

// Depreciated
function colabs_get_embed($key = 'colabs_embed', $width, $height, $class = 'video', $id = null, $preserve_dimensions = false) {
	// Run new function
	return colabs_embed( 'key='.$key.'&width='.$width . '&height=' . $height . '&class=' . $class . '&id=' . $id . '&preserve_dimensions=' . $preserve_dimensions );

}

/*-----------------------------------------------------------------------------------*/
/* CoLabs Show Page Menu */
/*-----------------------------------------------------------------------------------*/

// Show menu in header.php
// Exlude the pages from the slider
function colabs_show_pagemenu( $exclude="" ) {
    // Split the featured pages from the options, and put in an array
    if ( get_option( 'colabs_ex_featpages') ) {
        $menupages = get_option( 'colabs_featpages' );
        $exclude = $menupages . ',' . $exclude;
    }
    
    $pages = wp_list_pages( 'sort_column=menu_order&title_li=&echo=0&depth=1&exclude='.$exclude);
    $pages = preg_replace( '%<a ([^>]+)>%U','<a $1><span>', $pages);
    $pages = str_replace( '</a>','</span></a>', $pages);
    echo $pages;
}

/*-----------------------------------------------------------------------------------*/
/* Get the style path currently selected */
/*-----------------------------------------------------------------------------------*/

function colabs_style_path() {
	$return = '';
	
	$style = $_REQUEST['style'];
	
	// Sanitize request input.
	$style = esc_attr( strtolower( trim( strip_tags( $style ) ) ) );
	
	if ( $style != '' ) {	
		$style_path = $style;
	} else {
		$stylesheet = esc_attr(get_option( 'colabs_alt_stylesheet' ));
    
		// Prevent against an empty return to $stylesheet.
		if ( '' == $stylesheet ) {
			$stylesheet = 'default.css';
		} // End IF Statement
		
		$style_path = str_replace( '.css', '', $stylesheet );	
	} // End IF Statement
	
	if ( 'default' == $style_path ) {	
		$return = 'images';
	} else {
		$return = 'styles/' . $style_path;
	} // End IF Statement
	
	echo $return;
	
} // End colabs_style_path()


/*-----------------------------------------------------------------------------------*/
/* Get page ID */
/*-----------------------------------------------------------------------------------*/
function get_page_id($page_slug){
	$page_id = get_page_by_path($page_slug);
    if ($page_id) {
        return $page_id->ID;
    } else {
        return null;
    }    
    
}

/*-----------------------------------------------------------------------------------*/
/* Tidy up the image source url */
/*-----------------------------------------------------------------------------------*/
function cleanSource($src) {

	// remove slash from start of string
	if(strpos($src, "/") == 0) {
		$src = substr($src, -(strlen($src) - 1));
	}

	// Check if same domain so it doesn't strip external sites
	$host = str_replace( 'www.', '', $_SERVER['HTTP_HOST']);
	if ( !strpos($src,$host) )
		return $src;


	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $host . "/i";
	$src = preg_replace ($regex, '', $src);
	$src = htmlentities ($src);
    
    // remove slash from start of string
    if (strpos($src, '/') === 0) {
        $src = substr ($src, -(strlen($src) - 1));
    }
	
	return $src;
}

/*-----------------------------------------------------------------------------------*/
/* Show image in RSS feed */
/* Original code by Justin Tadlock http://justintadlock.com */
/*-----------------------------------------------------------------------------------*/
if (get_option('colabs_rss_thumb') == "true"){
	add_filter('the_excerpt_rss', 'add_image_RSS');
	add_filter('the_content_feed', 'add_image_RSS');
}
function add_image_RSS( $content ) {
	
	if ( ! is_feed() ) return $content;
  global $post, $id;

	// Get the "image" from custom field
	$image = colabs_image('return=true&link=url');
	$image_width = '175';

	// If there's an image, display the image with the content
	if($image != '') {
		$content = '<p style="float: right; margin: 0 0 10px 15px; width:' . esc_attr( intval( $image_width ) ) . 'px; height: auto;">
		<img src="' . esc_url( $image ) . '" width="' . esc_attr( intval( $image_width ) ) . '" style="max-width: 100%; height: auto;" />
		</p>' . $content;
		return $content;
	} 

	// If there's not an image, just display the content
	else {
		$content = $content;
		return $content;
	}
} 

/*-----------------------------------------------------------------------------------*/
/* Show analytics code in footer */
/*-----------------------------------------------------------------------------------*/
function colabs_analytics(){
	$output = get_option( 'colabs_google_analytics' );
	if ( $output != "" ) 
		echo stripslashes($output) . "\n";
}
add_action( 'wp_footer','colabs_analytics' );


/*-----------------------------------------------------------------------------------*/
/* Show GoSquared code in footer */
/*-----------------------------------------------------------------------------------*/
function colabs_gosquared(){
	$output = get_option( 'colabs_gosquared_id' );
	if ( $output <> "" ){
?>
<!-- GoSquared Analytics -->
<script type="text/javascript">
    var GoSquared={};
    GoSquared.acct = "<?php echo stripslashes($output); ?>";
    (function(w){
        function gs(){
            w._gstc_lt=+(new Date); var d=document;
            var g = d.createElement("script"); g.type = "text/javascript"; g.async = true; g.src = "//d1l6p2sc9645hc.cloudfront.net/tracker.js";
            var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(g, s);
        }
        w.addEventListener?w.addEventListener("load",gs,false):w.attachEvent("onload",gs);
    })(window);
</script>
<?php
    echo "\n";
    }
}
add_action( 'wp_footer','colabs_gosquared' );

/*-----------------------------------------------------------------------------------*/
/* Browser detection body_class() output */
/*-----------------------------------------------------------------------------------*/
add_filter('admin_body_class', 'admin_browser_body_class' );
function admin_browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes .= 'lynx';
	elseif($is_gecko) $classes .= 'gecko';
	elseif($is_opera) $classes .= 'opera';
	elseif($is_NS4) $classes .= 'ns4';
	elseif($is_safari) $classes .= 'safari';
	elseif($is_chrome) $classes .= 'chrome';
	elseif($is_IE) {
		$browser = $_SERVER['HTTP_USER_AGENT']; 
		$browser = substr( "$browser", 25, 8); 
		if ($browser == "MSIE 7.0"  )
			$classes .= 'ie7';
		elseif ($browser == "MSIE 6.0" )
			$classes .= 'ie6'; 
		elseif ($browser == "MSIE 8.0" )
			$classes .= 'ie8'; 
		else	
			$classes .= 'ie';
	}
	else $classes .= 'unknown';

	if($is_iphone) $classes .= 'iphone';

	return $classes;
}

add_filter( 'body_class','browser_body_class' );
function browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) {
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$browser = substr( "$browser", 25, 8);
		if ($browser == "MSIE 7.0"  ) {
			$classes[] = 'ie7';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 6.0" ) {
			$classes[] = 'ie6';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 8.0" ) {
			$classes[] = 'ie8';
			$classes[] = 'ie';
		} elseif ($browser == "MSIE 9.0" ) {
			$classes[] = 'ie9';
			$classes[] = 'ie';
		} else {
			$classes[] = 'ie';
		}
	}
	else $classes[] = 'unknown';

	if( $is_iphone ) $classes[] = 'iphone';

	// Alternative style body class.
	$style = get_option( 'colabs_alt_stylesheet', 'default' );
	$style = str_replace( '.css', '', $style );
	if ( '' != $style ) {
		$classes[] = 'alt-style-' . esc_attr( $style );
	}
	return $classes;
}

/*-----------------------------------------------------------------------------------*/
/* Twitter's Blogger.js output for Twitter widgets */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'colabs_twitter_script') ) {
	function colabs_twitter_script($unique_id,$username,$limit,$exclude_replies='') {
	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	
	    function twitterCallback2(twitters) {
	    
	      var statusHTML = [];
	      for (var i=0; i<twitters.length; i++){
	        var username = twitters[i].user.screen_name;
	        var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
	          return '<a href="'+url+'">'+url+'</a>';
	        }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
	          return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
	        });
        	statusHTML.push( '\
		        <li>\
		        	<span class="content">'+status+'\
		        	<a style="font-size:85%" class="time" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id_str+'">('+relative_time(twitters[i].created_at)+')</a></span>\
		        </li>' );
	      }
	      document.getElementById( 'twitter_update_list_<?php echo $unique_id; ?>').innerHTML = statusHTML.join( '' );
	      
	      var template = '\
	      	<span class="author">\
	      		<img src="<%= user.profile_image_url %>">&nbsp;\
	      		<a class="username" href="http://twitter.com/<%= user.screen_name %>">\
	      			<strong><%= user.screen_name %></strong>\
	      		</a>\
	      	</span>';
	      if( typeof _ !== 'undefined' ) {
	      	jQuery( _.template( template, { user: twitters[0].user } )).insertAfter('.widget_colabs_twitter ul');
	      }
	    }
	    
	    function relative_time(time_value) {
	      var values = time_value.split( " " );
	      time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
	      var parsed_date = Date.parse(time_value);
	      var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
	      var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
	      delta = delta + (relative_to.getTimezoneOffset() * 60);
	    
	      if (delta < 60) {
	        return 'less than a minute ago';
	      } else if(delta < 120) {
	        return 'about a minute ago';
	      } else if(delta < (60*60)) {
	        return (parseInt(delta / 60)).toString() + ' minutes ago';
	      } else if(delta < (120*60)) {
	        return 'about an hour ago';
	      } else if(delta < (24*60*60)) {
	        return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
	      } else if(delta < (48*60*60)) {
	        return '1 day ago';
	      } else {
	        return (parseInt(delta / 86400)).toString() + ' days ago';
	      }
	    }
	//-->!]]>
	</script>
    <?php
        if ( $exclude_replies != '' ){ $exclude_replies_str = '&amp;exclude_replies='.$exclude_replies; } else { $exclude_replies_str = ''; }
    ?>
	<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/<?php echo $username; ?>.json?callback=twitterCallback2&amp;count=<?php echo $limit; ?>&amp;include_rts=t<?php echo $exclude_replies_str; ?>"></script>
	<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Template Detector */
/*-----------------------------------------------------------------------------------*/
function colabs_active_template($filename = null){

	if(isset($filename)){
		
		global $wpdb;
		$query = "SELECT *,count(*) AS used FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = '$filename' GROUP BY meta_value";
		$results = $wpdb->get_row($wpdb->prepare($query),'ARRAY_A' ); // Select thrid coloumn accross
				
		if(empty($results))
			return false;
			
		$post_id = $results['post_id'];
		$trash = get_post_status($post_id); // Check for trash
		
		if($trash != 'trash')
			return true;
		else
	 		return false;
	
	} else {
		return false; // No $filename argument was set
	}

}

/*-----------------------------------------------------------------------------------*/
/* CoLabs URL shortener */
/*-----------------------------------------------------------------------------------*/

function colabs_short_url($url) {
	$service = get_option( 'colabs_url_shorten' );
	$bitlyapilogin = get_option( 'colabs_bitly_api_login' );
	$bitlyapikey = get_option( 'colabs_bitly_api_key' );
	if (isset($service)) {
		switch ($service) 
		{
    		case 'TinyURL':
    			$shorturl = getTinyUrl($url);
    			break;
    		case 'Bit.ly':
    			if (isset($bitlyapilogin) && isset($bitlyapikey) && ($bitlyapilogin != '') && ($bitlyapikey != '')) {
    				$shorturl = make_bitly_url($url,$bitlyapilogin,$bitlyapikey,'json' );
    			}
    			else {
    				$shorturl = getTinyUrl($url);
    			}
    			break;
    		case 'Off':
    			$shorturl = $url;
    			break;
    		default:
    			$shorturl = $url;
    			break;
    	}
	}
	else {
		$shorturl = $url;
	}
	return $shorturl;
}

//TinyURL
function getTinyUrl($url) {
	$geturl = wp_remote_get( "http://tinyurl.com/api-create.php?url=".$url);
	$tinyurl = $geturl['body'];
	return $tinyurl;
}

//Bit.ly
function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
	//create the URL
	$bitly = wp_remote_get('http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format);
	
	//get the url
	$response = $bitly['body'];
	
	//parse depending on desired format
	if('json' == strtolower($format))
	{
		$json = @json_decode($response,true);
		return $json['results'][$url]['shortUrl'];
	}
	else //xml
	{
		$xml = simplexml_load_string($response);
		return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
	}
}


/*-----------------------------------------------------------------------------------*/
/* colabs_title() */
/*-----------------------------------------------------------------------------------*/

function colabs_title(){

	global $post;
	$layout = ''; 
	
	// Setup the variable that will, ultimately, hold the title value.
	$title = '';
	$sep = get_option( 'seo_colabs_separator' );	
	if(empty($sep)) { $sep = " | ";} else { $sep = ' ' . $sep . ' ';}
	
	//Taxonomy Details WP 3.0 only
	if ( function_exists( 'get_taxonomies') ) :
		global $wp_query; 
		$taxonomy_obj = $wp_query->get_queried_object();
		if ( ! empty( $taxonomy_obj->name ) && function_exists( 'is_post_type_archive' ) && ! is_post_type_archive() ) :
			$taxonomy_nice_name = $taxonomy_obj->name;
			$term_id = $taxonomy_obj->term_taxonomy_id;
			$taxonomy_short_name = $taxonomy_obj->taxonomy;
			$taxonomy_top_level_items = get_taxonomies(array( 'name' => $taxonomy_short_name), 'objects' );
			$taxonomy_top_level_item = $taxonomy_top_level_items[$taxonomy_short_name]->label;
		elseif ( ! empty( $taxonomy_obj->name ) && function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) :
			$archive_name = $taxonomy_obj->label;
		endif;
	endif;
	
	//3rd Party Plugins
	$use_third_party_data = false;
	if('true' == get_option( 'seo_colabs_use_third_party_data')){
		$use_third_party_data = true;
	}
		
	if(
		(
			class_exists( 'All_in_One_SEO_Pack') || 
			class_exists( 'Headspace_Plugin') || 
			class_exists( 'WPSEO_Admin' ) || 
			class_exists( 'WPSEO_Frontend' )
    	)
	&& 
		( $use_third_party_data == true ) ) { wp_title($sep); return; }

	$use_wp_title = get_option( 'seo_colabs_wp_title' );
	$home_layout = get_option( 'seo_colabs_home_layout' );
	$single_layout = get_option( 'seo_colabs_single_layout' );
	$page_layout = get_option( 'seo_colabs_page_layout' );
	$archive_layout = get_option( 'seo_colabs_archive_layout' );

	global $wp_locale;
	$m = get_query_var('m');
	$year = get_query_var('year');
    $monthnum = get_query_var('monthnum');
    $day = get_query_var('day');
    $search = get_query_var('s');

	$wptitle = '';

    $t_sep = $sep; // Temporary separator, for accurate flipping, if necessary
 
    // If there is a post
    if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
        $wptitle = single_post_title( '', false );
    }
 
    // If there's a post type archive
    if ( is_post_type_archive() ) {
        $post_type = get_query_var( 'post_type' );
        if ( is_array( $post_type ) )
            $post_type = reset( $post_type );
        $post_type_object = get_post_type_object( $post_type );
        if ( ! $post_type_object->has_archive )
            $wptitle = post_type_archive_title( '', false );
    }
 
    // If there's a category or tag
    if ( is_category() || is_tag() ) {
        $wptitle = single_term_title( '', false );
    }
 
    // If there's a taxonomy
    if ( is_tax() ) {
        $term = get_queried_object();
        if ( $term ) {
            $tax = get_taxonomy( $term->taxonomy );
            $wptitle = single_term_title( $tax->labels->name . $t_sep, false );
        }
    }
 
    // If there's an author
    if ( is_author() && ! is_post_type_archive() ) {
        $author = get_queried_object();
        if ( $author )
            $wptitle = $author->display_name;
    }
 
    // Post type archives with has_archive should override terms.
    if ( is_post_type_archive() && $post_type_object->has_archive )
        $wptitle = post_type_archive_title( '', false );
 
    // If there's a month
    if ( is_archive() && !empty($m) ) {
        $my_year = substr($m, 0, 4);
        $my_month = $wp_locale->get_month(substr($m, 4, 2));
        $my_day = intval(substr($m, 6, 2));
        $wptitle = $my_year . ( $my_month ? $t_sep . $my_month : '' ) . ( $my_day ? $t_sep . $my_day : '' );
    }
 
    // If there's a year
    if ( is_archive() && !empty($year) ) {
        $wptitle = $year;
        if ( !empty($monthnum) )
            $wptitle .= $t_sep . $wp_locale->get_month($monthnum);
        if ( !empty($day) )
            $wptitle .= $t_sep . zeroise($day, 2);
    }
 
    // If it's a search
    if ( is_search() ) {
        /* translators: 1: separator, 2: search phrase */
        $wptitle = sprintf(__('Search Results %1$s %2$s'), $t_sep, strip_tags($search));
    }
 
    // If it's a 404 page
    if ( is_404() ) {
        $wptitle = __('Page not found');
    }
	
	
	$output = '';
	if('true' == $use_wp_title){
		
		if(is_home() OR is_front_page()){
			switch ($home_layout){
				case 'a': $output = get_bloginfo( 'name') . $sep . get_bloginfo( 'description' ); 
				break;
				case 'b': $output = get_bloginfo( 'name' ); 
				break;
				case 'c': $output = get_bloginfo( 'description' ); 
				break;
				case 'd': $output = get_bloginfo( 'description' ) . $sep . get_bloginfo( 'name'); 
				break;
				}
			if(is_paged()){
				$paged_var = get_query_var( 'paged' );
				if('after' == get_option( 'seo_colabs_paged_var_pos')){
				
					$output .= $sep . get_option( 'seo_colabs_paged_var') . ' ' . $paged_var;

				} else {
									
					$output = get_option( 'seo_colabs_paged_var') . ' ' . $paged_var . $sep . $output;

				}
				
			}
			$output = stripslashes($output);
			echo $output;
		}
		else {
		if (is_single()) { $layout = $single_layout; }
		elseif  (is_page()) { $layout = $page_layout; }
		elseif  (is_archive()) { $layout = $archive_layout; }
		elseif  (is_tax()) { $layout = $archive_layout; }
		elseif  (is_search()) { $layout = 'search'; }
		elseif  (is_404()) { $layout = $single_layout; }
		
		
		
		//Check if there is a custom value added to post meta
		$colabsseo_title = get_post_meta($post->ID,'seo_title',true); // CoLabsSEO
		$aio_title = get_post_meta($post->ID,'_aioseop_title',true); // All-in-One SEO Pack
		$headspace_title = get_post_meta($post->ID,'_headspace_page_title',true); // Headspace SEO
		$wpseo_title = get_post_meta( $post->ID,'_yoast_wpseo_title', true ); // WordPress SEO
		
		if( get_option( 'seo_colabs_wp_custom_field_title') != 'true' && is_singular() ) {
			if( ! empty($colabsseo_title ) ){
				$layout = 'colabsseo';
			} elseif(!empty($aio_title) AND $use_third_party_data) {
				$layout = 'aioseo';
			} elseif(!empty($headspace_title) AND $use_third_party_data) {
				$layout = 'headspace';
			} elseif(!empty($wpseo_title) AND $use_third_party_data) {
				$layout = 'wpseo';
			}
		}

			switch ( $layout ) {
				case 'a': $output = $wptitle . $sep . get_bloginfo( 'name' );
				break;
				case 'b': $output = $wptitle;
				break;
				case 'c': $output = get_bloginfo( 'name') . $sep . $wptitle;
				break;
				case 'd': $output = $wptitle . $sep . get_bloginfo( 'description' );
				break;
				case 'e': $output = get_bloginfo( 'name') . $sep . $wptitle . $sep . get_bloginfo( 'description' );
				break;
				case 'f': $output = $wptitle . $sep . get_bloginfo( 'name'). $sep . get_bloginfo( 'description' );
				break;
				case 'search':  $output = get_bloginfo( 'name') . $sep . $wptitle; // Search is hardcoded
				break;
				case 'colabsseo':  $output = $colabsseo_title; // CoLabsSEO Title
				break;
				case 'aioseo':  $output = $aio_title; // All-in-One SEO Pack Title
				break;
				case 'headspace':  $output = $headspace_title; // Headspace Title
				break;
				case 'wpseo':  $output = $wpseo_title; // WordPress SEO Title
				break;
			}
			if(is_paged()){
				$paged_var = get_query_var( 'paged' );
				if('after' == get_option( 'seo_colabs_paged_var_pos')){
					$output .= $sep . get_option( 'seo_colabs_paged_var') . ' ' . $paged_var;
				} else {
					$output = get_option( 'seo_colabs_paged_var') . ' ' . $paged_var . $sep . $output;
				}
			}
			$output = stripslashes($output);
			
			if(empty($output)) {
				$title = wp_title( '&raquo;', false );
			} else {
				$title = $output;
			}
			
		}
	}
	else {

		if ( is_home() ) { $title = get_bloginfo( 'name') . $sep . get_bloginfo( 'description' ); } 
		elseif ( is_search() ) { $title = get_bloginfo( 'name') . $sep . __( 'Search Results', 'colabsthemes' );  }  
		elseif ( is_author() ) { $title = get_bloginfo( 'name') . $sep . __( 'Author Archives', 'colabsthemes' );  }  
		elseif ( is_single() ) { $title = $wptitle . $sep . get_bloginfo( 'name' );  }
		elseif ( is_page() ) { $title = get_bloginfo( 'name' ) . $sep . $wptitle;  }
		elseif ( is_category() ) { $title = get_bloginfo( 'name') . $sep . __( 'Category Archive', 'colabsthemes' ) . $sep . single_cat_title( '',false );  }
		elseif ( is_tax() ) { $title = get_bloginfo( 'name') . $sep . $taxonomy_top_level_item . __( ' Archive', 'colabsthemes' ) . $sep . $taxonomy_nice_name;  }   
		elseif ( is_day() ) { $title = get_bloginfo( 'name') . $sep . __( 'Daily Archive', 'colabsthemes' ) . $sep . get_the_time( 'jS F, Y' );  }
		elseif ( is_month() ) { $title = get_bloginfo( 'name') . $sep . __( 'Monthly Archive', 'colabsthemes' ) . $sep . get_the_time( 'F' );  }
		elseif ( is_year() ) { $title = get_bloginfo( 'name') . $sep . __( 'Yearly Archive', 'colabsthemes' ) . $sep . get_the_time( 'Y' );  }
		elseif ( is_tag() ) {  $title = get_bloginfo( 'name') . $sep . __( 'Tag Archive', 'colabsthemes' ) . $sep . single_tag_title( '',false); }
		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) { $title = get_bloginfo( 'name') . $sep . $archive_name . __( ' Archive', 'colabsthemes' );  }
	}
	
	// Allow child themes/plugins to filter the title value.
	$title = apply_filters( 'colabs_title', $title, $sep );
	
	// Display the formatted title.
	echo $title;
}

/*-----------------------------------------------------------------------------------*/
/* Do nothing for colabs_meta() and change with colabs_add_meta() */
/*-----------------------------------------------------------------------------------*/
function colabs_meta(){
  //Nothing
}

add_action( 'colabsthemes_wp_head_before', 'colabs_add_meta' );
if ( !function_exists( 'colabs_add_meta') ) {
function colabs_add_meta(){
  
		global $post, $wpdb;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		
		// Basic Output
		echo '<meta http-equiv="Content-Type" content="'. get_bloginfo( 'html_type') .'; charset='. get_bloginfo( 'charset') .'" />' . "\n";
		
		// Under SETTIGNS > PRIVACY in the WordPress backend
		if ( get_option( 'blog_public') == 0 ) { return; }
		
		//3rd Party Plugins
		$use_third_party_data = false;
		if('true' == get_option( 'seo_colabs_use_third_party_data')){
			$use_third_party_data = true;
		}
		
		if(
			(
			class_exists( 'All_in_One_SEO_Pack') || 
    		class_exists( 'Headspace_Plugin') || 
    		class_exists( 'WPSEO_Admin' ) || 
    		class_exists( 'WPSEO_Frontend' )
    		)
		&& ( $use_third_party_data == true ) ) { return; }
		
		// Robots
		if (
			! class_exists( 'All_in_One_SEO_Pack') && 
    		! class_exists( 'Headspace_Plugin') && 
    		! class_exists( 'WPSEO_Admin' ) && 
    		! class_exists( 'WPSEO_Frontend' )
		) {
			$index = 'index';
			$follow = 'nofollow';
			
			if ( is_category() && get_option( 'seo_colabs_meta_indexing_category') != 'true' ) { $index = 'noindex'; }  
			elseif ( is_tag() && get_option( 'seo_colabs_meta_indexing_tag') != 'true') { $index = 'noindex'; }
			elseif ( is_search() && get_option( 'seo_colabs_meta_indexing_search') != 'true' ) { $index = 'noindex'; }  
			elseif ( is_author() && get_option( 'seo_colabs_meta_indexing_author') != 'true') { $index = 'noindex'; }  
			elseif ( is_date() && get_option( 'seo_colabs_meta_indexing_date') != 'true') { $index = 'noindex'; }
			
			// Set default to follow			
			if ( 'true' == get_option( 'seo_colabs_meta_single_follow') )
				$follow = 'follow';  
	
			// Set individual post/page to follow/unfollow
			if ( is_singular() ) {
				if ( 'follow' == $follow AND 'true' == get_post_meta($post->ID,'seo_follow',true)) 
					$follow = 'nofollow';  
				elseif ( 'nofollow' == $follow AND 'true' == get_post_meta($post->ID,'seo_follow',true)) 
					$follow = 'follow';  
			}							
						
			if(is_singular() && 'true' == get_post_meta($post->ID,'seo_noindex',true)) { $index = 'noindex';  }
			
			echo '<meta name="robots" content="'. $index .', '. $follow .'" />' . "\n";
		}
		
		/* Description */
		$description = '';
		
		$home_desc_option = get_option( 'seo_colabs_meta_home_desc' );
		$singular_desc_option = get_option( 'seo_colabs_meta_single_desc' );
		
		//Check if there is a custom value added to post meta
    if($post){
      $colabsseo_desc = get_post_meta($post->ID,'seo_description',true); // CoLabsSEO
      $aio_desc = get_post_meta($post->ID,'_aioseop_description',true); // All-in-One SEO Pack
      $headspace_desc = get_post_meta($post->ID,'_headspace_description',true); // Headspace SEO
      $wpseo_desc = get_post_meta($post->ID,'_yoast_wpseo_metadesc',true); // WordPress SEO
    }
    
		//Singular setup
		if(!empty($aio_desc) AND $use_third_party_data) {
			$singular_desc_option = 'aioseo';
		} elseif(!empty($headspace_desc) AND $use_third_party_data) {
			$singular_desc_option = 'headspace';
		} elseif( ! empty( $wpseo_desc ) AND $use_third_party_data) {
			$singular_desc_option = 'wpseo';
		}

		
		if(is_home() OR is_front_page()){
			switch($home_desc_option){
				case 'a': $description = '';
				break;
				case 'b': $description = get_bloginfo( 'description' );
				break;
				case 'c': $description = get_option( 'seo_colabs_meta_home_desc_custom' );
				break;
			}
		}
		elseif(is_singular()){
			
			switch($singular_desc_option){
				case 'a': $description = '';
				break;
				case 'b': $description = trim(strip_tags($colabsseo_desc));
				break; 
				case 'c': 
	
    			if(is_single()){
							$posts = get_post( $post_id );
					}elseif(is_page()){
							$posts = get_post( $post_id );
					}
					$post_content =  $posts->post_excerpt;
					if(empty($post_content)){
					$post_content = $posts->post_content;
					}
					
					$post_content = esc_attr( strip_tags( strip_shortcodes( $post_content ) ) );
					
					$description = colabs_text_trim($post_content,30);
					
				break;
				case 'aioseo':  $description = $aio_desc; // All-in-One Description
				break;
				case 'headspace':  $description = $headspace_desc; // Headspace Description
				break;
				case 'wpseo':  $description = $wpseo_desc; // WordPress SEO Description
				break;
				
			}			
		}
		
		if(empty($description) AND 'true' == get_option( 'seo_colabs_meta_single_desc_sitewide')){
			$description = get_option( 'seo_colabs_meta_single_desc_custom' );
		}
		
		
		// $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8' ); // Replaced with line below to accommodate special characters. // 2010-11-15.
		$description = esc_attr( $description );
		$description = stripslashes($description);
		
		// Faux-htmlentities using an array of key => value pairs.
		// TO DO: Clean-up and move to a re-usable function.
		$faux_htmlentities = array(
								'& ' => '&amp; ', 
								'<' => '&lt;', 
								'>' => '&gt;'
							 );
		
		foreach ( $faux_htmlentities as $old => $new ) {
		
			$description = str_replace( $old, $new, $description );
		
		} // End FOREACH Loop
		
		if(!empty($description)){
			echo '<meta name="description" content="'.$description.'" />' . "\n";
		}
		
		/* Keywords */
		$keywords = '';
		
		$home_key_option = get_option( 'seo_colabs_meta_home_key' );
		$singular_key_option = get_option( 'seo_colabs_meta_single_key' );
		
		//Check if there is a custom value added to post meta
    if($post){
      $colabsseo_keywords = get_post_meta($post->ID,'seo_keywords',true); // CoLabsSEO
      $aio_keywords = get_post_meta($post->ID,'_aioseop_keywords',true); // All-in-One SEO Pack
      $headspace_keywords = get_post_meta($post->ID,'_headspace_keywords',true); // Headspace SEO
      $wpseo_keywords = get_post_meta($post->ID,'_yoast_wpseo_focuskw',true); // WordPress SEO
		}
    
		//Singular setup
		
		if(!empty($aio_keywords) AND $use_third_party_data) {
			$singular_key_option = 'aioseo';
		} elseif(!empty($headspace_keywords) AND $use_third_party_data) {
			$singular_key_option = 'headspace';
		} elseif( ! empty( $wpseo_keywords ) AND $use_third_party_data) {
			$singular_key_option = 'wpseo';
		}	
			
		if(is_home() OR is_front_page()){
			switch($home_key_option){
				case 'a': $keywords = '';
				break;
				case 'c': $keywords = get_option( 'seo_colabs_meta_home_key_custom' );
				break;
			}
		}
		elseif(is_singular()){
			
			switch($singular_key_option){
				case 'a': $keywords = '';
				break;
				case 'b': $keywords = $colabsseo_keywords;
				break;
				case 'c': 
					
					$the_keywords = array(); 
					//Tags
					if(get_the_tags($post->ID)){ 
						foreach(get_the_tags($post->ID) as $tag) {
							$tag_name = $tag->name; 
							$the_keywords[] = strtolower($tag_name);
						}
					}
					//Cats
					if(get_the_category($post->ID)){ 
						foreach(get_the_category($post->ID) as $cat) {
							$cat_name = $cat->name; 
							$the_keywords[] = strtolower($cat_name);
						}
					}
					//Other Taxonomies
					$all_taxonomies = get_taxonomies();
					$addon_taxonomies = array();
					if(!empty($all_taxonomies)){
						foreach($all_taxonomies as $key => $taxonomies){
							if(	$taxonomies != 'category' AND 
								$taxonomies != 'post_tag' AND 
								$taxonomies != 'nav_menu' AND
								$taxonomies != 'link_category'){
								$addon_taxonomies[] = $taxonomies;
							}
						}
					}
					$addon_terms = array();
					if(!empty($addon_taxonomies)){
						foreach($addon_taxonomies as $taxonomies){
							$addon_terms[] = get_the_terms($post->ID, $taxonomies);
						}
					}
					if(!empty($addon_terms)){
						 foreach($addon_terms as $addon){
						 	if(!empty($addon)){
						 		foreach($addon as $term){
						 			$the_keywords[] = strtolower($term->name);
						 		}
						 	}
						 }
					}
					$keywords = implode( ",",$the_keywords);
				break;
				case 'aioseo':  $keywords = $aio_keywords; // All-in-One Title
				break;
				case 'headspace':  $keywords = $headspace_keywords; // Headspace Title
				break;
				case 'wpseo':  $keywords = $wpseo_keywords; // Headspace Title
				break;
				}
		}
		
		if(empty($keywords) AND 'true' == get_option( 'seo_colabs_meta_single_key_sitewide')){
			$keywords = get_option( 'seo_colabs_meta_single_key_custom' );
		}
		
		$keywords = htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8' );
		$keywords = stripslashes($keywords);

		
		if(!empty($keywords)){
			echo '<meta name="keywords" content="'.$keywords.'" />' . "\n";
		}
		
    if ( is_front_page() || is_home() ) :
      echo '<link rel="canonical" href="'.home_url('').'" />' . "\n";
    elseif(is_singular()):  
      echo '<link rel="canonical" href="'.get_permalink().'" />' . "\n";
    endif;
    
    if(get_option( 'seo_colabs_google_publisher') != ''):
      echo '<link rel="publisher" href="'.get_option( 'seo_colabs_google_publisher').'" />' . "\n";
    endif;
    
    if(get_option( 'seo_colabs_fb_publisher') != ''):
      echo '<meta property="article:publisher" content="'.get_option( 'seo_colabs_fb_publisher').'" />' . "\n";
    endif;
    
    if(get_option( 'seo_colabs_twitter_publisher') != ''):
      echo '<meta name="twitter:card" content="summary"/>' . "\n";
      if ( is_front_page() || is_home() ) {
        echo '<meta name="twitter:description" content="'.get_bloginfo('description').'"/>' . "\n";
        echo '<meta name="twitter:title" content="'.get_bloginfo('name').'"/>' . "\n";
      }elseif( is_singular() ){
        echo '<meta name="twitter:description" content="'.strip_tags( get_the_excerpt() ).'"/>' . "\n";
        echo '<meta name="twitter:title" content="'.get_the_title().'"/>' . "\n";
      }
      echo '<meta name="twitter:site" content="@'.get_option( 'seo_colabs_twitter_publisher').'"/>' . "\n";
      echo '<meta name="twitter:domain" content="'.get_bloginfo('name').'"/>' . "\n";
      echo '<meta name="twitter:creator" content="@'.get_option( 'seo_colabs_twitter_publisher').'"/>' . "\n";
      if (( 'summary_large_image' === get_option( 'seo_colabs_twitter_card'))&&( is_singular() ) ) {
        if ( has_post_thumbnail() ) {
          echo '<meta name="twitter:image:src" content="'.colabs_image('return=true&link=url').'"/>' . "\n";
        }
			}
    endif;
    
    $home_url = trailingslashit( home_url() );
    $search_url =  $home_url . '?s={search_term}';
    echo '<script type="application/ld+json">{ "@context": "http://schema.org", "@type": "WebSite", "url": "' . $home_url . '", "potentialAction": { "@type": "SearchAction", "target": "' . $search_url .'", "query-input": "required name=search_term" } }</script>' . "\n";
}
}

//Add Post Custom Settings
add_action( 'admin_head','seo_add_custom' );

function seo_add_custom() {

		$seo_template = array();
		
		$seo_colabs_wp_title = get_option( 'seo_colabs_wp_title' );
		$seo_colabs_meta_single_desc = get_option( 'seo_colabs_meta_single_desc' );
		$seo_colabs_meta_single_key = get_option( 'seo_colabs_meta_single_key' );
		
		// a = off
		if( $seo_colabs_wp_title != 'true' OR 'a' == $seo_colabs_meta_single_desc OR 'a' == $seo_colabs_meta_single_key) {
			
			$output = "";
			if ( $seo_colabs_wp_title != 'true' )
				$output .= "Custom Page Titles, ";
			if ( 'a' == $seo_colabs_meta_single_desc )
				$output .= "Custom Descriptions, ";
			if ( 'a' == $seo_colabs_meta_single_key )
				$output .= "Custom Keywords";			
				
			$output = rtrim($output, ", " );
			
			$desc = 'Additional SEO custom fields available: <strong>'.$output.'</strong>. Go to <a href="' . admin_url( 'admin.php?page=colabsthemes_seo' ) . '">SEO Settings</a> page to activate.';
			
		} else {
			$desc = 'Go to <a href="'.admin_url( 'admin.php?page=colabsthemes_seo').'">SEO Settings</a> page for more SEO options.';
		}
		
		$seo_template[] = array (	"name"  => "seo_info_1",
										"std" => "",
										"label" => "SEO ",
										"type" => "info",
										"desc" => $desc);

		// Change checkbox depending on "Add meta for Posts & Pages to 'follow' by default" checkbox value.
		
		$followstatus = get_option( 'seo_colabs_meta_single_follow' );

		if ( $followstatus != "true" ) { 

			$seo_template[] = array (	"name"  => "seo_follow", 
											"std" => 'false', 
											"label" => "Set follow",
											"type" => "checkbox",
											"desc" => "Let search engines <strong>follow</strong> links on this post/page." );
										
		} else {
		
			$seo_template[] = array (	"name"  => "seo_follow", 
											"std" => 'false', 
											"label" => "Set nofollow",
											"type" => "checkbox",
											"desc" => "Prevent search engines from following links on this post/page." );
		
		} // End IF Statement
		
		$seo_template[] = array (	"name"  => "seo_noindex",
										"std" => "false",
										"label" => "Set noindex",
										"type" => "checkbox",
										"desc" => "Prevent search engines from indexing this post/page." );

		if( 'true' == get_option( 'seo_colabs_wp_title')){
		$seo_template[] = array (	"name"  => "seo_title",
										"std" => "",
										"label" => "Custom Page Title",
										"type" => "text",
										"desc" => "Add a custom title for this post/page." );
		}
		
		if( 'b' == get_option( 'seo_colabs_meta_single_desc')){								
		$seo_template[] = array (	"name"  => "seo_description",
										"std" => "",
										"label" => "Custom Description",
										"type" => "textarea",
										"desc" => "Add a custom meta description for this post/page." );
		}
		
		if( 'b' == get_option( 'seo_colabs_meta_single_key')){			
		$seo_template[] = array (	"name"  => "seo_keywords",
										"std" => "",
										"label" => "Custom Keywords",
										"type" => "text",
										"desc" => "Add custom meta keywords for this post/page. (comma separated)" );	
		}
		
		//3rd Party Plugins
		if('true' == get_option( 'seo_colabs_use_third_party_data')){
			$use_third_party_data = true;
		} else {
			$use_third_party_data = false;
		}
		
		if( (
			class_exists( 'All_in_One_SEO_Pack') || 
    		class_exists( 'Headspace_Plugin') || 
    		class_exists( 'WPSEO_Admin' ) || 
    		class_exists( 'WPSEO_Frontend' )
			) AND 
		( $use_third_party_data == true )) { 
			delete_option( 'colabs_custom_seo_template' ); 
		}
		else {

			update_option( 'colabs_custom_seo_template',$seo_template);
			
		}	

}

/*-----------------------------------------------------------------------------------*/
/* CoLabs Text Trimmer */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'colabs_text_trim') ) {
	function colabs_text_trim($text, $words = 50)
	{ 
		$matches = preg_split( "/\s+/", $text, $words + 1);
		$sz = count($matches);
		if ($sz > $words) 
		{
			unset($matches[$sz-1]);
			return implode( ' ',$matches)." ...";
		}
		return $text;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Array */
/* Documentation:
/*
/* name: The name of the Google Font.
/* variant: The Google Font API variants available for the font.
/*-----------------------------------------------------------------------------------*/

// Available Google webfont names
$google_fonts = array(	array( 'name' => "Cantarell", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Cardo", 'variant' => ''),
						array( 'name' => "Crimson Text", 'variant' => ''),
						array( 'name' => "Droid Sans", 'variant' => ':r,b'),
						array( 'name' => "Droid Sans Mono", 'variant' => ''),
						array( 'name' => "Droid Serif", 'variant' => ':r,b,i,bi'),
						array( 'name' => "IM Fell DW Pica", 'variant' => ':r,i'),
						array( 'name' => "Inconsolata", 'variant' => ''),
						array( 'name' => "Josefin Sans Std Light", 'variant' => ''),
						array( 'name' => "Josefin Slab", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Lobster", 'variant' => ''),
						array( 'name' => "Molengo", 'variant' => ''),
						array( 'name' => "Nobile", 'variant' => ':r,b,i,bi'),
						array( 'name' => "OFL Sorts Mill Goudy TT", 'variant' => ':r,i'),
						array( 'name' => "Old Standard TT", 'variant' => ':r,b,i'),
						array( 'name' => "Reenie Beanie", 'variant' => ''),
						array( 'name' => "Tangerine", 'variant' => ':r,b'),
						array( 'name' => "Vollkorn", 'variant' => ':r,b'),
						array( 'name' => "Yanone Kaffeesatz", 'variant' => ':r,b'),
						array( 'name' => "Cuprum", 'variant' => ''),
						array( 'name' => "Neucha", 'variant' => ''),
						array( 'name' => "Neuton", 'variant' => ''),
						array( 'name' => "PT Sans", 'variant' => ':r,b,i,bi'),
						array( 'name' => "PT Sans Caption", 'variant' => ':r,b'),
						array( 'name' => "PT Sans Narrow", 'variant' => ':r,b'),
						array( 'name' => "Philosopher", 'variant' => ''),
						array( 'name' => "Allerta", 'variant' => ''),
						array( 'name' => "Allerta Stencil", 'variant' => ''),
						array( 'name' => "Arimo", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Arvo", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Bentham", 'variant' => ''),
						array( 'name' => "Coda", 'variant' => ':800'),
						array( 'name' => "Cousine", 'variant' => ''),
						array( 'name' => "Covered By Your Grace", 'variant' => ''),
			 			array( 'name' => "Geo", 'variant' => ''),
						array( 'name' => "Just Me Again Down Here", 'variant' => ''),
						array( 'name' => "Puritan", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Raleway", 'variant' => ':100'),
						array( 'name' => "Tinos", 'variant' => ':r,b,i,bi'),
						array( 'name' => "UnifrakturCook", 'variant' => ':bold'),
						array( 'name' => "UnifrakturMaguntia", 'variant' => ''),
						array( 'name' => "Mountains of Christmas", 'variant' => ''),
						array( 'name' => "Lato", 'variant' => ''),
						array( 'name' => "Orbitron", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Allan", 'variant' => ':bold'),
						array( 'name' => "Anonymous Pro", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Copse", 'variant' => ''),
						array( 'name' => "Kenia", 'variant' => ''),
						array( 'name' => "Ubuntu", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Vibur", 'variant' => ''),
						array( 'name' => "Sniglet", 'variant' => ':800'),
						array( 'name' => "Syncopate", 'variant' => ''),
						array( 'name' => "Cabin", 'variant' => ':400,400italic,700,700italic,'),
						array( 'name' => "Merriweather", 'variant' => ''),
						array( 'name' => "Maiden Orange", 'variant' => ''),
						array( 'name' => "Just Another Hand", 'variant' => ''),
						array( 'name' => "Kristi", 'variant' => ''),
						array( 'name' => "Corben", 'variant' => ':b'),
						array( 'name' => "Gruppo", 'variant' => ''),
						array( 'name' => "Buda", 'variant' => ':light'),
						array( 'name' => "Lekton", 'variant' => ''),
						array( 'name' => "Luckiest Guy", 'variant' => ''),
						array( 'name' => "Crushed", 'variant' => ''),
						array( 'name' => "Chewy", 'variant' => ''),
						array( 'name' => "Coming Soon", 'variant' => ''),
						array( 'name' => "Crafty Girls", 'variant' => ''),
						array( 'name' => "Fontdiner Swanky", 'variant' => ''),
						array( 'name' => "Permanent Marker", 'variant' => ''),
						array( 'name' => "Rock Salt", 'variant' => ''),
						array( 'name' => "Sunshiney", 'variant' => ''),
						array( 'name' => "Unkempt", 'variant' => ''),
						array( 'name' => "Calligraffitti", 'variant' => ''),
						array( 'name' => "Cherry Cream Soda", 'variant' => ''),
						array( 'name' => "Homemade Apple", 'variant' => ''),
						array( 'name' => "Irish Growler", 'variant' => ''),
						array( 'name' => "Kranky", 'variant' => ''),
						array( 'name' => "Schoolbell", 'variant' => ''),
						array( 'name' => "Slackey", 'variant' => ''),
						array( 'name' => "Walter Turncoat", 'variant' => ''),
						array( 'name' => "Radley", 'variant' => ''),
						array( 'name' => "Meddon", 'variant' => ''),
						array( 'name' => "Kreon", 'variant' => ':r,b'),
						array( 'name' => "Dancing Script", 'variant' => ''),
						array( 'name' => "Goudy Bookletter 1911", 'variant' => ''),
						array( 'name' => "PT Serif Caption", 'variant' => ':r,i'),
						array( 'name' => "PT Serif", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Astloch", 'variant' => ':b'),
						array( 'name' => "Bevan", 'variant' => ''),
						array( 'name' => "Anton", 'variant' => ''),
						array( 'name' => "Expletus Sans", 'variant' => ':b'),
						array( 'name' => "VT323", 'variant' => ''),
						array( 'name' => "Pacifico", 'variant' => ''),
						array( 'name' => "Candal", 'variant' => ''),
						array( 'name' => "Architects Daughter", 'variant' => ''),
						array( 'name' => "Indie Flower", 'variant' => ''),
						array( 'name' => "League Script", 'variant' => ''),
						array( 'name' => "Cabin Sketch", 'variant' => ':b'),
						array( 'name' => "Quattrocento", 'variant' => ''),
						array( 'name' => "Amaranth", 'variant' => ''),
						array( 'name' => "Irish Grover", 'variant' => ''),
						array( 'name' => "Oswald", 'variant' => ''),
						array( 'name' => "EB Garamond", 'variant' => ''),
						array( 'name' => "Nova Round", 'variant' => ''),
						array( 'name' => "Nova Slim", 'variant' => ''),
						array( 'name' => "Nova Script", 'variant' => ''),
						array( 'name' => "Nova Cut", 'variant' => ''),
						array( 'name' => "Nova Mono", 'variant' => ''),
						array( 'name' => "Nova Oval", 'variant' => ''),
						array( 'name' => "Nova Flat", 'variant' => ''),
						array( 'name' => "Terminal Dosis Light", 'variant' => ''),
						array( 'name' => "Michroma", 'variant' => ''),
						array( 'name' => "Miltonian", 'variant' => ''),
						array( 'name' => "Miltonian Tattoo", 'variant' => ''),
						array( 'name' => "Annie Use Your Telescope", 'variant' => ''),
						array( 'name' => "Dawning of a New Day", 'variant' => ''),
						array( 'name' => "Sue Ellen Francisco", 'variant' => ''),
						array( 'name' => "Waiting for the Sunrise", 'variant' => ''),
						array( 'name' => "Special Elite", 'variant' => ''),
						array( 'name' => "Quattrocento Sans", 'variant' => ''),
						array( 'name' => "Smythe", 'variant' => ''),
						array( 'name' => "The Girl Next Door", 'variant' => ''),
						array( 'name' => "Aclonica", 'variant' => ''),
						array( 'name' => "News Cycle", 'variant' => ''),
						array( 'name' => "Damion", 'variant' => ''),
						array( 'name' => "Wallpoet", 'variant' => ''),
						array( 'name' => "Over the Rainbow", 'variant' => ''),
						array( 'name' => "MedievalSharp", 'variant' => ''),
						array( 'name' => "Six Caps", 'variant' => ''),
						array( 'name' => "Swanky and Moo Moo", 'variant' => ''),
						array( 'name' => "Bigshot One", 'variant' => ''),
						array( 'name' => "Francois One", 'variant' => ''),
						array( 'name' => "Sigmar One", 'variant' => ''),
						array( 'name' => "Carter One", 'variant' => ''),
						array( 'name' => "Holtcolabsd One SC", 'variant' => ''),
						array( 'name' => "Paytone One", 'variant' => ''),
						array( 'name' => "Monofett", 'variant' => ''),
						array( 'name' => "Rokkitt", 'variant' => ''),
						array( 'name' => "Megrim", 'variant' => ''),
						array( 'name' => "Judson", 'variant' => ':r,ri,b'),
						array( 'name' => "Didact Gothic", 'variant' => ''),
						array( 'name' => "Play", 'variant' => ':r,b'),
						array( 'name' => "Ultra", 'variant' => ''),
						array( 'name' => "Metrophobic", 'variant' => ''),
						array( 'name' => "Mako", 'variant' => ''),
						array( 'name' => "Shanti", 'variant' => ''),
						array( 'name' => "Caudex", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Jura", 'variant' => ''),
						array( 'name' => "Ruslan Display", 'variant' => ''),
						array( 'name' => "Brawler", 'variant' => ''),
						array( 'name' => "Nunito", 'variant' => ''),
						array( 'name' => "Wire One", 'variant' => ''),
						array( 'name' => "Podkova", 'variant' => ''),
						array( 'name' => "Muli", 'variant' => ''),
						array( 'name' => "Maven Pro", 'variant' => ''),
						array( 'name' => "Tenor Sans", 'variant' => ''),
						array( 'name' => "Limelight", 'variant' => ''),
						array( 'name' => "Playfair Display", 'variant' => ''),
						array( 'name' => "Artifika", 'variant' => ''),
						array( 'name' => "Lora", 'variant' => ''),
						array( 'name' => "Kameron", 'variant' => ':r,b'),
						array( 'name' => "Cedarville Cursive", 'variant' => ''),
						array( 'name' => "Zeyada", 'variant' => ''),
						array( 'name' => "La Belle Aurore", 'variant' => ''),
						array( 'name' => "Shadows Into Light", 'variant' => ''),
						array( 'name' => "Lobster Two", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Nixie One", 'variant' => ''),
						array( 'name' => "Redressed", 'variant' => ''),
						array( 'name' => "Bangers", 'variant' => ''),
						array( 'name' => "Open Sans Condensed", 'variant' => ':r,i'),
						array( 'name' => "Open Sans", 'variant' => ':r,i,b,bi'),
						array( 'name' => "Varela", 'variant' => ''),
						array( 'name' => "Goblin One", 'variant' => ''),
						array( 'name' => "Asset", 'variant' => ''),
						array( 'name' => "Gravitas One", 'variant' => ''),
						array( 'name' => "Hammersmith One", 'variant' => ''),
						array( 'name' => "Stardos Stencil", 'variant' => ''),
						array( 'name' => "Love Ya Like A Sister", 'variant' => ''),
						array( 'name' => "Loved by the King", 'variant' => ''),
						array( 'name' => "Bowlby One SC", 'variant' => ''),
						array( 'name' => "Forum", 'variant' => ''),
						array( 'name' => "Patrick Hand", 'variant' => ''),
						array( 'name' => "Varela Round", 'variant' => ''),
						array( 'name' => "Yeseva One", 'variant' => ''),
						array( 'name' => "Give You Glory", 'variant' => ''),
						array( 'name' => "Modern Antiqua", 'variant' => ''),
						array( 'name' => "Bowlby One", 'variant' => ''),
						array( 'name' => "Tienne", 'variant' => ''),
						array( 'name' => "Istok Web", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Yellowtail", 'variant' => ''),
						array( 'name' => "Pompiere", 'variant' => ''),
						array( 'name' => "Unna", 'variant' => ''),
						array( 'name' => "Rosario", 'variant' => ''),
						array( 'name' => "Leckerli One", 'variant' => ''),
						array( 'name' => "Snippet", 'variant' => ''),
						array( 'name' => "Ovo", 'variant' => ''),
						array( 'name' => "IM Fell English", 'variant' => ':r,i'),
						array( 'name' => "IM Fell English SC", 'variant' => ''),
						array( 'name' => "Gloria Hallelujah", 'variant' => ''),
						array( 'name' => "Kelly Slab", 'variant' => ''),
						array( 'name' => "Black Ops One", 'variant' => ''),
						array( 'name' => "Carme", 'variant' => ''),
						array( 'name' => "Aubrey", 'variant' => ''),
						array( 'name' => "Federo", 'variant' => ''),
						array( 'name' => "Delius", 'variant' => ''),
						array( 'name' => "Rochester", 'variant' => ''),
						array( 'name' => "Rationale", 'variant' => ''),
						array( 'name' => "Abel", 'variant' => ''),
						array( 'name' => "Marvel", 'variant' => ':r,b,i,bi'),
						array( 'name' => "Actor", 'variant' => ''),
						array( 'name' => "Delius Swash Caps", 'variant' => ''),
						array( 'name' => "Smokum", 'variant' => ''),
						array( 'name' => "Tulpen One", 'variant' => ''),
						array( 'name' => "Coustard", 'variant' => ':r,b'),
						array( 'name' => "Andika", 'variant' => ''),
						array( 'name' => "Alice", 'variant' => ''),
						array( 'name' => "Questrial", 'variant' => ''),
						array( 'name' => "Comfortaa", 'variant' => ':r,b'),
						array( 'name' => "Geostar", 'variant' => ''),
						array( 'name' => "Geostar Fill", 'variant' => ''),
						array( 'name' => "Volkhov", 'variant' => ''),
						array( 'name' => "Voltaire", 'variant' => ''),
						array( 'name' => "Montez", 'variant' => ''),
						array( 'name' => "Short Stack", 'variant' => ''),
						array( 'name' => "Vidaloka", 'variant' => ''),
						array( 'name' => "Aldrich", 'variant' => ''),
						array( 'name' => "Numans", 'variant' => ''),
						array( 'name' => "Days One", 'variant' => ''),
						array( 'name' => "Gentium Book Basic", 'variant' => ''),
						array( 'name' => "Monoton", 'variant' => ''),
						array( 'name' => "Alike", 'variant' => ''),
						array( 'name' => "Delius Unicase", 'variant' => ''),
						array( 'name' => "Abril Fatface", 'variant' => ''),
						array( 'name' => "Dorsa", 'variant' => ''),
						array( 'name' => "Antic", 'variant' => ''),
						array( 'name' => "Passero One", 'variant' => ''),
						array( 'name' => "Fancolabsd Text", 'variant' => ''),
						array( 'name' => "Prociono", 'variant' => ''),
						array( 'name' => "Merienda One", 'variant' => ''),
						array( 'name' => "Changa One", 'variant' => ''),
						array( 'name' => "Julee", 'variant' => ''),
						array( 'name' => "Prata", 'variant' => ''),
						array( 'name' => "Adamina", 'variant' => ''),
						array( 'name' => "Sorts Mill Goudy", 'variant' => ''),
						array( 'name' => "Terminal Dosis", 'variant' => ''),
						array( 'name' => "Sansita One", 'variant' => ''),
						array( 'name' => "Chivo", 'variant' => ''),
						array( 'name' => "Spinnaker", 'variant' => ''),
						array( 'name' => "Poller One", 'variant' => ''),
						array( 'name' => "Alike Angular", 'variant' => ''),
						array( 'name' => "Gochi Hand", 'variant' => ''),
						array( 'name' => "Poly", 'variant' => ''),
						array( 'name' => "Andada", 'variant' => ''),
						array( 'name' => "Federant", 'variant' => ''),
						array( 'name' => "Ubuntu Condensed", 'variant' => ''),
						array( 'name' => "Ubuntu Mono", 'variant' => '')
);

/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Stylesheet Generator */
/*-----------------------------------------------------------------------------------*/
/*
INSTRUCTIONS: Needs to be loaded for the Google Fonts options to work for font options. Add this to
the specific themes includes/theme-actions.php or functions.php:

add_action( 'wp_enqueue_scripts', 'colabs_google_webfonts' );
*/

if (!function_exists( "colabs_google_webfonts")) {
	function colabs_google_webfonts() {

		global $google_fonts;
		$fonts = '';
		$output = '';

		// Setup CoLabs Options array
		global $colabs_options;

		// Go through the options
		if ( !empty($colabs_options) ) {

			foreach ( $colabs_options as $option ) {

				// Check if option has "face" in array
				if ( is_array($option) && isset($option['face']) ) {

					// Go through the google font array
					foreach ($google_fonts as $font) {

						// Check if the google font name exists in the current "face" option
						if ( $option['face'] == $font['name'] AND !strstr($fonts, $font['name'])){

							// Add google font to output
							$fonts .= $font['name'].$font['variant']."|";
						}	
					}
					
					$new_stacks = get_option( 'colabs_custom_google_font' );
					
					if(!empty($new_stacks)){
						foreach($new_stacks as $name => $stack){	
							if ( $option['face'] == $stack AND !strstr($fonts, $name)){
							$fonts .= $name."|";
							}
						}
					}
				}

			}

			// Output google font css in header
			if ( $fonts ) {
				$fonts = str_replace( " ","+",$fonts);
				$output = ( is_ssl() ? 'https' : 'http' ) .'://fonts.googleapis.com/css?family=' . $fonts;
				$output = str_replace( '|"','"',$output);
        wp_enqueue_style( 'colabs-custom-google-font', $output );
			}
		}

	}
}


/*---------------------------------------------------------------------------------*/
/* Detects the Charset of String and Converts it to UTF-8 */
/*---------------------------------------------------------------------------------*/
if ( !function_exists( 'colabs_encoding_convert') ) {
	function colabs_encoding_convert($str_to_convert) {
		if ( function_exists( 'mb_detect_encoding') ) {
			$str_lang_encoding = mb_detect_encoding($str_to_convert);
			//if no encoding detected, assume UTF-8
			if (!$str_lang_encoding) {
				//UTF-8 assumed
				$str_lang_converted_utf = $str_to_convert;
			} else {
				//Convert to UTF-8
				$str_lang_converted_utf = mb_convert_encoding($str_to_convert, 'UTF-8', $str_lang_encoding);
			}
		} else {
			$str_lang_converted_utf = $str_to_convert;
		}
	
		return $str_lang_converted_utf;
	}
}

/*---------------------------------------------------------------------------------*/
/* WP Login logo */
/*---------------------------------------------------------------------------------*/
if ( !function_exists( 'colabs_custom_login_logo') ) {
	function colabs_custom_login_logo() {
		$logo = get_option( 'framework_colabs_custom_login_logo' );
	    $dimensions = @getimagesize( $logo );
		echo '<style type="text/css">h1 a { background-image:url( '.$logo.' ); height: '.$dimensions[1].'px ; }</style>';
	}
	if ( get_option( 'framework_colabs_custom_login_logo') )
		add_action( 'login_head', 'colabs_custom_login_logo' );
}

/*-----------------------------------------------------------------------------------*/
/* colabs_pagination() - Custom loop pagination function  */
/*-----------------------------------------------------------------------------------*/
/*
/* Additional documentation: http://codex.wordpress.org/Function_Reference/paginate_links
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'base' (optional) 				- The query argument on which to determine the pagination (for advanced users)
/* 'format' (optional) 				- The format in which the query argument is formatted in it's raw format (for advanced users)
/* 'total' (optional) 				- The total amount of pages
/* 'current' (optional) 			- The current page number
/* 'prev_next' (optional) 			- Whether to include the previous and next links in the list or not.
/* 'prev_text' (optional) 			- The previous page text. Works only if 'prev_next' argument is set to true.
/* 'next_text' (optional) 			- The next page text. Works only if 'prev_next' argument is set to true.
/* 'show_all' (optional) 			- If set to True, then it will show all of the pages instead of a short list of the pages near the current page. By default, the 'show_all' is set to false and controlled by the 'end_size' and 'mid_size' arguments.
/* 'end_size' (optional) 			- How many numbers on either the start and the end list edges.
/* 'mid_size' (optional) 			- How many numbers to either side of current page, but not including current page.
/* 'add_fragment' (optional) 		- An array of query args to add using add_query_arg().
/* 'type' (optional) 				- Controls format of the returned value. Possible values are:
									  'plain' - A string with the links separated by a newline character.
									  'array' - An array of the paginated link list to offer full control of display.
									  'list' - Unordered HTML list.
/* 'before' (optional) 				- The HTML to display before the paginated links.
/* 'after' (optional) 				- The HTML to display after the paginated links.
/* 'echo' (optional) 				- Whether or not to display the paginated links (alternative is to "return").
/*
/* Query Parameter (optional) 		- Specify a custom query which you'd like to paginate.
/*
/*-----------------------------------------------------------------------------------*/
/**
 * colabs_pagination() is used for paginating the various archive pages created by WordPress. This is not
 * to be used on single.php or other single view pages.
 *
 * @since 3.7.0
 * @uses paginate_links() Creates a string of paginated links based on the arguments given.
 * @param array $args Arguments to customize how the page links are output.
 * @param object $query An optional custom query to paginate.
 */

if ( ! function_exists( 'colabs_pagination' ) ) {

	function colabs_pagination( $args = array(), $query = '' ) {
		global $wp_rewrite, $wp_query;
		
		do_action( 'colabs_pagination_start' );
		
		if ( $query ) {
		
			$wp_query = $query;
		
		} // End IF Statement
	
		/* If there's not more than one page, return nothing. */
		if ( 1 >= $wp_query->max_num_pages )
			return;
	
		/* Get the current page. */
		$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );
	
		/* Get the max number of pages. */
		$max_num_pages = intval( $wp_query->max_num_pages );
	
		/* Set up some default arguments for the paginate_links() function. */
		$defaults = array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => $max_num_pages,
			'current' => $current,
			'prev_next' => true,
			'prev_text' => __( '&laquo; Previous', 'colabsthemes' ), // Translate in WordPress. This is the default.
			'next_text' => __( 'Next &raquo;', 'colabsthemes' ), // Translate in WordPress. This is the default.
			'show_all' => false,
			'end_size' => 1,
			'mid_size' => 1,
			'add_fragment' => '',
			'type' => 'plain',
			'before' => '<div class="pagination colabs-pagination">', // Begin colabs_pagination() arguments.
			'after' => '</div>',
			'echo' => true,
      'use_search_permastruct' => true
		);
    
    /* Allow themes/plugins to filter the default arguments. */
		$defaults = apply_filters( 'colabs_pagination_args_defaults', $defaults );
    
		/* Add the $base argument to the array if the user is using permalinks. */
		if( $wp_rewrite->using_permalinks() && ! is_search() )
			$defaults['base'] = user_trailingslashit( trailingslashit( get_pagenum_link() ) . 'page/%#%' );
    
    /* Force search links to use raw permastruct for more accurate multi-word searching. */
		if ( is_search() )
			$defaults['use_search_permastruct'] = false;
      
		/* If we're on a search results page, we need to change this up a bit. */
		if ( is_search() ) {
		/* If we're in BuddyPress, use the default "unpretty" URL structure. */
			if ( class_exists( 'BP_Core_User' ) || $defaults['use_search_permastruct'] == false ) {				
				$search_query = get_query_var( 's' );
				$paged = get_query_var( 'paged' );
				$base = add_query_arg( 's', urlencode( $search_query ) );
				$base = add_query_arg( 'paged', '%#%' );
				$defaults['base'] = $base;
			} else {
				$search_permastruct = $wp_rewrite->get_search_permastruct();
				if ( ! empty( $search_permastruct ) ) {
					$base = get_search_link();
					$base = add_query_arg( 'paged', '%#%', $base );
					$defaults['base'] = $base;
				}
			}
		}
	
		/* Merge the arguments input with the defaults. */
		$args = wp_parse_args( $args, $defaults );
	
		/* Allow developers to overwrite the arguments with a filter. */
		$args = apply_filters( 'colabs_pagination_args', $args );
	
		/* Don't allow the user to set this to an array. */
		if ( 'array' == $args['type'] )
			$args['type'] = 'plain';
		
		/* Make sure raw querystrings are displayed at the end of the URL, if using pretty permalinks. */
		$pattern = '/\?(.*?)\//i';
		
		preg_match( $pattern, $args['base'], $raw_querystring );
		
		if( $wp_rewrite->using_permalinks() && $raw_querystring )
			$raw_querystring[0] = str_replace( '', '', $raw_querystring[0] );
			@$args['base'] = str_replace( $raw_querystring[0], '', $args['base'] );
			@$args['base'] .= substr( $raw_querystring[0], 0, -1 );

		/* Get the paginated links. */
		$page_links = paginate_links( $args );
	
		/* Remove 'page/1' from the entire output since it's not needed. */
		$page_links = str_replace( array( '&#038;paged=1\'', '/page/1\'' ), '\'', $page_links );
	
		/* Wrap the paginated links with the $before and $after elements. */
		$page_links = $args['before'] . $page_links . $args['after'];
	
		/* Allow devs to completely overwrite the output. */
		$page_links = apply_filters( 'colabs_pagination', $page_links );
	
		do_action( 'colabs_pagination_end' );
		
		/* Return the paginated links for use in themes. */
		if ( $args['echo'] )
			echo $page_links;
		else
			return $page_links;
			
	} // End colabs_pagination()

} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* colabs_breadcrumbs() - Custom breadcrumb generator function  */
/*
/* Params:
/*
/* Arguments Array:
/*
/* 'separator' 			- The character to display between the breadcrumbs.
/* 'before' 			- HTML to display before the breadcrumbs.
/* 'after' 				- HTML to display after the breadcrumbs.
/* 'front_page' 		- Include the front page at the beginning of the breadcrumbs.
/* 'show_home' 			- If $show_home is set and we're not on the front page of the site, link to the home page.
/* 'echo' 				- Specify whether or not to echo the breadcrumbs. Alternative is "return".
/*
/*-----------------------------------------------------------------------------------*/
/**
 * The code below is inspired by Justin Tadlock's Hybrid Core.
 *
 * colabs_breadcrumbs() shows a breadcrumb for all types of pages.  Themes and plugins can filter $args or input directly.  
 * Allow filtering of only the $args using get_the_breadcrumb_args.
 *
 * @since 3.7.0
 * @param array $args Mixed arguments for the menu.
 * @return string Output of the breadcrumb menu.
 */
function colabs_breadcrumbs( $args = array() ) {
	global $wp_query, $wp_rewrite;


	/* Create an empty variable for the breadcrumb. */
	$breadcrumb = '';

	/* Create an empty array for the trail. */
	$trail = array();
	$path = '';

	/* Set up the default arguments for the breadcrumb. */
	$defaults = array(
		'separator' => '&gt;',
		'before' => '<span class="breadcrumb-title">' . __( 'You are here:', 'colabsthemes' ) . '</span>',
		'after' => false,
		'front_page' => true,
		'show_home' => __( 'Home', 'colabsthemes' ),
		'echo' => true, 
		'show_posts_page' => true,
    'show_only_first_taxonomy_tree' => false
	);

	/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
	if ( is_singular() ){
		$defaults["singular_{$wp_query->post->post_type}_taxonomy"] = false;
  }  

	/* Apply filters to the arguments. */
	$args = apply_filters( 'colabs_breadcrumbs_args', $args );

	/* Parse the arguments and extract them for easy variable naming. */
	extract( wp_parse_args( $args, $defaults ) );

	/* If $show_home is set and we're not on the front page of the site, link to the home page. */
	if ( !is_front_page() && $show_home )
		$trail[] = '<a href="' . esc_url( home_url() ) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $show_home . '</a>';

	/* If viewing the front page of the site. */
	if ( is_front_page() ) {
		if ( !$front_page )
			$trail = false;
		elseif ( $show_home )
			$trail['trail_end'] = "{$show_home}";
	}

	/* If viewing the "home"/posts page. */
	elseif ( is_home() ) {
		$home_page = get_page( $wp_query->get_queried_object_id() );
		$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( $home_page->post_parent, '' ) );
		$trail['trail_end'] = get_the_title( $home_page->ID );
	}

	/* If viewing a singular post (page, attachment, etc.). */
	elseif ( is_singular() ) {

		/* Get singular post variables needed. */
		$post = $wp_query->get_queried_object();
		$post_id = absint( $wp_query->get_queried_object_id() );
		$post_type = $post->post_type;
		$parent = $post->post_parent;
		$post_type_object = get_post_type_object( $post_type );

		/* If an attachment, check if there are any pages in its hierarchy based on the slug. */
		if ( 'attachment' == $post_type ) {
			/* If $front has been set, add it to the $path. */
			if ( ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['slug'] ) )
				$path .= $post_type_object->rewrite['slug'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) && '/' != $path )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( '', $path ) );
		}

		/* If there's an archive page, add it to the trail. */
		if ( ! empty( $post_type_object->has_archive ) )
			$trail['post_type_archive_link'] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . esc_html( $post_type_object->labels->name ) . '</a>';

		/* If the post type path returns nothing and there is a parent, get its parents. */
		if ( empty( $path ) && 0 !== $parent || 'attachment' == $post_type )
			$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( $parent, '' ) );

		/* Toggle the display of the posts page on single blog posts. */
		if ( 'post' == $post_type && $show_posts_page == true && 'page' == get_option( 'show_on_front' ) ) {
			$posts_page = get_option( 'page_for_posts' );
			if ( $posts_page != '' && is_numeric( $posts_page ) ) {
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( $posts_page, '' ) );
			}
		}

		/* Display terms for specific post type taxonomy if requested. */
		if ( isset( $args["singular_{$post_type}_taxonomy"] ) ) {
			$raw_terms = get_the_terms( $post_id, $args["singular_{$post_type}_taxonomy"] );

			if ( is_array( $raw_terms ) && 0 < count( $raw_terms ) && ! is_wp_error( $raw_terms ) ) {
				$links = array();
				$count = 0;

				$sorted = $raw_terms;

				$terms_by_ancestor = array();
				foreach ( $raw_terms as $k => $v ) {
					$ancestors = array_reverse( get_ancestors( $v->term_id, $args["singular_{$post_type}_taxonomy"] ) );
					if ( isset( $ancestors[0] ) ) {
						$key = $ancestors[0];
					} else {
						$key = $v->term_id;
					}
					$terms_by_ancestor[$key][$v->term_id] = get_term_by( 'term_id', $v->term_id, $args["singular_{$post_type}_taxonomy"] );
				}

				if ( 0 < count( $terms_by_ancestor ) ) {
					$sorted = array();
					foreach ( $terms_by_ancestor as $k => $v ) {
						if ( 0 < count( $v ) ) {
							foreach ( $v as $i => $j ) {
								$sorted[$i] = $j;
							}
						}
					}
					foreach ( $sorted as $k => $v ) {
						if ( isset( $sorted[$v->parent] ) ) {
							unset( $sorted[$v->parent] );
						}
					}
				}

				foreach ( $sorted as $k => $v ) {
					$count++;
					if ( isset( $args['show_only_first_taxonomy_tree'] ) && true == (bool)$args['show_only_first_taxonomy_tree'] && 1 < $count ) continue; // Display only the first match.
					$parents = colabs_get_term_parents( $v->term_id, $args["singular_{$post_type}_taxonomy"], true, '|-|', $v->name, array() );
					if ( $parents != '' && ! is_wp_error( $parents ) ) {
						$parents_arr = explode( '|-|', $parents );
						foreach ( $parents_arr as $p ) {
							if ( $p != '' && ! in_array( $p, $links ) ) { $links[] = $p; }
						}
					}
				}

				if ( 0 < count( $links ) ) {
					foreach ( $links as $k => $v ) {
						$trail[] = $v;
					}
				}
			}
		}

		/* End with the post title. */
		$post_title = get_the_title( $post_id ); // Force the post_id to make sure we get the correct page title.
		if ( !empty( $post_title ) )
			$trail['trail_end'] = $post_title;
	}

	/* If we're viewing any type of archive. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_tax() || is_category() || is_tag() ) {

			/* Get some taxonomy and term variables. */
			$term = $wp_query->get_queried_object();
			$taxonomy = get_taxonomy( $term->taxonomy );

			/* Get the path to the term archive. Use this to determine if a page is present with it. */
			if ( is_category() )
				$path = get_option( 'category_base' );
			elseif ( is_tag() )
				$path = get_option( 'tag_base' );
			else {
				if ( $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
					$path = trailingslashit( $wp_rewrite->front );
				$path .= $taxonomy->rewrite['slug'];
			}

			/* Get parent pages by path if they exist. */
			if ( $path )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( '', $path ) );

			/* If the taxonomy is hierarchical, list its parent terms. */
			if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) );

			/* Add the term name to the trail end. */
			$trail['trail_end'] = $term->name;
		}

		/* If viewing a post type archive. */
		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {

			/* Get the post type object. */
			$post_type_object = get_post_type_object( get_query_var( 'post_type' ) );

			/* If $front has been set, add it to the $path. */
			if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If there's a slug, add it to the $path. */
			if ( !empty( $post_type_object->rewrite['archive'] ) )
				$path .= $post_type_object->rewrite['archive'];

			/* If there's a path, check for parents. */
			if ( !empty( $path ) && '/' != $path )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( '', $path ) );

			/* Add the post type [plural] name to the trail end. */
			$trail['trail_end'] = $post_type_object->labels->name;
		}

		/* If viewing an author archive. */
		elseif ( is_author() ) {

			/* If $front has been set, add it to $path. */
			if ( !empty( $wp_rewrite->front ) )
				$path .= trailingslashit( $wp_rewrite->front );

			/* If an $author_base exists, add it to $path. */
			if ( !empty( $wp_rewrite->author_base ) )
				$path .= $wp_rewrite->author_base;

			/* If $path exists, check for parent pages. */
			if ( !empty( $path ) )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( '', $path ) );

			/* Add the author's display name to the trail end. */
			$trail['trail_end'] = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
		}

		/* If viewing a time-based archive. */
		elseif ( is_time() ) {

			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g:i a', 'colabsthemes' ) );

			elseif ( get_query_var( 'minute' ) )
				$trail['trail_end'] = sprintf( __( 'Minute %1$s', 'colabsthemes' ), get_the_time( __( 'i', 'colabsthemes' ) ) );

			elseif ( get_query_var( 'hour' ) )
				$trail['trail_end'] = get_the_time( __( 'g a', 'colabsthemes' ) );
		}

		/* If viewing a date-based archive. */
		elseif ( is_date() ) {

			/* If $front has been set, check for parent pages. */
			if ( $wp_rewrite->front )
				$trail = array_merge( $trail, colabs_breadcrumbs_get_parents( '', $wp_rewrite->front ) );

			if ( is_day() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'colabsthemes' ) ) . '">' . get_the_time( __( 'Y', 'colabsthemes' ) ) . '</a>';
				$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F', 'colabsthemes' ) ) . '">' . get_the_time( __( 'F', 'colabsthemes' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'j', 'colabsthemes' ) );
			}

			elseif ( get_query_var( 'w' ) ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'colabsthemes' ) ) . '">' . get_the_time( __( 'Y', 'colabsthemes' ) ) . '</a>';
				$trail['trail_end'] = sprintf( __( 'Week %1$s', 'colabsthemes' ), get_the_time( esc_attr__( 'W', 'colabsthemes' ) ) );
			}

			elseif ( is_month() ) {
				$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y', 'colabsthemes' ) ) . '">' . get_the_time( __( 'Y', 'colabsthemes' ) ) . '</a>';
				$trail['trail_end'] = get_the_time( __( 'F', 'colabsthemes' ) );
			}

			elseif ( is_year() ) {
				$trail['trail_end'] = get_the_time( __( 'Y', 'colabsthemes' ) );
			}
		}
	}

	/* If viewing search results. */
	elseif ( is_search() )
		$trail['trail_end'] = sprintf( __( 'Search results for &quot;%1$s&quot;', 'colabsthemes' ), esc_attr( get_search_query() ) );

	/* If viewing a 404 error page. */
	elseif ( is_404() )
		$trail['trail_end'] = __( '404 Not Found', 'colabsthemes' );

	/* Allow child themes/plugins to filter the trail array. */
	$trail = apply_filters( 'colabs_breadcrumbs_trail', $trail, $args );

	/* Connect the breadcrumb trail if there are items in the trail. */
	if ( is_array( $trail ) ) {

		/* Open the breadcrumb trail containers. */
		$breadcrumb = '<div class="breadcrumb breadcrumbs colabs-breadcrumbs"><div class="breadcrumb-trail">';

		/* If $before was set, wrap it in a container. */
		if ( !empty( $before ) )
			$breadcrumb .= '<span class="trail-before">' . wp_kses_post( $before ) . '</span> ';

		/* Wrap the $trail['trail_end'] value in a container. */
		if ( !empty( $trail['trail_end'] ) )
			$trail['trail_end'] = '<span class="trail-end">' . wp_kses_post( $trail['trail_end'] ) . '</span>';

		/* Format the separator. */
		if ( !empty( $separator ) )
			$separator = '<span class="sep">' . wp_kses_post( $separator ) . '</span>';

		/* Join the individual trail items into a single string. */
		$breadcrumb .= join( " {$separator} ", $trail );

		/* If $after was set, wrap it in a container. */
		if ( !empty( $after ) )
			$breadcrumb .= ' <span class="trail-after">' . wp_kses_post( $after ) . '</span>';

		/* Close the breadcrumb trail containers. */
		$breadcrumb .= '</div></div>';
	}

	/* Allow developers to filter the breadcrumb trail HTML. */
	$breadcrumb = apply_filters( 'colabs_breadcrumbs', $breadcrumb );

	/* Output the breadcrumb. */
	if ( $echo )
		echo $breadcrumb;
	else
		return $breadcrumb;


} // End colabs_breadcrumbs()

if ( ! function_exists( 'colabs_set_default_breadcrumb_taxonomies' ) ) {
/**
 * Cater for Colorlabs post types where we know the taxonomy. These should be done in each plugin, in future.
 * @since  1.8.9
 * @param  array $args Arguments.
 * @return array       Arguments.
 */
function colabs_set_default_breadcrumb_taxonomies ( $args ) {
	$post_types = get_post_types( array( 'public' => true ) );
	if ( 0 < count( $post_types ) ) {
		foreach ( $post_types as $k => $v ) {
			$taxonomies = get_taxonomies( array( 'object_type' => array( $k ), 'public' => true ) );
			$post_types[$k] = '';
			// Choose the first taxonomy, if one is present.
			if ( 0 < count( $taxonomies ) ) {
				foreach ( $taxonomies as $i => $j ) {
					if ( '' != $post_types[$k] ) continue;
					$post_types[$k] = $j;
				}
			}

			if ( '' != $post_types[$k] && ! isset( $args['singular_' . $k . '_taxonomy'] ) && is_singular() && ( $k == get_post_type() ) ) {
				$args['singular_' . $k . '_taxonomy'] = $post_types[$k];
			}
		}
	}

	return $args;
} // End colabs_set_default_breadcrumb_taxonomies()
}
add_filter( 'colabs_breadcrumbs_args', 'colabs_set_default_breadcrumb_taxonomies' );

/*-----------------------------------------------------------------------------------*/
/* colabs_breadcrumbs_get_parents() - Retrieve the parents of the current page/post */
/*-----------------------------------------------------------------------------------*/
/**
 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create 
 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given 
 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
 *
 * @since 3.7.0
 * @param int $post_id ID of the post whose parents we want.
 * @param string $path Path of a potential parent page.
 * @return array $trail Array of parent page links.
 */
function colabs_breadcrumbs_get_parents( $post_id = '', $path = '' ) {

	/* Set up an empty trail array. */
	$trail = array();

	/* If neither a post ID nor path set, return an empty array. */
	if ( empty( $post_id ) && empty( $path ) )
		return $trail;

	/* If the post ID is empty, use the path to get the ID. */
	if ( empty( $post_id ) ) {

		/* Get parent post by the path. */
		$parent_page = get_page_by_path( $path );

		/* ********************************************************************
		Modification: The above line won't get the parent page if
		the post type slug or parent page path is not the full path as required
		by get_page_by_path. By using get_page_with_title, the full parent
		trail can be obtained. This may still be buggy for page names that use
		characters or long concatenated names.
		******************************************************************* */

		if( empty( $parent_page ) )
		        // search on page name (single word)
			$parent_page = get_page_by_title ( $path );

		if( empty( $parent_page ) )
			// search on page title (multiple words)
			$parent_page = get_page_by_title ( str_replace( array('-', '_'), ' ', $path ) );

		/* End Modification */

		/* If a parent post is found, set the $post_id variable to it. */
		if ( !empty( $parent_page ) )
			$post_id = $parent_page->ID;
	}

	/* If a post ID and path is set, search for a post by the given path. */
	if ( $post_id == 0 && !empty( $path ) ) {

		/* Separate post names into separate paths by '/'. */
		$path = trim( $path, '/' );
		preg_match_all( "/\/.*?\z/", $path, $matches );

		/* If matches are found for the path. */
		if ( isset( $matches ) ) {

			/* Reverse the array of matches to search for posts in the proper order. */
			$matches = array_reverse( $matches );

			/* Loop through each of the path matches. */
			foreach ( $matches as $match ) {

				/* If a match is found. */
				if ( isset( $match[0] ) ) {

					/* Get the parent post by the given path. */
					$path = str_replace( $match[0], '', $path );
					$parent_page = get_page_by_path( trim( $path, '/' ) );

					/* If a parent post is found, set the $post_id and break out of the loop. */
					if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
						$post_id = $parent_page->ID;
						break;
					}
				}
			}
		}
	}

	/* While there's a post ID, add the post link to the $parents array. */
	while ( $post_id ) {

		/* Get the post by ID. */
		$page = get_page( $post_id );

		/* Add the formatted post link to the array of parents. */
		$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' .  esc_html( get_the_title( $post_id ) ) . '</a>';

		/* Set the parent post's parent to the post ID. */
		$post_id = $page->post_parent;
	}

	/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
	if ( isset( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent posts. */
	return $trail;

} // End colabs_breadcrumbs_get_parents()

/*-----------------------------------------------------------------------------------*/
/* colabs_breadcrumbs_get_term_parents() - Retrieve the parents of the current term */
/*-----------------------------------------------------------------------------------*/
/**
 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress 
 * function get_category_parents() but handles any type of taxonomy.
 *
 * @since 3.7.0
 * @param int $parent_id The ID of the first parent.
 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
 * @return array $trail Array of links to parent terms.
 */
function colabs_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {

	/* Set up some default arrays. */
	$trail = array();
	$parents = array();

	/* If no term parent ID or taxonomy is given, return an empty array. */
	if ( empty( $parent_id ) || empty( $taxonomy ) )
		return $trail;

	/* While there is a parent ID, add the parent term link to the $parents array. */
	while ( $parent_id ) {

		/* Get the parent term. */
		$parent = get_term( $parent_id, $taxonomy );

		/* Add the formatted term link to the array of parent terms. */
		$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

		/* Set the parent term's parent as the parent ID. */
		$parent_id = $parent->parent;
	}

	/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
	if ( !empty( $parents ) )
		$trail = array_reverse( $parents );

	/* Return the trail of parent terms. */
	return $trail;
	
} // End colabs_breadcrumbs_get_term_parents()

/**
 * Retrieve term parents with separator.
 *
 * @param int $id Term ID.
 * @param string $taxonomy.
 * @param bool $link Optional, default is false. Whether to format with link.
 * @param string $separator Optional, default is '/'. How to separate terms.
 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
 * @param array $visited Optional. Already linked to terms to prevent duplicates.
 * @return string
 */

if ( ! function_exists( 'colabs_get_term_parents' ) ) {
function colabs_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
	$chain = '';
	$parent = get_term( $id, $taxonomy );
	if ( is_wp_error( $parent ) )
		return $parent;

	if ( $nicename ) {
		$name = $parent->slug;
	} else {
		$name = $parent->name;
	}

	if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= colabs_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	}

	if ( $link ) {
		$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( 'View %s', 'colabsthemes' ), $parent->name ) ) . '">' . esc_html( $parent->name ) . '</a>' . $separator;
	} else {
		$chain .= $name.$separator;
	}
	return $chain;
} // End colabs_get_term_parents()
}

/*-----------------------------------------------------------------------------------*/
/* get_admin_color() - Echo list of current wp admin css colors */
/*-----------------------------------------------------------------------------------*/
function get_admin_color(){
    
    //$_wp_admin_css_colors[$key] = (object) array('name' => $name, 'url' => $url, 'colors' => $colors);
    global $_wp_admin_css_colors;
    //get current colors[0] variable
    echo  $_wp_admin_css_colors[get_user_option('admin_color')] -> {'colors'}[0];
    echo '<br />';
    //get current admin color scheme
    echo get_user_option('admin_color'); 
    echo '<br />'; 
}


/*-----------------------------------------------------------------------------------*/
/* colabs_prepare_category_ids_from_option()
 *
 * Setup an array of category IDs, from a given theme option.
 * Attempt to transform category slugs into ID values as well.
 *
 * Params: String $option
 * Return: Array $cats
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'colabs_prepare_category_ids_from_option' ) ) {

	function colabs_prepare_category_ids_from_option ( $option ) {

		$cats = array();

		$stored_cats = get_option( $option );

		$cats_raw = explode( ',', $stored_cats );

		if ( is_array( $cats_raw ) && ( count( $cats_raw ) > 0 ) ) {
			foreach ( $cats_raw as $k => $v ) {
				$value = trim( $v );

				if ( is_numeric( $value ) ) {
					$cats_raw[$k] = $value;
				} else {
					$cat_obj = get_category_by_slug( $value );
					if ( isset( $cat_obj->term_id ) ) {
						$cats_raw[$k] = $cat_obj->term_id;
					}
				}

				$cats = $cats_raw;
			}
		}

		return $cats;

	} // End colabs_prepare_category_ids_from_option()

}

/*-----------------------------------------------------------------------------------*/
/* Move tracking code from footer to header */
/*-----------------------------------------------------------------------------------*/
	
	add_action( 'init', 'colabs_move_tracking_code', 20 );

	function colabs_move_tracking_code () {
		$move_code = get_option( 'framework_colabs_move_tracking_code' );
		
		if ( ! is_admin() && isset( $move_code ) && ( 'true' == $move_code ) ) {
			remove_action( 'wp_footer', 'colabs_analytics' );
			add_action( 'colabsthemes_wp_head_after', 'colabs_analytics' );
		}
	} // End colabs_move_tracking_code()

/*-----------------------------------------------------------------------------------*/
/* colabs_get_dynamic_value() */
/* Replace values in a provided array with theme options, if available. */
/*
/* $settings array should resemble: $settings = array( 'theme_option_without_colabs_' => 'default_value' );
/*
/* @since 4.4.4 */
/*-----------------------------------------------------------------------------------*/

function colabs_get_dynamic_values ( $settings ) {
	global $colabs_options;
	
	if ( is_array( $colabs_options ) ) {
		foreach ( $settings as $k => $v ) {
			if ( isset( $colabs_options['colabs_' . $k] ) && ( $colabs_options['colabs_' . $k] != '' ) ) { $settings[$k] = $colabs_options['colabs_' . $k]; }
		}
	}
	
	return $settings;
} // End colabs_get_dynamic_values()

/*-----------------------------------------------------------------------------------*/
/* colabs_get_posts_by_taxonomy()
/*
/* Selects posts based on specified taxonomies.
/*
/* @since 4.5.0
/* @param array $args
/* @return array $posts
/*-----------------------------------------------------------------------------------*/
 
 function colabs_get_posts_by_taxonomy ( $args = null ) {
 	global $wp_query;
 	
 	$posts = array();
 	
 	/* Parse arguments, and declare individual variables for each. */
 	
 	$defaults = array(
 						'limit' => 5, 
 						'post_type' => 'any', 
 						'taxonomies' => 'post_tag, category', 
 						'specific_terms' => '', 
 						'relationship' => 'OR', 
 						'order' => 'DESC', 
 						'orderby' => 'date', 
 						'operator' => 'IN', 
 						'exclude' => ''
 					);
 					
 	$args = wp_parse_args( $args, $defaults );
 	
 	extract( $args, EXTR_SKIP );
 	
 	// Make sure the order value is safe.
 	if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) { $order = $defaults['order']; }
 	
 	// Make sure the orderby value is safe.
 	if ( ! in_array( $orderby, array( 'none', 'id', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order' ) ) ) { $orderby = $defaults['orderby']; }
 	
 	// Make sure the operator value is safe.
 	if ( ! in_array( $operator, array( 'IN', 'NOT IN', 'AND' ) ) ) { $orderby = $defaults['operator']; }
 	
 	// Convert our post types to an array.
 	if ( ! is_array( $post_type ) ) { $post_type = explode( ',', $post_type ); }
 	
 	// Convert our taxonomies to an array.
 	if ( ! is_array( $taxonomies ) ) { $taxonomies = explode( ',', $taxonomies ); }
 	
 	// Convert exclude to an array.
 	if ( ! is_array( $exclude ) && ( $exclude != '' ) ) { $exclude = explode( ',', $exclude ); }
 	
 	if ( ! count( (array)$taxonomies ) ) { return; }
 	
 	// Clean up our taxonomies for use in the query.
 	if ( count( $taxonomies ) ) {
 		foreach ( $taxonomies as $k => $v ) {
 			$taxonomies[$k] = trim( $v );
 		}
 	}
 	
 	// Determine which terms we're going to relate to this entry.
 	$related_terms = array();
 	
 	foreach ( $taxonomies as $t ) {
 		$terms = get_terms( $t, 'orderby=id&hide_empty=1' );
 		
 		if ( ! empty( $terms ) ) {
 			foreach ( $terms as $k => $v ) {
 				$related_terms[$t][$v->term_id] = $v->slug;
 			}
 		}
 	}
 	
 	// If specific terms are available, use those.
 	if ( ! is_array( $specific_terms ) ) { $specific_terms = explode( ',', $specific_terms ); }
 	
 	if ( count( $specific_terms ) ) {
 		foreach ( $specific_terms as $k => $v ) {
 			$specific_terms[$k] = trim( $v );
 		}
 	}
 	
 	// Look for posts with the same terms.
 	
 	// Setup query arguments.
 	$query_args = array();
 	
 	if ( $post_type ) { $query_args['post_type'] = $post_type; }
 	
 	if ( $limit ) {
 		$query_args['posts_per_page'] = $limit;
 		// $query_args['nopaging'] = true;
 	}
 	
 	// Setup specific posts to exclude.
 	if ( count( $exclude ) > 0 ) {
 		$query_args['post__not_in'] = $exclude;
 	}
 	
 	$query_args['order'] = $order;
 	$query_args['orderby'] = $orderby;
 	
 	$query_args['tax_query'] = array();
 	
 	// Setup for multiple taxonomies.
 	
 	if ( count( $related_terms ) > 1 ) {
 		$query_args['tax_query']['relation'] = $args['relationship'];
 	}
 	
 	// Add the taxonomies to the query arguments.
 	
 	foreach ( (array)$related_terms as $k => $v ) {
 		$terms_for_search = array_values( $v );
 	
 		if ( count( $specific_terms ) ) {
 			$specific_terms_by_tax = array();
 			
 			foreach ( $specific_terms as $i => $j ) {
 				if ( in_array( $j, array_values( $v ) ) ) {
 					$specific_terms_by_tax[] = $j;
 				}
 			}
 			
 			if ( count( $specific_terms_by_tax ) ) {
 				$terms_for_search = $specific_terms_by_tax;
 			}
 		}
 	
 		$query_args['tax_query'][] = array(
			'taxonomy' => $k,
			'field' => 'slug',
			'terms' => $terms_for_search, 
			'operator' => $operator
		);
 	}
 	
 	if ( empty( $query_args['tax_query'] ) ) { return; }
 	
 	$query_saved = $wp_query;
 	
 	$query = new WP_Query( $query_args );
 	
 	if ( $query->have_posts() ) {
 		while( $query->have_posts() ) {
 			$query->the_post();
 			
 			$posts[] = $query->post;
 		}
 	}
 	
 	$query = $query_saved;
 	
 	wp_reset_postdata();
 
 	return $posts;
 
 } // End colabs_get_posts_by_taxonomy()

/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Remove page template
/*-----------------------------------------------------------------------------------*/
function colabs_remove_page_template( $files_to_delete = array() ){

if( !class_exists('WP_Theme') ){

    // As convenience, allow a single value to be used as a scalar without wrapping it in a useless array()
    if ( is_scalar( $files_to_delete ) ) $files_to_delete = array( $files_to_delete );

    // remove TLA if it was provided
    $files_to_delete = preg_replace( "/\.[^.]+$/", '', $files_to_delete );
	if ( function_exists( 'wp_get_theme' ) ){
    $theme = wp_get_theme();
	$template_files = $theme->get_files( 'php', 1 );
	} 
    foreach ( $template_files as $file_path ){
        foreach( $files_to_delete as $file_name ){
            if ( preg_match( '/\/'.$file_name.'\.[^.]+$/', $file_path ) ){
                $key = array_search( $file_path, $template_files );
                if ( $key ) unset ( $template_files[$key] );
            }
        }
    }

}else{

    $return = "<script>jQuery(document).ready(function(){ ";
    foreach( $files_to_delete as $template ){
        $return .= "jQuery( '#page_template option[value=\"$template\"]' ).remove();";
    }
    $return .= "});</script>";
    
    echo $return;
    
}
}

/*-----------------------------------------------------------------------------------*/
/*  Open Graph Meta Function    */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('og_meta')) {
add_action( 'colabsthemes_wp_head_before', 'og_meta' );
function og_meta(){ 
  if ( is_home() && '' == get_option( 'colabs_og_enable' ) ) {
    echo '<meta property="og:title" content="'.get_bloginfo('name').'" />'."\n";
    echo '<meta property="og:type" content="author" />'."\n";
    echo '<meta property="og:url" content="'.home_url('').'" />'."\n";
    echo '<meta property="og:image" content="'.get_option('colabs_og_img').'"/>'."\n";
    echo '<meta property="og:site_name" content="'.get_option('colabs_og_sitename').'" />'."\n";
    echo '<meta property="fb:admins" content="'.get_option('colabs_og_admins').'" />'."\n";
    echo '<meta property="og:description" content="'.get_option('blogdescription ').'" />'."\n";
	}
	
	if ( ( is_page() || is_single() ) && '' == get_option( 'colabs_og_enable' ) ) {
		echo '<meta property="og:title" content="'.get_the_title().'" />'."\n";
		echo '<meta property="og:type" content="article" />'."\n";
		echo '<meta property="og:url" content="'.get_permalink().'" />'."\n";
    $ogdesc = str_replace( '[&hellip;]', '&hellip;', strip_tags( get_the_excerpt() ) );
		echo '<meta property="og:description" content="'.$ogdesc.'" />'."\n";
    
		$image = colabs_image('return=true&link=img&width=300&height=300&size=thumbnail'); 
    if( $image ){ 
	    //get img url
	    preg_match('@<img.+src="(.*)".*>@Uims', $image, $matches);
	    $src = $matches[1];
	    echo '<meta property="og:image" content="'.$src.'"/>'."\n";
	  }
    
		echo '<meta property="og:site_name" content="'.get_option('colabs_og_sitename').'" />'."\n";
    
		if( get_option('colabs_og_admins') ) :
			echo '<meta property="fb:admins" content="'.get_option('colabs_og_admins').'" />'."\n";
		endif; 
    
    $tags = get_the_tags();
    if ( ! is_wp_error( $tags ) && ( is_array( $tags ) && $tags !== array() ) ) {
      foreach ( $tags as $tag ) {
        echo '<meta property="article:tag" content="'.$tag->name.'>" />'."\n";
      }
    }
    
    $terms = get_the_category();
    if ( ! is_wp_error( $terms ) && ( is_array( $terms ) && $terms !== array() ) ) {
      foreach ( $terms as $term ) {
        echo '<meta property="article:section" content="'.$term->name.'" />'."\n";
      }
    }
    $pub = get_the_date( 'c' );
    echo '<meta property="article:published_time" content="'.$pub.'" />'."\n";

		$mod = get_the_modified_date( 'c' );
		if ( $mod != $pub ) {
      echo '<meta property="article:modified_time" content="'.$mod.'" />'."\n";
      echo '<meta property="og:updated_time" content="'.$mod.'" />'."\n";
		}
  } 
}}

/*-----------------------------------------------------------------------------------*/
/*  PressTrends Function    */
/*-----------------------------------------------------------------------------------*/
global $colabs_options;
	if ( 'true' == $colabs_options[ 'colabs_pt_enable' ] ) {
		add_action( 'admin_footer', 'presstrends', 100 );
	}

//Presstrends
function presstrends_old() {
global $colabs_options, $wpdb;
if( $colabs_options[ 'colabs_pt_enable' ] != 'true' ) { return; }
if( !empty( $colabs_options['colabs_pt_auth'] ) ) delete_option('colabs_pt_auth');

// Add your PressTrends and Theme API Keys
$api_key = 'jd6va6237f19951mnxcktjzh27x4me683sd9';

// NO NEED TO EDIT BELOW
$data = get_transient( 'presstrends_data' );
if (!$data || '' == $data){
    
    $api_base = 'http://api.presstrends.io/index.php/api/sites/update/api/';
    $url = $api_base . $api_key . '/';
    $data = array();
    $count_posts = wp_count_posts();
    $count_pages = wp_count_posts('page');
    $comments_count = wp_count_comments();

    $plugin_count = count(get_option('active_plugins'));
    $all_plugins = get_plugins();
    
    foreach($all_plugins as $plugin_file => $plugin_data) {
        $plugin_name .= $plugin_data['Name'];
        $plugin_name .= '&';
    }
    
    $posts_with_comments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type='post' AND comment_count > 0");
    $comments_to_posts = number_format(($posts_with_comments / $count_posts->publish) * 100, 0, '.', '');
    $pingback_result = $wpdb->get_var('SELECT COUNT(comment_ID) FROM '.$wpdb->comments.' WHERE comment_type = "pingback"');
    
    $data['url'] = stripslashes(str_replace(array('http://', '/', ':' ), '', site_url()));
    $data['posts'] = $count_posts->publish;
    $data['pages'] = $count_pages->publish;
    $data['comments'] = $comments_count->total_comments;
    $data['approved'] = $comments_count->approved;
    $data['spam'] = $comments_count->spam;
    
    $data['pingbacks'] = $pingback_result;
    $data['post_conversion'] = $comments_to_posts;

    $data['theme_version'] = COLABS_THEME_VER;
    $data['theme_name'] = COLABS_THEME_NAME;
    $data['site_name'] = str_replace( ' ', '', get_bloginfo( 'name' ));
    $data['plugins'] = $plugin_count;
    $data['plugin'] = urlencode($plugin_name);
    $data['wpversion'] = get_bloginfo('version');
    
    foreach ( $data as $k => $v ) {
        $url .= $k . '/' . $v . '/';
    }
        
    $response = wp_remote_get( $url );

	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
		// Silence is golden.
	} else {
		set_transient('presstrends_data', $data, 60*60*24);
	}

}}

/*-----------------------------------------------------------------------------------*/
/* colabs_feedburner_link() */
/*-----------------------------------------------------------------------------------*/ 
/**
 * colabs_feedburner_link()
 *
 * Replace the default RSS feed link with the Feedburner URL, if one
 * has been provided by the user.
 *
 * @package CoLabsFramework
 * @subpackage Filters
 */
 
add_filter( 'feed_link', 'colabs_feed_link', 10 );

if ( ! function_exists( 'colabs_feed_link' ) ) { 
	function colabs_feed_link ( $output, $feed = null ) {		
		$default = get_default_feed();	
		if ( ! $feed ) $feed = $default;		
		if ( ( '' != get_option( 'colabs_feedlinkurl' )) && ( $feed == $default ) && ( ! stristr( $output, 'comments' ) ) ) $output = get_option( 'colabs_feedlinkurl' );	
		return $output;	
	} // End colabs_feedburner_link()
}

if ( ! function_exists( 'colabs_custom_comments_rss' ) ) { 
	function colabs_custom_comments_rss( $post_id = '', $feed = '',$text = '' ) {
		$url = get_post_comments_feed_link($post_id, $feed);
		if ( empty($text) )
		$text = __('Comments Feed','colabsthemes');
		if ( get_option(  'colabs_feedlinkcomments' ) !='') $url= get_option(  'colabs_feedlinkcomments' );
		$commentrss = '<a href="'.$url.'">'.$text.'</a>';
		return $commentrss;
	}
}
add_filter('post_comments_feed_link_html','colabs_custom_comments_rss');

add_theme_support('automatic-feed-links');

/*-----------------------------------------------------------------------------------*/
/* colabs_link - Alternate Link & RSS URL */
/*-----------------------------------------------------------------------------------*/
add_action( 'colabsthemes_wp_head_before', 'colabs_link');
if (!function_exists('colabs_link')) {
function colabs_link(){ 
	
	echo '<link rel="pingback" href="'.get_bloginfo('pingback_url').'" />'."\n";
	echo '<link rel="alternate" type="text/xml" title="RSS .92" href="'. get_bloginfo('rss_url').'" />'."\n";
	echo '<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="'.get_bloginfo('atom_url').'" />'."\n\n";
	 
}}

/**
 * Linkify Twitter Text
 * 
 * @param string s Tweet
 * 
 * @return string a Tweet with the links, mentions and hashtags wrapped in <a> tags 
 */
function linkify_twitter_text($tweet){
  $url_regex = '/((https?|ftp|gopher|telnet|file|notes|ms-help):((\/\/)|(\\\\))+[\w\d:#@%\/\;$()~_?\+-=\\\.&]*)/';
  $tweet = preg_replace($url_regex, '<a href="$1" target="_blank">'. "$1" .'</a>', $tweet);
  $tweet = preg_replace( array(
    '/\@([a-zA-Z0-9_]+)/',    # Twitter Usernames
    '/\#([a-zA-Z0-9_]+)/'    # Hash Tags
  ), array(
    '<a href="http://twitter.com/$1" target="_blank">@$1</a>',
    '<a href="http://twitter.com/search?q=%23$1" target="_blank">#$1</a>'
  ), $tweet );
  
  return $tweet;
}

/*-----------------------------------------------------------------------------------*/
/* New Twitter Api 1.1 Feed() */
/*-----------------------------------------------------------------------------------*/ 

function colabs_update_tweet_urls($content) {
    $maxLen = 16;
    //split long words
    $pattern = '/[^\s\t]{'.$maxLen.'}[^\s\.\,\+\-\_]+/';
    $content = preg_replace($pattern, '$0 ', $content);

    //
    $pattern = '/\w{2,4}\:\/\/[^\s\"]+/';
    $content = preg_replace($pattern, '<a href="$0" title="" target="_blank">$0</a>', $content);

    //search
    $pattern = '/\#([a-zA-Z0-9_-]+)/';
    $content = preg_replace($pattern, '<a href="https://twitter.com/#%21/search/%23$1" title="" target="_blank">$0</a>', $content);

    //user
    $pattern = '/\@([a-zA-Z0-9_-]+)/';
    $content = preg_replace($pattern, '<a href="https://twitter.com/#!/$1" title="" target="_blank">$0</a>', $content);

    return $content;
}

function colabs_get_tweets_bearer_token( $consumer_key, $consumer_secret ){
    $consumer_key = rawurlencode( $consumer_key );
    $consumer_secret = rawurlencode( $consumer_secret );

    $token = maybe_unserialize( get_option( 'colabs_twitter_token' ) );

    if( ! is_array($token) || empty($token) || $token['consumer_key'] != $consumer_key || empty($token['access_token']) ) {
        $authorization = base64_encode( $consumer_key . ':' . $consumer_secret );

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Authorization: Basic '.$authorization,
                'content' => 'grant_type=client_credentials'
            )
        );
        $context = stream_context_create($options);
        $result = json_decode( @file_get_contents('https://api.twitter.com/oauth2/token', false, $context) );
        $token = serialize( array(
            'consumer_key'      => $consumer_key,
            'access_token'      => $result->access_token
        ) );
        update_option( 'colabs_twitter_token', $token );
    }
}

function colabs_get_tweets( $instance = array() ){
    extract($instance);
    $token = maybe_unserialize( get_option( 'colabs_twitter_token' ) );
		if('' == $token){
			colabs_get_tweets_bearer_token($consumer_key,$consumer_secret);
			return colabs_get_tweets();
		}
    if( strpos($query, 'from:') === 0  ) {
        $query_type = 'user_timeline';
        $query = substr($query, 5);
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.rawurlencode($query).'&count='.$number;
    } else {
        $query_type = 'search';
        $url =  'https://api.twitter.com/1.1/search/tweets.json?q='.rawurlencode($query).'&count='.$number;
    }

    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer '.$token['access_token']
        )
    );
    $context = stream_context_create($options);
    $result = json_decode( @file_get_contents( $url, false, $context) );

    if( isset( $result->errors ) && $result->code == 89 ) {
        delete_option( 'colabs_twitter_token' );
        colabs_get_tweets_bearer_token();
        return colabs_get_tweets();
    } 

    $tweets = array();
    if( 'user_timeline' == $query_type ) {
        if( !empty($result) ) {
            $tweets = $result;
        }
    } else {
        if( !empty($result->statuses) ) {
            $tweets = $result->statuses;
        }

    }

    $follow_button = '<a href="https://twitter.com/__name__" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @__name__</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		
		$before_item = '<div class="tweet-item '.$query_type.'">';
		$after_item = '</div>';
		
		if($list_before)$before_item = $list_before;
		if($list_after)$after_item = $list_after;
		
    if( !empty($tweets) ) {
        foreach ($tweets as $tweet ) {
            $text = colabs_update_tweet_urls( $tweet->text );
            $time = human_time_diff( strtotime($tweet->created_at), time() );
            $url = 'http://twitter.com/'.$tweet->user->id.'/status/'.$tweet->id_str;
            $screen_name = $tweet->user->screen_name;
            $name = $tweet->user->name;
            $profile_image_url = $tweet->user->profile_image_url;

            echo $before_item;
            if( 'search' == $query_type ) {
                echo '<div class="twitter-user">';
                if( $show_account == 'true' ) {
                    echo '<a href="https://twitter.com/'.$screen_name.'" class="user">';
                    if( $show_avatar && $profile_image_url ) {
                        echo '<img src="'.$profile_image_url.'" width="16px" height="16px" >';
                    }
                    echo '&nbsp;<strong class="name">'.$name.'</strong>&nbsp;<span class="screen_name">@'.$screen_name.'</span></a>';
                }
                echo '</div>';
            }

            echo    '<div class="tweet-content">'.$text.' <span class="time"><a target="_blank" title="" href="'.$url.'"> about '.$time.' ago</a></span></div>';
            
            if( 'search' == $query_type ) {
                if( $show_follow == 'true' ) {
                    echo str_replace('__name__', $screen_name, $follow_button);
                }
            }
            echo $after_item;
        }

        if( 'user_timeline' == $query_type ) {
					if(( $show_account == 'true' )||( $show_follow == 'true')) {
            echo    '<div class="twitter-user">';
            if( $show_account == 'true' ) {
                echo '<a href="https://twitter.com/'.$screen_name.'" class="user">';
                if( $show_avatar && $profile_image_url ) {
                    echo '<img src="'.$profile_image_url.'" width="16px" height="16px" >';
                }
                echo '&nbsp;<strong class="name">'.$name.'</strong>&nbsp;<span class="screen_name">@'.$screen_name.'</span></a>';
            }
            if( $show_follow == 'true') {
                echo str_replace('__name__', $screen_name, $follow_button);
            }
            echo    '</div>';
					}	
        }   
    }
}
?>
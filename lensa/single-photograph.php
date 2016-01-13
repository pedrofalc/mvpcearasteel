<?php get_header(); ?>
<div class="main-container container">
<div class="row">
  <header class="page-heading block-background block-inner">
    <h3><?php _e("Single","colabsthemes"); echo ' '.get_post_type(); ?></h3>
    <div class="minimize"></div>
  </header><!-- .page-heading -->
  
  <div class="main-content block-background column col8">
    <?php if(have_posts()) : while(have_posts()): the_post();?>
      <div class="block-inner">
        <article <?php post_class('entry-post');?>>
          <header class="entry-header">
            <h2 class="entry-title"><?php the_title(); ?></h2>            
            <ul class="entry-meta">
              <li class="entry-author icon-pencil"><?php the_author_posts_link(); ?></li>
              <li class="entry-category icon-tags"><?php echo get_the_term_list( get_the_ID(), 'photograph-categories', '', ', ', '' );?></li>
              <?php if(comments_open()){ ?>
                    <li class="entry-comments-count icon-comment"><a href="<?php comments_link(); ?>"><?php comments_number( __('Add Comment','colabsthemes'), __('1 Comment','colabsthemes'), __('% Comments','colabsthemes') ); ?></a></li>
              <?php } ?>
              <li class="entry-author icon-heart"><?php echo get_like(get_the_ID()); ?></li>
            </ul>
          </header>
          
          <?php if(has_post_thumbnail()):?>  
          <figure class="entry-media">
            <?php 
            the_post_thumbnail('full');
            
            $attachment_metadata = wp_get_attachment_metadata( get_post_thumbnail_id( $post->ID ));
            if($camera=='')$camera = $attachment_metadata['image_meta']['camera'];
            if($taken=='')$taken = $attachment_metadata['image_meta']['created_timestamp'];
            if($iso=='')$iso = $attachment_metadata['image_meta']['iso'];
            if($speed=='')$speed = $attachment_metadata['image_meta']['shutter_speed'];
            if($aperture=='')$aperture = $attachment_metadata['image_meta']['aperture'];
            ?>
          </figure><!-- .entry-media -->
          <?php endif;?>
          
          <?php if($attachment_metadata):?>
          <div class="exif-data">
            <h3><i class="icon-camera"></i> <?php _e('Exif Data','colabsthemes');?></h3>
            
            <ul>
            <li><span class="column col6"><?php _e('Uploaded','colabsthemes'); ?></span> <span class="exif-info"><?php the_date() ?></span></li>
            <?php if($camera) echo '<li><span class="column col6">'.__('Camera','colabsthemes').'</span><span class="exif-info">'.$camera.'</span></li>'; ?>
            <?php if($lens) echo '<li><span class="column col6">'.__('Lens','colabsthemes').'</span><span class="exif-info"> '.$lens.'</span></li>'; ?>
            <?php if($taken) echo '<li><span class="column col6">'.__('Taken','colabsthemes').'</span><span class="exif-info"> '.date(get_option('date_format'),$taken).'</span></li>'; ?>
            <?php if($iso) echo '<li><span class="column col6">'.__('ISO','colabsthemes').'</span><span class="exif-info"> '.$iso.'</span></li>'; ?>
            <?php if($speed) echo '<li><span class="column col6">'.__('Shutter Speed','colabsthemes').'</span><span class="exif-info"> '.$speed.'</span></li>'; ?>
            <?php if($aperture) echo '<li><span class="column col6">'.__('Aperture','colabsthemes').'</span><span class="exif-info"> '.$aperture.'</span></li>'; ?>
            </ul>
          </div>
          <?php endif;?>
          
          <div class="entry-content">
            <?php the_content(); ?>
            <?php wp_link_pages(array('before' => __('<p><strong>Pages:</strong>','colabsthemes'), 'after' => '</p>', 'next_or_number' => 'number')); ?>
          </div><!-- .entry-content -->
          
          <?php echo colabs_share(); ?>
          
          <?php colabs_ad_gen($post->ID);?>
        </article>
        
      <?php comments_template(); ?>
        
      </div><!-- .block-inner -->
    <?php endwhile;endif;?>
  </div><!-- .main-content -->
    
	<?php get_sidebar(); ?>
    
</div>
</div>
<?php get_footer(); ?>
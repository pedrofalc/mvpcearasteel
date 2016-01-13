<article class="entry-post">

  <header class="entry-header">
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>            
    <?php colabs_post_meta(); ?>
  </header>
    
  <figure class="entry-media">
  <?php  
	$single_top = get_post_custom_values("colabs_single_top");
	if (($single_top[0]!='')||($single_top[0]=='none')){
		if ($single_top[0]=='single_video'){
			$embed = colabs_get_embed('colabs_embed',400,231,'single_video',$post->ID);
			if ($embed!=''){
				echo '<div class="single_video">'.$embed.'</div>'; 
			}
		}elseif($single_top[0]=='single_image'){
			colabs_image('width=620&link=img');				
		}										
	}else{
		colabs_image('width=620&link=img');		
	}
  ?>
  </figure><!-- .entry-media -->
  
  <div class="entry-content">
  <?php colabs_custom_excerpt(); ?>
  <p class="more"><a href="<?php the_permalink() ?>"><?php _e("More","colabsthemes"); ?></a></p>
  </div><!-- .entry-content -->
  
</article>
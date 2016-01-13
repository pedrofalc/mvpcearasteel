<?php while(have_posts()): the_post(); ?>
	<li class="gallery-item photograph-gallery column col4">
    <div class="gallery-details">  
      <a href="<?php colabs_image('link=url&size=full'); ?>" rel="lightbox"><i class="icon-eye-open"></i></a>
      <a href="<?php the_permalink(); ?>"><i class="icon-link"></i></a>
      <?php 
      echo '<div class="like">
          <p class="entry-likes" data-like="'.get_like(get_the_ID()).'_'.get_the_ID().'">
            <i class="icon-heart '.$_COOKIE['like_'.get_the_ID()].'"></i> 
            <span>'.get_like(get_the_ID()).'</span> 
            '.__("Loves","colabsthemes").'
          </p>
          </div>';
      echo  '<div class="time">
          <p class="entry-time">
            <i class="icon-time"></i>
            <span>'.get_the_date().'</span> 
          </p>
          </div>'; 
      ?>
    </div>
    <?php colabs_image('width=300&link=img'); ?>
	</li>
<?php endwhile; ?>
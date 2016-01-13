<?php
/*
Template Name: Blog
*/
?>
<?php get_header(); ?>
<div class="main-container container">
  <div class="row">
    <header class="page-heading block-background block-inner">
      <h3><?php the_title(); ?></h3>
      <div class="minimize"></div>
    </header><!-- .page-heading -->
  
    <div class="main-content block-background column col8">
      <div class="block-inner">
      <?php
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $wp_query = new WP_Query(array('post_type' => 'post', 'paged'=>$paged));
      if($wp_query->have_posts()) : while($wp_query->have_posts()): $wp_query->the_post();
        get_template_part('content','post');
      endwhile;	
      colabs_pagenav();
      endif;
      ?>
      </div><!-- .block-inner -->
    </div><!-- .main-content -->
    
    <?php get_sidebar(); ?>
    
  </div>
</div>  
<?php get_footer(); ?>
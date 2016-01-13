<?php
/**
 Template name:Homepage
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */

get_header(); ?>

	<img id="cycle-loader" src="../lib/images/ajax-loader.gif" />

	<div id="maximage">

				<?php $count = ft_of_get_option('fabthemes_slide_number');
				$slidecat =ft_of_get_option('fabthemes_slide_categories');
				$query = new WP_Query( array( 'cat' =>$slidecat,'posts_per_page' =>$count,'post_type' => 'slide' ) );
	           	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();	?>
	 	
		 		<?php	$thumb = get_post_thumbnail_id();
						$img_url = wp_get_attachment_url( $thumb,'full' ); ?>
						
				<img class="slide-image" src="<?php echo $img_url ?>"/>
				
				<?php endwhile; endif; ?>
	</div>
	<div class="mesh"></div>
	<div class="dome">
		<ul>
			<li id="arrow_left"></li>
			<li id="arrow_right"></li>
		</ul>
	</div>
	
<?php get_footer(); ?>
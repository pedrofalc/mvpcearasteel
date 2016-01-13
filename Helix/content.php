<?php
/**
 * @package fabthemes
 * @since fabthemes 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fabthemes' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	</header><!-- .entry-header -->

	<?php
		$thumb = get_post_thumbnail_id();
		$img_url = wp_get_attachment_url( $thumb,'full' ); //get full URL to image (use "large" or "medium" if the images too big)
		$image = aq_resize( $img_url, 500, 250, true ); //resize & crop the image
	?>
			
	<?php if($image) : ?>
		<a href="<?php the_permalink(); ?>"><img class="postimg" src="<?php echo $image ?>"/></a>
	<?php endif; ?>	
	
	<div class="entry-content cf">
		<div class="postdate">
			<div class="date"><?php the_time('d') ?></div>
			<div class="mear"><?php the_time('F, Y') ?></div>
		</div>
		<div class="postentry">
			<?php the_excerpt(); ?>
			<a href="<?php the_permalink(); ?>" class="rmore">  Read More </a>
		</div>
		
	</div><!-- .entry-summary -->

		
</article><!-- #post-<?php the_ID(); ?> -->

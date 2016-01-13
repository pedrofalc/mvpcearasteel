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


	<div class="entry-content cf">
			<?php the_excerpt(); ?>
			<a href="<?php the_permalink(); ?>" class="rmore">  Read More </a>
		
		
	</div><!-- .entry-summary -->

		
</article><!-- #post-<?php the_ID(); ?> -->

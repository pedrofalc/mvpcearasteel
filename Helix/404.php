<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */

get_header(); ?>
<div id="main" class="cf">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<article id="post-0" class="post error404 not-found">
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'fabthemes' ); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'fabthemes' ); ?></p>

				
				</div><!-- .entry-content -->
			</article><!-- #post-0 .post .error404 .not-found -->

		</div><!-- #content .site-content -->
	</div><!-- #primary .content-area -->
</div>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */

get_header(); ?>
<div id="main" class="cf">
		<section id="primary" class="content-area">
			<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'fabthemes' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</header><!-- .page-header -->


				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'search' ); ?>

				<?php endwhile; ?>

				<div class="paginate cf ">
					<?php kriesi_pagination(); ?>
				</div>


			<?php else : ?>

				<?php get_template_part( 'no-results', 'search' ); ?>

			<?php endif; ?>

			</div><!-- #content .site-content -->
		</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package fabthemes
 * @since fabthemes 1.0
 */
?>
		<div id="secondary" class="widget-area" role="complementary">
			<?php get_search_form(); ?>
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
			<?php endif; // end sidebar widget area ?>
			<?php get_template_part( 'sponsors' ); ?>
		</div><!-- #secondary .widget-area -->

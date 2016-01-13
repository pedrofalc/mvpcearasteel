<?php
global $post;
$layout = get_option('colabs_layout_settings');
if ( isset($post) && get_post_meta($post->ID,'layout',true)!='' ){$layout = get_post_meta($post->ID,'layout',true);}
if($layout != 'one-col'):
?>
<div class="primary-sidebar shop-sidebar column col4">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-shop') ) :  ?>
	<?php endif; ?>
</div><!-- .primary-sidebar -->
<?php endif;?>
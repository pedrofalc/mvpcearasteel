<?php if(('big_slider' == get_option('colabs_frontpage_style') && colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php') ) || (!colabs_check_plugin_active('lensa-marketplace/lensa-marketplace.php'))):?>
<!-- Progress bar for supersized -->
<div class="progress-wrapper">
  <div class="progress-bar"></div>
</div>
<?php endif;?>

<footer class="footer-section container">
  <div class="row">
    <?php colabs_social_net("social-links") ?>
    <div class="copyrights"><?php colabs_credit(); ?></div>
  </div>
</footer><!-- .footer-section -->

</div>

<?php wp_footer(); ?>
</body>
</html>
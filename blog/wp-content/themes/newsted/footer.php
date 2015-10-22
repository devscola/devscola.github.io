<footer>
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo('name'); ?></a> <?php _e('Copyright &copy; 2014', 'newsted'); ?> &ndash; <a href="<?php echo esc_url(__('http://www.wpmultiverse.com/themes/newsted/', 'newsted')); ?>" target="_blank"><?php _e('Newsted Theme', 'newsted'); ?></a></p>
			</div>
			<div class="col-md-6">
				<nav><?php wp_nav_menu(array('theme_location' => 'footer','depth' => 1,'container' => false,'fallback_cb' => false)); ?></nav>
			</div>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
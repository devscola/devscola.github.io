<?php if (is_active_sidebar('home-left-sidebar') || is_active_sidebar('home-middle-sidebar') || is_active_sidebar('home-right-sidebar')) : ?>
<div id="widgets-home" >
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<?php dynamic_sidebar('home-left-sidebar'); ?>
			</div>
			<div class="col-md-4">
				<?php dynamic_sidebar('home-middle-sidebar'); ?>
			</div>
			<div class="col-md-4">
				<?php dynamic_sidebar('home-right-sidebar'); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>	
<?php get_header(); ?>	
<?php get_sidebar('home'); ?>
<div class="container">
	<div class="row">
		<div id="main-holder" class="col-md-8">
			<?php 
				if (have_posts()) :				
					while (have_posts()) : the_post();					
						get_template_part('content', get_post_format());
					endwhile;
				else :
					get_template_part('content', 'none');
				endif; 
			?>
			<?php newsted_pagination(); ?>	
		</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
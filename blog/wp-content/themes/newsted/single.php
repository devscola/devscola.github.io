<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div id="main-holder" class="col-md-8">
			<?php 
				if (have_posts()) :				
					while (have_posts()) : the_post();					
						get_template_part('content', get_post_format());
						comments_template();
					endwhile;
				else :
					get_template_part('content', 'none');
				endif; 
			?>	
		</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
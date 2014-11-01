<?php get_header(); ?>
<div class="container">
	<div class="row">
		<div id="main-holder" class="col-md-8">
			<?php				
				while (have_posts()) : the_post();					
					get_template_part('content', 'page');
					comments_template();				
				endwhile;
			?>	
		</div>
	<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>
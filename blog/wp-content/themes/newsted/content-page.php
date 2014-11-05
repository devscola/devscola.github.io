<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php the_post_thumbnail('featured'); ?>	
	<div id="post-content">			
		<?php the_title('<h1 id="post-title">', '</h1>'); ?>
		<?php the_content() ?>				
	</div>	
</article>	
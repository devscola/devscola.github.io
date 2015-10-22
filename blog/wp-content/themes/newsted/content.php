<?php if (is_single()) : ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php the_post_thumbnail('featured'); ?>
		<p class="date-author"><?php the_time(get_option('date_format')); ?> <span><?php _e('by', 'newsted'); ?></span> <?php the_author(); ?></p>	
		<div id="post-content">			
			<?php the_title('<h1 id="post-title">', '</h1>'); ?>
			<?php the_content() ?>
			<?php wp_link_pages('before=<div id="pager-post">&after=</div>'); ?>		
			<?php
			$post_tags = get_the_tags();
				if ($post_tags) :					
					echo '<div id="post-tags" class="clearfix">';
					    foreach($post_tags as $tag) {
					    	echo '<a href="' . get_tag_link( $tag->term_id ) . '">#' . $tag->name.'</a>'; 
					  	}
				  	echo '</div>';					  	
				endif;
			?>					
		</div>		
		<div id="post-footer" class="clearfix">			
			<div id="post-nav">
				<?php previous_post_link('%link','<div id="post-nav-prev"><span>&laquo;</span> Previous Post</span></div>'); ?> 	
				<?php next_post_link('%link','<div id="post-nav-next">Next Post <span>&raquo;</span></div>'); ?> 	
			</div>	
		</div>	
	</article>	
<?php else : ?>
<div <?php post_class(); ?>>
	<div class="teaser">		
		<?php the_post_thumbnail('featured'); ?>		
		<p class="date-author"><?php the_time(get_option('date_format')); ?> <span><?php _e('by', 'newsted'); ?></span> <?php the_author(); ?> <?php if (is_sticky()) : ?><span class="sticky-txt">&starf; <?php _e('Featured', 'newsted'); ?></span><?php endif; ?></p>	
		<?php the_title('<h3 class="teaser-title"><a href="' . esc_url( get_permalink()) . '" rel="bookmark">', '</a></h3>'); ?>
		<div class="teaser-text">
			<?php the_excerpt(); ?>
		</div>			
	</div>
</div>
<?php endif; ?>
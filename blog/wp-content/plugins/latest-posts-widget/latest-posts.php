<?php
/*
Plugin Name: Latest Posts Widget
Plugin URI: https://wordpress.org/plugins/latest-posts-widget/
Description: Adds a widget that shows the most recent posts of your site with excerpt, featured image, date by sorting & ordering feature
Author: Mrinal Kanti Roy
Version: 1.0
Author URI: http://profiles.wordpress.org/mkrdip/
*/

// Don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Register our styles
 *
 * @return void
 */
function lp_register_widget_styles() {
	wp_register_style( 'latest-posts-widget', plugins_url( 'latest-posts-widget/latest-posts.css' ) );
	wp_enqueue_style( 'latest-posts-widget' );
}

add_action( 'wp_enqueue_scripts', 'lp_register_widget_styles' );

/**
 * Register thumbnail sizes.
 *
 * @return void
 */
function lp_add_image_size() {
	$sizes = get_option( 'mkrdip_latest_posts_thumb_sizes' );

	if ( $sizes ) {
		foreach ( $sizes as $id => $size ) {
			add_image_size( 'latest_posts_thumb_size' . $id, $size[0], $size[1], true );
		}
	}
}

add_action( 'init', 'lp_add_image_size' );


/**
 * Latest Posts Widget Class
 *
 * Shows the latest post with some configurable options
 */
class LP_Post_Widget extends WP_Widget {

	function LP_Post_Widget() {
		$widget_ops = array( 'classname'   => 'latest-posts-widget', 'description' => __( 'List latest posts of your site with thumbnails' ) );
		$this->WP_Widget( 'latest-posts-widget', __( 'Latest Posts' ), $widget_ops );
	}

	// Displays latest posts widget on blog.
	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$sizes = get_option( 'mkrdip_latest_posts_thumb_sizes' );

		$valid_sort_orders = array( 'date', 'title', 'comment_count', 'rand' );
		if ( in_array( $instance['sort_by'], $valid_sort_orders ) ) {
			$sort_by    = $instance['sort_by'];
			$sort_order = ( bool ) isset( $instance['asc_sort_order'] ) ? 'ASC' : 'DESC';
		} else {
			// by default, display latest first
			$sort_by    = 'date';
			$sort_order = 'DESC';
		}

		// Get array of post info.
		$cat_posts = new WP_Query( array(
			'posts_per_page' => $instance['num'],
			'orderby'        => $sort_by,
			'order'          => $sort_order
		) );

		// Excerpt length filter
		$new_excerpt_length = create_function( '$length', "return " . $instance["excerpt_length"] . ";" );

		if ( $instance['excerpt_length'] > 0 ) {
			add_filter( 'excerpt_length', $new_excerpt_length );
		}

		echo $before_widget;

		// Widget title
		if ( ! empty( $instance['title'] ) ) {
			echo $before_title;
			echo $instance['title'];
			echo $after_title;
		}

		// Post list
		echo "<ul>\n";

		while ( $cat_posts->have_posts() ) {
			$cat_posts->the_post();
			?>
			<li class="recent-post-thumb-item">
				<a class="post-title" href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>


				<?php if ( isset( $instance['date'] ) ) : ?>
					<p class="post-date"><?php the_time( "j M Y" ); ?></p>
				<?php endif; ?>

				<?php if ( current_theme_supports( "post-thumbnails" ) && isset( $instance["thumb"] ) && has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'latest_posts_thumb_size' . $this->id ); ?>
					</a>
				<?php endif; ?>

				<?php if ( isset( $instance['excerpt'] ) ) : ?>
					<?php the_excerpt(); ?>
				<?php endif; ?>

				<?php if ( isset( $instance['comment_num'] ) ) : ?>
					<p class="comment-num">(<?php comments_number(); ?>)</p>
				<?php endif; ?>
			</li>
			<?php
		}

		echo "</ul>\n";

		echo $after_widget;

		remove_filter( 'excerpt_length', $new_excerpt_length );

		wp_reset_postdata();
	}

	/**
	 * Update the options
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$sizes = get_option( 'mkrdip_latest_posts_thumb_sizes' );

		if ( !$sizes ) {
			$sizes = array( );
		}

		$sizes[$this->id] = array( $new_instance['thumb_w'], $new_instance['thumb_h'] );
		update_option( 'mkrdip_latest_posts_thumb_sizes', $sizes );

		return $new_instance;
	}

	/**
	 * The widget configuration form back end.
	 *
	 * @param  array $instance
	 * @return void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( ( array ) $instance, array(
			'title'          => __( 'Latest Posts' ),
			'num'            => __( '' ),
			'sort_by'        => __( '' ),
			'asc_sort_order' => __( '' ),
			'excerpt'        => __( '' ),
			'excerpt_length' => __( '' ),
			'comment_num'    => __( '' ),
			'date'           => __( '' ),
			'thumb'          => __( '' ),
			'thumb_w'        => __( '' ),
			'thumb_h'        => __( '' )
		) );

		$title          = $instance['title'];
		$num            = $instance['num'];
		$sort_by        = $instance['sort_by'];
		$asc_sort_order = $instance['asc_sort_order'];
		$excerpt        = $instance['excerpt'];
		$excerpt_length = $instance['excerpt_length'];
		$comment_num    = $instance['comment_num'];
		$date           = $instance['date'];
		$date           = $instance['thumb'];
		$date           = $instance['thumb_w'];
		$date           = $instance['thumb_h'];
		?>

		<p>
			<label for="<?php echo $this->get_field_id( "title" ); ?>">
				<?php _e( 'Title' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id( "title" ); ?>" name="<?php echo $this->get_field_name( "title" ); ?>" type="text" value="<?php echo esc_attr( $instance["title"] ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "num" ); ?>">
				<?php _e( 'Number of posts to show' ); ?>:
				<input style="text-align: center;" id="<?php echo $this->get_field_id( "num" ); ?>" name="<?php echo $this->get_field_name( "num" ); ?>" type="text" value="<?php echo absint( $instance["num"] ); ?>" size='3' />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "sort_by" ); ?>">
				<?php _e( 'Sort by' ); ?>:
				<select id="<?php echo $this->get_field_id( "sort_by" ); ?>" name="<?php echo $this->get_field_name( "sort_by" ); ?>">
					<option value="date"<?php selected( $instance["sort_by"], "date" ); ?>>Date</option>
					<option value="title"<?php selected( $instance["sort_by"], "title" ); ?>>Title</option>
					<option value="comment_count"<?php selected( $instance["sort_by"], "comment_count" ); ?>>Number of comments</option>
					<option value="rand"<?php selected( $instance["sort_by"], "rand" ); ?>>Random</option>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "asc_sort_order" ); ?>">
				<input type="checkbox" class="checkbox"  id="<?php echo $this->get_field_id( "asc_sort_order" ); ?>"
					   name="<?php echo $this->get_field_name( "asc_sort_order" ); ?>"
					   <?php checked( ( bool ) $instance["asc_sort_order"], true ); ?> />
					   <?php _e( 'Reverse sort order (ascending)' ); ?>
			</label>
		</p>


		<p>
			<label for="<?php echo $this->get_field_id( "excerpt" ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( "excerpt" ); ?>" name="<?php echo $this->get_field_name( "excerpt" ); ?>"<?php checked( ( bool ) $instance["excerpt"], true ); ?> />
				<?php _e( 'Show post excerpt' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "excerpt_length" ); ?>">
				<?php _e( 'Excerpt length (in words):' ); ?>
			</label>
			<input style="text-align: center;" type="text" id="<?php echo $this->get_field_id( "excerpt_length" ); ?>" name="<?php echo $this->get_field_name( "excerpt_length" ); ?>" value="<?php echo $instance["excerpt_length"]; ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "comment_num" ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( "comment_num" ); ?>" name="<?php echo $this->get_field_name( "comment_num" ); ?>"<?php checked( ( bool ) $instance["comment_num"], true ); ?> />
				<?php _e( 'Show number of comments' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( "date" ); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( "date" ); ?>" name="<?php echo $this->get_field_name( "date" ); ?>"<?php checked( ( bool ) $instance["date"], true ); ?> />
				<?php _e( 'Show post date' ); ?>
			</label>
		</p>

		<?php if ( function_exists( 'the_post_thumbnail' ) && current_theme_supports( "post-thumbnails" ) ) : ?>
			<p>
				<label for="<?php echo $this->get_field_id( "thumb" ); ?>">
					<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( "thumb" ); ?>" name="<?php echo $this->get_field_name( "thumb" ); ?>"<?php checked( ( bool ) $instance["thumb"], true ); ?> />
					<?php _e( 'Show post thumbnail' ); ?>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Thumbnail dimensions (in pixels)' ); ?>:<br />
					<label for="<?php echo $this->get_field_id( "thumb_w" ); ?>">
						Width: <input class="widefat" style="width:30%;" type="text" id="<?php echo $this->get_field_id( "thumb_w" ); ?>" name="<?php echo $this->get_field_name( "thumb_w" ); ?>" value="<?php echo $instance["thumb_w"]; ?>" />
					</label>

					<label for="<?php echo $this->get_field_id( "thumb_h" ); ?>">
						Height: <input class="widefat" style="width:30%;" type="text" id="<?php echo $this->get_field_id( "thumb_h" ); ?>" name="<?php echo $this->get_field_name( "thumb_h" ); ?>" value="<?php echo $instance["thumb_h"]; ?>" />
					</label>
				</label>
			</p>
		<?php endif; ?>

		<?php
	}

}

add_action( 'widgets_init', create_function( '', 'return register_widget("LP_Post_Widget");' ) );

<?php
/*
 * Plugin Name: Popular Posts Tabbed Widget for Jetpack
 * Plugin URI: 
 * Description: Shows a tabbed widget for most popular, most commented and latest blog posts. Most popular posts tab uses data from Jetpack Stats module.
 * Author: Ryann Micua
 * Author URI: http://pogidude.com/about
 * Version: 1.3
 * Stable Tag: 1.3
 * License: GPL2+
 * Text Domain: pptwj
 * Domain Path: /languages/
 */

define( 'PPTWJ_VERSION', '1.3');
//e.g. /var/www/example.com/wordpress/wp-content/plugins/plugin-slug
define( "PPTWJ_DIR", plugin_dir_path( __FILE__ ) );
//e.g. http://example.com/wordpress/wp-content/plugins/plugin-slug
define( "PPTWJ_URL", plugin_dir_url( __FILE__ ) );
//e.g. after-post-action-box/after-post-action-box.php
define( 'PPTWJ_BASENAME', plugin_basename( __FILE__ ) );
define( 'PPTWJ_ASSETS_URL', trailingslashit( PPTWJ_URL ) . 'assets/' );
define( 'PPTWJ_DOMAIN', 'pptwj' );

/**
 * Load admin file
 */
include PPTWJ_DIR . '/admin.php';

/**
 * Load languages
 */
include PPTWJ_DIR . '/load-lang.php';

/**
 * Register the widget for use in Appearance -> Widgets
 */
add_action( 'widgets_init', 'pptwj_init' );

function pptwj_init() {
	register_widget( 'Popular_Posts_Tabbed_Widget_Jetpack' );
}

class Popular_Posts_Tabbed_Widget_Jetpack extends WP_Widget {

	protected $defaults;
	protected $popular_days = 0;
	private static $_days = 0;
	private static $_stats_enabled = false;
	private static $current_instance = null;
	const _tablename = 'popularpostsdata';
	
	function Popular_Posts_Tabbed_Widget_Jetpack () {
		$this->__construct();
	}
	
	function __construct(){
		
		include_once PPTWJ_DIR . 'get-the-image.php';

		/**
		 * Check if Jetpack is connected to WordPress.com and Stats module is enabled
		 */
		// Currently, this widget depends on the Stats Module
		if (
			( !defined( 'IS_WPCOM' ) || !IS_WPCOM )
		&&
			!function_exists( 'stats_get_csv' )
		) {
			self::$_stats_enabled = false;
		} else {
			self::$_stats_enabled = true;
		}

		/* Set up some default widget settings. */
		$this->defaults = array(
			'number' => 5, 
			'thumb_size' => 45, 
			'order' => self::$_stats_enabled ? 'pop' : 'latest', 
			'days' => '60', 
			'show_views' => '',
			'show_date' => '',
			'pop' => self::$_stats_enabled ? 'off' : 'on',
			'latest' => '', 
			'comments' => '',
			'popular_range' => 'all',
			'comments_range' => 'daily'
		);

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'pptwj', 'description' => __( 'This widget is the Tabs that classically goes into the sidebar. It contains the Popular posts, Latest Posts and Recent comments.', 'pptwj') );

		/* Widget control settings. */
		$control_ops = array( 
			'width' => 240, 
			'height' => 300, 
			'id_base' => 'pptwj' 
		);

		/* Create the widget. */
		parent::__construct( 'pptwj', __('Popular Posts Tabbed Widget', 'pptwj' ), $widget_ops, $control_ops );

		/* Load scripts and css */
		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_enqueue_scripts', array('Popular_Posts_Tabbed_Widget_Jetpack','enqueueAssets' ) );
			//setup action to handle ajax call
			add_action( 'wp_ajax_pptwj_tabwidget_list', array( 'Popular_Posts_Tabbed_Widget_Jetpack', 'ajaxProcessor') );
			add_action( 'wp_ajax_nopriv_pptwj_tabwidget_list', array( 'Popular_Posts_Tabbed_Widget_Jetpack', 'ajaxProcessor' ) );
		}
	}

   /*----------------------------------------
	  update()
	  ----------------------------------------
	
	* Function to update the settings from
	* the form() function.
	
	* Params:
	* - Array $new_instance
	* - Array $old_instance
	----------------------------------------*/
	
	function update ( $new_instance, $old_instance ) {
		
		$defaults = $this->defaults;

		$instance = $old_instance;
		
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['number'] = intval( $new_instance['number'] );
		$instance['thumb_size'] = intval( $new_instance['thumb_size'] );
		//$instance['days'] = intval( $new_instance['days'] );

		$instance['show_date'] = isset( $new_instance['show_date'] ) ? esc_attr( $new_instance['show_date'] ) : '';
		$instance['show_views'] = isset( $new_instance['show_views'] ) ? esc_attr( $new_instance['show_views'] ) : '';

		$instance['order'] = esc_attr( $new_instance['order'] );
		$instance['pop'] = isset( $new_instance['pop'] ) ? esc_attr( $new_instance['pop'] ) : '';
		$instance['latest'] = isset( $new_instance['latest'] ) ? esc_attr( $new_instance['latest'] ) : '';
		$instance['comments'] = isset( $new_instance['comments'] ) ? esc_attr( $new_instance['comments'] ) : '';
		$instance['popular_range'] = isset( $new_instance['popular_range'] ) ? $new_instance['popular_range'] : $defaults['popular_range'];
		$instance['comments_range'] = isset( $new_instance['comments_range'] ) ? $new_instance['comments_range'] : $defaults['comments_range'];

		if( !self::$_stats_enabled ){
			$instance['pop'] = 'on';

			if( $new_instance['order'] == 'pop' ){
				//'pop' cannot be the first visible tab since it is disabled
				$instance['order'] = $defaults['order'];
			}
		}
		
		return $instance;
		
	} // End update()

   /*----------------------------------------
	 form()
	 ----------------------------------------
	  
	  * The form on the widget control in the
	  * widget administration area.
	  
	  * Make use of the get_field_id() and 
	  * get_field_name() function when creating
	  * your form elements. This handles the confusing stuff.
	  
	  * Params:
	  * - Array $instance
	----------------------------------------*/

   function form( $instance ) { 

		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$title = isset( $instance['title'] ) ?esc_attr($instance['title']) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'pptwj'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<p>
		   <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts:', 'pptwj' ); ?>
		   <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo isset( $instance['number'] ) ? $instance['number'] : ''; ?>" />
		   </label>
		</p>
		<p>
		   <label for="<?php echo $this->get_field_id( 'thumb_size' ); ?>"><?php _e( 'Thumbnail Size (0=disable):', 'pptwj' ); ?>
		   <input class="widefat" id="<?php echo $this->get_field_id( 'thumb_size' ); ?>" name="<?php echo $this->get_field_name( 'thumb_size' ); ?>" type="text" value="<?php echo isset( $instance['thumb_size'] ) ? $instance['thumb_size'] : ''; ?>" />
		   </label>
		</p>

		<!--p>
			<label><input id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" <?php checked( $instance['show_date'], 'on' ); ?>> <?php _e( 'Display Publish Date', 'pptwj' ); ?></label>
		</p-->

		<p>
			<label><input id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" type="checkbox" <?php checked( $instance['show_views'], 'on' ); ?>> <?php _e( 'Display Page Views', 'pptwj' ); ?></label>
		</p>

		<?php /*
		<p>
		   <label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e( 'Popular limit (days):', 'pptwj' ); ?>
		   <input class="widefat" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo isset( $instance['days'] ) ? $instance['days'] : ''; ?>" />
		   </label>
		</p>
		*/ ?>
		<p>
			<?php $order = isset( $instance['order'] ) ? $instance['order'] : 'pop'; ?>
		    <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'First Visible Tab:', 'pptwj' ); ?></label>
		    <select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
		        <option value="pop" <?php selected( $order, 'pop' ); ?>><?php _e( 'Popular', 'pptwj' ); ?></option>
		        <option value="latest" <?php selected( $order, 'latest' ); ?>><?php _e( 'Latest', 'pptwj' ); ?></option>
		        <option value="comments" <?php selected( $order, 'comments' ); ?>><?php _e( 'Comments', 'pptwj' ); ?></option>
		    </select>
		</p>
		<p><strong><?php _e( 'Hide Tabs:', 'pptwj' ); ?></strong></p>

		<?php if( !self::$_stats_enabled ) : ?>
			<div class="pptwj-require-error" style="background: #FFEBE8; border: 1px solid #c00; color: #333; margin: 1em 0; padding: 3px 5px; "><?php _e('Popular Posts tab requires the <a href="http://wordpress.org/extend/plugins/jetpack/" target="_blank">Jetpack plugin</a> to be activated and connected. It also requires the Jetpack Stats module to be enabled.', 'pptwj' ); ?></div>
		<?php endif; ?>

		<p>
			<label><input id="<?php echo $this->get_field_id( 'pop' ); ?>" name="<?php echo $this->get_field_name( 'pop' ); ?>" type="checkbox" <?php checked( $instance['pop'], 'on' ); ?>> <?php _e( 'Popular', 'pptwj' ); ?></label>
		</p>
		<p>
		   <label><input id="<?php echo $this->get_field_id( 'latest' ); ?>" name="<?php echo $this->get_field_name( 'latest' ); ?>" type="checkbox" <?php checked( $instance['latest'], 'on' ); ?>> <?php _e( 'Latest', 'pptwj' ); ?></label>
		</p>
		<p>
		   <label><input id="<?php echo $this->get_field_id( 'comments' ); ?>" name="<?php echo $this->get_field_name( 'comments' ); ?>" type="checkbox" <?php checked( $instance['comments'], 'on' ); ?>> <?php _e( 'Comments', 'pptwj' ); ?></label>
		</p>

		<p>
			<strong><?php _e( 'Default Date Range', 'pptwj' ); ?></strong><br />
			<small><?php _e('Select the default range that would be shown for each relevant tabs on page load', 'pptwj'); ?></small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('popular_range'); ?>"><?php _e('Popular Posts Date Range', 'pptwj'); ?></label>
			<?php $popular_range = $instance['popular_range']; ?>
			<select id="<?php echo $this->get_field_id( 'popular_range' ); ?>" name="<?php echo $this->get_field_name( 'popular_range' ); ?>" class="widefat">
				<option value="all" <?php selected( $popular_range, 'all' ); ?>><?php _e('All', 'pptwj'); ?></option>
				<option value="monthly" <?php selected( $popular_range, 'monthly' ); ?>><?php _e('Monthly', 'pptwj'); ?></option>
				<option value="weekly" <?php selected( $popular_range, 'weekly' ); ?>><?php _e('Weekly', 'pptwj'); ?></option>
				<option value="daily" <?php selected( $popular_range, 'daily' ); ?>><?php _e('Daily', 'pptwj'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('comments_range'); ?>"><?php _e('Commented Posts Date Range', 'pptwj'); ?></label>
			<?php $comments_range = $instance['comments_range']; ?>
			<select id="<?php echo $this->get_field_id( 'comments_range' ); ?>" name="<?php echo $this->get_field_name( 'comments_range' ); ?>" class="widefat">
				<option value="all" <?php selected( $comments_range, 'all' ); ?>><?php _e('All', 'pptwj'); ?></option>
				<option value="monthly" <?php selected( $comments_range, 'monthly' ); ?>><?php _e('Monthly', 'pptwj'); ?></option>
				<option value="weekly" <?php selected( $comments_range, 'weekly' ); ?>><?php _e('Weekly', 'pptwj'); ?></option>
				<option value="daily" <?php selected( $comments_range, 'daily' ); ?>><?php _e('Daily', 'pptwj'); ?></option>
			</select>
		</p>
		<?php

	} // End form()


	function widget($args, $instance) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );
		self::$current_instance = $instance;

		extract( $args );
	
		$number = isset( $instance['number'] ) ? $instance['number'] : 5;
		$thumb_size = intval( $instance['thumb_size'] );
		$order = isset( $instance['order'] ) ? $instance['order'] : 'pop';
		//$days = isset( $instance['days'] ) ? $instance['days'] : '';
		$pop = ''; if ( array_key_exists( 'pop', $instance ) ) $pop = $instance['pop'];
		$latest = ''; if ( array_key_exists( 'latest', $instance ) ) $latest = $instance['latest'];
		$comments = ''; if ( array_key_exists( 'comments', $instance ) ) $comments = $instance['comments'];
		$popular_range = $instance['popular_range'];
		$comments_range = $instance['comments_range'];

		$filter_links = array('day' => __('Today', 'pptwj'),'week' => __('Week','pptwj'),'month' => __('Month','pptwj'), 'all' => __('All','pptwj') );

		$data = array(
			'time' => '',
			'tab' => '',
			'numberposts' => $number,
			'thumb' => $thumb_size
		);

		$title = $instance['title'];
		?>
	
			<?php echo $before_widget; ?>

			<?php
			/* If a title was input by the user, display it. */
			if ( !empty( $title ) ){ 
				echo $before_title . apply_filters( 'widget_title', $title, $instance, $this->id_base ) . $after_title;
			} ?>

			<div class="pptwj-tabs-wrap">
	
				<ul class="tab-links">
					<?php if ( $order == "latest" && !$latest == "on") { ?><li class="latest"><a href="#tab-latest"><?php _e( 'Latest', 'pptwj' ); ?></a></li>
					<?php } elseif ( $order == "comments" && !$comments == "on") { ?><li class="comments"><a href="#tab-comm"><?php _e( 'Comments', 'pptwj' ); ?></a></li>
					<?php } ?>
					
					<?php if (!$pop == "on") { ?><li class="popular"><a href="#tab-pop"><?php _e( 'Popular', 'pptwj' ); ?></a></li><?php } ?>
					<?php if ($order <> "comments" && !$comments == "on") { ?><li class="comments"><a href="#tab-comm"><?php _e( 'Comments', 'pptwj' ); ?></a></li><?php } ?>
					<?php if ($order <> "latest" && !$latest == "on") { ?><li class="latest"><a href="#tab-latest"><?php _e( 'Latest', 'pptwj' ); ?></a></li><?php } ?>
				</ul>
				
				<div class="clear"></div>
	
				<div class="boxes box inside">
	
					<?php if ( $order == "latest" && !$latest == "on") { ?>
					<div id="tab-latest">
						<ul class="list">
							<?php echo self::showLatest( $number, $thumb_size ); ?>
						</ul>
					</div><!-- #tab-latest -->
					<?php } elseif ( $order == "comments" && !$comments == "on") { ?>
					<div id="tab-comm">
						<ul class="tab-filter-list" data-type="comments">
							<li>
								<?php 
								foreach( $filter_links as $key => $val ): ?>
									<a href="#" data-time="<?php echo $key; ?>" data-numberposts="<?php echo $number; ?>" data-thumb="<?php echo $thumb_size; ?>" data-tab="commented"><?php echo $val; ?></a>
								<?php endforeach; ?>
							</li>
						</ul>
						<ul class="list">
							<?php echo self::showMostCommented( $number, $thumb_size, $comments_range ); ?>
						</ul>
					</div><!-- #tab-comm -->
					<?php } ?>
	
					<?php if (!$pop == "on") { ?>
					<div id="tab-pop">
						<ul class="tab-filter-list" data-type="popular">
							<li>
								<?php 
								foreach( $filter_links as $key => $val ): ?>
									<a href="#" data-time="<?php echo $key; ?>" data-numberposts="<?php echo $number; ?>" data-thumb="<?php echo $thumb_size; ?>" data-tab="popular"><?php echo $val; ?></a>
								<?php endforeach; ?>
							</li>
						</ul>
						<ul class="list">
							<?php echo self::showMostViewed( $number, $thumb_size, $popular_range ); ?>
						</ul>
					</div><!-- #tab-pop -->
					<?php } ?>
					<?php if ($order <> "latest" && !$latest == "on") { ?>
					<div id="tab-latest">
						<ul class="list">
							<?php echo self::showLatest( $number, $thumb_size ); ?>
						</ul>
					</div><!-- #tab-latest -->
					<?php } ?>
					<?php if ($order <> "comments" && !$comments == "on") { ?>
					<div id="tab-comm">
						<ul class="tab-filter-list" data-type="comments">
							<li>
								<?php 
								foreach( $filter_links as $key => $val ): ?>
									<a href="#" data-time="<?php echo $key; ?>" data-numberposts="<?php echo $number; ?>" data-thumb="<?php echo $thumb_size; ?>" data-tab="commented"><?php echo $val; ?></a>
								<?php endforeach; ?>
							</li>
						</ul>
						<ul class="list">
							<?php echo self::showMostCommented( $number, $thumb_size, $comments_range ); ?>
						</ul>
					</div><!-- #tab-comm -->
					<?php } ?>
					<div class="pptwj-loader"><img src="<?php echo includes_url(); ?>/images/wpspin-2x.gif" alt="Ajax spinner"></div>
				</div><!-- /.boxes -->
			</div><!-- /pptwj-tabs-wrap -->

			<?php echo $after_widget; ?>
			<?php
	}

	
	/**
	 * Display Latest posts
	 */
	static function showLatest( $posts = 5, $size = 45 ) {
		global $post;
		$latest = get_posts( array( 'suppress_filters' => false, 'ignore_sticky_posts' => 1, 'orderby' => 'post_date', 'order' => 'desc', 'numberposts' => $posts ) );
		
		ob_start();
		$count = 1;
		$date_format = get_option('date_format');
		foreach($latest as $post) :
			setup_postdata($post);
			if( $count++ % 2 ){ 
				$class = 'odd'; 
			} else {
				$class = 'even';
			}
		?>
			<li class="<?php echo $class; ?>">
				<?php if ($size <> 0){
					$imageArgs = array(
						'width' => $size,
						'height' => $size,
						'image_class' => 'thumbnail',
						'format' => 'array',
						'default_image' => self::getWidgetUrl() . 'default.png'
					);
					$postImage = pptwj_get_the_image($imageArgs); 
				}
				?>
				<?php if( !empty( $postImage['src'] ) ): 
					$css = "width: {$size}px; height: {$size}px;";
				?>
				<a class="post-thumb" href="<?php the_permalink(); ?>"><img src="<?php echo $postImage['src']; ?>" alt="<?php echo $postImage['alt']; ?>" width="<?php echo $postImage['width']; ?>" height="<?php echo $postImage['height']; ?>" style="<?php echo $css; ?>"/></a>
				<?php endif; ?>
				
				<a class="item-title" title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

				<?php echo self::item_meta(get_the_time($date_format, $post)); ?>

				<div class="fix"></div>
			</li>
		<?php endforeach;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}


	
	/**
	 * Display most commented posts
	 */
	static function showMostCommented( $posts = 5, $size = 45, $days = null ) {
		global $post; 

		$args = array(
			'limit' => $posts,
			'range' => $days,
			'order_by' => 'comments'
		);
		$popular = self::get_posts_by_popular_posts_plugin( $args );
		
		ob_start();
		
		if( !$popular ):
			?>
			<li><?php _e('Sorry. No data yet.', 'pptwj' ); ?></li>
			<?php
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		endif;
		
		$count = 1;
		foreach($popular as $p) :
			if( $count++ % 2 ){ 
				$class = 'odd'; 
			} else {
				$class = 'even';
			}
		
		?>
		<li class="<?php echo $class; ?>">
			<?php if ($size <> 0){
				$imageArgs = array(
					'width' => $size,
					'height' => $size,
					'image_class' => 'thumbnail',
					'format' => 'array',
					'default_image' => self::getWidgetUrl() . 'default.png'
				);
				$postImage = pptwj_get_the_image($imageArgs, $p['id']); 
			}
			?>
			<?php if( !empty( $postImage['src'] ) ): 
				$css = "width: {$size}px; height: {$size}px;";
			?>
				<a class="post-thumb" href="<?php echo $p['permalink']; ?>"><img src="<?php echo $postImage['src']; ?>" alt="<?php echo $postImage['alt']; ?>" width="<?php echo $postImage['width']; ?>" height="<?php echo $postImage['height']; ?>" style="<?php echo $css; ?>"/></a>
			<?php endif; ?>
			<a class="item-title" title="<?php echo $p['title']; ?>" href="<?php echo $p['permalink']; ?>"><?php echo $p['title']; ?></a>
			<?php echo self::item_meta($p['postdate']); ?>
			<div class="fix"></div>
		</li>
		<?php endforeach;
		
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
	
	/**
	 * Display most viewed
	 */
	static function showMostViewed( $posts = 5, $size = 45, $days = 'all' ) {
		global $post; 

		$args = array(
			'limit' => $posts,
			'range' => $days
		);

		$popular = self::get_posts_by_wp_com( $args );

		ob_start();

		if( !$popular ):
			$message = !self::$_stats_enabled ? __('<a href="http://wordpress.org/extend/plugins/jetpack/" target="_blank">Jetpack plugin</a> with Stats module needs to be enabled.', 'pptwj') : __('Sorry. No data yet.', 'pptwj');
			?>
			<li><?php echo $message; ?></li>
			<?php
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		endif;
		
		$count = 1;
		foreach($popular as $p) :
			if( $count++ % 2 ){ 
				$class = 'odd'; 
			} else {
				$class = 'even';
			}
		
		?>
		<li class="<?php echo $class; ?>">
			<?php if ($size <> 0){
				$imageArgs = array(
					'width' => $size,
					'height' => $size,
					'image_class' => 'thumbnail',
					'format' => 'array',
					'default_image' => self::getWidgetUrl() . 'default.png'
				);
				$postImage = pptwj_get_the_image($imageArgs, $p['id']); 
			}
			?>
			<?php if( !empty( $postImage['src'] ) ): 
				$css = "width: {$size}px; height: {$size}px;";
			?>
				<a class="post-thumb" href="<?php echo $p['permalink']; ?>"><img src="<?php echo $postImage['src']; ?>" alt="<?php echo $postImage['alt']; ?>" width="<?php echo $postImage['width']; ?>" height="<?php echo $postImage['height']; ?>" style="<?php echo $css; ?>"/></a>
			<?php endif; ?>
			<a class="item-title" title="<?php echo $p['title']; ?>" href="<?php echo $p['permalink']; ?>"><?php echo $p['title']; ?></a>
			<?php echo self::item_meta($p['postdate'], $p['views']); ?>
			<div class="fix"></div>
		</li>
		<?php endforeach;
		
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}


	/**
	 * Uses data gathered by Jetpack stats and stored in WordPress.com servers
	 */
	static function get_posts_by_wp_com( $args ){

		if( !self::$_stats_enabled || !function_exists('stats_get_csv'))
			return array();

		$defaults = array(
			'limit' => 5,
			'range' => 'all', //daily|weekly|monthly|all
			'post_type' => 'post',
			'date_format' => get_option('date_format')
			);
		$args = wp_parse_args( (array) $args, $defaults );

		$limit = intval( $args['limit'] );

		/** TODO: limit $limit to 100? **/

		$days = 2;
		switch( $args['range'] ){
			case 'weekly':  $days = 7; break;
			case 'monthly': $days = 30; break;
			case 'daily' :  $days = 2; break; //make this 2 days to account for timezone differences
			case 'all':
			default:        $days = -1; break; //get all
		}

		/** we only limit to 50 posts. but change this if you want **/
		$top_posts = stats_get_csv( 'postviews', array( 'days' => $days, 'limit' => 50 ) );

		if( !$top_posts ){
			return array();
		}

		/** Store post_id into array **/
		$post_view_ids = array_filter( wp_list_pluck( $top_posts, 'post_id' ) );
		if ( !$post_view_ids ) {
			return array();
		}

		// cache
		get_posts( array( 'include' => join( ',', array_unique( $post_view_ids ) ) ) );

		// return posts list
		$posts = array();
		$counter = 0;
		foreach( $top_posts as $top_post ){

			//should only trigger for homepage
			if(empty($top_post['post_id']))
				continue;
			
			$post = get_post( $top_post['post_id'] );

			if ( !$post )
				continue;

			if( $args['post_type'] != $post->post_type )
				continue;

			$permalink = get_permalink( $post->ID );
			$postdate = date_i18n( $args['date_format'], strtotime( $post->post_date ) );
			$views = number_format_i18n( $top_post['views'] );

			if ( empty( $post->post_title ) ) {
				$title_source = $post->post_content;
				$title = wp_html_excerpt( $title_source, 50 );
				$title .= '&hellip;';
			} else {
				$title = $post->post_title;
			}
			
			$data = array(
				'title' => $title,
				'permalink' => $permalink,
				'views' => $views,
				'id' => $post->ID,
				'postdate' => $postdate
			);
			
			$posts[] = $data;
			$counter++;

			if( $counter == $limit )
				break;

		}

		return $posts;

	}

	/**
	 * Uses data gathered using WP Popular Posts plugin
	 * @url http://wordpress.org/extend/plugins/wordpress-popular-posts
	 *
	 * As of v1.0: Currently used only for grabbing most commented posts within certain period.
	 * Isn't yet used to grab data from WP Popular Posts plugin. Maybe later.
	 * 
	 * @param  array $args
	 * @return array
	 */
	static function get_posts_by_popular_posts_plugin( $args ){
		$defaults = array(
			'limit' => 5,
			'order_by' => 'views',
			'range' => 'all', //daily|weekly|monthly|all
			'post_type' => 'post',
			'date_format' => get_option('date_format'),
			'stats_tag' => array(
				'comment_count' => false,
				'views' => false,
				'author' => false
			)
		);
		$args = wp_parse_args( (array) $args, $defaults );
		
		global $wpdb;
		$table = $wpdb->prefix.self::_tablename;
			
		$fields = "";
		$join = "";
		$where = "";
		$having = "";
		$orderby = "";
		
		if( $args['range'] == 'all' ){
		/* RANGE: All */
			
			//views
			if ($args['order_by'] == "views" || $args['order_by'] == "avg" || $args['stats_tag']['views']) {
				$join .= " LEFT JOIN {$table} v ON p.ID = v.postid ";
					
				if ( $args['order_by'] == "avg" ) {
					$fields .= ", ( IFNULL(v.pageviews, 0)/(IF ( DATEDIFF('".self::now()."', MIN(v.day)) > 0, DATEDIFF('".self::now()."', MIN(v.day)), 1) )) AS 'avg_views' ";
				} else {						
					$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews' ";							
				}
			}
			
			// comments
			if ($args['order_by'] == "comments" || $args['stats_tag']['comment_count']) {
				$fields .= ", p.comment_count AS 'comment_count' ";
			}
			
		} elseif( $args['range'] == 'yesterday' || $args['range'] == 'daily' ){
		/* RANGE: Daily */
		
			// views				
			if ($args['order_by'] == "views" || $args['order_by'] == "avg" || $args['stats_tag']['views']) {
				$join .= " LEFT JOIN (SELECT id, SUM(pageviews) AS 'pageviews', day FROM (SELECT id, pageviews, day FROM {$table}cache WHERE day > DATE_SUB('".self::now()."', INTERVAL 1 DAY) ORDER BY day) sv GROUP BY id) v ON p.ID = v.id ";
					
				$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews' ";
			}
			
			// comments
			if ($args['order_by'] == "comments" || $args['stats_tag']['comment_count']) {
				$fields .= ", IFNULL(c.comment_count, 0) AS 'comment_count' ";
				$join .= " LEFT JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS 'comment_count' FROM $wpdb->comments WHERE comment_approved = 1 AND comment_date > DATE_SUB('".self::now()."', INTERVAL 1 DAY) GROUP BY comment_post_ID ORDER BY comment_date DESC) c ON p.ID = c.comment_post_ID ";
			}
		
		} elseif( $args['range'] == 'weekly' ){
		/* RANGE: Weekly */
		
			// views				
			if ($args['order_by'] == "views" || $args['order_by'] == "avg" || $args['stats_tag']['views']) {
				$join .= " LEFT JOIN (SELECT id, SUM(pageviews) AS 'pageviews', day FROM (SELECT id, pageviews, day FROM {$table}cache WHERE day > DATE_SUB('".self::now()."', INTERVAL 1 WEEK) ORDER BY day) sv GROUP BY id) v ON p.ID = v.id ";
										
				if ( $args['order_by'] == "avg" ) {
					$fields .= ", ( IFNULL(v.pageviews, 0)/(IF ( DATEDIFF('".self::now()."', MIN(v.day)) > 0, DATEDIFF('".self::now()."', MIN(v.day)), 1) )) AS 'avg_views' ";
				} else {						
					$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews' ";							
				}					
			}
			
			// comments
			if ($args['order_by'] == "comments" || $args['stats_tag']['comment_count']) {
				$fields .= ", IFNULL(c.comment_count, 0) AS 'comment_count' ";
				$join .= " LEFT JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS 'comment_count' FROM $wpdb->comments WHERE comment_approved = 1 AND comment_date > DATE_SUB('".self::now()."', INTERVAL 1 WEEK) GROUP BY comment_post_ID ORDER BY comment_date DESC) c ON p.ID = c.comment_post_ID ";
			}
		
		} elseif( $args['range'] == 'monthly' ){
		/* RANGE: Monthly - last 30 days*/

			// views				
			if ($args['order_by'] == "views" || $args['order_by'] == "avg" || $args['stats_tag']['views']) {
				$join .= " LEFT JOIN (SELECT id, SUM(pageviews) AS 'pageviews', day FROM (SELECT id, pageviews, day FROM {$table}cache WHERE day > DATE_SUB('".self::now()."', INTERVAL 1 MONTH) ORDER BY day) sv GROUP BY id) v ON p.ID = v.id ";
										
				if ( $args['order_by'] == "avg" ) {
					$fields .= ", ( IFNULL(v.pageviews, 0)/(IF ( DATEDIFF('".self::now()."', MIN(v.day)) > 0, DATEDIFF('".self::now()."', MIN(v.day)), 1) )) AS 'avg_views' ";
				} else {						
					$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews' ";							
				}					
			}
			
			// comments
			if ($args['order_by'] == "comments" || $args['stats_tag']['comment_count']) {
				$fields .= ", IFNULL(c.comment_count, 0) AS 'comment_count' ";
				$join .= " LEFT JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS 'comment_count' FROM $wpdb->comments WHERE comment_approved = 1 AND comment_date > DATE_SUB('".self::now()."', INTERVAL 1 MONTH) GROUP BY comment_post_ID ORDER BY comment_date DESC) c ON p.ID = c.comment_post_ID ";
			}
			
		}//endif
		
		// sorting options
		switch( $args['order_by'] ) {
			case 'comments':
				if ($args['range'] == "all") {
					$where .= " AND p.comment_count > 0 ";
					$orderby = 'p.comment_count';
				} else {
					$where .= " AND c.comment_count > 0 ";
					$orderby = 'c.comment_count';
				}
				break;
					
			case 'views':
				$where .= " AND v.pageviews > 0 ";
				$orderby = 'v.pageviews';
				break;
					
			case 'avg':
				if ($args['range'] == "yesterday" || $args['range'] == "daily") {
					$where .= " AND v.pageviews > 0 ";
					$orderby = 'v.pageviews';
				} else {
					$having = " HAVING avg_views > 0.0000 ";
					$orderby = 'avg_views';
				}
					
				break;
					
			default:
				$orderby = 'v.pageviews';
				break;
		}//endswitch

		// post filters			
		// * post types - based on code seen at https://github.com/williamsba/WordPress-Popular-Posts-with-Custom-Post-Type-Support
		$post_types = explode(",", $args['post_type']);
		$i = 0;
		$len = count($post_types);
		$sql_post_types = "";
					
		if ($len > 1) { // we are getting posts from more that one ctp				
			foreach ( $post_types as $post_type ) {
				$sql_post_types .= "'" .$post_type. "'";
					
				if ($i != $len - 1) $sql_post_types .= ",";
					
				$i++;
			}

			$where .= " AND p.post_type IN({$sql_post_types}) ";
		} else if ($len == 1) { // post from one ctp only
			$where .= " AND p.post_type = '".$args['post_type']."' ";
		}
		
		/** Form Query **/
		$query = "SELECT p.ID AS 'id', p.post_title AS 'title', p.post_date AS 'date', p.post_author AS 'uid' {$fields} FROM {$wpdb->posts} p {$join} WHERE p.post_status = 'publish' AND p.post_password = '' {$where} GROUP BY p.ID {$having} ORDER BY {$orderby} DESC LIMIT " . $args['limit'] . ";";
		
		$mostpopular = $wpdb->get_results($query);

		if( !is_array( $mostpopular ) || empty( $mostpopular ) ){ //no posts to show
			return array();
		} else {
			// return posts list
			$posts = array();
			foreach( $mostpopular as $p ){
				$permalink = get_permalink( $p->id );
				$postdate = date_i18n( $args['date_format'], strtotime( $p->date ) );
				$pageviews = ($args['order_by'] == "views" || $args['order_by'] == "avg" || $args['stats_tag']['views']) ? (($args['order_by'] == "views" || $args['order_by'] == "comments") ? number_format($p->pageviews) : ( ($args['range'] == "yesterday" || $args['range'] == "daily") ? number_format($p->pageviews) : number_format($p->avg_views, 2)) ) : 0;
				$title = $p->title;
				$title = strip_tags( $title );
				
				$data = array(
					'title' => $title,
					'permalink' => $permalink,
					'views' => $pageviews,
					'id' => $p->id,
					'postdate' => $postdate
				);
				array_push( $posts, $data );
			}
			return $posts;
		}
		
	}//get_posts_by_popular_posts_plugin()

	static function get_posts_formatted( $post_ids, $count, $date_format = '' ){

		if( empty( $date_format ) )
			$date_format = get_option('date_format');

		$counter = 0;

		$posts = array();
		foreach ( (array) $post_ids as $id ) {
			$post = get_post( $id );
		
			// hide private and password protected posts
			if ( 'publish' != $post->post_status || !empty( $post->post_password ) || empty( $post->ID ) )
				continue;

			// Both get HTML stripped etc on display
			if ( empty( $post->post_title ) ) {
				$title_source = $post->post_content;
				$title = wp_html_excerpt( $title_source, 50 );
				$title .= '&hellip;';
			} else {
				$title = $post->post_title;
			}
			
			$postdate = date_i18n( $date_format, strtotime( $post->post_date ) );
			$permalink = get_permalink( $post->ID );
	
			$posts[] = compact( 'title', 'permalink', 'id' );
			$counter++;
		
			if ( $counter == $count )
				break; // only need to load and show x number of likes
		}

		return $posts;
	}
	
	/**
	 * Display the meta data for each list item
	 * @param  string $date
	 * @return string html
	 */
	static function item_meta($date = '', $views = ''){
		$instance = self::$current_instance;
		$output = '';

		//this will not show views with 0 count
		if(!empty($views) && $instance['show_views'] == 'on'){
			$views_text = $views > 1 ? ' views' : ' view';
			$output .= '<span class="pptwj-views-count">(' . $views . $views_text. ')</span>';
		}

		if($date && $instance['show_date'] == 'on'){
			$output .= empty($output) ? '' : ' '; //add space
			$output .= '<span class="pptwj-date">' . $date . '</span>';
		}

		return '<span class="meta">'. $output . '</span>';
	}

	static function now(){
		return current_time('mysql');
	}

	static function ajaxProcessor(){
		$data = stripslashes_deep( $_POST );
		if( $data['action'] != 'pptwj_tabwidget_list' ) return;
		
		$thumb = intval( $data['thumb'] );
		$numberposts = intval( $data['numberposts'] );
		
		switch( $data['time'] ){
			case 'day':    $time = 'daily'; break;
			case 'week' :  $time = 'weekly'; break;
			case 'month' : $time = 'monthly'; break;
			case 'all' :
			default:       $time = 'all'; break;
		}
		
		$contents = '';
		switch( $data['tab'] ){
			case 'commented':
				$contents = self::showMostCommented( $numberposts, $thumb, $time );
				break;
			case 'popular':
				$contents = self::showMostViewed( $numberposts, $thumb, $time );
				break;
			default:
				$contents = '';
				break;
		}
		
		echo $contents;
		exit;
	}

	/**
	 * Be sure to set the correct URL below
	 */
	protected static function getWidgetUrl(){
		return PPTWJ_URL;
	}

	/**
	 * Enqueue scripts and styles
	 * @return none
	 */
	static function enqueueAssets(){
			$widgetUrl = self::getWidgetUrl();
			wp_enqueue_script('pptwj-widget-tab-js', $widgetUrl . 'tab.js', array( 'jquery' ) );
			wp_enqueue_style( 'pptwj-widget-tab-css', $widgetUrl . 'tab.css' );
			
			$js_vars = array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) );
			
			wp_localize_script( 'pptwj-widget-tab-js', 'PPTWJ', $js_vars );
	}
} // End Class
?>
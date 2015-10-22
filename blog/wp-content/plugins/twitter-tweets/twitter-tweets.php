<?php
/**
 * Plugin Name: Twitter Tweets
 * Version: 0.9
 * Description: Display latest tweets on WordPress blog from Twitter account.
 * Author: WebLizar
 * Author URI: http://www.weblizar.com
 * Plugin URI: http://www.weblizar.com/plugins/
 */

/**
 * Constant Values & Variables
 */
define("WEBLIZAR_TWITTER_PLUGIN_URL", plugin_dir_url(__FILE__));
define("WEBLIZAR_TWITTER_TEXT_DOMAIN", "weblizar_twitter");

/**
 * Widget Code.
 */
class WeblizarTwitter extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'weblizar_twitter', // Base ID
            'Twitter Tweets', // Name
            array( 'description' => __( 'Display latest tweets from your Twitter account', WEBLIZAR_TWITTER_TEXT_DOMAIN ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
		// Outputs the content of the widget
		extract($args); // Make before_widget, etc available.
		
		$title = apply_filters('title', $instance['title']);		
		
		echo $before_widget;
		
		if (!empty($title)) {	echo $before_title . $title . $after_title;	}
		
        $TwitterUserName    =   apply_filters( 'weblizar_twitter_user_name', $instance['TwitterUserName'] );
        $Theme              =   apply_filters( 'weblizar_twitter_theme', $instance['Theme'] );
        $Height             =   apply_filters( 'weblizar_twitter_height', $instance['Height'] );
        $Width              =   apply_filters( 'weblizar_twitter_width', $instance['Width'] );
        $LinkColor          =   apply_filters( 'weblizar_twitter_link_color', $instance['LinkColor'] );
        $ExcludeReplies     =   apply_filters( 'weblizar_twitter_exclude_replies', $instance['ExcludeReplies'] );
        $AutoExpandPhotos   =   apply_filters( 'weblizar_twitter_auto_expand_photo', $instance['AutoExpandPhotos'] );
        $TwitterWidgetId    =   apply_filters( 'weblizar_twitter_widget_id', $instance['TwitterWidgetId'] );
        ?>
        
		<div style="display:block;width:100%;float:left;overflow:hidden">
			<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/<?php echo $TwitterUserName; ?>" min-width="<?php echo $Width; ?>" height="<?php echo $Height; ?>" data-theme="<?php echo $Theme; ?>" data-link-color="<?php echo $LinkColor; ?>px" data-widget-id="<?php echo $TwitterWidgetId; ?>">Twitter Tweets</a>
			<script>
				!function(d,s,id) {
					var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}
				} (document,"script","twitter-wjs");
			</script>
		</div>
        <?php
		echo $after_widget;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        if ( isset( $instance[ 'TwitterUserName' ] ) ) {
            $TwitterUserName = $instance[ 'TwitterUserName' ];
        } else {
            $TwitterUserName = "weblizar";
        }

        if ( isset( $instance[ 'Theme' ] ) ) {
            $Theme = $instance[ 'Theme' ];
        } else {
            $Theme = "light";
        }

        $Height = "450";
        if ( isset( $instance[ 'Height' ] ) ) {
            $Height = $instance[ 'Height' ];
        }

        $Width = "";
        if ( isset( $instance[ 'Width' ] ) ) {
            $Width = $instance[ 'Width' ];
        }

        if ( isset( $instance[ 'LinkColor' ] ) ) {
            $LinkColor = $instance[ 'LinkColor' ];
        } else {
            $LinkColor = "#CC0000";
        }

        if ( isset( $instance[ 'ExcludeReplies' ] ) ) {
            $ExcludeReplies = $instance[ 'ExcludeReplies' ];
        } else {
            $ExcludeReplies = "yes";
        }

        if ( isset( $instance[ 'AutoExpandPhotos' ] ) ) {
            $AutoExpandPhotos = $instance[ 'AutoExpandPhotos' ];
        } else {
            $AutoExpandPhotos = "yes";
        }

        if ( isset( $instance[ 'TwitterWidgetId' ] ) ) {
            $TwitterWidgetId = $instance[ 'TwitterWidgetId' ];
        } else {
            $TwitterWidgetId = "462084801944485888";
        }
       
		if ( isset( $instance[ 'title' ] ) ) {
			 $title = $instance[ 'title' ];
		}
		else {
			 $title = __( 'Tweets', 'weblizar' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
	
        <p>
            <label for="<?php echo $this->get_field_id( 'TwitterUserName' ); ?>"><?php _e( 'Twitter Username' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'TwitterUserName' ); ?>" name="<?php echo $this->get_field_name( 'TwitterUserName' ); ?>" type="text" value="<?php echo esc_attr( $TwitterUserName ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'TwitterWidgetId' ); ?>"><?php _e( 'Twitter Widget Id' ); ?> (Required)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'TwitterWidgetId' ); ?>" name="<?php echo $this->get_field_name( 'TwitterWidgetId' ); ?>" type="text" value="<?php echo esc_attr( $TwitterWidgetId ); ?>">
            Get Your Twitter Widget Id: <a href="https://dev.twitter.com/discussions/20722" target="_blank">HERE</a>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'Theme' ); ?>"><?php _e( 'Theme' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'Theme' ); ?>" name="<?php echo $this->get_field_name( 'Theme' ); ?>">
                <option value="light" <?php if($Theme == "light") echo "selected=selected" ?>>Light</option>
                <option value="dark" <?php if($Theme == "dark") echo "selected=selected" ?>>Dark</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'Height' ); ?>"><?php _e( 'Height' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'Height' ); ?>" name="<?php echo $this->get_field_name( 'Height' ); ?>" type="text" value="<?php echo esc_attr( $Height ); ?>">
        </p>

        <!--<p>
            <label for="<?php /*echo $this->get_field_id( 'Width' ); */?>"><?php /*_e( 'Width' ); */?></label>
            <input class="widefat" id="<?php /*echo $this->get_field_id( 'Width' ); */?>" name="<?php /*echo $this->get_field_name( 'Width' ); */?>" type="text" value="<?php /*echo esc_attr( $Width ); */?>">
        </p>-->

        <p>
            <label for="<?php echo $this->get_field_id( 'LinkColor' ); ?>"><?php _e( 'URL Link Color:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'LinkColor' ); ?>" name="<?php echo $this->get_field_name( 'LinkColor' ); ?>" type="text" value="<?php echo esc_attr( $LinkColor ); ?>">
            Find More Color Codes <a href="http://html-color-codes.info/" target="_blank">HERE</a>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'ExcludeReplies' ); ?>"><?php _e( 'Exclude Replies on Tweets' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'ExcludeReplies' ); ?>" name="<?php echo $this->get_field_name( 'ExcludeReplies' ); ?>">
                <option value="yes" <?php if($ExcludeReplies == "yes") echo "selected=selected" ?>>Yes</option>
                <option value="no" <?php if($ExcludeReplies == "no") echo "selected=selected" ?>>No</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'AutoExpandPhotos' ); ?>"><?php _e( 'Auto Expand Photos in Tweets' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'AutoExpandPhotos' ); ?>" name="<?php echo $this->get_field_name( 'AutoExpandPhotos' ); ?>">
                <option value="yes" <?php if($AutoExpandPhotos == "yes") echo "selected=selected" ?>>Yes</option>
                <option value="no" <?php if($AutoExpandPhotos == "no") echo "selected=selected" ?>>No</option>
            </select>
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : 'Tweets';
        $instance['TwitterUserName'] = ( ! empty( $new_instance['TwitterUserName'] ) ) ? strip_tags( $new_instance['TwitterUserName'] ) : 'weblizar';
        $instance['Theme'] = ( ! empty( $new_instance['Theme'] ) ) ? strip_tags( $new_instance['Theme'] ) : 'light';
        $instance['Height'] = ( ! empty( $new_instance['Height'] ) ) ? strip_tags( $new_instance['Height'] ) : '450';
        $instance['Width'] = ( ! empty( $new_instance['Width'] ) ) ? strip_tags( $new_instance['Width'] ) : '';
        $instance['LinkColor'] = ( ! empty( $new_instance['LinkColor'] ) ) ? strip_tags( $new_instance['LinkColor'] ) : '#CC0000';
        $instance['ExcludeReplies'] = ( ! empty( $new_instance['ExcludeReplies'] ) ) ? strip_tags( $new_instance['ExcludeReplies'] ) : 'yes';
        $instance['AutoExpandPhotos'] = ( ! empty( $new_instance['AutoExpandPhotos'] ) ) ? strip_tags( $new_instance['AutoExpandPhotos'] ) : 'yes';
        $instance['TwitterWidgetId'] = ( ! empty( $new_instance['TwitterWidgetId'] ) ) ? strip_tags( $new_instance['TwitterWidgetId'] ) : '462084801944485888';
        return $instance;
    }

} // end of class WeblizarTwitter

// register WeblizarTwitter widget
function WeblizarTwitterWidget() {
    register_widget( 'WeblizarTwitter' );
}
add_action( 'widgets_init', 'WeblizarTwitterWidget' );
?>

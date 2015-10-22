<?php

// theme setup
if (!function_exists('newsted_setup')):
	function newsted_setup() {	
		register_nav_menus(array(
			'primary' => __('Primary Menu', 'newsted'),			
			'footer' => __('Footer Menu', 'newsted')	
		));
		add_theme_support('post-thumbnails');
		add_theme_support('automatic-feed-links');		
		add_image_size('featured', 750, 430, true);		
		add_editor_style(get_template_directory_uri() . '/assets/css/editor-style.css');
		// set content width 
		global $content_width;  
		if (!isset($content_width)) {$content_width = 720;}			
	}
endif; 
add_action('after_setup_theme', 'newsted_setup');

// load css 
function newsted_css() {		
	wp_enqueue_style('newsted_damion', '//fonts.googleapis.com/css?family=Damion');
	wp_enqueue_style('newsted_open_sans', '//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,800italic,700,800,400');
	wp_enqueue_style('newsted_bootstrap_css', get_template_directory_uri() . '/assets/css/bootstrap.min.css');	   
	wp_enqueue_style('newsted_style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'newsted_css');

// load javascript
function newsted_javascript() {	
	wp_enqueue_script('newsted_script', get_template_directory_uri() . '/assets/js/newsted.js', array('jquery'), '1.0', true);	
	if (is_singular() && comments_open()) {wp_enqueue_script('comment-reply');}
}
add_action('wp_enqueue_scripts', 'newsted_javascript');

// html5 shiv
function newsted_html5_shiv() {
    echo '<!--[if lt IE 9]>';
    echo '<script src="'. get_template_directory_uri() .'/assets/js/html5shiv.js"></script>';
    echo '<![endif]-->';
}
add_action('wp_head', 'newsted_html5_shiv');

// page titles
function newsted_title($title, $sep) {
	global $paged, $page;
	if (is_feed()) {
		return $title;
	}
	$title .= get_bloginfo('name');	
	$site_description = get_bloginfo('description', 'display');
	if ( $site_description && (is_home() || is_front_page())) {
		$title = "$title $sep $site_description";
	}	
	if ( $paged >= 2 || $page >= 2 ) {
		$title = "$title $sep " . sprintf( __('Page %s', 'newsted'), max($paged, $page));
	}
	return $title;
}
add_filter('wp_title', 'newsted_title', 10, 2);

// pagination
if (!function_exists('newsted_pagination')):
	function newsted_pagination() {
		global $wp_query;
		$big = 999999999;	
		echo '<div class="pager">';	
		echo paginate_links( array(
			'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
			'format' => '?paged=%#%',
			'current' => max(1, get_query_var('paged')),
			'total' => $wp_query->max_num_pages,
			'prev_next' => False,
		));
		echo '</div>';
	}
endif;

// widgets
function newsted_widgets_init() {
	register_sidebar(array(
		'name' => __('Primary Sidebar', 'newsted'),
		'id' => 'primary-sidebar',
		'description' => __('Widgets in this area will appear in the right sidebar on all pages and posts.', 'newsted'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>'
	));	
	register_sidebar(array(
		'name' => __('Home - Left', 'newsted'),
		'id' => 'home-left-sidebar',
		'description' => __('Widgets in this area will appear in the left column of the home page', 'newsted'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>'
	));	
	register_sidebar(array(
		'name' => __('Home - Middle', 'newsted'),
		'id' => 'home-middle-sidebar',
		'description' => __('Widgets in this area will appear in the middle column of the home page', 'newsted'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>'
	));	
	register_sidebar(array(
		'name' => __('Home - Right', 'newsted'),
		'id' => 'home-right-sidebar',
		'description' => __('Widgets in this area will appear in the right column of the home page', 'newsted'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>'
	));	
}
add_action('widgets_init', 'newsted_widgets_init');

// excerpts
function newsted_excerpt($more) {
	return '...<p><a href="'. get_permalink( get_the_ID() ) . '">' . __('Continue Reading &raquo;', 'newsted') . '</a></p>';
}
add_filter('excerpt_more', 'newsted_excerpt');

// theme customizer
function newsted_customize_register($wp_customize) {
	// upload logo
	$wp_customize->add_section('newsted_logo_section', array(
		'title' => __('Upload Logo', 'newsted'),
		'priority' => 900,
		'type' => 'option'		
	));
	$wp_customize->add_setting('newsted_logo_setting', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw'
	));
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'logo', array(
		'label' => __('Logo', 'newsted'),
		'section' => 'newsted_logo_section',
		'settings' => 'newsted_logo_setting'
	)));
	// hero text
	$wp_customize->add_section(
        'header_content', array(
            'title' => __('Header Text', 'newsted'),
            'description' => __('Heading, text & link that appears above the content on the homepage.', 'newsted'),
            'priority' => 900,
        )
    );
	$wp_customize->add_setting('hero_heading', array(        
        'sanitize_callback' => 'newsted_sanitize_text',
    )); 	
	$wp_customize->add_control('hero_heading', array(
	        'label' => __('Heading', 'newsted'),
	        'section' => 'header_content',
	        'type' => 'text',
	    )
	);
	class Customize_Textarea_Control extends WP_Customize_Control {
	    public $type = 'textarea'; 
	    public function render_content() {
	        ?>
	        <label>
	        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
	        <textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
	        </label>
	        <?php
	    }
	}	
	$wp_customize->add_setting('hero_text',  array(        
        'sanitize_callback' => 'newsted_sanitize_text'
    )); 
	$wp_customize->add_control(new Customize_Textarea_Control($wp_customize, 'hero_text', array(
	    'label'   => __('Text', 'newsted'),
	    'section' => 'header_content',
	    'settings'   => 'hero_text',
	)));
	$wp_customize->add_setting('hero_url_text', array(
		'sanitize_callback' => 'newsted_sanitize_text'
	)); 	
	$wp_customize->add_control(
	    'hero_url_text', array(
	        'label' => __('Button Text', 'newsted'),
	        'section' => 'header_content',
	        'type' => 'text',
	    )
	);
	$wp_customize->add_setting('hero_url', array(
		'sanitize_callback' => 'esc_url_raw'
	)); 	
	$wp_customize->add_control(
	    'hero_url', array(
	        'label' => __('Button URL', 'newsted'),
	        'section' => 'header_content',
	        'type' => 'text',
	    )
	);
	function newsted_sanitize_text($input) {
    	return wp_kses_post(force_balance_tags($input));
	}
	// custom color
	$wp_customize->add_setting('link_color', array(        
        'default' => '#20b2aa',
	    'sanitize_callback' => 'sanitize_hex_color'
    )); 	
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'links', array(
		'label' => __('Link Color', 'newsted'),        
        'section' => 'colors',
        'settings' => 'link_color'
    )));
}
add_action('customize_register', 'newsted_customize_register');

// customizer CSS
function newsted_customize_css() {
    ?>
     <style type="text/css">
        a:hover, a:focus {color:<?php echo get_theme_mod('link_color'); ?>;}
        header nav li.current-menu-item a {border-color:<?php echo get_theme_mod('link_color'); ?>;}
        header nav .sub-menu li a:hover, #hero-text a {background-color:<?php echo get_theme_mod('link_color'); ?>;}
     </style>
    <?php
}
add_action('wp_head', 'newsted_customize_css');

// custom header image
$header_img = array(
	'width' => 1200,
	'height' => 430,	
	'header-text' => false
);
add_theme_support('custom-header', $header_img);

// custom background
$background = array(
	'default-color' => '#f9f9f9'
);
add_theme_support('custom-background', $background);

// comments
if (!function_exists('newsted_comment')) :
	function newsted_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		?>	
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"> 	
		<div id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-header">	
				<div class="comment-author">
					<?php echo get_avatar($comment, 40); ?> 
					<p class="comment-author-name"><span><?php comment_author_link(); ?></span><br /><?php echo get_comment_date() . ' - ' . get_comment_time() ?></p>
				</div>				
			</div>
			<div class="comment-body">			
				<?php comment_text(); ?>
				<?php if ('0' == $comment->comment_approved) : ?>				
					<p class="comment-awaiting-moderation"><?php _e('Comment is awaiting moderation!', 'newsted'); ?></p>					
				<?php endif; ?>				
			</div>			
		</div>
	<?php 
	}
endif;

?>
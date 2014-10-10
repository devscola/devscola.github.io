<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>" /> 
	<title><?php wp_title( '|', true, 'right' ); ?></title>              
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <link rel="profile" href="http://gmpg.org/xfn/11" />        
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header <?php if (get_header_image() || get_theme_mod('hero_heading') || get_theme_mod('hero_text')){ ?>id="header-active"<?php } ?>>
	<div id="header-top">
		<div class="container">
				<div class="row">
					<div class="col-md-3">	
						<?php if (get_theme_mod('newsted_logo_setting')): ?>
					        <a id="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src='<?php echo esc_url(get_theme_mod('newsted_logo_setting')); ?>' alt='<?php echo esc_attr(get_bloginfo('name', 'display')); ?>'></a>
					    <?php else: ?>
					        <a id="site-name" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo('name'); ?></a>
					    <?php endif; ?>						
					</div>
					<div class="col-md-9">			
						<nav>			
							<?php wp_nav_menu(array('theme_location' => 'primary','depth' => 2,'container' => false,'fallback_cb' => false)); ?>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if (is_front_page()) : ?>		
		<?php if (get_header_image() || get_theme_mod('hero_heading') || get_theme_mod('hero_text')) : ?>
			<div id="hero-img" style="background-image: url('<?php header_image(); ?>');"></div>
			<div id="hero-text" class="container">
				<?php if (get_theme_mod('hero_heading')) : ?>
					<h1><?php echo get_theme_mod('hero_heading'); ?></h1>
				<?php endif; ?>			
				<?php if (get_theme_mod('hero_text')) : ?>
					<p><?php echo get_theme_mod('hero_text'); ?></p>
				<?php endif; ?>	
				<?php if (get_theme_mod('hero_url')) : ?>
					<a href="<?php echo get_theme_mod('hero_url'); ?>"><?php echo get_theme_mod('hero_url_text'); ?></a>
				<?php endif; ?>	
			</div>
		<?php endif; ?>			
	<?php endif; ?>	
</header>
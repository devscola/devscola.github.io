<?php

add_action( 'admin_notices', 'pptwj_plugin_notice' );
add_action('admin_init', 'pptwj_plugin_notice_hide' );

function pptwj_plugin_notice(){
	global $current_user;
	$user_id = $current_user->ID;
	$nag_notice = '_pptwj_nag_notice';

	if( !current_user_can( 'manage_options' ) )
		return;

	if( !get_user_meta( $user_id, $nag_notice ) ){

		echo '<div class="updated"><p>';

		printf( __('Installation of Popular Posts Tabbed Widget for Jetpack plugin successful! Go to the <a href="%1$s">Widgets page</a> to use your new Popular Posts Tabbed Widget. <a href="%2$s">Hide notice</a>', PPTWJ_DOMAIN ), admin_url('widgets.php'), add_query_arg( array('pptwj-action' => 'hide-install-notice' ) ) );

		echo '</p></div>';
	}
}

function pptwj_plugin_notice_hide(){
	global $current_user;
	$user_id = $current_user->ID;
	$nag_notice = '_pptwj_nag_notice';

	if( !isset( $_GET['pptwj-action'] ) )
		return;

	$action = $_GET['pptwj-action'];

	/** Hide nag notice **/
	if( 'hide-install-notice' == $action ){
		$val = 1;
		add_user_meta( $user_id, $nag_notice, $val, true );
	}
}
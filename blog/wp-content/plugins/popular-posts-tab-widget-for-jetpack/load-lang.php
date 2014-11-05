<?php

function pptwj_loadlang() {
	load_plugin_textdomain( 'pptwj', false, dirname( PPTWJ_BASENAME ) . '/languages/' ); 
}
add_action('plugins_loaded', 'pptwj_loadlang');
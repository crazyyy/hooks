<?php 
	if( !defined('ABSPATH') ) 
		exit();
	
	if( !defined('WP_UNINSTALL_PLUGIN') )
		exit();
	
	
	global $wpdb;
	require_once( 'classes/NewRoyalSliderMain.php' );
	$slider_table = NewRoyalSliderMain::get_sliders_table_name();
	$wpdb->query( "DROP TABLE IF EXISTS $slider_table" );
	delete_option("new_royalslider_version");
	delete_option('new_royalslider_config');
	delete_option('new_royalslider_anim_block_classes');

?>
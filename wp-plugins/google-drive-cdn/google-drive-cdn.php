<?php
/*
Plugin Name: Google Drive as CDN by WP-Buddy
Plugin URI: http://wp-buddy.com/documentation/plugins/google-drive-cdn-wordpress-plugin/
Description: This plugin allows you to use your personal Google Drive as a static content delivery network (CDN) for WordPress.
Author: wp-buddy
Author URI: http://wp-buddy.com
Version: 1.7.1
Domain Path: /assets/langs/
Text Domain: google-drive-cdn
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// don't use: does not work on some hosts
// set_include_path( get_include_path() . PATH_SEPARATOR . trailingslashit( dirname( __FILE__ ) ) . '/classes/' );

define( 'WPB_GOOGLE_INC', plugin_dir_path( __FILE__ ) . 'classes/' );

if ( ! function_exists( 'wpbgdc_autoloader' ) ) {
	/**
	 * The autoloader class
	 *
	 * @param string $class_name
	 *
	 * @return bool
	 * @since 1.0
	 */
	function wpbgdc_autoloader( $class_name ) {

		// do not include classes that already exist
		if ( class_exists( $class_name ) ) {
			return true;
		}

		$file = trailingslashit( dirname( __FILE__ ) ) . 'classes/' . strtolower( $class_name ) . '.php';
		if ( is_file( $file ) ) {
			require_once( $file );
			return true;
		}

		if ( false === stripos( $class_name, 'Google' ) ) {
			return false;
		}

		$file = trailingslashit( dirname( __FILE__ ) ) . 'classes/' . str_replace( '_', '/', $class_name ) . '.php';
		if ( is_file( $file ) ) {
			try {
				require_once( $file );
			} catch ( Exception $e ) {
				$message = '<div class="error"><p>' . $e->getMessage() . '</p></div>';
				wp_die( $message );
			}
			return true;
		}

		return false;
	}
}

// registering the autoloader function
try {
	spl_autoload_register( 'wpbgdc_autoloader', true );
} catch ( Exception $e ) {
	function __autoload( $class_name ) {
		wpbgdc_autoloader( $class_name );
	}
}


if ( ! isset( $GLOBALS['wpb_google_drive_cdn'] ) ) {
	$GLOBALS['wpb_google_drive_cdn'] = new WPB_Google_Drive_Cdn( __FILE__ );
}
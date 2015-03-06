<?php
/**
 * @package    WPBuddy Plugin
 * @subpackage Google Drive CDN
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @version 1.4
 */
interface WPB_Plugin_Interface {
	public function load_translation();
	public function site_transient_update_plugins( $trans );
	public function plugins_api( $api, $action, $args );
	public function get_client_upgrade_data();
	public function plugins_url( $path, $plugin = null );
	public function update_filters();
	public function activation_hooks();
	public function deactivation_hooks();
	public function theme_on_activation();
	public function get_textdomain();
	public function get_plugin_name_sanitized();
	public function get_plugin_slug_name();
	public function set_plugin_inclusion( $inclusion );
	public function set_plugin_file( $file );
	public function set_plugin_path( $file = null );
	public function track( $tasks );
	public function is_auto_update();
	public function upgrade();
	public function set_new_version();
}
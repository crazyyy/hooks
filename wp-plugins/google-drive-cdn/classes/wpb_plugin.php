<?php

/**
 * @package    WPBuddy Plugin
 * @subpackage Google Drive CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
/**
 * Provides some general Plugin functions
 * Use the on_activation and on_deactivation function in child classes
 *
 * @version 2.8.5
 */
class WPB_Plugin implements WPB_Plugin_Interface {


	/**
	 * _inclusion
	 * How to include the plugin
	 * can be "in_theme" as well
	 *
	 * (default value: 'plugin')
	 *
	 * @var string
	 * @access protected
	 * @since  1.0
	 */
	protected $_inclusion = 'plugin';

	/**
	 * _plugin_path
	 * The plugin path
	 *
	 * (default value: null)
	 *
	 * @var mixed
	 * @access private
	 * @since  2.0
	 */
	protected $_plugin_path = null;


	/**
	 * _plugin_file
	 * The plugin path with filename
	 *
	 * (default value: null)
	 *
	 * @var mixed
	 * @access private
	 * @since  2.0
	 */
	protected $_plugin_file = null;

	/**
	 * _plugin_textdomain
	 *
	 * (default value: wpb )
	 *
	 * @var string
	 * @access protected
	 * @since  2.0
	 */
	protected $_plugin_textdomain = 'wpbp';


	/**
	 * _plugin_name
	 *
	 * (default value: wpbuddy_plugin )
	 *
	 * @var string
	 * @access protected
	 * @since  2.0
	 */
	protected $_plugin_name = 'wpbuddy_plugin';


	/**
	 * Whether to check for updates on wp-buddy.com servers
	 * (default value: wpbuddy_plugin )
	 * @since 2.6
	 * @var bool
	 */
	protected $_auto_update = false;


	/**
	 * The URL to the settings page where the purchase code can be entered
	 * @since 2.8
	 * @var string
	 */
	public $_purchase_code_settings_page_url = '';

	/**
	 * __construct function.
	 *
	 * @access public
	 *
	 * @param null   $file
	 * @param null   $plugin_url
	 * @param string $inclusion
	 *
	 * @param bool   $auto_update
	 *
	 * @return \WPB_Plugin
	 * @since  1.0
	 */
	public function __construct( $file = null, $plugin_url = null, $inclusion = 'plugin', $auto_update = true ) {

		// stop here if there is no file given
		if ( is_null( $file ) ) {
			return new WP_Error( 'wpbp', 'There is no plugin-file!' );
		}

		// how the plugin will be included
		$this->set_plugin_inclusion( $inclusion );

		// set plugin path
		$this->set_plugin_file( $file );

		// set the plugin path
		$this->set_plugin_path( $file );

		// set the auto_update variable (if auto_updates should be done)
		$this->set_auto_update( $auto_update );

		// load the translation
		add_action( 'init', array( &$this, 'load_translation' ) );

		// set update filters
		$this->update_filters();

		// set activation hooks
		$this->activation_hooks();

		// set de-activation hooks
		$this->deactivation_hooks();

		// is fired when upgrading a plugin
		$this->upgrade();

		// brings up a notice to enter the purchase code
		add_action( 'admin_notices', array( &$this, 'purchase_code_warning' ) );
	}


	/**
	 * load_translation function.
	 * loads the translation file
	 *
	 * @access public
	 * @return void
	 * @since  1.0 using oad_textdomain
	 * @since  2.7.3 using load_plugin_textdomain
	 */
	public function load_translation() {
		// we can find the translation in the assets/langs/{plugin-name} folder
		load_plugin_textdomain( $this->get_textdomain(), false, $this->get_plugin_name_sanitized() . '/assets/langs/' );
	}


	/**
	 * update check function.
	 *
	 * @access public
	 *
	 * @param $trans
	 *
	 * @return void
	 * @since  1.0
	 */
	public function site_transient_update_plugins( $trans ) {

		// never do this if it's not an admin page
		if ( ! is_admin() ) {
			return $trans;
		}

		// read the plugins we have received from the webserver
		$remote_plugins = $this->get_client_upgrade_data();

		// stop here if there are no plugins to check
		if ( ! $remote_plugins ) {
			return $trans;
		}

		// run through all plugins and do a version_compare
		// here the $plugin_slug is something like "rich-snippets-wordpress-plugin/rich-snippets-wordpress-plugin.php"
		foreach ( get_plugins() as $plugin_slug => $plugin ) {

			// if the plugin is not in our list, go to the next one
			if ( ! isset( $remote_plugins[$plugin_slug] ) ) {
				continue;
			}

			// the actual version compare
			// if the version is lower we will add the plugin information to the $trans array
			if ( version_compare( $plugin['Version'], $remote_plugins[$plugin_slug]->version, '<' ) ) {
				$trans->response[$plugin_slug]      = new stdClass();
				$trans->response[$plugin_slug]->url = $remote_plugins[$plugin_slug]->homepage;

				// here the slug-name is something like "rich-snippets-wordpress-plugin"
				// extracted from the filename
				// this only works if the plugin is inside of a folder
				$trans->response[$plugin_slug]->slug        = str_replace( array( '/', '.php' ), '', strstr( $plugin_slug, '/' ) );
				$trans->response[$plugin_slug]->package     = $remote_plugins[$plugin_slug]->download_url;
				$trans->response[$plugin_slug]->new_version = $remote_plugins[$plugin_slug]->version;
				$trans->response[$plugin_slug]->upgrade_notice = $remote_plugins[$plugin_slug]->upgrade_notice;
				$trans->response[$plugin_slug]->id          = '0';
			}
			else {
				if ( isset( $trans->response[$plugin_slug] ) ) {
					unset( $trans->response[$plugin_slug] );
				}
			}
		}

		return $trans;
	}

	/**
	 * plugins_api function.
	 * Will return the plugin information or false. If it returns false WordPress will look after some plugin information in the wordpress.org plugin database
	 *
	 * @access   public
	 *
	 * @param boolean      $api
	 * @param string       $action
	 * @param array|object $args
	 *
	 * @internal param mixed $def
	 * @return stdClass | boolean
	 * @since    1.0
	 */
	public function plugins_api( $api, $action, $args ) {

		$slug = $this->get_plugin_name_sanitized();

		if ( false !== $api ) {
			return false;
		}

		if ( ! isset( $args->slug ) ) {
			return false;
		}

		if ( $slug != $args->slug ) {
			return false;
		}

		if ( 'plugin_information' != $action ) {
			return false;
		}

		$plugins = $this->get_client_upgrade_data();

		if ( ! $plugins ) {
			return false;
		}

		$extended_slug = str_replace( WP_PLUGIN_DIR . '/', '', $this->_plugin_file );

		if ( ! isset( $plugins[$extended_slug] ) ) {
			return false;
		}

		return $plugins[$extended_slug]; // stdClass object
	}

	/**
	 * get_client_upgrade_data function.
	 *
	 * @access public
	 * @return array | false
	 * @since  1.0
	 * @global $wp_version
	 * @global $wpb_has_plugin_remote_sent
	 */
	public function get_client_upgrade_data() {
		global $wpb_has_plugin_remote_sent;

		// if yes, than just return the results
		if ( isset( $wpb_has_plugin_remote_sent[$this->get_plugin_name_sanitized()] ) ) {
			return $wpb_has_plugin_remote_sent[$this->get_plugin_name_sanitized()];
		}

		/**
		 * The database can only have 64 characters for the name
		 */
		$transient_name = substr( 'wpbp_u_' . $this->get_plugin_name_sanitized(), 0, 64 );

		// if a plugin-check was already done, return the results
		$transient_plugins = $this->get_transient( $transient_name );

		if ( false === $transient_plugins ) {
			// the transient is no longer valid because it returned 'false'. => we have to do a request
			$do_request = true;
		}
		else {
			// the transient is valid and returned anything else than 'false'. => we have NOT to do a request
			$do_request = false;

			// but first we have to check if we are on the update-core.php page. if so we HAVE to do the request
			global $pagenow;
			if ( isset( $pagenow ) && 'update-core.php' == $pagenow ) {
				$do_request = true;
			}
		}

		if ( ! $do_request ) {
			return $transient_plugins;
		}

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = get_plugins();

		// what wp-version do we have here?
		global $wp_version;

		$purchase_code = ( ( method_exists( $this, 'get_purchase_code' ) ) ? $this->get_purchase_code() : '' );
		if ( empty( $purchase_code ) ) {
			return false;
		}

		// prepare the elements for the POST-call
		$post_elements = array(
			'action'        => 'wpbcb_ajax_plugin_update',
			'plugins'       => $plugins,
			'wp_version'    => $wp_version,
			'purchase_code' => $purchase_code,
			'blog_url'      => home_url()
		);

		// some more options for the POST-call
		$options = array(
			'timeout'    => 5,
			'body'       => $post_elements,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
		);

		$data = wp_remote_post( base64_decode( 'aHR0cDovL3dwLWJ1ZGR5LmNvbS93cC1hZG1pbi9hZG1pbi1hamF4LnBocA==' ), $options );

		// alright. We did the request, we store an empty array into the transient if something goes wrong later (this means if there was a 404 error or something like that)
		// just to prevent doing the same remote_post over and over again
		$this->set_transient( $transient_name, array(), 60 * 60 * 24 * 7 );

		if ( ! is_wp_error( $data ) && 200 == wp_remote_retrieve_response_code( $data ) ) {
			if ( $body = json_decode( wp_remote_retrieve_body( $data ), true ) ) {
				if ( is_array( $body ) && isset( $body['plugins'] ) && is_serialized( $body['plugins'] ) ) {
					$remote_plugins = unserialize( $body['plugins'] );

					// set transient for a later usage. set transient to 24 hours
					$this->set_transient( $transient_name, $remote_plugins, 60 * 60 * 24 * 7 );

					$GLOBALS['wpb_has_plugin_remote_sent'][$this->get_plugin_name_sanitized()] = $remote_plugins;

					return $remote_plugins;
				}
			}
		}

		return false;
	}


	/**
	 * replaces the WordPress' built in plugins_url function
	 *
	 * @param      $path
	 * @param null $plugin
	 *
	 * @return string
	 */
	public function plugins_url( $path, $plugin = null ) {
		if ( is_null( $plugin ) ) {
			$plugin = $this->_plugin_file;
		}
		if ( 'plugin' == $this->_inclusion ) {
			return plugins_url( $path, $plugin );
		}
		else {
			return get_template_directory_uri() . '/' . $path;
		}
	}


	/**
	 * Does updates for the current plugin. But only when it's not included in a theme
	 * @return void
	 * @since 2.0
	 */
	public function update_filters() {

		if ( 'plugin' == $this->_inclusion && $this->is_auto_update() ) {

			// Plugin update hooks
			// Automatically plugin update check
			add_filter( 'site_transient_update_plugins', array( &$this, 'site_transient_update_plugins' ) );

			// plugin api info screen
			add_filter( 'plugins_api', array( &$this, 'plugins_api' ), - 100, 3 );

		}
	}


	/**
	 * creates the activation hooks.
	 * if it is used as a plugin the register_activation_hook will be used
	 * if it is used within a theme the load-themes.php action is used
	 *
	 * @return void
	 * @since 2.0
	 */
	public function activation_hooks() {
		// for themes
		if ( 'in_theme' == $this->_inclusion ) {
			add_action( 'load-themes.php', array( &$this, 'theme_on_activation' ) );
		}
		// for plugins
		elseif ( 'plugin' == $this->_inclusion && isset( $this->_plugin_file ) && method_exists( $this, 'on_activation' ) ) {
			register_activation_hook( $this->_plugin_file, array( &$this, 'on_activation' ) );
		}
	}


	/**
	 * creates the deactivation_hooks
	 * if it is used as a plugin the register_deactivation_hook is used
	 * if it is used within a theme the switch-theme action is used
	 */
	public function deactivation_hooks() {
		// for themes
		if ( 'in_theme' == $this->_inclusion && method_exists( $this, 'on_deactivation' ) ) {
			add_action( 'switch_theme', array( &$this, 'on_deactivation' ) );
		}
		// and for plugins
		elseif ( 'plugin' == $this->_inclusion && isset( $this->_plugin_file ) && method_exists( $this, 'on_deactivation' ) ) {
			register_deactivation_hook( $this->_plugin_file, array( &$this, 'on_deactivation' ) );
		}
	}


	/**
	 * If the plugin is called within a theme this is the pre_activation hook
	 * Please use the action [plugin_name]_on_activation to hook into the activation
	 * @since 1.0
	 * @return void
	 */
	public function theme_on_activation() {
		global $pagenow;

		if ( $pagenow == 'themes.php' && isset( $_REQUEST['activated'] ) ) {

			if ( method_exists( $this, 'on_activation' ) ) {
				$this->on_activation();
			}

		}
	}


	/**
	 * GETTERS
	 */

	/**
	 * Returns the text domain name
	 * @return string
	 * @since 1.0
	 */
	public function get_textdomain() {
		if ( isset( $this->_plugin_textdomain ) ) {
			return $this->_plugin_textdomain;
		}
		return '';
	}


	/**
	 * Returns the sanitized name of the current plugin
	 *
	 * @return string
	 * @since 2.0
	 */
	public function get_plugin_name_sanitized() {
		return sanitize_key( $this->_plugin_name );

	}


	/**
	 * WARNING: This function is deprecated. Use get_plugin_name_sanitized() instead
	 * This returns the plugin slug name which seems not to be defined within WordPress
	 * So my understanding of a plugins slug name is the filename without the .php in the end
	 * but the get_plugins function returns the full name (with folder and .php). for ex: schema-corporate/schema-corporate.php
	 * But this function just returns "schema-corporate".
	 * The slug will be stripped out of the Class name
	 *
	 * @access     protected
	 * @return string
	 * @since      1.0
	 * @deprecated use get_plugin_name_sanitized instead
	 */
	public function get_plugin_slug_name() {
		return $this->get_plugin_name_sanitized();
	}

	/**
	 * SETTERS
	 */

	/**
	 * Set how to the plugin will be included.
	 *
	 * @param string $inclusion Can either be 'plugin' or 'in_theme'
	 *
	 * @return void
	 * @since 2.0
	 */
	public function set_plugin_inclusion( $inclusion ) {
		$this->_inclusion = $inclusion;
	}


	/**
	 * Sets the plugin file variable
	 *
	 * @param string $file
	 *
	 * @return void
	 * @since 2.0
	 */
	public function set_plugin_file( $file ) {
		$this->_plugin_file = $file;
	}


	/**
	 * Whether to check for updates on WP-Buddy Servers
	 * @since 2.6
	 *
	 * @param bool $auto_update
	 */
	public function set_auto_update( $auto_update ) {
		$this->_auto_update = $auto_update;
	}


	/**
	 * set_plugin_path function.
	 * Sets the plugin path
	 *
	 * @since  2.0
	 * @access private
	 *
	 * @param mixed $file (default: null)
	 *
	 * @return void
	 */
	public function set_plugin_path( $file = null ) {
		$this->_plugin_path = dirname( $file );
		if ( is_null( $this->_plugin_path ) ) {
			$this->_plugin_path = dirname( __FILE__ );
		}
	}


	/**
	 * Returns the plugins file
	 * @since 2.3
	 * @return mixed|null
	 */
	public function get_plugin_file() {
		return $this->_plugin_file;
	}


	/**
	 * Returns the plugins root-path
	 * @since 2.3
	 * @return string
	 */
	public function get_plugin_path() {
		return trailingslashit( dirname( $this->_plugin_file ) );
	}


	/**
	 * Track the current task
	 *
	 * @param array $tasks
	 *
	 * @since 2.5
	 *
	 * @return void
	 *
	 */
	public function track( $tasks ) {
		// assume that tracking is not allowed when the method 'is_tracking' does not exist
		if ( ! method_exists( $this, 'is_tracking' ) ) {
			return;
		}

		// stop here if tracking is not allowed
		if ( ! $this->is_tracking() ) {
			return;
		}

		// what wp-version do we have here?
		global $wp_version;

		// prepare the elements for the POST-call
		$post_elements = array(
			'action'        => 'wpb_track',
			'wp_version'    => $wp_version,
			'purchase_code' => ( ( method_exists( $this, 'get_purchase_code' ) ) ? $this->get_purchase_code() : '' ),
			'blog_url'      => home_url(),
			'tasks'         => $tasks,
			'plugin_name'   => $this->get_plugin_name_sanitized()
		);

		// some more options for the POST-call
		$options = array(
			'timeout'    => 5,
			'body'       => $post_elements,
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
		);

		try {
			wp_remote_post( base64_decode( 'aHR0cDovL3dwLWJ1ZGR5LmNvbS93cC1hZG1pbi9hZG1pbi1hamF4LnBocA==' ), $options );
		} catch ( Exception $e ) {
		}

	}

	/**
	 * Checks whether the auto-update functionality is on (true) or off (false)
	 * @since 2.6
	 * @return bool
	 */
	public function is_auto_update() {
		return (bool) $this->_auto_update;
	}


	/**
	 * This function fires the on_upgrade function
	 * @since 2.7
	 */
	public function upgrade() {
		// this is for testing only
		//update_option( 'wpb_plugin_' . $this->get_plugin_name_sanitized() . '_version', '1.1.2' );

		// update the version (this comes later then the following lines)
		// this is to make sure to only upgrade once each version
		add_action( 'init', array( &$this, 'set_new_version' ), 10 );

		// only do the upgrade if the current version is higher than the version before
		if ( version_compare( get_option( 'wpb_plugin_' . $this->get_plugin_name_sanitized() . '_version', 0 ), $this->get_plugin_version(), '>=' ) ) {
			return;
		}

		if ( method_exists( $this, 'on_upgrade' ) ) {
			add_action( 'init', array( &$this, 'on_upgrade' ), 5 );
		}

	}


	/**
	 * returns the current plugins version
	 * @since 2.7
	 * @return int
	 */
	public function get_plugin_version() {
		if ( isset( $this->_plugin_version ) ) {
			return $this->_plugin_version;
		}
		return 0;
	}

	/**
	 * Sets the new version after upgrading
	 * @since 2.7.2
	 */
	public function set_new_version() {
		update_option( 'wpb_plugin_' . $this->get_plugin_name_sanitized() . '_version', $this->get_plugin_version() );
	}


	/**
	 * Shows a purchase code warning
	 * @since 2.8
	 */
	public function purchase_code_warning() {
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}
		if ( ! isset( $this->_pagehook ) ) {
			return;
		}

		global $hook_suffix;
		if ( ! isset( $hook_suffix ) ) {
			return;
		}

		if ( $hook_suffix != $this->_pagehook ) {
			return;
		}
		if ( ! method_exists( $this, 'get_purchase_code' ) ) {
			return;
		}

		$purchase_code = $this->get_purchase_code();
		if ( ! empty( $purchase_code ) ) {
			return false;
		}

		?>
		<div class="updated">
			<p><?php echo sprintf( __( 'You should consider entering you purchase code for the %s plugin because you get every update immediately delivered to your WordPress installation.', $this->get_textdomain() ), '<a href="' . $this->_purchase_code_settings_page_url . '">' . $this->get_plugin_full_name() . '</a>' ); ?></p>
		</div>
	<?php

	}

	/**
	 * Returns the plugins full name
	 * @since 2.8
	 */
	public function get_plugin_full_name() {
		$plugin_data = get_plugin_data( $this->get_plugin_file(), false, true );
		if ( ! isset( $plugin_data['Name'] ) ) {
			return '';
		}
		if ( empty( $plugin_data['Name'] ) ) {
			return '';
		}
		return $plugin_data['Name'];
	}


	/**
	 * @param string $name
	 *
	 * @since 2.8.5
	 * @return mixed
	 */
	public function get_transient( $name ) {
		$transient = get_option( $name, null );
		if ( ! is_array( $transient ) ) {
			// transient is not valid because the option does not exist
			return false;
		}

		if ( ! isset( $transient['time'] ) ) {
			// transient is no longer valid because the time does not exist
			return false;
		}

		if ( current_time( 'timestamp' ) > $transient['time'] ) {
			// transient is no longer valid because we're over time
			return false;
		}

		if ( ! isset( $transient['value'] ) ) {
			// transient is no longer valid because the content does not exist
			return false;
		}

		return $transient['value'];

	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param int    $time timestamp
	 *
	 * @return bool
	 *
	 * @since 2.8.5
	 */
	public function set_transient( $name, $value, $time ) {
		$a = array(
			'time'  => current_time( 'timestamp' ) + $time,
			'value' => $value
		);
		return update_option( $name, $a );
	}


}
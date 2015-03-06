<?php
/**
 * @package    WPBuddy Plugin
 * @subpackage Google Drive CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @version 1.7.1
 */
class WPB_Google_Drive_Cdn extends WPB_Plugin {


	/**
	 * _plugin_textdomain
	 *
	 * @var string
	 * @access protected
	 * @since  1.0
	 */
	protected $_plugin_textdomain = 'google-drive-cdn';


	/**
	 * _plugin_name
	 *
	 * @var string
	 * @access protected
	 * @since  1.0
	 */
	protected $_plugin_name = 'google-drive-cdn';


	/**
	 * _plugin_version
	 * The plugin version
	 *
	 * (default value: '1.0')
	 *
	 * @var string
	 * @access private
	 */
	public $_plugin_version = '1.7.1';


	/**
	 * The current replacement count
	 * @var int
	 * @since 1.0
	 */
	private $_counter = 0;


	/**
	 * if a css file is in the buffer at the moment
	 * @since 1.2
	 * @var bool
	 */
	public $_is_buffer_css_file = false;


	/**
	 * Contains the full path to the file (inclusive filename) to the current css file
	 * @since 1.2
	 * @var string
	 */
	public $_buffer_css_file_path = '';


	/**
	 * Contains the full url to the file (inclusive filename) to the current css file
	 * @var string
	 */
	public $_buffer_css_file_url = '';


	/**
	 * The list of excluded files. Will be filled within the buffer() function.
	 *
	 * @since  1.6
	 * @access public
	 *
	 * @var array $_excluded_files The key is the URL.
	 */
	private $_excluded_files = array();


	/**
	 * The construct
	 * @uses  WPB_Plugin::__construct()
	 *
	 * @param null   $file
	 * @param null   $plugin_url
	 * @param string $inclusion
	 *
	 * @since 1.0
	 */
	public function __construct( $file = null, $plugin_url = null, $inclusion = 'plugin' ) {

		// call the parent constructor first
		parent::__construct( $file, $plugin_url, $inclusion );

		$this->_purchase_code_settings_page_url = admin_url( 'options-general.php?page=wpbgdc' );

		// do the admin stuff
		$this->do_admin();

		// do the non-admin stuff
		$this->do_non_admin();

		// always add admin bar items
		add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_items' ) );

		add_action( 'wpbgdc_hourly_event', array( $this, 'ping_sync' ) );

		add_filter( 'cron_schedules', array( $this, 'add_schedule_time' ), 10, 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	}


	/**
	 * Do admin stuff
	 * @since 1.0
	 */
	private function do_admin() {
		if ( ! is_admin() ) {
			return;
		}
		if ( 'plugin' == $this->_inclusion ) {
			add_filter( 'plugin_action_links_' . plugin_basename( $this->_plugin_file ), array( $this, 'plugin_action_links' ) );
		}

		// Create the admin menu in backend
		add_action( 'admin_menu', array( $this, 'menu' ) );

		// Creates the settings (and the settings page)
		$settings = new WPB_Google_Drive_Cdn_Settings( $this );
		add_action( 'admin_init', array( &$settings, 'settings' ) );

		// retrieving the code to request a token
		add_action( 'load-settings_page_wpbgdc', array( $this, 'got_code' ) );

		// clears the local caching table
		add_action( 'load-settings_page_wpbgdc', array( $this, 'clear_cache' ) );

		// delete error logs
		add_action( 'load-settings_page_wpbgdc', array( $this, 'delete_error_logs' ) );

		// disconnects from google drive
		add_action( 'load-settings_page_wpbgdc', array( $this, 'disconnect' ) );

		// stops the sync process
		add_action( 'load-settings_page_wpbgdc', array( $this, 'stop_sync' ) );

		// removes the google drive folder
		add_action( 'load-settings_page_wpbgdc', array( $this, 'empty_google_drive' ) );

		// start syncing (on click on the sync-button from the backend)
		add_action( 'load-settings_page_wpbgdc', array( $this, 'start_sync' ) );

		// search folder
		add_action( 'load-settings_page_wpbgdc', array( $this, 'search_folder' ) );

		// syncs the wp-content dir to Google Drive
		add_action( 'wp_ajax_wpbgdc_sync', array( $this, 'ajax_sync' ) );

		// the syncing loop which runs in the background
		add_action( 'wp_ajax_wpbgdc_db_sync', array( $this, 'db_sync' ) );
		add_action( 'wp_ajax_nopriv_wpbgdc_db_sync', array( $this, 'db_sync' ) );

		add_action( 'update_option_wpbgdc', array( $this, 'reschedule_event' ), 10, 2 );

		// adding mime types to wordpress to that wp_check_filetype gets the right thing
		add_filter( 'mime_types', array( $this, 'mime_types' ) );
	}

	/**
	 * Do non-admin stuff
	 * @since 1.0
	 */
	private function do_non_admin() {
		if ( is_admin() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );

		global $ossdlcdn;

		if ( isset( $ossdlcdn ) && function_exists( 'wp_cache_phase2' ) && (bool) $ossdlcdn ) {
			add_filter( 'wp_cache_ob_callback_filter', array( $this, 'buffer' ), 5, 1 );
		}
		else {
			add_action( 'init', array( $this, 'ob_start' ), - 9999 );
		}
	}

	/**
	 * Adds links to the plugins menu (where the plugins are listed)
	 *
	 * @param array $links
	 *
	 * @since 1.2
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . get_admin_url( null, 'options-general.php?page=' . str_replace( 'settings_page_', '', $this->_pagehook ) ) . '">' . __( 'Settings', $this->get_textdomain() ) . '</a>';
		$links[] = '<a href="http://wp-buddy.com" target="_blank">' . __( 'More Plugins by WPBuddy', $this->get_textdomain() ) . '</a>';
		return $links;
	}

	/**
	 * menu function.
	 * will add the menu in the WordPress backend
	 *
	 * @access public
	 *
	 * @param mixed $id
	 *
	 * @return void
	 * @since  1.0
	 */
	public function menu( $id ) {

		// create the menu (which generates the pagehook)
		$this->_pagehook = add_options_page( __( 'Google Drive CDN', $this->get_textdomain() ), __( 'Google Drive CDN', $this->get_textdomain() ), 'administrator', 'wpbgdc', array( $this, 'settings_page' ) );

		// only add the scripts for the google author plugins page in backend
		add_action( 'load-' . $this->_pagehook, array( $this, 'scripts' ) );
	}

	/**
	 * scripts function.
	 * adds the scripts which are important for add_metabox (in backend).
	 * this function will be called from menu function (see below) after the pagehook was set to only import them on the specific page.
	 *
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function scripts() {

		// needed for the postpoxes to open and close properly
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		wp_enqueue_style( 'wpbgdcdn-backend', $this->plugins_url( 'assets/css/style-backend.css' ) );
		wp_enqueue_style( 'dashicons' );

	}

	/**
	 * Shows the settings page
	 * @since 1.0
	 */
	public function settings_page() {

		// get the columns
		global $screen_layout_columns;

		$metabox_class = new WPB_Google_Drive_Cdn_Metaboxes( $this );

		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		$is_api_working = $helper->is_api_working();

		$client_id = WPB_Google_Drive_Cdn_Settings::get_setting( 'client_id' );

		$client_secret = WPB_Google_Drive_Cdn_Settings::get_setting( 'client_secret' );

		add_meta_box( 'wpbgdc_enabling_api', __( '1. Enable the Drive API', $this->get_textdomain() ), array( $metabox_class, 'enabling_api' ), $this->_pagehook, 'normal', 'core' );

		if ( ! empty( $client_id ) && ! empty( $client_secret ) ) {
			add_meta_box( 'wpbgdc_connect', __( '2. Connect with your Google Drive', $this->get_textdomain() ), array( $metabox_class, 'connect' ), $this->_pagehook, 'normal', 'low' );
		}

		if ( $is_api_working ) {
			add_meta_box( 'wpbgdc_info', __( '3. Instructions - READ THIS CAREFULLY', $this->get_textdomain() ), array( $metabox_class, 'info' ), $this->_pagehook, 'normal', 'low' );
		}

		if ( $is_api_working ) {
			add_meta_box( 'wpbgdc_settings', __( '4. Settings', $this->get_textdomain() ), array( $metabox_class, 'settings_section' ), $this->_pagehook, 'normal', 'low' );
		}

		add_meta_box( 'wpbgdc_log', __( 'Error Logs, hints and statistics', $this->get_textdomain() ), array( $metabox_class, 'error_logs' ), $this->_pagehook, 'normal', 'low' );

		// Add metabox "about"
		add_meta_box( 'wpbgdc_about', __( 'About', $this->get_textdomain() ), array( $metabox_class, 'about' ), $this->_pagehook, 'side', 'default' );

		// Add metabox "links"
		add_meta_box( 'wpbgdc_links', __( 'Helpful links', $this->get_textdomain() ), array( $metabox_class, 'links' ), $this->_pagehook, 'side', 'default' );

		// Add metabox "social"
		add_meta_box( 'wpbgdc_social', __( 'Like this plugin?', $this->get_textdomain() ), array( $metabox_class, 'social' ), $this->_pagehook, 'side', 'default' );

		// Add metabox "subscribe"
		add_meta_box( 'wpbgdc_subscribe', __( 'Get our free Newsletter', $this->get_textdomain() ), array( $metabox_class, 'subscribe' ), $this->_pagehook, 'side', 'default' );


		if ( isset( $_REQUEST['notice'] ) ) {
			switch ( $_REQUEST['notice'] ) {
				case 'cache_emptied':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'Local cache has been cleared.', $this->get_textdomain() ) . '</strong></p></div>';
					break;
				case 'removed_files':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'All files created by this plugin have been trashed.', $this->get_textdomain() ) . '</strong></p></div>';
					break;
				case 'disconnected':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'Google Drive as been disconnected', $this->get_textdomain() ) . '</strong></p></div>';
					break;
				case 'deleted_error_logs':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'Error log has been cleared.', $this->get_textdomain() ) . '</strong></p></div>';
					break;
				case 'last_error':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . $this->get_last_error() . '</strong></p></div>';
					break;
				case 'sync_started':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'Sync / Upload started...', $this->get_textdomain() ) . '</strong></p></div>';
					break;
				case 'sync_stopped':
					echo '<div class="updated" id="setting-error-settings_updated"><p><strong>' . __( 'Sync will be stopped after the current file has been uploaded successfully.', $this->get_textdomain() ) . '</strong></p></div>';
					break;
			}
		};
		?>

		<div class="wrap" id="wpbgdc">
			<form action="<?php echo admin_url( 'options.php' ); ?>" method="post" class="wpbgdc-form">
				<h2><?php echo __( 'Google Drive CDN Settings', $this->get_textdomain() ); ?></h2><br />

				<div id="poststuff" class="metabox-holder has-right-sidebar">

					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes( $this->_pagehook, 'side', array() ); ?>
					</div>

					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes( $this->_pagehook, 'normal', array() ); ?>
						</div>
					</div>

					<br class="clear" />

					<?php
					settings_fields( 'wpbgdc_options_group' );
					wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
					wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
					?>
				</div>
				<!-- /poststuff -->
			</form>
		</div><!-- /wrap -->
		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery( document ).ready( function ( $ ) {

				/* close postboxes that should be closed */
				jQuery( '.if-js-closed' ).removeClass( 'if-js-closed' ).addClass( 'closed' );

				/* postboxes setup */
				postboxes.add_postbox_toggles( '<?php echo $this->_pagehook; ?>' );

				<?php

				if( empty( $client_id ) OR empty( $client_secret ) ) {echo "jQuery( '#wpbgdc_enabling_api' ).removeClass('closed');";
}
				else {echo "jQuery( '#wpbgdc_enabling_api' ).addClass('closed');";
}

				if( ! $is_api_working ) {echo "jQuery( '#wpbgdc_connect' ).removeClass('closed');";
}
				else {echo "jQuery( '#wpbgdc_connect' ).addClass('closed');";
}
				?>

				/* Saves the current post box states to avoid flashing up on page load */
				postboxes.save_state( '<?php echo $this->_pagehook; ?>' );

			} );
			/* ]]> */
		</script>
	<?php
	}


	/**
	 * Get's the token from the URL the OAuth returns
	 * @since 1.0
	 */
	public function got_code() {
		if ( ! isset( $_REQUEST['state'] ) ) {
			return;
		}
		if ( 'got_code' != $_REQUEST['state'] ) {
			return;
		}

		if ( ! isset( $_REQUEST['code'] ) OR ( isset( $_REQUEST['error'] ) ) ) {
			return '<div class="error"><p>' . __( 'Google\'s API returned an error!', $this->get_textdomain() )
			. ' '
			. ( ( isset( $_REQUEST['error'] ) ) ? '(' . $_REQUEST['error'] . ')' : '' )
			. '</p></div>';
		}

		$client = $this->get_google_client( false ); // do not set access token because it has not yet been set

		try {
			$accessToken = $client->authenticate( $_REQUEST['code'] );
		} catch ( Exception $e ) {
			$this->set_error( $e->getMessage() . '(wpbgdc: got_code 1)' );
		}

		try {
			$client->setAccessToken( $accessToken );
		} catch ( Exception $e ) {
			$this->set_error( $e->getMessage() . '(wpbgdc: got_code 2)' );
		}

		WPB_Google_Drive_Cdn_Settings::set_setting( 'token', $accessToken, 'wpbgdc_oauth_token' );

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc&action=search_folder' ) );

	}


	/**
	 * @since 1.0
	 *
	 * @param bool $set_access_token If the access token should be set (default: true)
	 *
	 * @return Google_Client
	 */
	public function get_google_client( $set_access_token = true ) {

		if ( isset( $this->_client ) ) {
			return $this->_client;
		}

		$client = new Google_Client();
		$client->setClassConfig( 'Google_IO_Abstract', 'request_timeout_seconds', 10 );

		if ( (bool) WPB_Google_Drive_Cdn_Settings::get_setting( 'temp_dir_change' ) ) {
			// temp dir from the settings
			$temp_dir = WPB_Google_Drive_Cdn_Settings::get_setting( 'temp_dir' );

			// if empty temp dir build it up
			if ( empty( $temp_dir ) ) {
				$upload_dir = wp_upload_dir();
				$temp_dir   = str_replace( $upload_dir['subdir'], '', $upload_dir['path'] );
			}

			$client->setClassConfig( 'Google_Cache_File', 'directory', $temp_dir );
		}

		// Get your credentials from the APIs Console
		$client->setClientId( WPB_Google_Drive_Cdn_Settings::get_setting( 'client_id' ) );
		$client->setClientSecret( WPB_Google_Drive_Cdn_Settings::get_setting( 'client_secret' ) );
		$client->setRedirectUri( admin_url( 'options-general.php?page=wpbgdc' ) );
		$client->addScope( Google_Service_Drive::DRIVE );

		$token = WPB_Google_Drive_Cdn_Settings::get_setting( 'token', 'wpbgdc_oauth_token' );

		// check if access token has expired
		// for this set the token
		if ( $set_access_token ) {
			if ( '' != $token ) {
				$client->setAccessToken( $token );
			}
		}

		// check if the token has expired. If so: request a new one
		if ( $client->isAccessTokenExpired() ) {

			$token = json_decode( $token, true );
			if ( is_null( $token ) ) {
				$this->set_error( 'Could not json decode the refresh token to ask for a new token. Please try to re-authenticate with your Google Drive.', false );
			}
			if ( ! isset( $token['refresh_token'] ) ) {
				$this->set_error( 'Invalid refresh token format. Please try to re-authenticate with your Google Drive.', false );
			}
			else {
				try {
					$client->refreshToken( $token['refresh_token'] );
					$refreshed_token = $client->getAccessToken();
					if ( ! is_string( $refreshed_token ) ) {
						throw new Exception( 'Got an invalid token while refreshing. Please try to re-authenticate with your Google Drive.' );
					}
					else {
						WPB_Google_Drive_Cdn_Settings::set_setting( 'token', $refreshed_token, 'wpbgdc_oauth_token' );
					}
				} catch ( Exception $e ) {
					$this->set_error( $e->getMessage() . ' (wpbgdc: refresh_token)', false );
				}
			}

		}

		$this->_client = $client;

		return $this->_client;
	}


	/**
	 * Mimics the realpath() functionality of PHP but with URLs
	 * @since 1.2
	 *
	 * @param string $address
	 *
	 * @return string $address
	 */
	private function canonicalize( $address ) {
		$address = explode( '/', $address );
		$keys    = array_keys( $address, '..' );

		foreach ( $keys AS $keypos => $key ) {
			array_splice( $address, $key - ( $keypos * 2 + 1 ), 2 );
		}

		$address = implode( '/', $address );
		$address = str_replace( './', '', $address );

		return $address;
	}

	// ../themes/schema-corporate/assets/img/patterns/60degree_gray_@2X.png
	// .themes/schema-corporate/assets/img/patterns

	/**
	 * Returns the real css file url for images
	 * @since 1.2
	 *
	 * @param string $url (which is something like this: url("../../bla.gif"))
	 *
	 * @return string $url which is something like this: http://mydomain.com/subfolder/bla.gif
	 */
	private function real_css_image_url( $url ) {
		$url = str_replace( array( 'url(', '"', "'", ')' ), '', $url );
		$url = trim( $url );

		// if http is found, don't do anything else
		if ( false !== stripos( $url, 'http://' ) ) {
			return $url;
		}

		// if https is found, don't do anything else
		if ( false !== stripos( $url, 'https://' ) ) {
			return $url;
		}

		// the file name of the CSS file that was invoked
		$filename = pathinfo( $this->_buffer_css_file_path, PATHINFO_BASENAME );

		// the URL to the CSS file that was invoked (without the file name at the end)
		$file_url_path = str_replace( $filename, '', $this->_buffer_css_file_url );

		$url = $this->canonicalize( $file_url_path . $url );

		return $url;
	}

	/**
	 * Collect URLs from the current page and stores it into the database for a later upload
	 * This function does not upload anything because it speeds up the loadtime of the current page
	 * It also does not check if the file is locally stored or not
	 *
	 * @param array $matches
	 * @param bool  $is_css_file this will be converted to true if $is_css_import is true
	 * @param bool  $is_css_import
	 *
	 * @return bool|null|string
	 *
	 * @since 1.0
	 */
	public function collect_urls( $matches, $is_css_file = false, $is_css_import = false ) {

		if ( $is_css_import ) {
			$is_css_file = true;
		}

		$architecture = '[url]';

		if ( $is_css_file ) {
			$architecture = 'url("[url]") ';
		}

		if ( $is_css_import ) {
			$architecture = '@import url("[url]") ' . $matches[1] . ';';
		}

		$url = $matches[0];

		$url = trim( $url );
		if ( '' == $url ) {
			return $url;
		}

		// leaves data: fields in URLs (used in some custom fonts)
		if ( false !== stripos( $url, 'data:' ) ) {
			return $url;
		}

		// removes versioning from urls (ex. my-file.css?v=3.3.2)
		$versioning = stripos( $url, '?' );
		if ( false !== $versioning ) {
			$url = substr( $url, 0, stripos( $url, '?' ) );
		}

		// removes versioning with #
		$versioning = stripos( $url, '#' );
		if ( false !== $versioning ) {
			$url = substr( $url, 0, stripos( $url, '#' ) );
		}

		// checks if the ORIGINAL file URL is in the list of excluded files
		if ( isset( $this->_excluded_files[$url] ) ) {
			return $url;
		}

		/**
		 * CSS files are tricky
		 * we first have to replace all relative paths with their absolute paths
		 * then we can decide if it should be uploaded
		 */
		if ( $is_css_file ) {
			$url = $this->real_css_image_url( $url );
		}

		// check if the file is in the caching database table
		$drive_url = WPB_Google_Drive_Cdn_Service_Helper::get_cached_url( $url );
		if ( empty( $drive_url ) ) {
			// if the file has not yet been uploaded to Google Drive, mark it (for doing this later)
			WPB_Google_Drive_Cdn_Service_Helper::record_url( $url );
			return $is_css_file ? str_replace( '[url]', $url, $architecture ) : $url;
		}

		// yes, it's in the db table. Go for it!

		// checks if the DRIVE file URL is in the list of excluded files
		if ( isset( $this->_excluded_files[$drive_url] ) ) {
			return $url;
		}

		// now check whether this url should be replaced
		$every_x = (int) WPB_Google_Drive_Cdn_Settings::get_setting( 'every_x' );

		if ( $every_x ) {
			if ( ! isset( $this->_replacement_counter ) ) {
				$this->_replacement_counter = 0;
			}
			$this->_replacement_counter = $this->_replacement_counter + 1;

			// replace every single link when $every_x is 1
			if ( 1 == $every_x ) {
				return $is_css_file ? str_replace( '[url]', $drive_url, $architecture ) : $drive_url;
			}

			// replace nothing
			if ( $every_x <= 0 ) {
				return $is_css_file ? str_replace( '[url]', $url, $architecture ) : $url;
			}

			// replace each x
			if ( 0 == $this->_replacement_counter % $every_x ) {
				return $is_css_file ? str_replace( '[url]', $drive_url, $architecture ) : $drive_url;
			}
		}

		// return the normal URL, because we cannot do anything here
		return $is_css_file ? str_replace( '[url]', $url, $architecture ) : $url;
	}


	/**
	 *
	 * @since 1.2
	 *
	 * @param $matches
	 *
	 * @return bool|null|string
	 */
	public function collect_css_file_urls( $matches ) {
		return $this->collect_urls( $matches, true );
	}


	/**
	 * Collect CSS file import URLs
	 *
	 * @since Unknown
	 *
	 * @param array $matches
	 *
	 * @return bool|null|string
	 */
	public function collect_css_file_imports( $matches ) {
		$new_matches = array(
			//											(1)												(2)
			// example: @import url("my-static-css-file.css") print, embossed ;
				$matches[7], // this is the primary URL (1)
				trim( $matches[11] ) // is is the stuff that comes after the URL (2)
		);
		return $this->collect_urls( $new_matches, true, true );
	}


	/**
	 * Replaces all links and returns the buffer
	 *
	 * @param string $buffer
	 *
	 * @return string
	 */
	public function buffer( $buffer ) {

		// get file extensions
		$file_extensions = WPB_Google_Drive_Cdn_Settings::get_setting( 'file_extensions' );
		if ( empty( $file_extensions ) ) {
			$file_extensions = 'jpeg, jpg, gif, png, css';
		}

		$file_extensions = explode( ',', $file_extensions );

		if ( false == $file_extensions ) {
			return $buffer;
		}

		// remove whitespaces
		$file_extensions = array_map( 'trim', $file_extensions );
		$file_extensions = array_map( 'strtolower', $file_extensions );

		// remove empty array values
		$file_extensions = array_filter( $file_extensions );

		/*
		 * filter the css extension when the user is not on SSL to prevent the so called "Mixed Content Blocker" that some browsers have
		 * @see @url http://www.pressrelations.de/new/standard/result_main.cfm?pfach=1&n_firmanr_=124352&sektor=pm&detail=1&r=532536&sid=&aktion=jour_pm&quelle=0
		 */
		if ( ! is_ssl() && ( $css_key = array_search( 'css', $file_extensions ) ) !== false ) {
			unset( $file_extensions[$css_key] );
		}

		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['excluded_files'] ) ) {
			$options['excluded_files'] = '';
		}

		$this->_excluded_files = explode( "\n", $options['excluded_files'] );
		$this->_excluded_files = array_filter( $this->_excluded_files, 'trim' );
		$this->_excluded_files = array_flip( $this->_excluded_files );

		if ( empty( $this->_excluded_files ) ) {
			$this->_excluded_files = array();
		}

		if ( $this->_is_buffer_css_file ) {
			// searches for @import urls. returns $matches[7] = URL and $matches[11] = stuff after the URL
			$buffer = preg_replace_callback( '#(@import){1}(\s)*(url)?(\s)*\(?(\s)*("|\')?([A-Za-z0-9-.:\/]*)?("|\')?(\s)*\)?(\s)*([A-Za-z0-9,\s]*)?;#is', array( $this, 'collect_css_file_imports' ), $buffer );
			return preg_replace_callback( '#url\((.+?)\)#is', array( $this, 'collect_css_file_urls' ), $buffer );
		}

		return preg_replace_callback( '#(?<=(\'|"))(https?://[\d\w\-\./]+\.(?:' . implode( '|', $file_extensions ) . ')(?!\.))#is', array( $this, 'collect_urls' ), $buffer );
	}


	/**
	 * Starts the output buffering
	 * @since 1.0
	 */
	public function ob_start() {
		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		// do not start buffering if the api isn't working
		if ( ! $helper->is_api_working() ) {
			return;
		}

		ob_start( array( $this, 'buffer' ) );
	}


	/**
	 * Creates the databases on activation (cronjob)
	 * @since 1.0
	 * @return void
	 */
	public function on_activation() {

		// stop syncing (necessary when sync was in action when the plugin has been deactivated)
		update_option( 'wpbgdc_currently_syncing', 0 );

		$options = get_option( 'wpbgdc' );
		if ( ! isset( $options['sync_interval'] ) ) {
			$options['sync_interval'] = 'hourly';
		}
		if ( empty( $options['sync_interval'] ) ) {
			$options['sync_interval'] = 'hourly';
		}
		wp_schedule_event( time(), $options['sync_interval'], 'wpbgdc_hourly_event' );

		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return;
		}

		// creating files table
		$sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wpbgdc_files` ( "
				. "`file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, "
				. "`file_live_url` tinytext NOT NULL, "
				. "`file_drive_url` tinytext NOT NULL, "
				. "`file_drive_id` varchar(255) NOT NULL, "
				. "`file_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, "
				. "`file_synced` tinyint(1) NOT NULL DEFAULT '0', "
				. "`file_etag` VARCHAR( 255 ) NOT NULL, "
				. "PRIMARY KEY (`file_id`) "
				. ") DEFAULT CHARSET = utf8 AUTO_INCREMENT=1 ;" . chr( 10 );

		$wpdb->query( $sql );
	}


	/**
	 * Removes the database tables on deactivation
	 * @since 1.0
	 * @return bool|false|int
	 */
	public function on_deactivation() {
		wp_clear_scheduled_hook( 'wpbgdc_hourly_event' );

		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return false;
		}

		$sql = "DROP TABLE `" . $wpdb->prefix . "wpbgdc_files`;";

		return $wpdb->query( $sql );
	}


	/**
	 * Searches for the upload folder on the Google Drive.
	 * If there is no such folder it will be created
	 * @since 1.0
	 */
	public function search_folder() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'search_folder' != $_REQUEST['action'] ) {
			return;
		}

		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		// search for the folder. if the folder does not exist, create it
		$helper->search_folder();

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) );
	}


	/**
	 * Adds the admin bar items
	 * @global WP_Admin_Bar $wp_admin_bar
	 * @since 1.0
	 */
	public function admin_bar_items() {
		global $wp_admin_bar;

		if ( ! $wp_admin_bar instanceof WP_Admin_Bar ) {
			return;
		}

		if ( is_network_admin() ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$title = '<span class="ab-label">' . __( 'Clear CDN cache', $this->get_textdomain() ) . '</span>';

		$wp_admin_bar->add_menu( array(
				'id'    => 'wpbgdc_item',
				'title' => $title,
				'href'  => admin_url( 'options-general.php?page=wpbgdc&action=clearcache' ),
				'meta'  => array( 'title' => __( 'Clear the Google to WordPress CDN cache', $this->get_textdomain() ) ),
		) );

		$last_sync_time = get_option( 'wpbgdc_currently_syncing', 0 );

		// check if the last upload was made long ago (on hour). if so, there was a problem with the sync process. we set it back so that the menu entry disappears.
		if ( ( $last_sync_time + HOUR_IN_SECONDS ) < time() && $last_sync_time != 0 ) {
			update_option( 'wpbgdc_currently_syncing', 0 );
		}

		if ( (bool) $last_sync_time ) {

			$options = get_option( 'wpbgdc' );
			if ( ! isset( $options['upload_new_files_only'] ) ) {
				$options['upload_new_files_only'] = 0;
			}

			$label = __( 'CDN sync in progress! Click here to stop.', $this->get_textdomain() );
			if ( (bool) $options['upload_new_files_only'] ) {
				$label = __( 'CDN upload in progress! Click here to stop.', $this->get_textdomain() );
			}

			$stop_sync = (bool) get_option( 'wpbgdc_stop_sync', 0 );
			if ( $stop_sync ) {
				$label = __( 'Stopping CDN sync/upload ...', $this->get_textdomain() );
			}

			$title = '<span class="dashicons dashicons-update wpbgdc-admin-bar-sync"></span> ' .
					'<span class="ab-label">' . $label . '</span>';

			$wp_admin_bar->add_menu( array(
					'id'    => 'wpbgdc_item_sync',
					'title' => $title,
					'href'  => $stop_sync ? '' : admin_url( 'options-general.php?page=wpbgdc&action=stop_sync&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_stop_sync' ) ),
					'meta'  => array( 'title' => __( 'The Google Drive CDN plugin currently syncs or uploads your items to your Google Drive storage.', $this->get_textdomain() ) ),
			) );
		}
	}


	/**
	 * Clears the CDN cache and all files from the Drive Folder
	 * @since 1.0
	 * @global wpdb $wpdb
	 */
	public function clear_cache() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'clearcache' != $_REQUEST['action'] ) {
			return;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_clearcache' ) ) {
			return;
		}

		global $wpdb;

		if ( ! $wpdb instanceof wpdb ) {
			return;
		}

		$wpdb->query( 'TRUNCATE TABLE `' . $wpdb->prefix . 'wpbgdc_files`' );

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) . '&notice=cache_emptied' );
	}


	/**
	 * Delete the  error logs
	 * @since 1.1
	 */
	public function delete_error_logs() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'delete_error_logs' != $_REQUEST['action'] ) {
			return;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_delete_error_logs' ) ) {
			return;
		}

		update_option( 'wpbgdc_error_log', array() );
		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) . '&notice=deleted_error_logs' );
	}


	/**
	 * Disconnects the Google Drive by removing the
	 * @since 1.1
	 */
	public function disconnect() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'disconnect' != $_REQUEST['action'] ) {
			return;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_disconnect' ) ) {
			return;
		}

		update_option( 'wpbgdc_currently_syncing', 0 );

		global $wpdb;
		if ( $wpdb instanceof wpdb ) {
			$wpdb->query( 'TRUNCATE TABLE `' . $wpdb->prefix . 'wpbgdc_files`' );
		}

		WPB_Google_Drive_Cdn_Settings::set_setting( 'token', '', 'wpbgdc_oauth_token' );
		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) . '&notice=disconnected' );
	}


	/**
	 * Removes all files from the Google Drive and clears the caching table
	 * @since 1.0
	 * @global wpdb $wpdb
	 */
	public function empty_google_drive() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'empty_google_drive' != $_REQUEST['action'] ) {
			return;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_empty_google_drive' ) ) {
			return;
		}

		global $wpdb;

		if ( $wpdb instanceof wpdb ) {
			$wpdb->query( 'TRUNCATE TABLE `' . $wpdb->prefix . 'wpbgdc_files`' );
		}

		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		// delete the folder and all its files
		$helper->delete_folder();

		// recreate the folder
		$helper->create_folder();

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) . '&notice=removed_files' );
	}


	/**
	 * Returns the purchase Code (for processing the update)
	 * @since 1.0
	 * @return string
	 */
	public function get_purchase_code() {
		return WPB_Google_Drive_Cdn_Settings::get_setting( 'purchase_code' );
	}


	/**
	 * Does the Google Drive Sync of the media library
	 * @since 1.0
	 */
	public function ajax_sync() {

		if ( ! isset( $_REQUEST['start'] ) ) {
			$start = 0;
		}
		else {
			$start = $_REQUEST['start'];
		}

		$files_to_upload_per_pageload = 3;

		$number_of_attachments = wp_count_posts( 'attachment' );
		$number_of_attachments = $number_of_attachments->inherit;

		$attachments = get_posts( array(
				'post_type'   => 'attachment',
				'offset'      => $start,
				'numberposts' => 3
		) );

		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		$google_drive_url = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_link', 'wpbgdc_folders' );

		$files_uploaded = 0;

		// @see http://codex.wordpress.org/Function_Reference/wp_upload_dir
		$upload_dir  = wp_upload_dir();
		$content_url = trailingslashit( $upload_dir['baseurl'] );
		$upload_dir  = trailingslashit( $upload_dir['basedir'] );

		// run through the attachments
		foreach ( $attachments as $attachment ) {

			// it has to be an instance of WP_Post otherwise this will not work
			if ( ! $attachment instanceof WP_Post ) {
				continue;
			}

			// this only works on non-multisites
			//$file = str_replace( content_url(), WP_CONTENT_DIR, $attachment->guid );

			$file = str_replace( $content_url, $upload_dir, wp_get_attachment_url( $attachment->ID ) );

			// don't do anything if file does not exist
			if ( ! is_file( $file ) ) {
				continue;
			}

			$new_url = $helper->file_upload( $file, $attachment->guid );

			//check if the google drive URL is in the new_url. if yes, the file has been uploaded
			if ( strstr( $new_url, $google_drive_url ) ) {
				$files_uploaded ++;
			}

		}

		// the json headers
		header( 'Cache-Control: no-cache, must-revalidate' );
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Content-type: application/json' );

		$uploaded_total = round( 100 / $number_of_attachments * ( $start + $files_uploaded ), 0 );

		// print json
		die( json_encode( array(
				'error'                 => 0,
				'number_of_attachments' => $number_of_attachments,
				'files_per_pageload'    => $files_to_upload_per_pageload,
				'files_uploaded'        => $files_uploaded,
				'uploaded_total'        => $uploaded_total,
				'next'                  => ( $start + $files_to_upload_per_pageload ),
				'contente_url'          => $content_url,
				'upload_dir'            => $upload_dir
		) ) );
	}


	/**
	 * Checks if sync is in progress. If not, it will ping the sync process to start syncing files to Google Drive
	 *
	 * @since 1.0
	 * @global string $wp_version
	 */
	public function ping_sync() {

		// prevent looping around
		if ( isset( $_REQUEST['wpbgdc_stop_ping'] ) ) {
			return;
		}

		/**
		 * Check if someone clicked the "stop sync button". If so, do not ping sync again (called by the db_sync function).
		 * Instead, stop here (= break) and set back the 'wpbgdc_stop_sync' option
		 */
		if ( (bool) get_option( 'wpbgdc_stop_sync', 0 ) ) {
			update_option( 'wpbgdc_stop_sync', 0 );
			return;
		}

		// do not sync if the sync process is already running
		if ( (bool) get_option( 'wpbgdc_currently_syncing', 0 ) ) {
			return;
		}

		global $wp_version;

		$nonce = wp_create_nonce( 'wpbgdc_sync_nonce' );

		// prepare the elements for the POST-call
		$post_elements = array(
				'action'           => 'wpbgdc_db_sync',
				'wpbgdc_nonce'     => $nonce,
				'wpbgdc_stop_ping' => 1
		);

		// some more options for the POST-call
		$options = array(
				'timeout'     => 5,
				'body'        => $post_elements,
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => false,
				'stream'      => false,
				'httpversion' => '1.1',
				'headers'     => array(
						'connection' => 'close',
				)
		);

		$post = wp_remote_post( admin_url( 'admin-ajax.php' ), $options );

		if ( is_wp_error( $post ) && 'http_request_failed' != $post->get_error_code() ) {
			$this->set_error( $post->get_error_message() . ' (wpbgdc: ping_sync 1)' );
		}

	}


	/**
	 * When click the "start sync" button from the backend, this will start the sync manually
	 * @since 1.0
	 */
	public function start_sync() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'start_sync' != $_REQUEST['action'] ) {
			return;
		}

		delete_option( 'wpbgdc_stop_sync' );

		update_option( 'wpbgdc_currently_syncing', 0 );

		$this->ping_sync();

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc&notice=sync_started' ) );
	}


	/**
	 * A loop that syncs files to the Google Drive
	 * @since 1.0
	 */
	public function db_sync() {

		//if( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) return;

		// nonces are always attached to a user
		// therefore nonces will not work because there is no user that can have a nonce

		//if( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_sync_nonce' ) ) return;

		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return;
		}

		// starting sync
		update_option( 'wpbgdc_currently_syncing', time() );

		// delete old caching entries if any
		WPB_Google_Drive_Cdn_Service_Helper::remove_old_entries();

		// @see http://codex.wordpress.org/Function_Reference/wp_upload_dir
		//$upload_dir = wp_upload_dir();
		//$content_url = trailingslashit( $upload_dir['baseurl'] ); // this is wp-content/uploads/
		$content_url = trailingslashit( content_url() ); // this is wp-content/
		//$upload_dir  = trailingslashit( $upload_dir['basedir'] );

		$content_dir = trailingslashit( WP_CONTENT_DIR );

		$service_helper = new WPB_Google_Drive_Cdn_Service_Helper( $this );

		/**
		 * If the "Upload new files only" option is set, the plugin will not check if the file has changed on Google Drive
		 * Instead it just uploads the files that have not yet been uploaded
		 */
		$options = get_option( 'wpbgdc' );
		if ( ! isset( $options['upload_new_files_only'] ) ) {
			$options['upload_new_files_only'] = 0;
		}

		$upload_only = (bool) $options['upload_new_files_only'];

		/***
		 * CHECK FOR NEW OR UPDATED FILES AND UPLOAD OR UPDATE THEM
		 */

		if ( $upload_only ) {
			$db_files = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'wpbgdc_files` WHERE `file_synced` = "0" AND `file_drive_id` = ""', OBJECT );
		}
		else {
			$db_files = $wpdb->get_results( 'SELECT * FROM `' . $wpdb->prefix . 'wpbgdc_files` WHERE `file_synced` = "0"', OBJECT );
		}

		/**
		 * check if the script should sleep after a file upload
		 */
		$sleeping_time = WPB_Google_Drive_Cdn_Settings::get_setting( 'sleep_time' );
		$sleeping_time = intval( $sleeping_time );
		$sleeping_time = max( 0, $sleeping_time );

		foreach ( $db_files as $db_file ) {

			$url            = $db_file->file_live_url;
			$is_content_url = false !== strpos( $url, $content_url ) ? true : false;

			/**
			 * Workaround for SSL-Sites
			 * This sets the $content_url to a new value when the file could not be found on https:// but on http
			 * or the other way around
			 */
			if ( ! $is_content_url ) {
				// are we on ssl?
				if ( false !== strpos( $content_url, 'https://' ) ) {
					// yes, we are on SSL
					// check if the file was found on non ssl-url
					$is_content_url = false !== stripos( $url, str_replace( 'https://', 'http://', $content_url ) ) ? true : false;
					if ( $is_content_url ) {
						$content_url = str_replace( 'https://', 'http://', $content_url );
					}
				}
				else {
					// no, we are not on SSL
					$is_content_url = false !== stripos( $url, str_replace( 'http://', 'https://', $content_url ) ) ? true : false;
					if ( $is_content_url ) {
						$content_url = str_replace( 'http://', 'https://', $content_url );
					}
				}
			}

			if ( ! $is_content_url ) {
				// set a flag in the db entry to prevent doing the same operation again
				$wpdb->update( $wpdb->prefix . 'wpbgdc_files', array( 'file_synced' => 1 ), array( 'file_id' => $db_file->file_id ) );
				continue;
			}

			// get the path to the file
			$file = str_replace( $content_url, $content_dir, $url );

			// if the file does not exist on the server, return the url = stop here
			if ( ! is_file( $file ) ) {
				// set a flag in the db entry to prevent doing the same operation again
				$wpdb->update( $wpdb->prefix . 'wpbgdc_files', array( 'file_synced' => 1 ), array( 'file_id' => $db_file->file_id ) );
				continue;
			}

			// count file uploads. set to 0 if not yet set
			if ( ! isset( $this->file_upload_count ) ) {
				$this->file_upload_count = 0;
			}

			// stop if 3 files have been uploaded because otherwise it's too much for the webserver
			if ( $this->file_upload_count >= 3 ) {

				// redirect will not work here because this was invoked by wp_remote_post and this function does not follow redirects to an unlimited length
				// wp_redirect( admin_url( 'admin-ajax.php' ) . '?action=wpbgdc_db_sync&wpbgdc_stop_ping=1&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_sync_nonce' ), 301 );
				// so we start doing another ping, but at first we have to stop the current sync process to restart it later (in the ping_sync() function)
				update_option( 'wpbgdc_currently_syncing', 0 );
				if ( isset( $_REQUEST['wpbgdc_stop_ping'] ) ) {
					unset( $_REQUEST['wpbgdc_stop_ping'] );
				}
				$this->ping_sync();

				// then die
				die();
			}

			// upload the file and update the database entry
			$service_helper->file_upload( $file, $url );

			sleep( $sleeping_time );

			// only count if a file has been uploaded
			if ( $service_helper->_file_uploaded ) {
				$this->file_upload_count ++;
			}

			// mark as synced anyway (it doesn't really matter if the file has been uploaded or not
			// the only thing that matters if there has been the approach to upload it
			$wpdb->update( $wpdb->prefix . 'wpbgdc_files', array( 'file_synced' => 1 ), array( 'file_id' => $db_file->file_id ) );

		}

		// stop syncing
		update_option( 'wpbgdc_currently_syncing', 0 );

		// reset the synced flag within all database entries
		$wpdb->query( 'UPDATE `' . $wpdb->prefix . 'wpbgdc_files` SET `file_synced`= 0' );

	}


	/**
	 * @param string $msg
	 * @param bool   $redirect
	 *
	 * @since 1.1
	 *
	 * @return array mixed
	 */
	public static function set_error( $msg, $redirect = true ) {
		$errors = get_option( 'wpbgdc_error_log', array() );
		if ( ! is_array( $errors ) ) {
			$errors = array();
		}
		$errors[] = date( 'c' ) . ': ' . $msg;

		update_option( 'wpbgdc_error_log', array_slice( $errors, - 10 ) );

		if ( $redirect ) {
			wp_redirect( admin_url( 'options-general.php?page=wpbgdc&notice=last_error' ) );
			die();
		}

		return $msg;
	}


	/**
	 * Returns the message of the last error in the log
	 * @since 1.1
	 * @return string
	 */
	public function get_last_error() {
		$errors = get_option( 'wpbgdc_error_log', array() );
		if ( ! is_array( $errors ) ) {
			return '';
		}
		return end( $errors );
	}


	/**
	 * Reschedules the hourly event after updating the settings page
	 * @since 1.2
	 *
	 * @param array $old_val
	 * @param array $new_val
	 */
	public function reschedule_event( $old_val, $new_val ) {
		if ( ! isset( $new_val['sync_interval'] ) ) {
			return;
		}

		//wp_reschedule will not work here because we have to know the timestamp, too (which we doesn't know at this state).
		wp_clear_scheduled_hook( 'wpbgdc_hourly_event' );
		wp_schedule_event( time(), $new_val['sync_interval'], 'wpbgdc_hourly_event' );
	}


	/**
	 * Does some upgrades
	 * @since 1.2
	 */
	public function on_upgrade() {

		// do this only when upgrading from lower versions than 1.2.2
		if ( version_compare( get_option( 'wpb_plugin_' . $this->get_plugin_name_sanitized() . '_version', 0 ), '1.2.2', '<' ) ) {
			global $wpdb;
			if ( ! $wpdb instanceof wpdb ) {
				return;
			}

			// check if there is a column named file_etag already
			$columns = $wpdb->get_results( 'SHOW columns from `' . $wpdb->prefix . 'wpbgdc_files` where field="file_etag"' );

			if ( is_array( $columns ) && count( $columns ) <= 0 ) {
				$sql = 'ALTER TABLE `' . $wpdb->prefix . 'wpbgdc_files` ADD `file_etag` VARCHAR( 255 ) NOT NULL';
				$wpdb->query( $sql );
			}
		}
	}


	/**
	 * Adds mime types to WordPress
	 * @since 1.2
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function mime_types( $types ) {

		$types['ttf']  = 'application/octet-stream';
		$types['woff'] = 'application/font-woff';
		$types['eot']  = 'application/vnd.ms-fontobject';
		return $types;
	}


	/**
	 * Adds new schedule times
	 *
	 * @param array $times
	 *
	 * @return array
	 * @since 1.5
	 */
	public function add_schedule_time( $times ) {

		$times['1week']  = array( 'interval' => WEEK_IN_SECONDS, 'display' => __( 'Once a week', $this->get_textdomain() ) );
		$times['2weeks'] = array( 'interval' => 2 * WEEK_IN_SECONDS, 'display' => __( 'Every 2th week', $this->get_textdomain() ) );
		$times['3weeks'] = array( 'interval' => 3 * WEEK_IN_SECONDS, 'display' => __( 'Every 3rd week', $this->get_textdomain() ) );
		$times['1month'] = array( 'interval' => 4 * WEEK_IN_SECONDS, 'display' => __( 'Once a month', $this->get_textdomain() ) );

		return $times;

	}


	/**
	 * Adds dashicons
	 * @since 1.5
	 */
	public function admin_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'wpbgdcdn-backend', $this->plugins_url( 'assets/css/style-backend.css' ) );
	}


	/**
	 * Adds dashicons to the frontend and only if the admin bar is showing
	 * @since 1.6.1
	 */
	public function frontend_styles() {
		if ( is_admin_bar_showing() ) {
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'wpbgdcdn-backend', $this->plugins_url( 'assets/css/style-backend.css' ) );
		}
	}


	/**
	 * Stops the sync process
	 * @since 1.1
	 */
	public function stop_sync() {
		if ( ! isset( $_REQUEST['action'] ) ) {
			return;
		}
		if ( 'stop_sync' != $_REQUEST['action'] ) {
			return;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['wpbgdc_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_REQUEST['wpbgdc_nonce'], 'wpbgdc_stop_sync' ) ) {
			return;
		}

		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		if ( ! is_a( $wpdb, 'wpdb' ) ) {
			return;
		}

		// reset the synced flag within all database entries
		$wpdb->query( 'UPDATE `' . $wpdb->prefix . 'wpbgdc_files` SET `file_synced`= 0' );

		//update_option( 'wpbgdc_currently_syncing', 0 );

		add_option( 'wpbgdc_stop_sync', 1, '', 'no' );

		wp_redirect( admin_url( 'options-general.php?page=wpbgdc' ) . '&notice=sync_stopped' );
	}
}

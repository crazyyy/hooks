<?php

/**
 * @package    WPBuddy Plugin
 * @subpackage Google Drive CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Handles the settings of the plugin
 * @version 1.0
 */
class WPB_Google_Drive_Cdn_Settings {


	/**
	 * @var null|WPB_Google_Drive_Cdn
	 */
	private $_google_drive_cdn = null;


	/**
	 * @param WPB_Google_Drive_Cdn $google_drive_cdn
	 *
	 * @since    1.0
	 * @return \WPB_Google_Drive_Cdn_Settings
	 */
	public function __construct( &$google_drive_cdn ) {
		$this->_google_drive_cdn = $google_drive_cdn;
	}

	/**
	 * Returns the content of a setting
	 *
	 * @param        $name
	 *
	 * @param string $option_name
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function get_setting( $name, $option_name = 'wpbgdc' ) {
		$options = get_option( $option_name );
		if ( ! isset( $options[$name] ) ) {
			return '';
		}
		return $options[$name];
	}


	/**
	 * Saves an option to the database
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @param string $option_name
	 *
	 * @return bool
	 */
	public static function set_setting( $name, $value, $option_name = 'wpbgdc' ) {
		$options        = get_option( $option_name );
		$options[$name] = $value;
		return update_option( $option_name, $options );
	}


	/**
	 * settings function.
	 * creates the settings-fields in the settings-metaboxes
	 *
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	function settings() {
		register_setting( 'wpbgdc_options_group', 'wpbgdc' );

		/**
		 * Client credentials
		 */

		add_settings_section(
			'wpbgdc_section_client_credentials',
			'',
			array( &$this, 'sectionText' ),
			'wpbgdc'
		);

		add_settings_field(
			'wpbgdc_client_id',
			__( 'Client ID', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_client_id' ),
			'wpbgdc',
			'wpbgdc_section_client_credentials',
			array(
				'label_for' => 'wpbgdc_client_id'
			)
		);

		add_settings_field(
			'wpbgdc_client_secret',
			__( 'Client secret', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_client_secret' ),
			'wpbgdc',
			'wpbgdc_section_client_credentials',
			array(
				'label_for' => 'wpbgdc_client_secret'
			)
		);

		add_settings_field(
			'wpbgdc_purchase_code',
			__( 'Purchase Code', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_purchase_code' ),
			'wpbgdc',
			'wpbgdc_section_client_credentials',
			array(
				'label_for' => 'wpbgdc_purchase_code'
			)
		);

		add_settings_field(
			'wpbgdc_temp_dir_change',
			__( 'Change temp dir', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_temp_dir_change' ),
			'wpbgdc',
			'wpbgdc_section_client_credentials',
			array(
				'label_for' => 'wpbgdc_temp_dir_change'
			)
		);

		add_settings_field(
			'wpbgdc_temp_dir',
			__( 'Temp. dir', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_temp_dir' ),
			'wpbgdc',
			'wpbgdc_section_client_credentials',
			array(
				'label_for' => 'wpbgdc_temp_dir'
			)
		);

		/**
		 * Settings
		 */

		add_settings_section(
			'wpbgdc_section_settings',
			'',
			array( &$this, 'sectionText' ),
			'wpbgdc'
		);

		add_settings_field(
			'wpbgdc_every_x',
			__( 'Only replace every', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_every_x' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_every_x'
			)
		);

		add_settings_field(
			'wpbgdc_cache_time',
			__( 'Cache time', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_cache_time' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_cache_time'
			)
		);

		add_settings_field(
			'wpbgdc_sync_interval',
			__( 'Sync / Upload interval', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_sync_interval' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_sync_interval'
			)
		);

		add_settings_field(
			'wpbgdc_sleep_time',
			__( 'Sleep time', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_sleep_time' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_sleep_time'
			)
		);

		add_settings_field(
			'wpbgdc_upload_new_files_only',
			__( 'Never sync / Upload new files only', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_upload_new_files_only' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_upload_new_files_only'
			)
		);

		add_settings_field(
			'wpbgdc_file_extensions',
			__( 'File extensions', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_file_extensions' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_file_extensions'
			)
		);

		add_settings_field(
			'wpbgdc_excluded_files',
			__( 'Excluded files', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_excluded_files' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_excluded_files'
			)
		);

		add_settings_field(
			'wpbgdc_disconnect_button',
			__( 'Actions', $this->_google_drive_cdn->get_textdomain() ),
			array( &$this, 'field_wpbgdc_disconnect_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_disconnect_button'
			)
		);

		add_settings_field(
			'wpbgdc_clearcache_button',
			'',
			array( &$this, 'field_wpbgdc_clearcache_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_clearcache_button'
			)
		);

		add_settings_field(
			'wpbgdc_trash_button',
			'',
			array( &$this, 'field_wpbgdc_trash_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_trash_button'
			)
		);

		add_settings_field(
			'wpbgdc_syncmedia_button',
			'',
			array( &$this, 'field_wpbgdc_syncmedia_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_syncmedia_button'
			)
		);

		add_settings_field(
			'wpbgdc_sync_button',
			'',
			array( &$this, 'field_wpbgdc_sync_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_sync_button'
			)
		);

		if ( (bool) get_option( 'wpbgdc_currently_syncing', 0 ) ) {
			add_settings_field(
				'wpbgdc_stop_sync_button',
				'',
				array( &$this, 'field_wpbgdc_stop_sync_button' ),
				'wpbgdc',
				'wpbgdc_section_settings',
				array(
					'label_for' => 'wpbgdc_stop_sync_button'
				)
			);
		}

		add_settings_field(
			'wpbgdc_save_button',
			'',
			array( &$this, 'field_wpbgdc_submit_button' ),
			'wpbgdc',
			'wpbgdc_section_settings',
			array(
				'label_for' => 'wpbgdc_submit_button'
			)
		);
	}


	/**
	 * sectionText function.
	 * this is a text-section
	 *
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	function sectionText() {
	}


	/**
	 * outputs the field for the client id
	 *
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function field_wpbgdc_client_id() {

		// get the options
		$options = get_option( 'wpbgdc' );

		echo "<input class=\"regular-text\" id='wpbgdc_client_id' name='wpbgdc[client_id]'  type='text' value='" . ( ( isset( $options['client_id'] ) ) ? esc_attr( $options['client_id'] ) : '' ) . "' />";

	}

	/**
	 * outputs the field for the client id
	 *
	 * @access public
	 * @return void
	 * @since  1.0
	 */
	public function field_wpbgdc_client_secret() {

		// get the options
		$options = get_option( 'wpbgdc' );

		echo "<input class=\"regular-text\" id='wpbgdc_client_secret' name='wpbgdc[client_secret]'  type='text' value='" . ( ( isset( $options['client_secret'] ) ) ? esc_attr( $options['client_secret'] ) : '' ) . "' />";

	}


	/**
	 * outputs the field for the "every_x"
	 * @since 1.0
	 * @return void
	 */
	public function field_wpbgdc_every_x() {
		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['every_x'] ) ) {
			$options['every_x'] = 3;
		}

		echo "<input class=\"small-text\" id='wpbgdc_every_x' name='wpbgdc[every_x]'  type='text' value='" . (int) $options['every_x'] . "' /> " . __( 'file', $this->_google_drive_cdn->get_textdomain() );
		echo '<p class="description">' . __( 'Increasing this number can improve load time because your own server can deliver files, too. Not every single file should be delivered from your Google Drive. It is better to spread them across multiple servers.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}


	/**
	 * outputs the html for the cache_time field
	 * @since 1.0
	 * @return void
	 */
	public function field_wpbgdc_cache_time() {

		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['cache_time'] ) ) {
			$options['cache_time'] = '';
		}

		echo "<input class=\"small-text\" id='wpbgdc_cache_time' name='wpbgdc[cache_time]'  type='text' value='" . $options['cache_time'] . "' /> " . __( 'hours (It\'s recommend to leave this empty because static files do not change very often.)', $this->_google_drive_cdn->get_textdomain() );
		echo '<p class="description">' . __( 'Please specify how long the URLs should be cached. Set to 0 to deactivate. Leave it blank to store the files for an indefinite period.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';

	}

	/**
	 * outputs the html for the cache_time field
	 * @since 1.0
	 * @return void
	 */
	public function field_wpbgdc_sync_interval() {

		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['sync_interval'] ) ) {
			$options['sync_interval'] = '';
		}

		echo '<select id="wpbgdc_sync_interval" name="wpbgdc[sync_interval]">';
		foreach ( wp_get_schedules() as $recurrence => $schedule_arr ) {
			echo '<option ' . ( ( $options['sync_interval'] == $recurrence ) ? 'selected="selected"' : '' ) . ' value="' . $recurrence . '">' . $schedule_arr['display'] . '</option>';
		}
		echo '</select>';

		echo '<p class="description">' . __( 'How often the files should be synced. If you have a very large media library you should consider doing this not too often because it will need a lot of resources on your webserver.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';

	}

	/**
	 * outputs the html for the sleep_time field
	 * @since 1.0
	 * @return void
	 */
	public function field_wpbgdc_sleep_time() {

		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['sleep_time'] ) ) {
			$options['sleep_time'] = 0;
		}

		echo "<input class=\"small-text\" id='wpbgdc_sleep_time' name='wpbgdc[sleep_time]'  type='number' min='0' value='" . intval( max( 0, $options['sleep_time'] ) ) . "' /> " . __( 'seconds', $this->_google_drive_cdn->get_textdomain() );
		echo '<p class="description">' . __( 'On some (cheap) webservers uploading files to Google Drive slows down your site. In this case you can define a time to wait until the next file is uploaded. This can help to reduce the CPU load. However the best solution would be to upgrade to a faster hosting that has more power.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';

	}

	/**
	 * outputs the html for the file extension fields
	 * @since 1.1
	 * @return void
	 */
	public function field_wpbgdc_file_extensions() {

		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['file_extensions'] ) ) {
			$options['file_extensions'] = 'jpeg, jpg, gif, png, css';
		}

		echo "<input class=\"regular-text\" id='wpbgdc_file_extensions' name='wpbgdc[file_extensions]'  type='text' value='" . esc_attr( $options['file_extensions'] ) . "' />";
		echo '<p class="description">' . __( 'File extensions to search for (comma separated). Note that already uploaded files remain in Google Drive if a file extension is removed. Clear your local CDN cache after updating this field.', $this->_google_drive_cdn->get_textdomain() )
				. ( ( ! is_ssl() ) ? ' <strong style="color: red;">' . __( 'The plugin found out that you are currently not using SSL. To prevent "Mixed Content Blocker" warnings some browsers produce, no CSS files will be used from your Google Drive.', $this->_google_drive_cdn->get_textdomain() ) : '' ) . '</strong>'
				. '</p>';

	}


	/**
	 * outputs the textarea field for the exlduded files
	 * @since 1.6
	 * @return void
	 */
	public function field_wpbgdc_excluded_files() {
		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['excluded_files'] ) ) {
			$options['excluded_files'] = '';
		}

		echo "<textarea cols='50' rows='10' class=\"large-text code\" id='wpbgdc_excluded_files' name='wpbgdc[excluded_files]'  type='text'>" . esc_textarea( $options['excluded_files'] ) . "</textarea>";
		echo '<p class="description">' . __( 'Add full URLs of files you want to exclude. One per line.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}


	/**
	 * outputs the html for the file extension fields
	 * @since 1.3
	 * @return void
	 */
	public function field_wpbgdc_temp_dir_change() {

		// get the options
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['temp_dir_change'] ) ) {
			$options['temp_dir_change'] = 0;
		}

		echo '<input style="width: auto;" class="regular-text" id="wpbgdc_temp_dir_change" name="wpbgdc[temp_dir_change]"  type="checkbox" value="1" ' . ( ( (bool) $options['temp_dir_change'] ) ? 'checked="checked"' : '' ) . ' />';
		echo '<p class="description">' . __( 'Activate this if you\'re getting a "open_basedir restriction in effect" error.', $this->_google_drive_cdn->get_textdomain() ) . ' <a href="http://wp-buddy.com/documentation/plugins/google-drive-cdn-wordpress-plugin/faq/" target="_blank">' . __( 'Read more about it here. (Question #15)', $this->_google_drive_cdn->get_textdomain() ) . '</a></p>';

	}

	/**
	 * outputs the html for the file extension fields
	 * @since 1.3
	 * @return void
	 */
	public function field_wpbgdc_temp_dir() {

		// get the options
		$options = get_option( 'wpbgdc' );

		$upload_dir = wp_upload_dir();

		if ( ! isset( $options['temp_dir'] ) ) {
			$options['temp_dir'] = str_replace( $upload_dir['subdir'], '', $upload_dir['path'] );
		}

		echo '<input class="regular-text" id="wpbgdc_temp_dir" name="wpbgdc[temp_dir]"  type="text" value="' . $options['temp_dir'] . '" />';
		echo '<p class="description">' . __( 'This is the path to a writable folder. Only set when "Change temp dir" is activated.', $this->_google_drive_cdn->get_textdomain() ) . ' <a href="http://wp-buddy.com/documentation/plugins/google-drive-cdn-wordpress-plugin/faq/" target="_blank">' . __( 'Read more about it here. (Question #15)', $this->_google_drive_cdn->get_textdomain() ) . '</a></p>';

	}


	/**
	 * outputs the html for the purchase code field
	 * @since 1.0
	 * @return void
	 */
	public function field_wpbgdc_purchase_code() {

		// get the options
		$options = get_option( 'wpbgdc' );
		echo "<input class=\"regular-text\" id='wpbgdc_purchase_code' name='wpbgdc[purchase_code]'  type='text' value='" . ( ( isset( $options['purchase_code'] ) ) ? esc_attr( $options['purchase_code'] ) : '' ) . "' />";
		echo '<p class="description">' . sprintf( __( 'In order to get automatic updates you should copy and paste your purchase code here. If you do not know where to get your purchase code, please click here: %s', $this->_google_drive_cdn->get_textdomain() ), '<a href="http://wp-buddy.com/wiki/where-to-find-your-purchase-code/" target="_blank">' . __( 'Where to find your purchase code', $this->_google_drive_cdn->get_textdomain() ) . '</a>' ) . '</p>';

	}


	/**
	 * Adds the submit button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_submit_button() {
		submit_button( null, 'primary', 'submit', false ); //no wrapping
	}


	/**
	 * Adds the disconnect-button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_disconnect_button() {
		echo '<a class="button" href="' . admin_url( 'options-general.php?page=wpbgdc&action=disconnect&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_disconnect' ) ) . '"><span class="dashicons dashicons-no wpbgdc-icon-no"></span> ' . __( 'Disconnect', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
		echo '<p class="description">' . __( 'Disconnects your Website from Google Drive. It does not delete any files.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

	/**
	 * Adds the clearcache-button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_clearcache_button() {
		echo '<a class="button" href="' . admin_url( 'options-general.php?page=wpbgdc&action=clearcache&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_clearcache' ) ) . '"><span class="dashicons dashicons-editor-unlink wpbgdc-icon-unlink"></span> ' . __( 'Clear local CDN cache', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
		echo '<p class="description">' . __( 'Clears the local cache (which means that all links from local to Google Drive files will be lost and the plugin has to fetch it again).', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

	/**
	 * Adds the trash-button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_trash_button() {
		echo ' <a class="button" href="' . admin_url( 'options-general.php?page=wpbgdc&action=empty_google_drive&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_empty_google_drive' ) ) . '"><span class="dashicons dashicons-post-trash wpbgdc-icon-trash"></span> ' . __( 'Trash CDN files', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
		echo '<p class="description">' . __( 'Moves all cached files to the Google Drive trash. It does not delete any files. You have to delete these files yourself.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

	/**
	 * Adds the sync-button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_sync_button() {
		echo '<a id="wpbgdc_sync_button" class="button" data-sync_text="' . __( 'Sync local CDN', $this->_google_drive_cdn->get_textdomain() ) . '" data-uploads_only_text="' . __( 'Upload new files', $this->_google_drive_cdn->get_textdomain() ) . '" href="' . admin_url( 'options-general.php?page=wpbgdc&action=start_sync&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_start_sanc' ) ) . '"><span class="dashicons dashicons-backup wpbgdc-icon-backup"></span> <span>' . __( 'Sync local CDN', $this->_google_drive_cdn->get_textdomain() ) . '</span></a>';
		echo '<p class="description">' . __( 'Syncs the local CDN Cache database with your Google Drive folder. Existing files will be updated (if necessary). Files that are not yet on Google Drive will be uploaded.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

	/**
	 * Adds the sync-media-library-button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_syncmedia_button() {
		echo '<a id="wpbgdc_sync_media_button" class="button" href="' . admin_url( 'admin-ajax.php' ) . '"><span class="dashicons dashicons-admin-media wpbgdc-icon-media"></span> ' . __( 'Sync my media library now', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
		echo ' <span id="wpbgdc_sync_processed" style="display:none; margin-left: 1em;">0%</span>';
		echo ' <span class="dashicons dashicons-update" id="wpbgdc_ajax_loader"></span>';
		echo '<p class="description">' . __( 'This syncs your media library. Leave this page if you want to stop syncing.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

	/**
	 * Adds the stop sync button
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_stop_sync_button() {
		echo '<a class="button wpbgdc-button-red" href="' . admin_url( 'options-general.php?page=wpbgdc&action=stop_sync&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_stop_sync' ) ) . '"><span class="dashicons dashicons-dismiss wpbgdc-icon-dismiss"></span> ' . __( 'Stop current sync process', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
	}

	/**
	 * Adds the "Only upload new files"
	 *
	 * @since 1.5
	 * @return void
	 */
	public function field_wpbgdc_upload_new_files_only() {
		$options = get_option( 'wpbgdc' );

		if ( ! isset( $options['upload_new_files_only'] ) ) {
			$options['upload_new_files_only'] = 0;
		}

		echo "<input id='wpbgdc_upload_new_files_only' name='wpbgdc[upload_new_files_only]' type='checkbox' value='1' " . checked( (bool) $options['upload_new_files_only'], true, false ) . " />";
		echo '<p class="description">' . __( 'If this is checked, the plugin will never sync your media library. It will only upload new files that haven\'t been uploaded yet. Great for very large websites.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
	}

}
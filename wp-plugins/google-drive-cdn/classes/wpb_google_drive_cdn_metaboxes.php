<?php

/**
 * @package    WPBuddy Plugin
 * @subpackage Google+ Drive CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Echoes out the Metabox contents
 * @since 1.0
 */
class WPB_Google_Drive_Cdn_Metaboxes {

	/**
	 * @var null|WPB_Google_Drive_Cdn
	 */
	private $_google_drive_cdn = null;


	/**
	 * @param WPB_Google_Drive_Cdn $google_drive_cdn
	 *
	 * @since    1.0
	 * @return \WPB_Google_Drive_Cdn_Metaboxes
	 */
	public function __construct( &$google_drive_cdn ) {
		$this->_google_drive_cdn = $google_drive_cdn;
	}


	/**
	 * creates the content for the enabling_api metabox
	 * shows some information on the plugin
	 *
	 * @access public
	 *
	 * @return void
	 * @since  1.0
	 */
	public function enabling_api() {

		echo '<p>', __( 'First, you need to enable the Drive API for the plugin. You can do this in your app\'s API project in the Google Cloud Console.', $this->_google_drive_cdn->get_textdomain() ), '</p>';
		echo '<p>', sprintf( __( '<strong>Please note</strong> that Google always tends to change things quickly. They have changed the way to register apps three times in the year 2013. So it can be that the little "How to" below is outdated when you have bought the plugin. In this case, please visit the FAQ page of the plugin to get the latest version of the How-To: %s', $this->_google_drive_cdn->get_textdomain() ), '<a target="_blank" href="http://wp-buddy.com/documentation/plugins/google-drive-cdn-wordpress-plugin/faq/">' . __( 'Google Drive as CDN FAQ page', $this->_google_drive_cdn->get_textdomain() ) . '</a>' ), '</p>';

		echo '<ol>';
		echo '<li>', sprintf( __( 'You need a Google account. You do not have a Google account? Click here to create one: %s', $this->_google_drive_cdn->get_textdomain() ), '<a href="https://www.google.com/accounts/NewAccount" target="_blank">https://www.google.com/accounts/NewAccount</a>' ), '</li>';
		echo '<li>', sprintf( __( 'Create an API project in the Google Cloud Console (former Google API Console) by following this link: %s', $this->_google_drive_cdn->get_textdomain() ), '<a href="https://cloud.google.com/console" target="_blank">https://cloud.google.com/console</a>' ), '</li>';
		echo '<li>', __( 'Click the red "Create Project" button and type in a name (ex. "WordPress to Google Drive") and a project ID (like "wp-to-gd" - should be unique)', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'If done click "APIs & auth" -> "APIs" and activate the "Drive API" and "Drive SDK" (set the switch to ON).', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Then click the "Consent screen" link in the menu.', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Type-in a product name (like "WordPress to GoogleDrive").', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Choose an E-Mail Address and save your settings.', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Then click the "APIs & auth" -> "Credentials" link.', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Click the red button which says "Create New Client ID".', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Choose "Web application".', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', sprintf( __( 'Enter %s in the field "Authorized Javascript origins"', $this->_google_drive_cdn->get_textdomain() ), '<strong>' . home_url() . '</strong>' ), '</li>';
		echo '<li>', sprintf( __( 'Enter %s in the "Authorized Redirect URI" field', $this->_google_drive_cdn->get_textdomain() ), '<strong>' . admin_url( 'options-general.php?page=wpbgdc' ) . '</strong>' ) . '</li>';
		echo '<li>', __( 'Then click "Create Client ID".', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'You should now see all the information that are required.', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '<li>', __( 'Copy&Paste the Client ID and the Client Secret in the text fields below.', $this->_google_drive_cdn->get_textdomain() ), '</li>';
		echo '</ol>';

		echo '<table class="form-table">';
		do_settings_fields( 'wpbgdc', 'wpbgdc_section_client_credentials' );
		echo '</table>';

		submit_button( null, 'primary', 'submit', false ); //no wrapping

	}

	/**
	 * creates the content for the howto-metabox
	 *
	 * @access public
	 *
	 * @return void
	 * @since  1.0
	 */
	public function connect() {

		$helper = new WPB_Google_Drive_Cdn_Service_Helper( $this->_google_drive_cdn );

		$is_api_working = $helper->is_api_working();

		$client = $this->_google_drive_cdn->get_google_client();
		$client->setState( 'got_code' );

		// get offline access
		$client->setAccessType( 'offline' );

		// makes the UI window appear ALWAYS
		$client->setApprovalPrompt( 'force' );

		echo '<p>' . __( 'Please note: you will be redirected to a Google\'s authorization page. Please press the blue button to give this application the right to upload files.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';

		echo '<a class="button-primary" href="' . $client->createAuthUrl() . '">' . __( 'Grant writing permissions to your Google Drive', $this->_google_drive_cdn->get_textdomain() ) . '</a>';

		if ( ! $is_api_working ) {
			echo '<div class="error"><p>', __( 'The Google Drive API is currently not working.', $this->_google_drive_cdn->get_textdomain() ), ' ' . __( 'Try to (re-)authenticate by using the button "Grant writing permissions to your Google Drive" button.', $this->_google_drive_cdn->get_textdomain() ), '</p></div>';
		}

	}


	/**
	 * creates the content for the settings metabox
	 *
	 * @access public
	 *
	 * @return void
	 * @since  1.0
	 */
	public function settings() {

		do_settings_sections( 'wpbgdc' );

		submit_button();

		echo '<div class="clear"></div>';

	}

	/**
	 * The "about" metabox
	 * @since 1.0
	 * @return void
	 */
	public function about() {
		?>
		<a href="http://bit.ly/TO0Z5w" target="_blank"><img src="https://wpbuddy.libra.uberspace.de/secure/wp-buddy-logo.png" alt="WPBuddy Logo" /></a><?php
	}


	/**
	 * Outputs the metabox content for the links
	 * @since 1.0
	 * @return void
	 */
	public function links() {
		?>
		<ul>
			<li>
				<a href="http://bit.ly/15uoww1" target="_blank"><?php echo __( 'Installation manual', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
			<li>
				<a href="http://bit.ly/W9GDQT" target="_blank"><?php echo __( 'Frequently Asked Questions', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
			<li>
				<a href="http://bit.ly/WW93Sk" target="_blank"><?php echo __( 'Report a bug', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
			<li>
				<a href="http://bit.ly/11UE2lF" target="_blank"><?php echo __( 'Request a function', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
			<li>
				<a href="http://bit.ly/XkivOW" target="_blank"><?php echo __( 'Submit a translation', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
			<li>
				<a href="http://bit.ly/UlDG4t" target="_blank"><?php echo __( 'More cool stuff by WPBuddy', $this->_google_drive_cdn->get_textdomain() ); ?></a>
			</li>
		</ul>
	<?php
	}


	/**
	 * Outputs the metabox content for the social links
	 * @return void
	 * @since 1.0
	 */
	public function social() {
		?>
		<p>
		<div class="g-plusone" data-size="medium" data-href="http://wp-buddy.com/products/plugins/google-drive-as-wordpress-cdn-plugin/"></div>
		</p>

		<script type="text/javascript">
			(function () {
				var po = document.createElement( 'script' );
				po.type = 'text/javascript';
				po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName( 'script' )[0];
				s.parentNode.insertBefore( po, s );
			})();
		</script>

		<p>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wp-buddy.com/products/plugins/google-drive-as-wordpress-cdn-plugin/" data-text="Check out the Google Drive as CDN WordPress Plugin" data-related="wp_buddy">Tweet</a>
		</p>
		<script>!function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[0];
				if ( !d.getElementById( id ) ) {
					js = d.createElement( s );
					js.id = id;
					js.src = "//platform.twitter.com/widgets.js";
					fjs.parentNode.insertBefore( js, fjs );
				}
			}( document, "script", "twitter-wjs" );</script>

		<p>
			<iframe src="//www.facebook.com/plugins/like.php?href=<?php echo urlencode( 'http://wp-buddy.com/products/plugins/google-drive-as-wordpress-cdn-plugin/' ); ?>&amp;send=false&amp;layout=button_count&amp;width=150&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
		</p>
	<?php
	}


	/**
	 * Outputs the metabox content for the subscription
	 * @since 1.0
	 * @return void
	 */
	public function subscribe() {
		global $current_user;
		get_currentuserinfo();
		$name = $current_user->user_firstname;
		if ( empty( $name ) ) {
			$name = $current_user->display_name;
		}
		?>
		<div class="wpbuddy-cr-form">
			<label style="width: 110px; display: inline-block;" for="text1210658"><?php echo __( 'Your first name', $this->_google_drive_cdn->get_textdomain() ); ?></label>
			<input style="width: 140px;" id="text1210658" name="209681" type="text" value="<?php echo $name; ?>" />
			<label style="width: 110px; display: inline-block;" for="text1210692"><?php echo __( 'Your E-Mail address', $this->_google_drive_cdn->get_textdomain() ); ?></label>
			<input style="width: 140px;" id="text1210692" name="email" value="<?php echo $current_user->user_email; ?>" type="text" />
			<a href="#" class="button button-primary"><?php echo __( 'Subscribe', $this->_google_drive_cdn->get_textdomain() ); ?></a>
		</div>
		<script type="text/javascript">
			//<![CDATA[

			jQuery( document ).ready( function () {
				/** Subscribe form **/
				jQuery( '.wpbuddy-cr-form a.button' ).click( function ( e ) {
					e.preventDefault();

					var name = jQuery( '#text1210658' ).val();
					var mail = jQuery( '#text1210692' ).val();

					jQuery( [
						'<form style="display:none;" action="https://10955.cleverreach.com/f/54067/wcs/" method="post" target="_blank">',
						'<input id="text1210692" name="email" value="' + mail + '" type="text"  />',
						'<input id="text1210658" name="209681" type="text" value="' + name + '"  />',
						'</form>'
					].join( '' ) ).appendTo( 'body' )[0];

				} );
			} );
			/* ]]> */
		</script>
	<?php
	}


	/**
	 * Outputs the metabox content for the information
	 * @return void
	 * @since 1.0
	 */
	public function info() {
		$folder_name = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_name', 'wpbgdc_folders' );
		$folder_link = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_link', 'wpbgdc_folders' );

		echo '<p><strong>', sprintf(
				__( 'You are now ready to go! The plugin has created a folder named %1$s on your Google Drive. Click here to view all files: %2$s', $this->_google_drive_cdn->get_textdomain() ),
				$folder_name,
				'<a href="' . $folder_link . '" target="_blank">' . $folder_link . '</a>'
		), '</strong></p>';

		echo '<p>', __( 'The plugin works like this:', $this->_google_drive_cdn->get_textdomain() ), '</p>';

		echo '<ol>';
		echo '<li>' . __( 'If anyone visits a page of your website, the plugin searches for files that can be uploaded to Google Drive.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'If the file is not yet uploaded to Google Drive the plugin will put it down into the cache-database for a later upload.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'Every hour the plugin reads all these entries from the cache-database to sync files to your Google Drive that have not yet been synced.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'Synced files can then be served directly from your Google Drive.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'You can manually start syncing the files that have been written to the cache-database by clicking the "Sync local CDN"-button.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'From time to time every single file gets located and uploaded to Google Drive. Because of this process the speed of your website should improve continuously.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '</ol>';

		echo '<p>', __( 'Please note:', $this->_google_drive_cdn->get_textdomain() ), '</p>';
		echo '<ol>';
		echo '<li>' . __( 'It is recommended to use a "caching plugin" like WP-Super-Cache to significantly improve page load time.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'You can start sync your local media library to Google Drive using the "Sync my media library now"-button. Depending on the size of your media library this will take some time.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'Google Drive delivers all files via secure SSL. If you are not using SSL on your domain you should remove CSS files from the file extensions field below. This is because it can lead to the "Mixed Content Blocker" problem some browsers have when your CSS file tries to import Non-SSL files via the @import function. The plugin will automatically stop referring to CSS files on Google Drive if it detects that your domain does not support SSL.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '<li>' . __( 'As per default this plugin syncs new or updated files every hour. This can cause problems if you\'re using a WordPress theme which change files on the file system. Try to manually sync files after changing any theme-related things.', $this->_google_drive_cdn->get_textdomain() ) . '</li>';
		echo '</ol>';

	}


	/**
	 * Outputs the metabox content for the settings
	 * @since 1.0
	 * @return void
	 */
	public function settings_section() {
		echo '<table class="form-table">';
		do_settings_fields( 'wpbgdc', 'wpbgdc_section_settings' );
		echo '</table>';

		?>
		<script type="text/javascript">
			//<![CDATA[

			var sync_running = false;

			function wbpgdc_ajax_start() {
				jQuery( '#wpbgdc_ajax_loader' ).css( 'opacity', 1 );
			}

			function wbpgdc_ajax_stop() {
				jQuery( '#wpbgdc_ajax_loader' ).css( 'opacity', 0 );
			}

			function wbpgdc_sync_button_text() {
				if ( jQuery( '#wpbgdc_upload_new_files_only' ).is( ':checked' ) ) {
					jQuery( '#wpbgdc_sync_button span:last-child' ).text( jQuery( '#wpbgdc_sync_button' ).data( 'uploads_only_text' ) );
				} else {
					jQuery( '#wpbgdc_sync_button span:last-child' ).text( jQuery( '#wpbgdc_sync_button' ).data( 'sync_text' ) );
				}
			}

			function do_sync( next ) {
				jQuery.ajax( '<?php echo admin_url( 'admin-ajax.php?action=wpbgdc_sync&start=' ); ?>' + next, {
					'type'      : 'GET',
					'dataType'  : 'json',
					'beforeSend': wbpgdc_ajax_start(),
					/*'complete'  : wbpgdc_ajax_stop(),*/
					'success'   : function ( data ) {
						sync_running = true;
						if ( data.error == 0 ) {
							jQuery( '#wpbgdc_sync_processed' ).text( data.uploaded_total + '%' );
							if ( data.next <= data.number_of_attachments ) {
								do_sync( data.next );
							}
							else {
								sync_running = false;
								jQuery( '#wpbgdc_sync_processed' ).fadeOut( 2000, function () {
									jQuery( this ).text( '0%' );
									wbpgdc_ajax_stop();
								} );
							}
						}
					}
				} );
			}

			jQuery( document ).ready( function () {
				jQuery( '#wpbgdc_sync_media_button' ).click( function ( e ) {
					e.preventDefault();
					jQuery( '#wpbgdc_sync_processed' ).show();
					if ( !sync_running ) do_sync( 0 );
				} );

				wbpgdc_sync_button_text();

				jQuery( '#wpbgdc_upload_new_files_only' ).click( function () {
					wbpgdc_sync_button_text();
				} );

			} );

			/* ]]> */
		</script>
	<?php
	}


	/**
	 * Shows the  metabox for the error logs
	 * @since 1.1
	 */
	public function error_logs() {

		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		$total_file_entries   = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'wpbgdc_files' );
		$total_synced_files   = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'wpbgdc_files WHERE file_drive_url != "" ' );
		$total_unsynced_files = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'wpbgdc_files WHERE file_drive_url = "" ' );

		echo '<h4>' . __( 'Statistics', $this->_google_drive_cdn->get_textdomain() ) . '</h4>';

		echo '<p><code>' . $total_file_entries . '</code> ' . __( 'Number of total file entries in the cache database.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
		echo '<p><code>' . $total_synced_files . '</code> ' . __( 'Number of synced files.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
		echo '<p><code>' . $total_unsynced_files . '</code> ' . __( 'Number of files that have not yet been synced or can\'t be synced.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';
		echo '<p><code>' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), wp_next_scheduled( 'wpbgdc_hourly_event' ) ) . '</code> ' . __( 'Date of the next automatic sync/update process.', $this->_google_drive_cdn->get_textdomain() ) . '</p>';

		$errors = get_option( 'wpbgdc_error_log', array() );
		if ( ! is_array( $errors ) ) {
			return;
		}

		if ( count( $errors ) ) {
			echo '<h4>' . __( 'Errors & Hints', $this->_google_drive_cdn->get_textdomain() ) . '</h4>';
		}

		krsort( $errors );
		foreach ( $errors as $error ) {
			echo $error . '<br />';
		}
		if ( count( $errors ) ) {
			echo ' <a class="button" href="' . admin_url( 'options-general.php?page=wpbgdc&action=delete_error_logs&wpbgdc_nonce=' . wp_create_nonce( 'wpbgdc_delete_error_logs' ) ) . '">' . __( 'Delete Logs', $this->_google_drive_cdn->get_textdomain() ) . '</a>';
		}
	}


}
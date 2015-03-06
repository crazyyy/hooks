<?php

/**
 * @package    WPBuddy Plugin
 * @subpackage Google+ Drive CDN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * CDN Helper
 *
 * @since  Unknown
 * @access public
 *
 */
class WPB_Google_Drive_Cdn_Service_Helper {

	/**
	 * @var null|WPB_Google_Drive_Cdn
	 * @since 1.0
	 */
	private $_google_drive_cdn = null;

	/**
	 * @var bool
	 * @since 1.0
	 */
	public $_file_uploaded = false;

	/**
	 * @param WPB_Google_Drive_Cdn $google_drive_cdn
	 *
	 * @since    1.0
	 * @return \WPB_Google_Drive_Cdn_Service_Helper
	 */
	public function __construct( $google_drive_cdn = null ) {
		$this->_google_drive_cdn = & $google_drive_cdn;
	}


	/**
	 * Searches the Google Drive CDN folder
	 * @since 1.0
	 *
	 * @param null /string $folder
	 */
	public function search_folder( $folder = null ) {
		if ( is_null( $folder ) ) {
			$folder = home_url();
			$folder = str_replace( 'http://', '', $folder );
			$folder = str_replace( 'https://', '', $folder );
			$folder = sanitize_key( $folder );
		}

		$service = new Google_Service_Drive( $this->_google_drive_cdn->get_google_client() );

		try {
			$files = $service->files->listFiles( array( 'q' => "'root' in parents" ) );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: search_folder 0)', false );
		}


		// Searching for the folder
		if ( isset( $files->items ) && is_array( $files->items ) ) {
			foreach ( $files->items as $file ) {
				if ( ! $file instanceof Google_Service_Drive_DriveFile ) {
					continue;
				}

				// Folder found
				if ( $folder == $file->getTitle() ) {
					// set the folder id
					WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_id', $file->getId(), 'wpbgdc_folders' );

					// set the folder name
					WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_name', $folder, 'wpbgdc_folders' );

					// set permissions to the folder if it exists
					$permission = new Google_Service_Drive_Permission();
					$permission->setValue( '' );
					$permission->setType( 'anyone' );
					$permission->setRole( 'reader' );

					// untrash a file if it has been trashed
					if ( $file->getExplicitlyTrashed() ) {
						$service->files->untrash( $file->getId() );
					}

					try {
						$service->permissions->insert( $file->getId(), $permission );
					} catch ( Exception $e ) {
						$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: search_folder 1)', false );
						continue;
					}

					try {
						// get the file infos to grab the webViewLink value
						$file_info = $service->files->get( $file->getId() );
					} catch ( Exception $e ) {
						$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: search_folder 2)', false );
						continue;
					}

					// set the webViewLink value
					WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_link', $file_info->webViewLink, 'wpbgdc_folders' );
					return $file->getId();
				}
			}
		};

		return $this->create_folder( $folder );
	}


	/**
	 * Creates the Google Drive CDN folder on the google drive
	 * @since 1.0
	 *
	 * @param null $folder
	 *
	 */
	public function create_folder( $folder = null ) {
		if ( is_null( $folder ) ) {
			$folder = home_url();
			$folder = str_replace( 'http://', '', $folder );
			$folder = str_replace( 'https://', '', $folder );
			$folder = sanitize_key( $folder );
		}

		$service = new Google_Service_Drive( $this->_google_drive_cdn->get_google_client() );

		$file = new Google_Service_Drive_DriveFile();
		$file->setTitle( $folder );
		$file->setDescription( home_url() . ' to Google Drive CDN Folder' );
		$file->setMimeType( 'application/vnd.google-apps.folder' );

		try {
			$createdFile = $service->files->insert( $file, array(
				'mimeType' => 'application/vnd.google-apps.folder',
			) );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: create_folder 1)', false );
			return;
		}

		WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_id', $createdFile->getId(), 'wpbgdc_folders' );

		// set permissions
		$permission = new Google_Service_Drive_Permission();
		$permission->setValue( '' );
		$permission->setType( 'anyone' );
		$permission->setRole( 'reader' );


		try {
			$service->permissions->insert( $createdFile->getId(), $permission );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: create_folder 2)', false );
			return;
		}

		try {
			$created_file_info = $service->files->get( $createdFile->getId() );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: create_folder 3)', false );
			return $createdFile->getId();
		}

		WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_link', $created_file_info->webViewLink, 'wpbgdc_folders' );

		WPB_Google_Drive_Cdn_Settings::set_setting( 'folder_name', $folder, 'wpbgdc_folders' );

		return $createdFile->getId();

	}


	/**
	 * Checks if a file exists
	 *
	 * @param string $file      path to file and filename
	 * @param string $url       the live URL to the file
	 * @param bool   $is_update perform an update instead of creating a new file
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function file_upload( $file, $url, $is_update = false ) {

		$this->_file_uploaded = false;

		// get the google drive service
		if ( ! isset( $this->_google_drive_service ) ) {
			$this->_google_drive_service = new Google_Service_Drive( $this->_google_drive_cdn->get_google_client() );
		}

		// get the google drive service instance
		$service = $this->_google_drive_service;
		if ( ! $service instanceof Google_Service_Drive ) {
			return $url;
		}

		// get the folder id
		$folder_id = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_id', 'wpbgdc_folders' );

		// create the folder name
		$file_folder_to_str = str_replace( WP_CONTENT_DIR, '', str_replace( basename( $file ), '', $file ) );
		$file_folder_to_str = str_replace( '/', '_', $file_folder_to_str );
		$file_folder_to_str = sanitize_key( $file_folder_to_str );

		// create the file basename
		$file_basename = $file_folder_to_str . basename( $file );

		// get the google drive URL
		$google_drive_url = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_link', 'wpbgdc_folders' );

		try {
			// search for the file.
			$files = $service->files->listFiles( array( 'q' => "title='" . $file_basename . "' and '" . $folder_id . "' in parents" ) );
		} catch ( Exception $e ) {
			// if there is an error fetching the file, return the normal url
			return $url;
		}

		// checks if the file is a CSS file
		$is_css_file = substr( $file, - 4, 4 ) == '.css';

		// search in the list of files for the current file
		foreach ( $files->items as $g_file ) {
			if ( ! $g_file instanceof Google_Service_Drive_DriveFile ) {
				continue;
			}

			// file exists
			if ( $g_file->getTitle() == $file_basename ) {

				// get the date of the current file
				$file_date = filemtime( $file );

				/**
				 * check if we should update the file
				 * this is the case when either
				 * the date of the file on Google Drive is lower than the local file OR
				 * when the etag is not correct (this is only used for css files at the moment)
				 */

				if ( ( strtotime( $g_file->getModifiedDate() ) < $file_date ) OR ( $is_css_file && $g_file->getEtag() != $this->get_etag_by_live_url( $url ) ) ) {

					$additionalParams = array();

					if ( $is_css_file ) {
						$additionalParams['data']     = $this->invoke_css_file( $file, $url );
						$additionalParams['mimeType'] = 'text/css';
					}
					else {
						$additionalParams['data'] = file_get_contents( $file );
					}

					try {
						$g_file = $service->files->update( $g_file->getId(), $g_file, $additionalParams );
					} catch ( Exception $e ) {
						$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: file_upload 1; File: ' . $url . ')', false );
					}
					$this->_file_uploaded = true;
				}

				// update the file cache entry as well
				$this->set_cached_url( $url, $google_drive_url . $g_file->getTitle(), $g_file->getId(), $g_file->getEtag() );

				// create the URL out of the Drive URL and return it.
				return $google_drive_url . $g_file->getTitle();
			}
		}

		if ( $is_css_file ) {
			$mime_type = 'text/css';
		}
		else {
			$mime_type = wp_check_filetype( $file );
			$mime_type = $mime_type['type'];
		}

		$parent = new Google_Service_Drive_ParentReference();
		$parent->setId( $folder_id );

		// upload the file
		$new_file = new Google_Service_Drive_DriveFile();
		$new_file->setTitle( $file_basename );
		$new_file->setMimeType( $mime_type );
		$new_file->setParents( array( $parent ) );
		$new_file->setFileSize( filesize( $file ) );

		$additionalParams = array(
			'mimeType'   => $mime_type,
			'uploadType' => 'media',
		);

		if ( $is_css_file ) {
			$additionalParams['data'] = $this->invoke_css_file( $file, $url );
		}
		else {
			$additionalParams['data'] = file_get_contents( $file );
		}

		try {
			$createdFile = $service->files->insert( $new_file, $additionalParams );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: file_upload 2; File: ' . $url . ')', false );
			return $url;
		}

		$file_drive_url = $google_drive_url . $createdFile->getTitle();

		$this->set_cached_url( $url, $file_drive_url, $createdFile->getId(), $createdFile->getEtag() );

		$this->_file_uploaded = true;

		return $file_drive_url;
	}


	/**
	 * checks if the api is working properly
	 * @since 1.0
	 */
	public function is_api_working() {

		// if the settings are not yet set, the api cant work even when it's working (sounds courios?)
		$client_id = WPB_Google_Drive_Cdn_Settings::get_setting( 'client_id' );
		if ( empty( $client_id ) ) {
			return false;
		}

		$client_secret = WPB_Google_Drive_Cdn_Settings::get_setting( 'client_secret' );
		if ( empty( $client_secret ) ) {
			return false;
		}

		// return false if the google drive url is empty
		$google_drive_url = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_link', 'wpbgdc_folders' );
		if ( empty( $google_drive_url ) ) {
			return false;
		}

		// return false if there is no folder id
		$folder_id = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_id', 'wpbgdc_folders' );
		if ( empty( $folder_id ) ) {
			return false;
		}

		try {
			// if any of these returns an error, the api will not work
			$service = new Google_Service_Drive( $this->_google_drive_cdn->get_google_client() );
			$service->files->get( $folder_id );
		} catch ( Exception $e ) {
			//$this->_google_drive_cdn->set_error( $e->getMessage(). '(wpbgdc: is_api_working 1)', false );
			return false;
		}

		return true;

	}


	/**
	 * Get's the URL from a database if there is any
	 * @since 1.0
	 *
	 * @param string $url
	 *
	 * @return bool|null|string
	 */
	public static function get_cached_url( $url ) {
		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return false;
		}

		return $wpdb->get_var( "SELECT file_drive_url FROM `" . $wpdb->prefix . "wpbgdc_files` WHERE `file_live_url` = '" . $wpdb->_escape( $url ) . "' AND `file_drive_url` != '' LIMIT 1" );
	}


	/**
	 * Saves the URL to the database
	 *
	 * @param string $url       The URL on the current server
	 * @param string $drive_url The URL on the Google Drive
	 * @param string $file_id   The file ID from the Google Drive
	 * @param string $etag
	 *
	 * @global wpdb  $wpdb
	 *
	 * @since 1.0
	 *
	 * @return bool|false|int
	 */
	public function set_cached_url( $url, $drive_url, $file_id, $etag ) {

		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return false;
		}

		// check if the url already exists in the database
		$is_cached = (bool) $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->prefix . "wpbgdc_files` WHERE `file_live_url` = '" . $wpdb->_escape( $url ) . "'" );

		// if it exists, update it
		if ( (bool) $is_cached ) {
			// initialise an update (even when nothing gets updated) to force MySQL to update the timestamp

			return $wpdb->update( $wpdb->prefix . "wpbgdc_files",
				array(
					'file_drive_url' => $drive_url,
					'file_drive_id'  => $file_id,
					'file_etag'      => $etag
				),
				array(
					'file_live_url' => $url
				)
			);
		}

		return $wpdb->insert( $wpdb->prefix . "wpbgdc_files", array(
				'file_drive_url' => $drive_url,
				'file_live_url'  => $url,
				'file_drive_id'  => $file_id,
				'file_etag'      => $etag
			) );
	}


	/**
	 * @return bool|false|int
	 * @since 1.0
	 */
	public static function remove_old_entries() {
		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return false;
		}

		$cache_time = WPB_Google_Drive_Cdn_Settings::get_setting( 'cache_time' ); // hours

		// if cache time has been set to '0' then we never cache anything. the cache can always be deleted
		if ( '0' == $cache_time ) {
			$cache_time = 0;

		} // never delete cache
		elseif ( empty( $cache_time ) ) {
			return;
		}

		$cache_time = intval( $cache_time ) * 60 * 60; // seconds
		$time_back  = time() - $cache_time;

		return $wpdb->query( 'DELETE FROM `' . $wpdb->prefix . 'wpbgdc_files` WHERE `file_timestamp` < FROM_UNIXTIME(' . $time_back . ')' );

	}


	/**
	 * @return bool
	 * @since 1.0
	 */
	public function delete_folder() {
		$service = new Google_Service_Drive( $this->_google_drive_cdn->get_google_client() );

		// get the folder id
		$folder_id = WPB_Google_Drive_Cdn_Settings::get_setting( 'folder_id', 'wpbgdc_folders' );

		try {
			$service->files->trash( $folder_id );
			// deleting the files directly will result in a bug @see http://stackoverflow.com/questions/16573970/google-drive-api-delete-method-bug
			// $service->files->delete( $folder_id );
		} catch ( Exception $e ) {
			$this->_google_drive_cdn->set_error( $e->getMessage() . '(wpbgdc: delete_folder 1)', false );
			return false;
		}

		return true;
	}


	/**
	 * Records the $url recognized in the frontend for a later upload
	 * @since 1.0
	 *
	 * @param string $url
	 *
	 * @return bool|\false|int
	 */
	public static function record_url( $url ) {
		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return false;
		}

		// check if the file is already in
		$in_db = (bool) $wpdb->get_var( "SELECT COUNT(*) FROM `" . $wpdb->prefix . "wpbgdc_files` WHERE `file_live_url` = '" . $wpdb->_escape( $url ) . "'" );

		if ( $in_db ) {
			return;
		}

		return $wpdb->insert( $wpdb->prefix . 'wpbgdc_files', array(
				'file_live_url' => $url
			) );
	}

	/**
	 * Returns the etag of the database field of a given url
	 * @sine 1.2
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function get_etag_by_live_url( $url ) {
		global $wpdb;
		if ( ! $wpdb instanceof wpdb ) {
			return '';
		}
		return $wpdb->get_var( "SELECT file_etag FROM `" . $wpdb->prefix . "wpbgdc_files` WHERE `file_live_url` = '" . $wpdb->_escape( $url ) . "'" );
	}


	/**
	 * Runs through the css file and searches for image files
	 * @since 1.2
	 *
	 * @param string $file The file path to the CSS file
	 * @param string $url  The full live (original) file url of the CSS file
	 *
	 * @return string
	 */
	public function invoke_css_file( $file, $url ) {

		$file_content = file_get_contents( $file );

		// if this is a CSS file that is on Google Drive already, do not invoke it again
		if ( false !== stripos( $url, 'https://googledrive.com' ) ) {
			return $file_content;
		}

		$this->_google_drive_cdn->_is_buffer_css_file   = true;
		$this->_google_drive_cdn->_buffer_css_file_path = $file;
		$this->_google_drive_cdn->_buffer_css_file_url  = $url;
		$file_content                                   = $this->_google_drive_cdn->buffer( $file_content );
		$this->_google_drive_cdn->_is_buffer_css_file   = false;

		return $file_content;
	}
}




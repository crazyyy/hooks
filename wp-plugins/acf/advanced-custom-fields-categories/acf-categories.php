<?php
	/*
	Plugin Name: Advanced Custom Fields: Categories
	Plugin URI: https://github.com/cubeweb/acf-addons
	Description: Categories is custom field that generates a multi drop down with all the categories or taxonomies from your wordpress site
	Version: 2.0.0.7
	Author: Cubeweb
	Author URI: http://www.cubeweb.gr
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/


	class acf_field_categories_plugin {
		/*
		*  Construct
		*
		*  @description:
		*  @since: 3.6
		*  @created: 1/04/13
		*/

		function __construct() {
			// set text domain
			/*
			$domain = 'acf-{{field_name}}';
			$mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
			load_textdomain( $domain, $mofile );
			*/


			// version 4+
			add_action( 'acf/register_fields', array( $this,
													  'register_fields' ) );


			// version 3-
			add_action( 'init', array( $this,
									   'init' ) );
		}


		/*
		*  Init
		*
		*  @description:
		*  @since: 3.6
		*  @created: 1/04/13
		*/

		function init() {

		}

		/*
		*  register_fields
		*
		*  @description:
		*  @since: 3.6
		*  @created: 1/04/13
		*/

		function register_fields() {
			include_once( 'categories-v4.php' );
		}

	}

	new acf_field_categories_plugin();

?>
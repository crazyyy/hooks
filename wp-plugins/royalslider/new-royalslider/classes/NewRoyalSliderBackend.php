<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider Backend
 */

class NewRoyalSliderBackend {
	private $rs_admin_pages = array('new_royalslider', 'new_royalslider_add_new', 'new_royalslider_settings');
	function __construct( ) {
		
		if(is_admin()) {
			require_once('third-party/class.settings-api.php');

			// ajax requests
            NewRoyalSliderPostsSource::init_ajax();
			add_action( 'wp_ajax_newRoyalSliderSave', array(&$this, 'ajax_save_slider') );
			add_action( 'wp_ajax_getSliderMarkup', array(&$this, 'ajax_get_slider_markup') );
			add_action( 'wp_ajax_refreshTemplate', array(&$this, 'ajax_refresh_template') );
			add_action( 'wp_ajax_addAnimBlockClass', array(&$this, 'ajax_add_anim_block_class') );
			add_action( 'wp_ajax_toggleActiveClass', array(&$this, 'ajax_toggle_active') );

			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			add_action( 'admin_init', array( &$this, 'build_config_page_options' ) );

			if( $this->is_royalslider_page() ) {
				

				add_filter( 'media_upload_tabs', array(&$this, 'rs_custom_tab_register' ));
				add_action( 'media_upload_new_royalslider', array(&$this, 'rs_custom_tab'));

				global $wp_scripts;
				
				$this->register_styles();
				$this->register_scripts();

				
				add_action( 'admin_enqueue_scripts', array( &$this, 'admin_print_styles' ) );
				add_action( 'admin_enqueue_scripts', array(&$this, 'admin_print_scripts' ) );
			}

			require_once('NewRoyalSliderGallery.php');
           	$this->slider_options =  new NewRoyalSliderGallery();
		}
	}







	/**
	 * Ajax callbacks
	 */
	function ajax_toggle_active() {
		check_ajax_referer('new_royalslider_toggle_active_ajax_nonce');

		$id = $_POST['id'];
		$active = isset($_POST['isActive']) && ($_POST['isActive'] == 1);

		global $wpdb;
		$table_name = NewRoyalSliderMain::get_sliders_table_name();
		$wpdb->update( 
			$table_name, 
			array(
				'active' => $active
			), 
			array( 'ID' => $id ), 
			array(
				'%d'
			),
			array( '%d' ) 
		);
		die();
	}
	function ajax_add_anim_block_class() {
		check_ajax_referer('new_royalslider_add_anim_block_class_nonce');
		$arr = get_option("new_royalslider_anim_block_classes");
		//$str = $_POST['classToAdd'];
		//classToRemove
		if( isset($_POST['classToAdd']) ) {
			$arr[] = $_POST['classToAdd'];
			update_option("new_royalslider_anim_block_classes", $arr);
		} else if( isset($_POST['classToRemove']) ) {

			foreach($arr as $key => $value) {

				if($value == $_POST['classToRemove'] ) {
					unset($arr[$key]);
				}

			}
			update_option("new_royalslider_anim_block_classes", $arr);
		}
		die();
	}
	function ajax_refresh_template() {
		check_ajax_referer('new_royalslider_refresh_template_nonce');

		if(isset($_POST['templateId'])) {
			require_once('NewRoyalSliderOptions.php');

			$templates = NewRoyalSliderOptions::getRsTemplates();
			$template_obj = $templates[ $_POST['templateId'] ];

			ob_start();

       		NewRoyalSliderOptions::output_options( $template_obj, isset($_POST['type']) ? $_POST['type'] : '' );

       		$template_css = isset($template_obj['template-css']) ? ( NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/' . $template_obj['template-css'] ) : '';
       		$template_css_class = isset($template_obj['template-css-class']) ? $template_obj['template-css-class'] : '';
       		?>
       		<input type="hidden" id="dynamic-options-data" data-css-class="<?php echo $template_css_class; ?> " data-css-path="<?php echo $template_css; ?>"/>
       		<?php
       		$options = ob_get_contents();
       		ob_end_clean();

       		$out = array(
       			'options' => $options,
       			'template' => isset($template_obj['template-html']) ? $template_obj['template-html'] : $templates['default']['template-html']
       		);

       		echo json_encode($out);

		}
		

		die();
	}
	function ajax_get_slider_markup() {
		check_ajax_referer('new_royalslider_preview_ajax_nonce');
	
	    $markup = stripslashes_deep($_POST['markup']);
	    $options = stripslashes_deep($_POST['options']);
	    if(isset($_POST['slides'])) {
	    	$slides = stripslashes_deep($_POST['slides']);
	    } else {
	    	$slides = null;
	    }

	    require_once('rsgenerator/NewRoyalSliderGenerator.php');
	    echo  NewRoyalSliderGenerator::generateSlides(true, '', $_POST['slider_type'], $markup, $slides, $options, stripslashes_deep($_POST['template']), $_POST['skin']);

	    die();
	}
	function ajax_save_slider() {
		check_ajax_referer('new_royalslider_save_ajax_nonce');

		global $wpdb;
		$data = stripslashes_deep($_POST);
		$options = isset($_POST['options']) ? json_encode( $data['options'] ) : '';
		$slides = isset($_POST['slides']) ? json_encode( $data['slides'] ) : '';
		$table_name =  NewRoyalSliderMain::get_sliders_table_name();
		//$type = strtolower($type);
		$values = array(
			'name' => $data['name'],
			'type' => $data['slider_type'],
			'skin' => $data['skin'],
			'slides' => $slides,
			'options' => $options,
			'template' => $data['template'],
			'template_html' => $data['template_html'] 
		);
		$format = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s'
		);
		
		if( isset($_POST['isCreate']) && $_POST['isCreate'] == 'true' ) {
			$wpdb->insert( 
				$table_name,
				$values, 
				$format
			);

			echo $wpdb->insert_id; 
		} else {
			if( isset($_POST['slider_id']) ) {
				$wpdb->update( 
					$table_name, 
					$values, 
					array( 'ID' => $_POST['slider_id'] ), 
					$format,
					array( '%d' ) 
				);
				NewRoyalSliderMain::delete_cache_for($_POST['slider_id'], $data['slider_type']);
				echo 'saved';
			} else {
				echo 'incorrect id';
			}
		}
		
		die();
	}


	function is_royalslider_page() {
		if (isset($_GET['page'])) {
			$page = strtolower( $_GET['page'] );
			if( in_array($page, $this->rs_admin_pages) ) {
				return true;
			}
		}
	    return false;
	}

	// Backend Styles
	function register_styles() {
		wp_register_style( "rs-codemirror", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/lib/codemirror.css" );
		wp_register_style( "rs-codemirror-theme-elegant", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/theme/elegant.css" );
		wp_register_style( "rs-codemirror-theme", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/theme/lesser-dark.css" );
    	wp_register_style( "new-royalslider-admin", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/royalslider-admin.css", array( ), NEW_ROYALSLIDER_WP_VERSION, 'screen' );
    	wp_register_style( 'jquery-rs-qtip-css', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/custom/jquery.qtip.css');
    	wp_register_style( "new-rs-jquery-ui", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/jquery-ui.css", array( ), '1.0', 'screen' );

    	wp_register_style( 'new-royalslider-core-css', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/royalslider.css', false, NEW_ROYALSLIDER_WP_VERSION, 'all' );

    	
    	$skins = NewRoyalSliderOptions::getRsSkins();
    	foreach($skins as $key => $value) {
    		if(isset($value['path']) ) {
    			wp_register_style( 'new-rs-skin-'.$key, $value['path'], array( 'new-royalslider-core-css' ), NEW_ROYALSLIDER_WP_VERSION, 'all' );
    		}
    		
    	}
		
		$templates = NewRoyalSliderOptions::getRsTemplates();
    	foreach($templates as $key => $value) {
    		if( isset($value['template-css']) )
    			wp_register_style( 'new-rs-template-'.$key, $value['template-css'], array( 'new-royalslider-core-css' ), NEW_ROYALSLIDER_WP_VERSION, 'all' );
    	}	
    }

	function admin_print_styles( ) {
		wp_enqueue_style('wp-jquery-ui-dialog');
		wp_enqueue_style('rs-codemirror');
		wp_enqueue_style('rs-codemirror-theme');
		wp_enqueue_style('rs-codemirror-theme-elegant');
		wp_enqueue_style('jquery-rs-qtip-css');
		wp_enqueue_style('new-rs-jquery-ui');
        wp_enqueue_style('thickbox');

        wp_enqueue_style( "new-royalslider-admin" );

        $skins = NewRoyalSliderOptions::getRsSkins();
    	foreach($skins as $key => $value) {
    		wp_enqueue_style( 'new-rs-skin-'.$key);
    	}

    	$templates = NewRoyalSliderOptions::getRsTemplates();
    	foreach($templates as $key => $value) {
    		if( isset($value['template-css']) )
    			wp_enqueue_style( 'new-rs-template-'.$key);
    	}

        wp_enqueue_style( 'new-royalslider-default-skin-css' );
    }
    

    // Backend Scripts
    function register_scripts() {
    	wp_register_script( 'rs-textchange', NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/jquery.textchange.js", array('jquery'), NEW_ROYALSLIDER_WP_VERSION, true);


    	wp_register_script( 'rs-codemirror', NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/lib/codemirror.js" );
    	wp_register_script( 'rs-codemirror-overlay', NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/lib/util/overlay.js" );


    	wp_register_script( 'rs-codemirror-xml', NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/codemirror/mode/xml/xml.js" );

    	wp_register_script( 'jquery-rs-qtip', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/custom/jquery.qtip.min.js', array('jquery'), NEW_ROYALSLIDER_WP_VERSION, true);
    	wp_register_script( 'jquery-html5-sortable', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/jquery.sortable.js', array('jquery'), NEW_ROYALSLIDER_WP_VERSION, true);
    	wp_register_script( 'new-rs-toJSON', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/toJSON.js', array('jquery'), NEW_ROYALSLIDER_WP_VERSION, 'all');

		wp_register_script( 'new-royalslider-main-js', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/jquery.royalslider.min.js', array('jquery'), NEW_ROYALSLIDER_WP_VERSION, true);




    	wp_register_script( 'new-royalslider-backend', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/royalslider-admin.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-dialog'), NEW_ROYALSLIDER_WP_VERSION, true);


    	wp_localize_script( 'new-royalslider-backend', 'newRsVars', array(
							'ajaxurl' => admin_url( 'admin-ajax.php' ),
							'toggleActiveNonce' => wp_create_nonce( 'new_royalslider_toggle_active_ajax_nonce' ),

			

							'saveNonce' => wp_create_nonce( 'new_royalslider_save_ajax_nonce' ),
							'previewNonce' => wp_create_nonce( 'new_royalslider_preview_ajax_nonce' ),
							'refreshTemplateNonce' => wp_create_nonce( 'new_royalslider_refresh_template_nonce' ),
							'createAdminSlideNonce' => wp_create_nonce( 'new_royalslider_new_admin_slide_nonce' ),
							'customSourceActionNonce' => wp_create_nonce( 'new_royalslider_custom_source_action_nonce' ),
							'addAnimBlockClassNonce' => wp_create_nonce( 'new_royalslider_add_anim_block_class_nonce' ),

							'img_folder' => NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/img/',

							'slide_html_markup_editor' => sprintf(__('Slide HTML markup editor | %sdocumentation and supported variables%s', 'new_royalslider'), '<a tabindex="-1" style="font-weight: normal;" href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-using-slide-markup-editor" target="_blank">', '</a>'),	

							'add_new_slide' => __('Create New Slide', 'new_royalslider'),		
							'drop_to_duplicate' => __('Drop here to duplicate', 'new_royalslider'),

							'supports_video' => __('Enter link to YouTube or Vimeo video page.', 'new_royalslider'),
							'fetching_video_data' => __('Fetching video data...', 'new_royalslider'),
							'incorrect_x_video_url' => __('Incorrect URL to %s video page.', 'new_royalslider'),
							'incorrect_video_url' => __('Only YouTube & Vimeo videos are supported by default.', 'new_royalslider'),
							'incorrect_id_url' => __('Incorrect video URL or problem with request.','new_royalslider'),
							'drop_to_duplicate' => __('Drop here to duplicate', 'new_royalslider'),

							'found_video' => __('Found video:', 'new_royalslider'),
							'fetch_title_description' => __('Fetch title and description', 'new_royalslider'),

							'add_anim_block_class_prompt' => __("Type class name to add and click \"OK\". It'll be saved in this list globally.", 'new_royalslider'),
							'remove_anim_block_class_prompt' => __("Type class name to remove and click \"OK\". It'll be removed from this list globally.", 'new_royalslider'),

							'change_image' => __('Change image', 'new_royalslider'),
							'add_image' => __('Add image', 'new_royalslider'),
							'saving' => __('Saving slider...', 'new_royalslider'),
							'creating_slide' => __('Creating slide...', 'new_royalslider'),
							'create_new_slide' => __('Create New Slide', 'new_royalslider'),
							'save_slider' => __('Save slider', 'new_royalslider'),
							'edit_royalslider' => __('Editing %s RoyalSlider #', 'new_royalslider'),
							'loading' => __('Loading...', 'new_royalslider'),
							'tab_image_video' => __('Image &amp; Video', 'new_royalslider'),
							'tab_block_editor' => __('Block editor', 'new_royalslider'),
							'tab_html_content' => __('HTML content', 'new_royalslider'),

							'loading_preview' => __('Loading preview...', 'new_royalslider'),
							'preview_slider' => __('Preview slider', 'new_royalslider'),
							'preview_title' => __('Preview <em>may look a bit different when embedded to site (<a href="http://help.dimsemenov.com/kb/wordpress-royalslider-faq/wp-why-slider-looks-a-bit-different-when-embedded-to-site" target="_blank">why</a>) </em>', 'new_royalslider'),
							'unexpected_output' => __('Unexpected output generated.', 'new_royalslider'),

							'loading_data' => __('Loading template...', 'new_royalslider'),
							'templates_text' => __('Templates', 'new_royalslider'),
							'change_template_warning' => __("Changing template will reset all options in this sidebar, but slides are kept. Continue?", 'new_royalslider'),
							'delete_warning' => __("You are about to permanently delete slider %s.\n 'Cancel' to stop, 'OK' to delete.", 'new_royalslider')

		));	


		
		wp_register_script( 'new-royalslider-blockeditor', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/royalslider-block-editor.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-dialog'), NEW_ROYALSLIDER_WP_VERSION, true);
		
		wp_localize_script( 'new-royalslider-blockeditor', 'newRsBeVars', array(
							

							'no_text' => __('No text content', 'new_royalslider'),
							'desktop_width' => __('Width', 'new_royalslider'),
							'desktop_height' => __('Height', 'new_royalslider'),
							'desktop_top' => __('Top', 'new_royalslider'),
							'desktop_bottom' => __('Bottom', 'new_royalslider'),
							'desktop_left' => __('Left', 'new_royalslider'),
							'desktop_right' => __('Right', 'new_royalslider'),
							'add_block' => __('Add HTML block', 'new_royalslider'),
							'add_image_block' => __('Add image', 'new_royalslider'),
							'new_block_text' => __('<h3>Block HTML text</h3>', 'new_royalslider'),
							
		));	
		wp_register_script( 'new-royalslider-video-tab', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/royalslider-video-tab.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-dialog'), NEW_ROYALSLIDER_WP_VERSION, true);
		wp_register_script( 'new-royalslider-preview-slider', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/royalslider-preview.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-ui-dialog'), NEW_ROYALSLIDER_WP_VERSION, true);

    }
	function admin_print_scripts( ) {
		wp_enqueue_script('thickbox');
		wp_enqueue_script('media-upload');

		wp_enqueue_script( 'rs-textchange' );
		wp_enqueue_script( 'new-rs-toJSON' );

		wp_enqueue_script( 'rs-codemirror' );
		wp_enqueue_script( 'rs-codemirror-xml' );
		wp_enqueue_script( 'rs-codemirror-overlay' );
		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-resizable');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-html5-sortable');

		wp_enqueue_script( 'jquery-rs-qtip' );

		wp_enqueue_script('new-royalslider-main-js');

		wp_enqueue_script( 'new-royalslider-preview-slider' );
		wp_enqueue_script( 'new-royalslider-blockeditor' );
		wp_enqueue_script( 'new-royalslider-video-tab' );
		wp_enqueue_script( 'new-royalslider-backend' );
		
	}



	// Media gallery tab
	function rs_custom_tab_register($tabs) {
		$newtab = array('new_royalslider' => __('RoyalSlider', 'new_royalslider'));
		return array_merge($tabs, $newtab);
	}
	function rs_custom_tab() {
		wp_iframe( array( &$this, 'media_tab_process' ) );
	}
	function media_tab_process () {
		media_upload_header();
		?>
		<form id="new_royalslider_add_shortcode" action="media-new.php" method="post">
			<?php submit_button( __( 'Add RoyalSlider', 'new_royalslider' ) ); ?>
		</form>
		<?php	
	}


	// Menu
	function admin_menu() {

		if(function_exists('get_royalslider')) {
			$title = __('New RoyalSlider', 'new_royalslider');
		} else {
			$title = __('RoyalSlider', 'new_royalslider');
		}
		add_menu_page(  $title, $title, 'publish_posts', 'new_royalslider', array( &$this, 'plugin_admin_home_page' ),  NEW_ROYALSLIDER_PLUGIN_URL.'lib/backend/img/new-royalslider-icon.png' );

		add_submenu_page( 'new_royalslider', __('Edit Sliders', 'new_royalslider'), __('Edit Sliders', 'new_royalslider'), 'publish_posts', 'new_royalslider', array( &$this, 'plugin_admin_home_page' ) );
		
		add_submenu_page( 'new_royalslider', __('RoyalSlider Global Settings', 'new_royalslider'), __('Settings', 'new_royalslider'), 'manage_options', 'new_royalslider_settings', array( &$this, 'plugin_config_page' ) );
	}


	function plugin_admin_home_page() {
		
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : "";
		if($action === 'edit') {
			require_once('NewRoyalSliderEdit.php');
			$addnew_page = new NewRoyalSliderEdit(false);
		} else if($action === 'add') {
			require_once('NewRoyalSliderEdit.php');
			$addnew_page = new NewRoyalSliderEdit(true);
		} else {
			require_once('NewRoyalSliderManage.php');
			$addnew_page = new NewRoyalSliderManage();
		}
	}
	
	function plugin_config_page() {
		$settings_api = RS_WeDevs_Settings_API::getInstance();
 
	    echo '<div class="wrap">';
	    echo '<h2>' . __('RoyalSlider Global Settings', 'new_royalslider') . '</h2>';
	    echo '<p>';
	 	echo __('To get premium support and access to plugin documentation, tips and tutorials please visit <a href="http://dimsemenov.com/private/forum.php" target="_blank">RoyalSlider Knowledge Base</a>.<br/>Unminified version of JavaScript file can be found on <a href="http://dimsemenov.com/private" target="_blank">slider build tool</a>.');
	 	echo '</p>';
	    settings_errors();
	 
	    $settings_api->show_navigation();
	    $settings_api->show_forms();
	    
	    echo '</div>';
	}

	function build_config_page_options() {
		$sections = array(
	        array(
	            'id' => 'new_royalslider_config',
	            'title' => __( '', 'new_royalslider' )
	        )
	    );

	    $fields = array(
	        'new_royalslider_config' => array(

	        	array(
	                'name' => 'embed',
	                'label' => __( 'Preload CSS and JavaSript files:', 'new_royalslider' ),
	                'desc' => esc_attr( __( "To include JavaScript and CSS files manually, call <?php register_new_royalslider_files(123); ?> function in your theme functions.php. It'll load all files associated with slider (instead of 123 put actual ID of your slider).", 'new_royalslider' ) ) . ' <a href="http://dimsemenov.com/private/forum.php?to=kb/wordpress-royalslider-tutorials/wp-including-javascript-and-css-files" target="_blank">' . __('Learn more on support desk', 'new_royalslider' ) . '</a>',
	                'type' => 'multicheck',
	                'options' => array(
	                	'posts_with_slider' => __('On posts with slider shortcode in content', 'new_royalslider'),
	                	'home_page' => __('On home (front) page', 'new_royalslider'),
	                    'every_page' => __('On every page (overrides all other options)', 'new_royalslider')
	                ),
	                'default' => array(
	                	'posts_with_slider' => 'posts_with_slider'
	                )
	            ),

				
				array(
	                'name' => 'allow_authors_cap',
	                'label' => __( 'Allow editing and creating sliders for Editors and Authors', 'new_royalslider' ),
	                'desc' => __( '*this settings page is always available only to Admins', 'new_royalslider' ),
	                'type' => 'radio',
	                'options' => array(
	                    'yes' => 'Yes',
	                    'no' => 'No'
	                ),
	                'default' => 'no'
	            ),

	            array(
	                'name' => 'cache_refresh_time',
	                'label' => __( 'Slider cache refresh time', 'new_royalslider' ),
	                'desc' => __( 'In hours. Cache is also cleared when you click on Save slider button in editor', 'new_royalslider' ),
	                'type' => 'text',
	                'default' => '24',
	                'size' => 'small'
	            ),

	            array(
	                'name' => 'override_all_default_galleries',
	                'label' => __( 'Override all default WordPress galleries in posts (without adding <code>royalslider</code> attribute to <code>[gallery]</code> shortcode)<br/>You can still add this attribute to your galleries if you want to override default.', 'new_royalslider' ),
	                'desc' => __( 'Enter ID of Default Gallery slider that should be used, or leave field empty to disable this feature' ),
	                'type' => 'text',
	                'default' => '',
	                'size' => 'small'
	            )

	        )
	    );
	 
	    $settings_api = RS_WeDevs_Settings_API::getInstance();
	 
	    $settings_api->set_sections( $sections );

	    $settings_api->set_fields( $fields );
	 
	    $settings_api->admin_init();
	}

}
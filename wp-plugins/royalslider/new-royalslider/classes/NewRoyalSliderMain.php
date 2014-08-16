<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderMain {
	public static $refresh_hours;
	public static $override_all_default_galleries;
	public static $sliders_init_code = array();

	private $scripts = array();
	private $styles = array();
	private $sliders_to_enqueue = array();
	private $has_slider_on_page;

	private $global_options;
	private $check_post_shortcode = false;
	private $load_on_home = false;
	private $load_on_every = false;
	private $load_all = false;
	private static $update_checker;

	public $plugin_base_name;
	
	function __construct( $file ) {

		$this->plugin_base_name = basename(dirname($file)).'/'.basename($file);

		// PluginUpdateChecker
		require 'third-party/plugin-update-checker.php';
		self::$update_checker = new RSPluginUpdateChecker_1_3(
		    'http://dimsemenov.com-updates.s3.amazonaws.com/plugins/rsupdate.json',
		     $file,
		    'newroyalslider'
		);


		add_action( 'init', array( &$this, 'init' ) );

		
		$this->global_options = get_option('new_royalslider_config');

		if(!$this->global_options) {
			$this->global_options = array(
				'embed' => array(
					'posts_with_slider' => 'posts_with_slider'
				),
				'allow_authors_cap' => 'no',
				'cache_refresh_time' => '24',
				'override_all_default_galleries' => ''
			);
			update_option('new_royalslider_config', $this->global_options);
		}
		if(isset($this->global_options['cache_refresh_time'])) {
			self::$refresh_hours = (float)$this->global_options['cache_refresh_time'];
		} else {
			self::$refresh_hours = 24;
		}
		if(isset($this->global_options['override_all_default_galleries']) && $this->global_options['override_all_default_galleries'] != '') {
			self::$override_all_default_galleries = (int)$this->global_options['override_all_default_galleries'];
		} else {
			self::$override_all_default_galleries = false;
		}

		if( isset($this->global_options) ) {
			if( isset($this->global_options['embed']) ) {
				$e = $this->global_options['embed'];

				if( isset($e['home_page']) ) {
					$this->load_on_home = true;
				}
				if( isset($e['every_page']) ) {
					$this->check_post_shortcode = false;
					$this->load_on_home = false;
					$this->load_on_every = true;
				} else if( isset($e['posts_with_slider']) ) {
					$this->check_post_shortcode = true;
				}
			}
		}

		


		add_shortcode('new_royalslider', array(&$this, 'shortcode'));  
		add_action( 'wpmu_new_blog', array(&$this, 'new_blog_added'), 10, 6);

		require_once('Widget.php');

		require_once('GalleryShortcode.php');
		$gallery_shortcode = new NewRoyalSliderGalleryShortcode();

		require_once('sources/Flickr.php');
		add_filter( 'new_rs_slides_filter', array('NewRoyalSliderFlickrSource', 'get_data'), 10, 3 );

		require_once('sources/500px.php');
		add_filter( 'new_rs_slides_filter', array('NewRoyalSlider500pxSource', 'get_data'), 10, 3 );

		require_once('sources/Posts.php');
		add_filter( 'new_rs_slides_filter', array('NewRoyalSliderPostsSource', 'get_data'), 10, 3 );

		if ( !current_theme_supports('post-thumbnails') ) 
			add_theme_support('post-thumbnails'); 
	}
	


	// Initialize everything
	function init() {
		$this->activate_db();
		$this->get_translation();

		require_once('NewRoyalSliderOptions.php');
		if( !is_admin() ) {
			
			add_action( 'wp_enqueue_scripts', array(&$this, 'find_and_register_scripts'));	
			//add_action( 'wp_print_styles', array( &$this, 'frontend_styles' ) );
			//add_action( 'init', array( &$this, 'update_jquery' ) );
			add_action( 'wp_footer', array( &$this, 'frontend_script' ) );
			add_action( 'wp_print_footer_scripts', array( &$this, 'custom_footer_scripts' ) );

		} else {
			if( isset($this->global_options['allow_authors_cap']) && $this->global_options['allow_authors_cap'] === 'yes' ) {
				// admins, editors, authors
				$capability = 'publish_posts';
			} else {
				// admins
				$capability = 'manage_options';
			}
			if( current_user_can( $capability ) ) {
				require_once( 'NewRoyalSliderBackend.php' );
				$this->slider_backend = new NewRoyalSliderBackend();
			}
		}
	}

	/**
	 * Manage scripts and styles
	 */
	function find_and_register_scripts() {

		if($this->check_post_shortcode) {
			global $posts;
			global $wpdb;
			
			$matches = array();
			$pattern = '\[(\[?)(gallery|new_royalslider)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			
			// find shortcode in current post
			if (isset($posts) && !empty($posts)) {
				foreach($posts as $post) {
					preg_match_all('/' . $pattern . '/s', $post->post_content, $matches);
					foreach($matches[2] as $key => $value) {
						if($value == 'new_royalslider') {							
							$attr = shortcode_parse_atts($matches[3][$key] );

							$this->push_script($attr['id']);
						} else if ($value == 'gallery') {
							$attr = shortcode_parse_atts($matches[3][$key] );

							if(self::$override_all_default_galleries) {
								$this->push_script( self::$override_all_default_galleries );
								continue;
							} else if(!isset($attr['royalslider'])) {
								continue;
							}
							$this->push_script($attr['royalslider']);
							
						}
					}
				}
			}
		}

		if ($this->load_on_home && is_front_page()) {
			$this->load_all = true;
		}

		if($this->load_on_every) {
			$this->load_all = true;
		}

		$this->frontend_styles();
		//echo 'loading scripts...'.$this->load_all.print_r($this->sliders_to_enqueue,true);

	}
	
	function push_style($id, $path) {
		if(!in_array($path, $this->styles)) {
			$this->styles[$id] = $path;
		}
	}

	function frontend_styles() {
		global $wpdb;
		$table = self::get_sliders_table_name();
		
		if( count($this->sliders_to_enqueue) > 0 || $this->load_all) {
			$this->has_slider_on_page = true;
			
			$qstr = " 
				SELECT id, skin, template FROM $table WHERE active=1
			";
		    if(!$this->load_all) {
		    	$ids = '(';
				foreach ($this->sliders_to_enqueue as $key => $value) {
					if($key != 0) {
						$ids .= ',';	
					}
					$ids .= (int)$value;
				}
				$ids .= ')';
		    	$qstr .= " AND id IN $ids ";
		    }
		    $res = $wpdb->get_results( $qstr , ARRAY_A );
		    require_once('NewRoyalSliderOptions.php');
		    $templates = NewRoyalSliderOptions::getRsTemplates();
		    $skins = NewRoyalSliderOptions::getRsSkins();

		    foreach ($res as $key => $slider_data) {
		    	// skins
		    	if(isset($slider_data['skin']) && isset($skins[ $slider_data['skin'] ])) {
		    		$this->push_style($slider_data['skin'], $skins[ $slider_data['skin'] ]['path'] );
		    	}

		    	// templates
		    	if(isset($slider_data['template']) && isset($templates[ $slider_data['template'] ])) {
		    		$template = $templates[ $slider_data['template'] ];
		    		if( isset($template['template-css']) ) {
		    			$this->push_style($slider_data['template'], $template['template-css']);
		    		}
		    		
		    	}
		    }
		} 

		if(count($this->styles) > 0) {
			wp_register_style( 'new-royalslider-core-css', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/royalslider.css', false, NEW_ROYALSLIDER_WP_VERSION, 'all' );
			wp_enqueue_style( 'new-royalslider-core-css' );
		}
		

		foreach($this->styles as $key => $style) {
			wp_register_style( $key.'-css', $style, array( 'new-royalslider-core-css' ), NEW_ROYALSLIDER_WP_VERSION, 'all' );
			wp_enqueue_style( $key.'-css' );
		}
		

	}
	function frontend_script() {
		if($this->has_slider_on_page) {

			wp_register_script( 'new-royalslider-main-js', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/jquery.royalslider.min.js', array('jquery'), NEW_ROYALSLIDER_WP_VERSION, 'all' );
			wp_enqueue_script('new-royalslider-main-js');
		}
	}

	public function push_script($id) {
		$this->sliders_to_enqueue[] = $id;
	}

	function custom_footer_scripts() {
		$init_codes = NewRoyalSliderMain::$sliders_init_code;
		if(count($init_codes) > 0 ) {
			echo "<script id=\"new-royalslider-init-code\" type=\"text/javascript\">\n";
			echo "jQuery(document).ready(function($) {\n";
			foreach($init_codes  as $key => $value) {
				echo $value;
			}
			echo "});\n";
			echo "</script>\n";
		}
	}

	function activate_db() {
		$curr_ver = get_option("new_royalslider_version");

		if($curr_ver != NEW_ROYALSLIDER_WP_VERSION) {
			global $wpdb;

			$charset_collate = '';
			if ( ! empty($wpdb->charset) )
	            $charset_collate = " DEFAULT CHARACTER SET $wpdb->charset";
	        if ( ! empty($wpdb->collate) )
	            $charset_collate .= " COLLATE $wpdb->collate";

			$table_name = NewRoyalSliderMain::get_sliders_table_name();
			
			$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
					  id 				mediumint(9) NOT NULL AUTO_INCREMENT,	
					  active            tinyint(1) not null default 1,
					  type 				varchar(100) NOT NULL,				  
					  name 				varchar(100) NOT NULL,
					  skin 				varchar(100) NOT NULL,
					  template          varchar(100) NOT NULL,
					  slides			longtext NOT NULL, 
					  options			mediumtext NOT NULL, 
					  template_html		mediumtext NOT NULL, 

					  PRIMARY KEY (id)
				)" . $charset_collate . ";";	
			$wpdb->query($sql);		

			// increase size of fields in old versions
			if($curr_ver && version_compare($curr_ver, '3.0.3', '<') ) {
				$upd_sql = "
				ALTER TABLE $table_name
					MODIFY type varchar(100),
					MODIFY name varchar(100),
					MODIFY skin varchar(100),
					MODIFY template varchar(100)
				";
				$wpdb->query($upd_sql);
			}

			$options = array(
				'timeout' => 10, //seconds
				'headers' => array(
					'Accept' => 'application/json',
					'Content-Type' => 'application/x-www-form-urlencoded; charset='.get_option('blog_charset'),
	            	'User-Agent' => 'WordPress/' . get_bloginfo("version") . ' RoyalSlider/' . NEW_ROYALSLIDER_WP_VERSION,
	           	    'Referer' => home_url()
				)
			);

			self::$update_checker->customRequestInfo( $options );

			update_option("new_royalslider_version", NEW_ROYALSLIDER_WP_VERSION);
		}

	}
	
	function new_blog_added( $blog_id, $user_id = null, $domain = null, $path = null, $site_id = null, $meta = null ) {
		global $wpdb;
		if ( is_plugin_active_for_network( $this->plugin_base_name ) ) {
			$old_blog = $wpdb -> blogid;
			switch_to_blog( $blog_id );
			$this->activate_db();
			switch_to_blog( $old_blog );
		}
	}

	function shortcode($atts, $content = null) {
		$shortcode_atts = shortcode_atts(array(
				"post_attachments" => false,
				"id" => -1
		), $atts);
		return $this->get_slider( (int)$shortcode_atts['id'], $shortcode_atts );
	}

	function get_translation() {
		load_plugin_textdomain( 'new_royalslider', false, NEW_ROYALSLIDER_DIRNAME . '/languages/' );
	}

	function get_slider($id, $shortcode_atts = null) {

		$rsdata = self::query_slider_data( $id );
		if(!$rsdata || !$rsdata[0]) {
    		return NewRoyalSliderMain::frontend_error(__('Incorrect RoyalSlider ID in shortcode or problem with query.', 'new_royalslider'));
    	}
		$rsdata = $rsdata[0];
		
		require_once('rsgenerator/NewRoyalSliderGenerator.php');

		$rsdata = apply_filters( 'new_rs_slider_data', $rsdata, $shortcode_atts );

		$output = '';
		$output .= NewRoyalSliderGenerator::generateSlides(
			false,
			$rsdata['id'], 
			$rsdata['type'], 
			$rsdata['template_html'], 
			$rsdata['slides'], 
			$rsdata['options'], 
			$rsdata['template'], 
			$rsdata['skin']
		);
		return $output;
	}

	


	/**
	 * Static functions
	 */
	public static function delete_cache_for($id, $type) {
		delete_transient( self::get_transient_key($id, $type) );
	} 
	public static function get_update_version($refresh_cache = false){
		if(self::$update_checker) {
			$upd = self::$update_checker->getUpdate();
			if($upd && isset($upd->version) ) {
				return $upd->version;
			} else {
				return false;
			}
		}
    }
	public static function get_transient_key($id, $type) {
		$key = 'new-royalslider-'.$type.'-'.$id;
		return $key;
	}
	public static function get_sliders_table_name(){
        global $wpdb;
        return $wpdb->prefix . "new_royalsliders";
    }
    public static function query_slider_data($id) {
    	global $wpdb;
    	$table = self::get_sliders_table_name();
	    return $wpdb->get_results( $wpdb->prepare( 
	        "
	            SELECT * FROM $table WHERE id=%d
	        ", 
	        $id
	    ), ARRAY_A );
    }
    public static function frontend_error($message) {
    	return '<p><strong>' . __('[RoyalSlider Error]', 'new_royalslider').' ' . $message . '</strong></p>';
    }
    public static function register_slider($id, $str) {
    	NewRoyalSliderMain::$sliders_init_code[$id] = $str;
    }
    

	/**
	* Title		: Aqua Resizer
	* Description	: Resizes WordPress images on the fly
	* Version	: 1.1.6
	* Author	: Syamil MJ
	* Author URI	: http://aquagraphite.com
	* License	: WTFPL - http://sam.zoy.org/wtfpl/
	* Documentation	: https://github.com/sy4mil/Aqua-Resizer/
	*
	* @param	string $url - (required) must be uploaded using wp media uploader
	* @param	int $width - (required)
	* @param	int $height - (optional)
	* @param	bool $crop - (optional) default to soft crop
	* @param	bool $single - (optional) returns an array if false
	* @uses		wp_upload_dir()
	* @uses		image_resize_dimensions() | image_resize()
	* @uses		wp_get_image_editor()
	*
	* @return str|array
	*/

	public static function aq_resize( $url, $width, $height = null, $crop = null, $single = true ) {
		
		//validate inputs
		if(!$url OR !$width ) return false;
		
		//define upload path & dir
		$upload_info = wp_upload_dir();
		$upload_dir = $upload_info['basedir'];
		$upload_url = $upload_info['baseurl'];
		
		//check if $img_url is local
		if(strpos( $url, $upload_url ) === false) return false;
		
		//define path of image
		$rel_path = str_replace( $upload_url, '', $url);
		$img_path = $upload_dir . $rel_path;
		
		//check if img path exists, and is an image indeed
		if( !file_exists($img_path) OR !getimagesize($img_path) ) return false;
		
		//get image info
		$info = pathinfo($img_path);
		$ext = $info['extension'];
		list($orig_w,$orig_h) = getimagesize($img_path);
		
		//get image size after cropping
		$dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
		$dst_w = $dims[4];
		$dst_h = $dims[5];
		
		//use this to check if cropped image already exists, so we can return that instead
		$suffix = "{$dst_w}x{$dst_h}";
		$dst_rel_path = str_replace( '.'.$ext, '', $rel_path);
		$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";
		
		if(!$dst_h) {
			//can't resize, so return original url
			$img_url = $url;
			$dst_w = $orig_w;
			$dst_h = $orig_h;
		}
		//else check if cache exists
		elseif(file_exists($destfilename) && getimagesize($destfilename)) {
			$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
		} 
		//else, we resize the image and return the new resized image url
		else {
			
			// Note: This pre-3.5 fallback check will edited out in subsequent version
			if(function_exists('wp_get_image_editor')) {
			
				$editor = wp_get_image_editor($img_path);
				
				if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
					return false;
				
				$resized_file = $editor->save();
				
				if(!is_wp_error($resized_file)) {
					$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path']);
					$img_url = $upload_url . $resized_rel_path;
				} else {
					return false;
				}
				
			} else {
			
				$resized_img_path = image_resize( $img_path, $width, $height, $crop ); // Fallback foo
				if(!is_wp_error($resized_img_path)) {
					$resized_rel_path = str_replace( $upload_dir, '', $resized_img_path);
					$img_url = $upload_url . $resized_rel_path;
				} else {
					return false;
				}
			
			}
			
		}
		
		//return the output
		if($single) {
			//str return
			$image = $img_url;
		} else {
			//array return
			$image = array (
				0 => $img_url,
				1 => $dst_w,
				2 => $dst_h
			);
		}
		
		return $image;
	}
	public static function get_embed_help_block($slider_id) {
		?>
<div class="right-info-bar" id="embed-info">
<h2><?php _e('Embedding slider', 'new_royalslider') ?></h2>
<ol>
    <li>
        <h3><?php _e('Step 1: Add slider HTML to your theme:', 'new_royalslider') ?></h3>
        <h4><?php _e('using shortcode', 'new_royalslider') ?></h2>

        <p><?php echo sprintf(__('Paste shortcode <code>[new_royalslider id="%1$d"]</code> in content area of any post.<br/> If you add slider that overrides default WordPress gallery, you need to add <code>royalslider="%1$d"</code> attribute to [gallery] shortcode.%2$s', 'new_royalslider'), 
            $slider_id, 
            ($slider_id == 123 ? __(' <br/><span class="no-id">Instead of 123 there should be ID of your slider.</span>', 'new_royalslider') : '') ); ?>
        </p>

        <h4><?php _e('using PHP', 'new_royalslider') ?></h2>
        <p><?php echo sprintf(__('Call <code>echo get_new_royalslider(%s);</code> function where you want slider to be
placed.', 'new_royalslider'), $slider_id ) ?></p>
        <h4><?php _e('using widget', 'new_royalslider') ?></h2>
        <p><?php _e('Go to WordPress widgets page, drag RoyaSlider widget to sidebar and select required slider from widget options.', 'new_royalslider') ?></p>
    </li>
    <li>
        <h3><?php _e('Step 2: Include CSS and JS files', 'new_royalslider') ?></h3>
        <p><?php echo sprintf(__('Make sure that JavaScript and CSS files are included on page where
you added slider.<br/><strong>By default slider adds scripts only to posts with slider shortcode</strong>, you may also add scripts on home page, on every page, or manually via PHP by calling <code>register_new_royalslider_files(%s);</code> in functions.php. Configuration  can be changed on <a href="%s">Settings
page</a>.', 'new_royalslider'), $slider_id ,get_admin_url() . "admin.php?page=new_royalslider_settings" ); ?></p>
		<p><?php _e('Learn more on <a href="http://dimsemenov.com/private/forum.php" target="_blank">RoyalSlider support desk</a>.', 'new_royalslider') ?></p>
    </li>

</ol>
</div>
		<?php
	}

}
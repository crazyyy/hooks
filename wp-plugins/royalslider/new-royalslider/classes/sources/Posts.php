<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderPostsSource {

	function __construct( ) { }

	public static function init_ajax() {
		add_action( 'wp_ajax_newRsGetPostTypeTerms', array('NewRoyalSliderPostsSource', 'get_post_type_terms' ) );
	}
	public static function get_post_type_terms() {
		check_ajax_referer('new_royalslider_custom_source_action_nonce');
		if(isset($_POST['post_type'])) {
			self::get_taxonomies_fields( $_POST['post_type'] );
		} else {
			_e('Missing post type', 'new_royalslider');
		}
		die();
	}
	public static function show_admin_options( $curr_options = null ) {

		?>
		<div class="rs-info">
			<p class="rs-awesome-paragraph"><?php _e('Here you may create RoyalSlider from your posts', 'new_royalslider'); ?></p>
			<p><?php _e('Slider uses Post Featured Image as a default source for images.<br/>To create custom layout use Slide Markup Editor - you may use shortcodes and get custom meta data from eacho post.', 'new_royalslider'); ?></p>
            <div class="help-video"><a class="in-page-action" href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-creating-royalslider-from-your-posts" target="_blank"><?php _e('View help video about how to create such slider', 'new_royalslider'); ?></a></div><br/>
        </div>
		<?php
		

		$post_types = get_post_types(array(
				'_builtin' => false
			));
		$post_types = array("post" => "post", "page" => "page") + $post_types;


				
		$post_types_arr = array();
		foreach ($post_types  as $key => $post_type ) {
		  	$selected = "";
		  	$posttype_obj = get_post_type_object($key);
		  	$post_types_arr[$key] = $posttype_obj->labels->singular_name;
		}


		$fields = array(
			array(
	            'name' => 'post_type',
	            'label' => __( 'Post type', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => $post_types_arr,
	            'default' => 'post',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'Post type to include in slider. Changing this option will automatically reload taxonomies (tags,categories...)', 'new_royalslider' )
            ),
			array(
	            'name' => 'max_posts',
	            'label' => __( 'Max posts', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'number',
	            'data-type' => 'num',
	            'default' => '5',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'Maximum number of posts to include in slider', 'new_royalslider' )
            ),
            array(
	            'name' => 'orderby',
	            'label' => __( 'Order posts by', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
	            	'date' => __( 'Date', 'new_royalslider' ),
	            	'comment_count' => __( 'Comments', 'new_royalslider' )
	            ),
	            'default' => 'date',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( '(random order can be set in Miscellaneous options)', 'new_royalslider' )
            ),
            array(
	            'name' => 'relation',
	            'label' => __( 'Relation', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
	            	'OR' => __( 'Match any selected taxonomy', 'new_royalslider' ),
	            	'AND' => __( 'Match all selected taxonomies', 'new_royalslider' )
	            ),
	            'default' => 'OR',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'Relation between terms. "Match all" will select only posts that have all selected taxonomies.', 'new_royalslider' )
            ),
            array(
	            'name' => 'link_the_slide',
	            'label' => __( 'Link slide to post', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
	            	'yes' => __( 'Yes, make link overlay over slide', 'new_royalslider' ),
	            	'no' => __( 'No', 'new_royalslider' )
	            ),
	            'default' => 'no',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'If enabled, links whole slide to corresponding post. Please note that such link doesn\'t work with auto-height option. You may edit slide markup to link specific button or image just by wrapping it with "a" HTML tag . {{link_url}} will return you URL to post.' )
            )
			
		);


		if( isset($curr_options) && isset($curr_options['posts']) ) {
			$opts = $curr_options['posts'];
			$fields = NewRoyalSliderOptions::parseCurrentOptions($fields, $opts);
		} else {
			$opts = array( 'taxonomies' => array( ) );
		}


	
		echo '<div id="rs-postssource-options" class="rs-body-options">';

		echo '<h3>' . __('Posts Settings', 'new_royalslider') .'</h3>';
		foreach ( $fields as $key => $field ) {
        	echo NewRoyalSliderOptions::get_field_html($field, 'posts');
        }

	        echo '<div id="rs-taxonomies-fields">';
	        echo self::get_taxonomies_fields( isset($opts['post_type']) ? $opts['post_type'] : 'post', isset($opts['taxonomies']) ? $opts['taxonomies'] : null );
			echo '</div>';
		 echo '<p style="color:#888;"><br/>' . __('If you need custom order, you can filter this query via PHP with help of WordPress filters. <a target="_blank" href="http://help.dimsemenov.com/kb/wordpress-royalslider-advanced/wp-modifying-order-of-posts-in-slider">Read more</a>') . '</p>';
        echo '</div>';
	}
	private static function get_taxonomies_fields($post_type, $curr_value = null) {
		

		$taxonomies =  self::list_taxonomies( $post_type );

		
		$new_arr = array();
		if($curr_value) {
			$new_arr['category'] = isset($curr_value['post_category']) ? $curr_value['post_category'] : '';
			if(isset($curr_value['tax_input'])) {
				foreach ($curr_value['tax_input'] as $key => $value) {
					$new_arr[$key] = $value;
				}
			}
			
		}
		
		
		foreach ($taxonomies as $key => $taxonomy) {

			
			

			$checked = null;
			if($curr_value && isset($new_arr[$key]) ) {
				$checked = $new_arr[$key];
			}
			 self::newrs_post_categories_meta_box(null, array(
	        	'args' => array(
	        		'taxonomy' => $key,
	        		'label' => $taxonomy,
	        		'checked_texonomies' => $checked
	        	)
	        ));
		}
       
       
	}
	/**
	 * Display post categories form fields. Based on default WP method.
	 *
	 * @param object $post
	 */
	private static function newrs_post_categories_meta_box( $post, $box ) {
		$defaults = array('taxonomy' => 'category');
		if ( !isset($box['args']) || !is_array($box['args']) )
			$args = array();
		else
			$args = $box['args'];
		extract( wp_parse_args($args, $defaults), EXTR_SKIP );
		$tax = get_taxonomy($taxonomy);

		//$checked_texonomies = isset($args['selected_cats']) ? $args['selected_cats'] : '';
		echo '<h4>' .$args['label']. __(' to include:', 'new_royalslider') . '</h4>';


		$terms_list = (array) get_terms($taxonomy, array( 'child_of' => 0, 'hierarchical' => 0, 'hide_empty' => 0 ) );
		if( count($terms_list) > 2000 ) {
			echo '<p><em>'. __('This taxonomy has too large number of items to display in admin (>2000). If you wish to add it <a target="_blank" href="http://help.dimsemenov.com/kb/wordpress-royalslider-advanced/wp-modifying-order-of-posts-in-slider">do this programatically</a>.', 'new_royalslider') . '</em></p>';
			return;
		}

		ob_start();
			$popular_ids = wp_popular_terms_checklist($taxonomy);
			$popular_terms = ob_get_contents();
		ob_end_clean();

		?>
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
			<ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
				<li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
				<li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop"><?php _e( 'Most Used', 'new_royalslider' ); ?></a></li>
			</ul>

			<div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
				<ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
					<?php echo $popular_terms;  ?>
				</ul>
			</div>

			<div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
				<ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear main-opts">
					<?php wp_terms_checklist(0, array( 'taxonomy' => $taxonomy, 'selected_cats' => $checked_texonomies, 'popular_cats' => $popular_ids ) ); ?>
				</ul>
			</div>
		</div>
		<?php
	}
	private static function list_taxonomies($post_type) {
		$taxonomies = (array)get_object_taxonomies( $post_type, 'objects' );
		$arr = array();
		foreach ($taxonomies as $key => $value) {
			$arr[$key] = $value->label;
		}
		return $arr;
	}


	public static function get_data($slides, $options, $type) {
		if($type === 'posts') {
			if(isset($options['posts'])) {


				$opts = $options['posts'];
				$args = array(
		        	'post_type' => $opts['post_type'],
		        	'post_status' => 'publish',
		        	'posts_per_page' => $opts['max_posts'],
		        	'order' => 'DESC',
		        	'orderby' => $opts['orderby'] == 'comment_count' ? 'comment_count' : 'date',
		        	'ignore_sticky_posts' => 1,
		        	'tax_query' => array(
		        		'relation' => $opts['relation']
					)
		        );

		        $new_arr = array();
				if( isset($opts['taxonomies']) ) {
					$curr_value = $opts['taxonomies'];
					if(isset($curr_value['post_category'])) {
						$new_arr['category'] = $curr_value['post_category'];
					}
					
					if(isset($curr_value['tax_input'])) {
						foreach ($curr_value['tax_input'] as $key => $value) {
							$new_arr[$key] = $value;
						}
					}
				}

				$tax_query_items = array();
		        foreach ($new_arr as $key => $value) {
		        	$args['tax_query'][] = array(
		        		'taxonomy' => $key,
						'field' => 'id',
						'terms' => $value,
						'operator' => $opts['relation'] == 'AND' ? 'AND' : 'IN' 
		        	);
		        }
		        
		    	$args = apply_filters('new_royalslider_posts_slider_query_args', $args);

		        $query = new WP_Query( $args );
		        $slides = array();

		        // echo '<pre>';
		        // print_r($args);
		        // echo '</pre>';
		        
		        global $post;
		        while ( $query->have_posts() ) : 
					$query->the_post();
					$slide = &$slides[];
				endwhile;
				wp_reset_query();

				return (array)$query->posts;

			}
		}
		return $slides;
	}
	
}
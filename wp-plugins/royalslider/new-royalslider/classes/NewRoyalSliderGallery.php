<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider Media Gallery Image select + Custom Slider Edit page
 */

if ( !class_exists( 'NewRoyalSliderGallery' ) ):
    class NewRoyalSliderGallery {
    	function __construct( ) {
    		require_once('NewRoyalSliderOptions.php');
    		if( isset($_REQUEST['newrs-a-gallery-enabled']) ) {
				add_filter( 'media_upload_tabs', array(&$this, 'remove_unused_tab'), 50, 2);
				    		add_filter( 'media_upload_form_url', array(&$this, 'parse_url'), 16, 2);

				add_filter( 'attachment_fields_to_edit', array(&$this, 'add_buttons'), 20, 2 );
    		}

    		add_action( 'wp_ajax_newRsCreateNewSlide', array(&$this, 'rs_create_new_slide') );
    		add_action( 'wp_ajax_newRsCustomMedia', array(&$this, 'rs_get_custom_media') );
    		add_action( 'wp_ajax_newRsSingleMedia', array(&$this, 'rs_get_single_media') );
    		add_filter( 'admin_enqueue_scripts', array(&$this, 'rs_admin_enqueue_scripts'), 11, 2);
    	}

		public function parse_url($form_action_url, $type) {
			if(isset($_REQUEST['newrs-a-gallery-enabled'])) {
				$form_action_url = $form_action_url . "&amp;newrs-a-gallery-enabled=".$_REQUEST['newrs-a-gallery-enabled'];
				echo '<input type="hidden" id="new_royalslider_media_library" val="1" />';
			}
			return $form_action_url;
		}
		
		function rs_admin_enqueue_scripts( $hook ) {
			wp_enqueue_script('thickbox');
			
			if( isset($_REQUEST['newrs-a-gallery-enabled']) && $_REQUEST['newrs-a-gallery-enabled'] ) {

				wp_register_style( "royalslider-admin", NEW_ROYALSLIDER_PLUGIN_URL . "lib/backend/royalslider-admin.css", array( ), NEW_ROYALSLIDER_WP_VERSION, 'screen' );
	   			wp_enqueue_style( "royalslider-admin" );


				wp_register_script( 'new-rs-toJSON', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/toJSON.js', array('jquery'));
				wp_enqueue_script( 'new-rs-toJSON' );

				wp_register_script( 'new-royalslider-gallery-js', NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/royalslider-gallery.js', array('jquery'));
				
				wp_localize_script( 'new-royalslider-gallery-js', 'rsMediaAddVars', array(
								'enabled' => true,
								'ajaxurl' => admin_url( 'admin-ajax.php' ),

								'getImagesNonce' => wp_create_nonce( 'new_royalslider_get_images_ajax_nonce' ),
								'getSingleImageNonce' => wp_create_nonce( 'new_royalslider_get_image_ajax_nonce' ),
								
								'add_to_slider' => __('Add to slider', 'new_royalslider'),
								'add_all_uploaded_to_slider' => __('Add all uploaded to slider', 'new_royalslider'),
								'add' => __('Add all ', 'new_royalslider'),
								'images_to_slider' => __(' images to slider', 'new_royalslider'),
								'add_to_slider_singular' => __('Add one image to slider', 'new_royalslider'),
								'adding' => __('Adding...', 'new_royalslider'),
								'added' => __('Added!', 'new_royalslider'),
								'isSingle' =>  isset($_REQUEST['newrs-a-gallery-single']) && $_REQUEST['newrs-a-gallery-single']
				));	
				
				wp_enqueue_script('new-royalslider-gallery-js');
			}
		}

		function rs_create_new_slide() {
			check_ajax_referer('new_royalslider_new_admin_slide_nonce');
			echo self::get_admin_slide_item(null);
			die();
		}

		function rs_get_custom_media() {
			check_ajax_referer('new_royalslider_get_images_ajax_nonce');

			foreach($_POST['attachments'] as $key => $attachment_id) {

				$image_post = get_post( $attachment_id );
				
				$slide_data = array(
					'image' => array(
						'attachment_id' => $attachment_id
					),
					'title' => $image_post->post_title,
					'description' => $image_post->post_excerpt
				);
				echo self::get_admin_slide_item($slide_data);
			}
			die();
		}

		function rs_get_single_media() {
			check_ajax_referer('new_royalslider_get_image_ajax_nonce');
			
			foreach($_POST['attachments'] as $key => $attachment_id) {
				$image_src = wp_get_attachment_image_src(  $attachment_id, 'thumbnail' );
				$large_src = wp_get_attachment_image_src(  $attachment_id, 'large' );
				$image_post = get_post( $attachment_id );
				
				$imagedata = array(
					'id' => $attachment_id,
					'src' => $image_src[0],
					'title' => $image_post->post_title,
					'caption' => $image_post->post_excerpt,
					'large' => $large_src[0],
					'large_width' => $large_src[1],
					'large_height'=> $large_src[2]
				);

				echo json_encode( $imagedata );
			}

			die();
		}

		static function get_admin_slide_item($slide_data) {
			$image_src = '';
			if( isset($slide_data['image']) ) {
				$image_data = $slide_data['image'];
				if(isset($image_data['attachment_id'])) {
					$image_src = wp_get_attachment_image_src( $image_data['attachment_id'], 'thumbnail' );
					$image_src = $image_src[0];
				}
			} else if(isset($slide_data['video']) && isset($slide_data['video']['thumb']) ) {
				$image_src = $slide_data['video']['thumb'];
			}

			if(!$image_src) {
				$image_src = NEW_ROYALSLIDER_PLUGIN_URL . 'lib/backend/img/empty150.png';
			}
			

			$out = '
				<div class="rsSlideItem">
					<div class="rsMainThumb" style="background-image: url(\'%s\');" ></div>
					<span class="rs-item-action rs-remove-slide" title="%s"></span>
					<span class="rs-item-action rs-edit-slide" title="%s"></span>
					<span class="rs-item-action rs-duplicate-slide" title="%s"></span>
					<div class="rs-tabs">
						
					    <div class="rs-tabs-container">
					    	<div class="rs-tabs-wrap">
							    <div class="rs-image-tab">
									%s
								</div>
								<div class="rs-animated-block-tab"><textarea class="rs-anim-block-textarea" name="slides[animated_blocks]" style="display:none;">%s</textarea></div>
								<div class="rs-html-tab">
									%s
								</div>
							</div>
					    </div>

					</div>
				</div>
			';

			return sprintf(
				$out,
				$image_src,

				__('Remove slide', 'new_royalslider'),
				__('Edit slide', 'new_royalslider'),
				__('Duplicate slide', 'new_royalslider'),

				


				

				self::getImageTab($slide_data, $image_src),
				isset($slide_data['animated_blocks']) ? $slide_data['animated_blocks'] : '',
				self::getHTMLTab($slide_data)
			);


			return $out;
		}
		
		static function getHTMLTab($slide_data) {
			$out = '';
			$out .= '<textarea name="slides[html]">';
			if( isset($slide_data['html']) ) {
				$out .= $slide_data['html'];
			}
			$out .= '</textarea>';
			return $out;
		}
		
		static function getImageTab($slide_data, $image_src) {
			$out = '';
			$attachment_id = '';
			$big_image = '';
			$big_image_src = '';
			$image_data = '';
			if(isset($slide_data['image'])) {
				$image_data = $slide_data['image'];

				if(isset($slide_data['image']['attachment_id'])) {
					$attachment_id = $slide_data['image']['attachment_id'];
					$big_image = wp_get_attachment_image_src( $attachment_id, 'large' );
					$big_image_src = $big_image[0];
				}
				
			}
				
			$add_label = $image_data ? __('Change image', 'new_royalslider') : __('Add image', 'new_royalslider');
			$hidden = !$image_data ? 'style="display:none"' : '';
				$out .= '<div class="rs-image-change-wrap">
							<img src="'.$image_src.'" />
							<a class="rs-select-image button button-primary" href="#">'. $add_label .'</a>
							<a class="rs-remove-image button" href="#"'.$hidden.'>'. __('Remove image', 'new_royalslider') .'</a>
				</div>';

				$out .= '<div class="rs-image-inputs-wrap">';
				$out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'title',
		                'label' => __( 'Title & alt <i class="help-ico"></i>', 'new_royalslider' ),
		                'desc' => __( 'Title of the slide. Alt tag of image. Caption. (by default).<br/> In Slide Markup Editor use <strong>{{title}}</strong> to get value of this field.', 'new_royalslider' ),
		                'type' => 'text',
		                'default' => isset($slide_data['title']) ? $slide_data['title'] : ''
		        ),'slides' );

		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'description',
		                'label' => __( 'Description <i class="help-ico"></i>', 'new_royalslider' ),
		                'desc' => __( 'Short description of slide, used by some templates (like content slider). <br/>In Slide Markup Editor use <strong>{{description}}</strong>} to get value of this field.', 'new_royalslider' ),
		                'type' => 'textarea',
		                'default' => isset($slide_data['description']) ? $slide_data['description'] : ''
		        ),'slides' );

		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'link',
		                'label' => __( 'Link <i class="help-ico"></i>', 'new_royalslider' ),
		                'desc' => __( 'Links whole slide to URL in this field. <br/><strong>Please note</strong> that full slide link doesn\'t work with auto-height option and overlays video. You can edit Slide Markup to link just specific button or image by wrapping it with "a" HTML tag .<br/>In Slide Markup Editor use <strong>{{link_url}}</strong> to get value of this field.', 'new_royalslider' ),
		                'type' => 'text',
		                'default' => isset($slide_data['link']) ? $slide_data['link'] : ''
		        ),'slides' );

		        $out .= '<div class="rs-video-select">';
		        	
		        	if( !isset($slide_data['video']) ) {
		        		$video_data = array(
		        			'url' => '',
                    		'thumb' => '',
                    		'image' => ''
		        		);
		        	} else {
		        		$video_data = $slide_data['video'];
		        	}
			        $out .= NewRoyalSliderOptions::get_field_html( array(
			                'name' => 'url',
			                'label' => __( 'YouTube or Vimeo video URL <i class="help-ico"></i>', 'new_royalslider' ),
			                'desc' => __( 'Link to YouTube or Vimeo video page. In formats like:<br/>vimeo.com/123123<br/>www.youtube.com/watch?v=7iIld0Z_wlc<br/>youtu.be/7iIld0Z_wlc<br/>In Slide Markup Editor use <strong>{{video_url}}</strong> to get value of this field.', 'new_royalslider' ),
			                'type' => 'text',
			                'default' => $video_data['url']
			        ),'slides[video]' );
			        $out .= NewRoyalSliderOptions::get_field_html( array(
			                'name' => 'image',
			                'type' => 'hidden',
			                'default' => isset($video_data['image']) ? $video_data['image'] : ''
			        ),'slides[video]' );
					$out .= NewRoyalSliderOptions::get_field_html( array(
				                'name' => 'thumb',
				                'type' => 'hidden',
				                'default' => isset($video_data['thumb']) ? $video_data['thumb'] : ''
				        ),'slides[video]' );
		        $out .= '</div>';

				
				$out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'attachment_id',
		                'type' => 'hidden',
		                'default' => $attachment_id
		        ),'slides[image]' );
				

		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'large',
		                'type' => 'hidden',
		                'default' => $big_image_src
		        ),'adminarea' );



		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'large_gen',
		                'type' => 'hidden',
		                'default' => $big_image_src
		        ),'adminarea' );

		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'large_width',
		                'type' => 'hidden',
		                'default' => $big_image ? $big_image[1] : ''
		        ),'adminarea' );

		        $out .= NewRoyalSliderOptions::get_field_html( array(
		                'name' => 'large_height',
		                'type' => 'hidden',
		                'default' => $big_image ? $big_image[2] : ''
		        ),'adminarea' );

		        $out .= '</div>';

			
			return $out;
		}
		

		public function add_buttons( $form_fields, $post ) {
			
			if(isset($_REQUEST['newrs-a-gallery-enabled'])) {

		        $form_fields['post_content']['value'] = '';
		        $form_fields['post_content']['input'] = 'hidden';

		        $form_fields['url']['value'] = '';
		        $form_fields['url']['input'] = 'hidden';

		        $form_fields['align']['value'] = 'aligncenter';
		        $form_fields['align']['input'] = 'hidden';

		        $form_fields['image-size']['value'] = 'thumbnail';
		        $form_fields['image-size']['input'] = 'hidden';

		        $label = isset($_REQUEST['newrs-a-gallery-single']) ? __('Select this image', 'new_royalslider') : __('Add to slider', 'new_royalslider');

				$form_fields['dsframework_media_box_add_button'] = array(
					'label' => '',
					'input' => 'html',
					'html'  => '<button data-attachment-id="'.$post->ID.'" class="primary-button button rs-media-add-btn dsframework-tb-add-image-button" data-attachment-description=""  data-attachment-alt-attr="">' . $label . '</button>',
				);
			}
			return $form_fields;
		}
		
		public function remove_unused_tab($tabs_to_add) {	
			if(isset($_REQUEST['newrs-a-gallery-enabled'])) {
				$tabs_to_add = array('type' => 'From Computer', 'library' => 'Media Library');
			}
			return $tabs_to_add;
		}
	}
		

		
endif;
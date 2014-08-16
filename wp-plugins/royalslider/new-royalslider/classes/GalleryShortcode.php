<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider Gallery Shortcode
 */

if ( !class_exists( 'NewRoyalSliderGalleryShortcode' ) ):
    class NewRoyalSliderGalleryShortcode {

        function __construct( ) {
           add_filter( 'post_gallery', array(&$this, 'gallery_shortcode'), 50, 2 );


        }
        function gallery_shortcode($output, $attr) {

        	global $post;

        	if(!isset($attr['royalslider'])) {
        		if(NewRoyalSliderMain::$override_all_default_galleries) {
        			$rsid = NewRoyalSliderMain::$override_all_default_galleries;
        		} else {
        			return $output;
        		}
        	} else {
        		$rsid = $attr['royalslider'];
        	}
        	
        	$rsdata = NewRoyalSliderMain::query_slider_data( $rsid );

        	if(!$rsdata || !$rsdata[0]) {
        		return NewRoyalSliderMain::frontend_error(__('Incorrect royalslider ID in gallery shortcode (or in Global, or problem with query.', 'new_royalslider'));
        	}
        	$rsdata = $rsdata[0];

			if ( isset( $attr['orderby'] ) ) {
				$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
				if ( !$attr['orderby'] )
					unset( $attr['orderby'] );
			}

			extract(shortcode_atts(array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post->ID,
				'itemtag'    => 'dl',
				'icontag'    => 'dt',
				'captiontag' => 'dd',
				'columns'    => 3,
				'size'       => 'thumbnail',
				'ids'        => '',
				'include'    => '',
				'exclude'    => ''
			), $attr));

			$id = intval($id);
			if ( 'RAND' == $order )
				$orderby = 'none';

			if ( !empty( $ids ) ) {
				// 'ids' is explicitly ordered
				$orderby = 'post__in';
				$include = $ids;
			}

			if ( !empty($include) ) {
				$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

				$attachments = array();
				foreach ( $_attachments as $key => $val ) {
					$attachments[$val->ID] = $_attachments[$key];
				}
			} elseif ( !empty($exclude) ) {
				$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
			} else {
				$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
			}

			if ( empty($attachments) )
				return NewRoyalSliderMain::frontend_error(__('No post attachments found.', 'new_royalslider'));

			if ( is_feed() ) {
				$output = "\n";
				foreach ( $attachments as $att_id => $attachment )
					$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
				return $output;
			}

			

			$output = '';

			require_once('rsgenerator/NewRoyalSliderGenerator.php');
			$rsdata['slides'] = $attachments;
			$rsdata = apply_filters( 'new_rs_slider_data', $rsdata, $attr );
			$output .= NewRoyalSliderGenerator::generateSlides(
				true,
				$rsdata['id'],
				'gallery', 
				$rsdata['template_html'], 
				$rsdata['slides'], 
				$rsdata['options'], 
				$rsdata['template'], 
				$rsdata['skin']
			);

			return $output;
		}
    }
endif;
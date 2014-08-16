<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderFlickrSource {


	function __construct( $curr_options = null ) {

	}
	public static function show_admin_options( $curr_options = null ) {
		?>
		<div class="rs-info">
			<p class="rs-awesome-paragraph"><?php _e('Here you can create gallery from any Flickr photoset', 'new_royalslider'); ?></p>
			<p><?php _e('To create such gallery go to your <a href="http://www.flickr.com/services/api/keys/" target="_blank">Flickr account</a> and request your API key. Then simply enter your data in settings below.<br/>Requested Flickr image thumbnail size is 75x75px, so for some templates you need to change default size of them, you can do this in right sidebar options "Thumbnails,tabs,bullets" -> "Thumbnail Width" and "Thumbnail Height.', 'new_royalslider'); ?></p>
            <div class="help-video"><a class="in-page-action" href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-creating-royalslider-from-flickr-photoset" target="_blank"><?php _e('View help video about how to create Flickr gallery', 'new_royalslider'); ?></a></div><br/>
        </div>
        <?php
		
		$fields = array(
			array(
				'name' => 'api_key',
                'label' => __( 'Flickr API key', 'new_royalslider' ),
                'desc' => __( 'API key', 'new_royalslider' ),
                'type' => 'text',
                'default' => '',
                'data-type' => 'str',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( 'Your <a href="http://www.flickr.com/services/api/keys/" target="_blank">Flickr API key</a>.', 'new_royalslider' )
            ),
			array(
				'name' => 'photoset_id',
                'label' => __( 'Flickr photoset ID', 'new_royalslider' ),
                'desc' => __( '', 'new_royalslider' ),
                'type' => 'text',
                'default' => '',
                'data-type' => 'str',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( 'Any <a href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-how-to-find-flickr-photoset-id" target="_blank">Flickr photoset ID</a>', 'new_royalslider' ),
            	'delimiter' => true
            ),
			array(
				'name' => 'max_items',
                'label' => __( 'Maximum items', 'new_royalslider' ),
                'desc' => __( 'Maximum items  to fetch from Flickr', 'new_royalslider' ),
                'type' => 'number',
                'default' => '10',
                'data-type' => 'num',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( 'Maximum number of items to include in slider.', 'new_royalslider' )
            ),
			array(
	            'name' => 'medSize',
	            'label' => __( 'Image size', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
	            	'' => __('500 on longest side', 'new_royalslider'),
	            	'_z' => __('640 on longest side', 'new_royalslider'),
	            	'_c' => __('800 on longest side', 'new_royalslider'),
	            	'_b' => __('1024 on longest side', 'new_royalslider')
	            ),
	            'default' => 'image',
	            'ignore' => true
	        ),
	        array(
            	'desc' => __( 'Size of default main slider image', 'new_royalslider' )
            )
		);
		

		if( isset($curr_options) && isset($curr_options['flickr']) ) {
			$flickr_opts = $curr_options['flickr'];
			$fields = NewRoyalSliderOptions::parseCurrentOptions($fields, $flickr_opts);
		}
	
		echo '<div id="rs-flickr-options" class="rs-body-options">';
		echo '<h3>' . __('Flickr Settings', 'new_royalslider') .'</h3>';
		foreach ( $fields as $key => $field ) {
        	echo NewRoyalSliderOptions::get_field_html($field, 'flickr');
        }
        echo '</div>';
	}
	public static function get_data($slides, $options, $type) {
		if($type === 'flickr') {
			if(isset($options['flickr'])) {
				// Array
				// (
				//     [api_key] => 123
				//     [photoset_id] => 123
				//     [max_items] => 20
				//     [medSize] => b
				// )
				$flickr_options = $options['flickr'];


				$data = array(
					'method' => 'flickr.photosets.getPhotos',
					'photoset_id' => $flickr_options['photoset_id'],
					'api_key'=> $flickr_options['api_key'],
					'per_page' => $flickr_options['max_items'],
					'format'=>'php_serial',
					'extras' => 'url_t'
		        );

				$url = 'http://api.flickr.com/services/rest/?'.http_build_query($data);
				$rsp = wp_remote_request($url);

				if ( !$rsp || is_wp_error( $rsp ) || 200 != $rsp['response']['code']) {
					return __('There was a problem with request. Please check Flickr settings and try again.', 'new_royalslider');
				}
				$rsp = $rsp['body'];
				$rsp_obj = unserialize($rsp);
				if(isset($rsp_obj['code'])) {
					return ' Flickr Responded: "'.$rsp_obj['message'] . '"';
				}
				$images = array();
				if(isset($rsp_obj['photoset']) ) {
					foreach($rsp_obj['photoset']['photo'] as $key => $value) {

						$def_thumb_url = $value['url_t'];
						$image = &$images[$key];
						$image['image'] = str_replace('_t', $flickr_options['medSize'], $def_thumb_url);
						$image['thumbnail'] = str_replace('_t', '_s', $def_thumb_url);
						$image['large_image'] = str_replace('_t', '_b', $def_thumb_url);
						$image['title'] = $value['title'];
						//$image['link'] = 
					} 
				}



				return $images;
			}
			//
		}
		return $slides;
	}
	


}



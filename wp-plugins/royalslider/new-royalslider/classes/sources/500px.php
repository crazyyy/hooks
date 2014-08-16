<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSlider500pxSource {

	

	function __construct( $curr_options = null ) {

	}
	public static function show_admin_options( $curr_options = null ) {
		?>
		<div class="rs-info">
			<p class="rs-awesome-paragraph"><?php _e('Here you can create RoyalSlider from <a href="http://500px.com" target="_blank">500px</a> photos', 'new_royalslider'); ?></p>
			<p><?php _e('To create such gallery go to your 500px account and request your API consumer key. Then simply enter your data in settings below.<br/>Requested 500px image thumbnail size is 70x70, so for some templates you need to change default size of them, you can do this in right sidebar options "Thumbnails,tabs,bullets" -> "Thumbnail Width" and "Thumbnail Height.', 'new_royalslider'); ?></p>
            <div class="help-video"><a class="in-page-action" href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-creating-royalslider-from-500px-photos" target="_blank"><?php _e('View help video about how to create 500px gallery', 'new_royalslider'); ?></a></div><br/>
        </div>
		<?php
		
		$fields = array(
			array(
				'name' => 'consumer_key',
                'label' => __( 'Consumer key', 'new_royalslider' ),
                'desc' => '',
                'type' => 'text',
                'default' => '',
                'data-type' => 'str',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( 'Your <a href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-where-to-get-500px-consumer-key" target="_blank">500px consumer key</a>.', 'new_royalslider' )
            ),

			array(
	            'name' => 'feature',
	            'label' => __( 'Photos source', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
					'user' => __('By [username]', 'new_royalslider'),
					'user_friends' => __('From [username] friends', 'new_royalslider'),
					'user_favorites' => __('[username] favorites', 'new_royalslider'),

					'popular' => __('Popular (globally)', 'new_royalslider'),
					'editors' => __('Editors choice ', 'new_royalslider'),
					'fresh_today' => __('Fresh today ', 'new_royalslider'),
					'upcoming' => __('Upcoming ', 'new_royalslider')
	            ),
	            'default' => 'image',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'The source of photos for gallery.', 'new_royalslider' )
            ),



   //          array(
	  //           'name' => 'display_by',
	  //           'label' => __( 'Display by', 'new_royalslider' ),
	  //           'desc' => __( '', 'new_royalslider' ),
	  //           'type' => 'select',
	  //           'data-type' => 'str',
	  //           'options' => array(
			// 		'' => __('Username', 'new_royalslider'),
			// 		'set' => __('Photo set', 'new_royalslider')
					
	  //           ),
	  //           'default' => 'image',
	  //           'ignore' => true
	  //       ),
   //          array(
   //          	'desc' => __( 'Get photos by username, or by photoset (premium feature).', 'new_royalslider' )
   //          ),
   //          array(
			// 	'name' => 'photoset',
   //              'label' => __( 'Photo set', 'new_royalslider' ),
   //              'desc' => __( '', 'new_royalslider' ),
   //              'type' => 'text',
   //              'default' => '',
   //              'data-type' => 'str',
   //              'size' => 'short',
   //              'ignore' => true
			// ),
			// array(
   //          	'desc' => __( 'Photoset ID', 'new_royalslider' ),
   //          	'delimiter' => true
   //          ),



			array(
				'name' => 'username',
                'label' => __( 'Username', 'new_royalslider' ),
                'desc' => __( '', 'new_royalslider' ),
                'type' => 'text',
                'default' => '',
                'data-type' => 'str',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( '500px username to get photos from', 'new_royalslider' ),
            	'delimiter' => true
            ),

            array(
	            'name' => 'category',
	            'label' => __( 'Category', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
					'' => __('Any Category', 'new_royalslider'),
					'Abstract' => __('Abstract', 'new_royalslider'),
					'Animals' => __('Animals', 'new_royalslider'),
					'Black and White' => __("Black and White", 'new_royalslider'),
					'Celebrities' => __('Celebrities', 'new_royalslider'),
					'City and Architecture' => __('Fresh today', 'new_royalslider'),
					'Commercial' => __('Commercial', 'new_royalslider'),
					'Concert' => __("Concert", 'new_royalslider'),
					'Family' => __("Family", 'new_royalslider'),
					'Fashion' => __("Fashion", 'new_royalslider'),
					'Film' => __("Film", 'new_royalslider'),
					'Fine Art' => __("Fine Art", 'new_royalslider'),
					'Food' => __("Food", 'new_royalslider'),
					'Journalism' => __("Journalism", 'new_royalslider'),
					'Landscapes' => __("Landscapes", 'new_royalslider'),
					'Macro' => __("Macro", 'new_royalslider'),
					'Nature' => __("Nature", 'new_royalslider'),
					'Nude' => __("Nude", 'new_royalslider'),
					'People' => __("People", 'new_royalslider'),
					'Performing Arts' => __("Performing Arts", 'new_royalslider'),
					'Sport' => __("Sport", 'new_royalslider'),
					'Still Life' => __("Still Life", 'new_royalslider'),
					'Street' => __("Street", 'new_royalslider'),
					'Transportation' => __("Transportation", 'new_royalslider'),
					'Travel' => __("Travel", 'new_royalslider'),
					'Underwater' => __("Underwater", 'new_royalslider'),
					'Urban Exploration' => __("Urban Exploration", 'new_royalslider'),
					'Wedding' => __("Wedding", 'new_royalslider')
	            ),
	            'default' => 'image',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'Optional', 'new_royalslider' )
            ),
   //          array(
			// 	'name' => 'Tags',
   //              'label' => __( '', 'new_royalslider' ),
   //              'desc' => __( '', 'new_royalslider' ),
   //              'type' => 'text',
   //              'default' => '',
   //              'data-type' => 'str',
   //              'size' => 'short',
   //              'ignore' => true
			// ),
			// array(
   //          	'desc' => __( 'Comma separated list of tags', 'new_royalslider' ),
   //          	'delimiter' => true
   //          ),
			array(
	            'name' => 'sort',
	            'label' => __( 'Sort by', 'new_royalslider' ),
	            'desc' => __( '', 'new_royalslider' ),
	            'type' => 'select',
	            'data-type' => 'str',
	            'options' => array(
					'' => __('Default', 'new_royalslider'),
					'rating' => __('Rating', 'new_royalslider'),
					'times_viewed' => __('Views', 'new_royalslider'),
					'votes_count' => __('Votes', 'new_royalslider'),
					'created_at' => __('Creation date, newest first.', 'new_royalslider')
					
	            ),
	            'default' => 'image',
	            'ignore' => true
	        ),
			array(
            	'desc' => __( 'Photos sort order', 'new_royalslider' )
            ),
			array(
				'name' => 'rpp',
                'label' => __( 'Maximum items', 'new_royalslider' ),
                'desc' => __( '', 'new_royalslider' ),
                'type' => 'number',
                'default' => '10',
                'data-type' => 'num',
                'size' => 'short',
                'ignore' => true
			),
			array(
            	'desc' => __( 'Maximum number of items to include in slider.', 'new_royalslider' )
            )
		);

		if( isset($curr_options) && isset($curr_options['rs_500px']) ) {
			$opts = $curr_options['rs_500px'];
			$fields = NewRoyalSliderOptions::parseCurrentOptions($fields, $opts);
		}
	
		echo '<div id="rs-500px-options" class="rs-body-options">';
		echo '<h3>' . __('500px Settings', 'new_royalslider') .'</h3>';
		foreach ( $fields as $key => $field ) {
        	echo NewRoyalSliderOptions::get_field_html($field, 'rs_500px');
        }
        echo '</div>';

		
	}
	public static function get_data($slides, $options, $type) {
		if($type === '500px') {
			if(isset($options['rs_500px'])) {

				$options = $options['rs_500px'];

				$data = array(
					'consumer_key' => $options['consumer_key'], 
					'username' => $options['username'], //'MichaelEggers',
					'feature' => $options['feature'],//'user', //user_favorites, user_friends
					'rpp' => $options['rpp'], // max photos to return, max 100
					'only' => $options['category'], // category
					'sort' => $options['sort']
					//'feature' => 'popular'
			    );

		        
		        //https://api.500px.com/v1/photos?
		        //https://api.500px.com/v1/collections/
				$url = 'https://api.500px.com/v1/photos?'.http_build_query($data);
				$url .= '&image_size[]=1&image_size[]=4';
				
				$response = wp_remote_get($url, array(
					'timeout' => 30,
					'redirection' => 5,
					'sslverify' => false
				));

				if (is_wp_error($response)) {
					return print_r($response, true) . ' Request URL: '.$url;;
				}
				
				$response = $response['body'];
				$response = json_decode($response, ARRAY_A);

				$images = array();
				if (isset($response['photos'])) {
					$photos = $response['photos'];
					
					
					foreach($photos as $key => $value) {
						$image = &$images[$key];
						$image['image'] = $value['image_url'][1];
						$image['thumbnail'] = $value['image_url'][0];
						$image['title'] = $value['name'];
						$image['description'] = $value['description'];
					} 
					
				} 
				if( count($images) > 0) {
					return $images;
				} else {
					return sprintf(__(' Unable to fetch data from 500px. %s', 'new_royalslider'), isset($response['error']) ? $response['error'] : '');
				}
			}
		}
		return $slides;
	}

	private function parse_flickr_data() {
		$url = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $this->options['user_id'] . '&format=rss2';
		$feed_data = fetch_feed( $url );
		if( !is_wp_error( $feed_data ) ) {

            $maxitems = $feed_data->get_item_quantity( $this->options['max_items'] );
            $rss_items = $feed_data->get_items( 0, $maxitems );
            
            // Loop through each item to build an array of slides
            $counter = 0;
            foreach( $rss_items as $key => $item ){
            	$image = &$images[$key];

                $image['title'] = $item->get_title();
                $image['description'] = $item->get_enclosure()->description;

                $image['image'] = $item->get_enclosure()->link;
                $image['width'] = $item->get_enclosure()->width;
                $image['height'] = $item->get_enclosure()->height;
                $image['thumbnail'] = $item->get_enclosure()->thumbnails[0];

                $image['link'] = $item->get_permalink();
                $image['author'] = $item->get_enclosure()->credits[0]->name;
            }
        }  
        
		return $images;
	}
	


}



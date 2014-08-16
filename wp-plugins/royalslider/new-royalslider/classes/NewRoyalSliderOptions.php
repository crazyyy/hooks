<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider options, templates and skins are defined here
 */



if ( !class_exists( 'NewRoyalSliderOptions' ) ):
    class NewRoyalSliderOptions {

    	private static $rs_templates;
    	private static $rs_skins;

    	function __construct() {}

        public static function output_options( $options, $type ) {

        	$sections = array(
        		'sopts' => 'Size & scaling',
        		'autoPlay' => 'Autoplay',
        		'fullscreen' => 'Fullscreen',
        		'video' => 'Video',
        		'deeplinking' => 'Deeplinking (permalinks)',
        		'thumbs' => 'Thumbnails, bullets, tabs',
        		'image_generation' => 'Image options',
        		'arrows' => 'Arrows',
        		'caption' => 'Caption',
        		'visibleNearby' => 'Nearby slides',
        		'general' => 'General',
        		'block' => 'Animated Blocks',
        		'misc' => 'Miscellaneous'

        	);
        	$fields = array(
        		'sopts' => array(
		            array(
		                'name' => 'width',
		                'label' => __( 'Width', 'new_royalslider' ),
		                'desc' => __( 'CSS-acceptable slider width. In px or percents. For example: 600px or 100%.', 'new_royalslider' ),
		                'type' => 'text',
		                'default' => '100%',
		                'data-type' => 'str',
		                'size' => 'short',
		                'ignore' => true
		            ),
		            array(
		                'name' => 'height',
		                'label' => __( 'Height', 'new_royalslider' ),
		                'desc' => __( 'CSS-acceptable slider height. In px: For example: 600px. Option is ignored if Auto Scale Slider is enabled.', 'new_royalslider' ),
		                'type' => 'text',
		                'default' => '400px',
		                'data-type' => 'str',
		                'size' => 'short',
		                'ignore' => true,
		                'delimeter' => true
		            ),
		            

		            array(
		                'name' => 'autoScaleSlider',
		                'label' => __( 'Auto scale slider', 'new_royalslider' ),
		                'desc' => __( 'Makes slider scale height based on ratio defined by width & height options below.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => 'sopts-autoScaleSliderWidth sopts-autoScaleSliderHeight',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'autoScaleSliderWidth',
		                'label' => __( 'Base width', 'new_royalslider' ),
		                'desc' => __( 'Based on "Base width" and "Base height", slider will autocalculate sizing ratio. E.g. if you set them both to 500 slider will be square. Only number should be entered, e.g.: 300', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '800',
		                'size' => 'short',
		                'data-type' => 'num'
		            ),
		            array(
		                'name' => 'autoScaleSliderHeight',
		                'label' => __( 'Base height', 'new_royalslider' ),
		                'desc' => __( 'Based on "Base width" and "Base height", slider will autocalculate sizing ratio. E.g. if you set them both to 500 slider will be square. Only number should be entered, e.g.: 300', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '400',
		                'size' => 'short',
		                'data-type' => 'num',
		                'delimeter' => true
		            ),

		            array(
		                'name' => 'autoHeight',
		                'label' => __( 'Auto height', 'new_royalslider' ),
		                'desc' => __( 'Automatically changes slider height based on size of current slide. Option might have conflict with Image Align Center and Image Scale Mode, so disable them if you use auto height.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),

		            array(
		                'name' => 'slidesSpacing',
		                'label' => __( 'Slides spacing', 'new_royalslider' ),
		                'desc' => __( "Spacing between slides in pixels (number)", 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => '8'
		            )
		        ),
'image_generation' => array(

		     
					'imageScaleMode' => array(
		                'name' => 'imageScaleMode',
		                'label' => __( 'Image scale mode', 'new_royalslider' ),
		                'desc' => __( 'Option defines how image inside slider should be resized.', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'default' => 'fit-if-smaller',
		                'options' => array(
		                    'fit-if-smaller' => __('Fit if image is smaller than area', 'new_royalslider'),
		                    'fill' => __('Fill the area', 'new_royalslider'),
		                    'fit' => __('Fit into area', 'new_royalslider'),
		                    'none' => __('No scaling', 'new_royalslider')
		                ),
		                'section' => 'sopts'
		            ),
		            'imageAlignCenter' => array(
		                'name' => 'imageAlignCenter',
		                'label' => __( 'Image align center', 'new_royalslider' ),
		                'desc' => __( 'Aligns main image to center of slide. Option doesn\'t work with "auto height"', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),
		            'imageScalePadding' => array(
		                'name' => 'imageScalePadding',
		                'label' => __( 'Image scale padding', 'new_royalslider' ),
		                'desc' => __( "Minimum distance between image and edge of slide (doesn't work with 'fill' scale mode).", 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => '4',
		                
		                'section' => 'sopts'
		            ),

        			'lazyLoading' =>  array(
		                'name' => 'lazyLoading',
		                'label' => __( 'Lazy-loading', 'new_royalslider' ),
		                'desc' => __( 'Number of slides to preload on each side.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => '',
		                'default' => 'false',
		                'delimeter' => true,
		            ),
        			'imageWidth' => array(
		                'name' => 'imageWidth',
		                'label' => __( 'Main image width', 'new_royalslider' ),
		                'desc' => __( 'Width of main image (number).', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '',
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            'imageHeight' => array(
		                'name' => 'imageHeight',
		                'label' => __( 'Main image height', 'new_royalslider' ),
		                'desc' => __( 'Height of main image (number).', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '',
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            
		            'imageSizesDesc' => array(
		            	'desc' => __( 'Leave fields empty to use default WP image sizes (large & thumb).', 'new_royalslider' )
		            ),

		            'thumbImageWidth' => array(
		                'name' => 'thumbImageWidth',
		                'label' => __( 'Thumb image width', 'new_royalslider' ),
		                'desc' => __( 'Width of thumbnail image (number)', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '',
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            'thumbImageHeight' => array(
		                'name' => 'thumbImageHeight',
		                'label' => __( 'Thumb image height', 'new_royalslider' ),
		                'desc' => __( 'Height of thumbnail image (number)', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '',
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            'thumbImageSizesDesc' => array(
		            	'desc' => __( 'You control here only size of thumb image, not size thumb area.', 'new_royalslider' )
		            )

        		),
				'thumbs' => array(
		            array(
		                'name' => 'controlNavigation',
		                'label' => __( 'Navigation type', 'new_royalslider' ),
		                'desc' => __( 'Tabs can have different size but cannot be scrollable. Thumbnails must have same size.', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'options' => array(
		                	'bullets' => __('Bullets', 'new_royalslider'),
		                	'thumbnails' => __('Thumbnails', 'new_royalslider'),
		                	'tabs' => __('Tabs', 'new_royalslider'),
		                    'none' => __('None', 'new_royalslider'),
		                ),
		                'default' => 'bullets',
		                'section' => 'sopts',
		                'delimeter' => true
		            ),


		             // thumbnails opts
		            array(
		                'name' => 'orientation',
		                'label' => __( 'Thumbs orientation', 'new_royalslider' ),
		                'desc' => __( 'Orientation of thumbnails.', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'options' => array(
		                	'horizontal' => __('Horizontal', 'new_royalslider'),
		                	'vertical' => __('Vertical', 'new_royalslider')
		                ),
		                'section' => 'thumbs',
		                'default' => 'horizontal',
		                'delimeter' => true
		            ),
		            array(
		                'name' => 'spacing',
		                'label' => __( 'Thumbs spacing', 'new_royalslider' ),
		                'desc' => __( 'Spacing between thumbnail items.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'step' => 1,
		                 'section' => 'thumbs',
		                'default' => '4'
		            ),
		            array(
		                'name' => 'paddingTop',
		                'label' => __( 'Top margin', 'new_royalslider' ),
		                'desc' => __( 'Margin top for horizontal thumbnails, margin from left for vertical.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'step' => 1,
		                 'section' => 'thumbs',
		                'default' => '0'
		            ),
		            array(
		                'name' => 'paddingBottom',
		                'label' => __( 'Bottom margin', 'new_royalslider' ),
		                'desc' => __( 'Margin bottom for horizontal thumbnails, margin from right for vertical.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                 'section' => 'thumbs',
		                 'step' => 1,
		                'default' => '0'
		            ),
		            array(
		                'name' => 'firstMargin',
		                'label' => __( 'First thumb distance' , 'new_royalslider' ),
		                'desc' => __( 'Distance from first thumbnail to start of scroller, and from last thumbnail to end fo scroller.', 'new_royalslider' ),
		                'type' => 'number',
		                'section' => 'thumbs',
		                'data-type' => 'num',
		                'default' => '0',
		                'step' => 1,
		                'delimeter' => true
		            ),


		            array(
		                'name' => 'thumbWidth',
		                'label' => __( 'Thumbnail width', 'new_royalslider' ),
		                'desc' => __( 'Width of thumbnail area, should be just number. <br/>Size of thumbnail image can be changed in Image Options. ', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'step' => 1,
		                 'section' => 'thumbs',
		                'default' => '96',
		                'ignore' => true
		            ),
		            array(
		                'name' => 'thumbHeight',
		                'label' => __( 'Thumbnail height', 'new_royalslider' ),
		                'desc' => __( 'Height of thumbnail area, should be just number. <br/>Size of thumbnail image can be changed in Image Options.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                 'section' => 'thumbs',
		                 'step' => 1,
		                'default' => '72',
		                'delimeter' => true,
		                'ignore' => true
		            ),


		            array(
		                'name' => 'arrows',
		                'label' => __( 'Thumbs arrows', 'new_royalslider' ),
		                'desc' => __( 'Thumbnails navigation arrows.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'arrowsAutoHide',
		                'label' => __( 'Auto hide thumbs arrows', 'new_royalslider' ),
		                'desc' => __( 'Auto hide thumbnails navigation arrows on hover', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'autoCenter',
		                'label' => __( 'Auto center thumbs' , 'new_royalslider' ),
		                'desc' => __( 'Automatically centers container with thumbnails if width of them is smaller then width of scroller.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'fitInViewport',
		                'label' => __( 'Fit thumbs in viewport' , 'new_royalslider' ),
		                'desc' => __( 'Reduces size of main slider area by thumbnails width or height, use it when you set 100% width to slider. This option is always true, when slider is in fullscreen mode.' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'transitionSpeed',
		                'label' => __( 'Transition speed', 'new_royalslider' ),
		                'desc' => __( 'Thumbs transition speed.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                 'section' => 'thumbs',
		                'default' => '600'
		            ),
		            array(
		                'name' => 'appendSpan',
		                'label' => __( 'Append span', 'new_royalslider' ),
		                'desc' => __( 'Adds span element to each thumbnail to allow extra styling via CSS (like border).', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'navigation',
		                'label' => __( 'Thumbs navigation', 'new_royalslider' ),
		                'desc' => __( 'Enabled/disables thumbs navigation completely (arrows, touch navigation).', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'section' => 'thumbs',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'thumbContent',
		                'label' => __( 'Thumbs content', 'new_royalslider' ),
		                'desc' => __( 'Option determines what should be put in each thumbnail by default. Please note that not every template and skin supports this option.', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'options' => array(
		                	'image' => __('Image', 'new_royalslider'),
		                	'title' => __('Title', 'new_royalslider')
		                ),
		                'default' => 'image'
		            ),
		            array(
		                'name' => 'drag',
		                'label' => __( 'Mouse drag scrolling', 'new_royalslider' ),
		                'desc' => __( 'Thumbs mouse drag.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                 'section' => 'thumbs',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'touch',
		                'label' => __( 'Touch scrolling', 'new_royalslider' ),
		                'desc' => __( 'Touch scrolling for thumbs.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                 'section' => 'thumbs',
		                'delimeter' => true
		            )
				),

        		
        		
		        
				
				


				'arrows' => array(
					array(
		                'name' => 'arrowsNav',
		                'label' => __( 'Arrows', 'new_royalslider' ),
		                'desc' => __( 'Direction arrows navigation (next/prev). Container of arrows may be controlled in Miscellaneous section, option Controls Inside.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => 'sopts-arrowsNavAutoHide sopts-arrowsNavHideOnTouch',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'arrowsNavAutoHide',
		                'label' => __( 'Auto hide arrows', 'new_royalslider' ),
		                'desc' => __( 'Show and hide arrows on slider hover.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'arrowsNavHideOnTouch',
		                'label' => __( 'Remove on touch devices', 'new_royalslider' ),
		                'desc' => __( 'Removes arrows completely on touch devices.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts',
		                'delimeter' => true
		            )
				),

				'autoPlay' => array(
		            array(
		                'name' => 'enabled',
		                'label' => __( 'Autoplay', 'new_royalslider' ),
		                'desc' => __( 'Automatically animates to next slide after given period of time.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => 'autoPlay-delay autoPlay-stopAtAction autoPlay-pauseOnHover',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'delay',
		                'label' => __( 'Delay', 'new_royalslider' ),
		                'desc' => __( 'Delay between movement in milliseconds (1sec=1000ms).', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => '2000',
		                'size' => 'short',
		                'data-type' => 'num'
		            ),
		            array(
		                'name' => 'stopAtAction',
		                'label' => __( 'Stop at action', 'new_royalslider' ),
		                'desc' => __( 'Stop autoplay at first user action.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            ),
		            array(
		                'name' => 'pauseOnHover',
		                'label' => __( 'Pause on hover', 'new_royalslider' ),
		                'desc' => __( 'Pause autoplay when user hovers over slider.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            )
		        ),
				'fullscreen' => array(
		            array(
		                'name' => 'enabled',
		                'label' => __( 'Fullscreen', 'new_royalslider' ),
		                'desc' => __( 'Adds ability to open slider in fullscreen.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => 'fullscreen-nativeFS fullscreen-buttonFS fullscreen-keyboardNav',
		                'default' => 'false'
		            ),

		            array(
		                'name' => 'nativeFS',
		                'label' => __( 'Use native', 'new_royalslider' ),
		                'desc' => __( 'Opens native browser fullscreen (if supported).', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),

		            array(
		                'name' => 'buttonFS',
		                'label' => __( 'Toggle button', 'new_royalslider' ),
		                'desc' => __( 'Adds toggle fullscreen button' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            ),

		            array(
		                'name' => 'keyboardNav',
		                'label' => __( 'Keyboard navigation
		                	', 'new_royalslider' ),
		                'desc' => __( 'Force keyboard arrows nav in fullscreen mode.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            )
				),
'caption' => array(
        			array(
		                'name' => 'globalCaption',
		                'label' => __( 'Caption', 'new_royalslider' ),
		                'desc' => __( 'Enables global caption.' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'child-opts' => 'sopts-globalCaptionInside',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'globalCaptionInside',
		                'label' => __( 'Place inside wrapper', 'new_royalslider' ),
		                'desc' => __( 'Placement of caption. If checked puts caption inside image container, otherwise in root slider container (that usually contains thumbnails and other controls).' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts',
		                'delimeter' => 'true'
		            )
        		),
        		'visibleNearby' => array(
        			array(
		                'name' => 'enabled',
		                'label' => __( 'Nearby slides', 'new_royalslider' ),
		                'desc' => __( 'Makes nearby slides visible.' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'child-opts' => 'visibleNearby-hiddenOverflow visibleNearby-centerArea visibleNearby-center visibleNearby-breakpoint visibleNearby-breakpointCenterArea visibleNearby-navigateByCenterClick',
		                'section' => 'visibleNearby'
		            ),
		            array(
		                'name' => 'hiddenOverflow',
		                'label' => __( 'Crop slides', 'new_royalslider' ),
		                'desc' => __( 'Crops slider area, uncheck to reveal nearby slides even more.' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'visibleNearby',
		            ),

		           
					array(
		                'name' => 'centerArea',
		                'label' => __( 'Center area ratio', 'new_royalslider' ),
		                'desc' => __( 'Option determines size of main slider area. \'0.8\' means that center slide will fill 80% of slide space.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => 0.6,
		                'step' => 0.1,
		                'min' => 0,
		                'max' => 1,
		                'section' => 'visibleNearby'
		            ),
					 array(
		                'name' => 'center',
		                'label' => __( 'Center curr slide', 'new_royalslider' ),
		                'desc' => __( 'If true, current slide is aligned to center, otherwise it\'s aligned to left side.' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'visibleNearby'
		            ),
					array(
		                'name' => 'breakpoint',
		                'label' => __( 'Breakpont', 'new_royalslider' ),
		                'desc' => __( 'Used for responsive design. Changes \'center area\' value to value that is set below when width of slider is less then value in this option. Set to 0 to disable. Should be number.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => 0,
		                'step' => 25
		            ),
		            array(
		                'name' => 'breakpointCenterArea',
		                'label' => __( 'BP-center area ratio', 'new_royalslider' ),
		                'desc' => __( 'Same as center area option, just for breakpoint.', 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => 0.8,
		                'step' => 0.1,
		                'min' => 0,
		                'max' => 1,
		                'section' => 'visibleNearby'
		            ),
		            array(
		                'name' => 'navigateByCenterClick',
		                'label' => __( 'Navigate by center click', 'new_royalslider' ),
		                'desc' => __( 'If disabled  - prevents navigation to next slide by clicking on current slide (requires \'Other -> navigate by click\ option to be enabled).', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'visibleNearby',
		                'delimeter' => true
		            )
        		),

				'video' => array(
		            array(
		                'name' => 'autoHideArrows',
		                'label' => __( 'Auto hide arrows', 'new_royalslider' ),
		                'desc' => __( 'Auto hide arrows when video is playing.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            ),
					array(
		                'name' => 'autoHideControlNav',
		                'label' => __( 'Auto hide navigation', 'new_royalslider' ),
		                'desc' => __( 'Auto hide navigation when video is playing.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),

		            array(
		                'name' => 'autoHideBlocks',
		                'label' => __( 'Auto hide blocks', 'new_royalslider' ),
		                'desc' => __( 'Auto hide animated blocks when video is playing.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),

		            array(
		                'name' => 'autoHideCaption',
		                'label' => __( 'Auto hide caption', 'new_royalslider' ),
		                'desc' => __( 'Auto hide global caption when video is playing.', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),



		            array(
		                'name' => 'youTubeCode',
		                'label' => __( 'YouTube code', 'new_royalslider' ),
		                'desc' => __( 'YouTube embed code. %id% is replaced by video id.' , 'new_royalslider'),
		                'type' => 'textarea',
		                'data-type' => 'str',
		                'default' => htmlentities('<iframe src="http://www.youtube.com/embed/%id%?rel=1&autoplay=1&showinfo=0" frameborder="no"></iframe>')
		            ),
		            array(
		                'name' => 'vimeoCode',
		                'label' => __( 'Vimeo code', 'new_royalslider' ),
		                'desc' => __( 'Vimeo embed code. %id% is replaced by video id.', 'new_royalslider' ),
		                'type' => 'textarea',
		                'data-type' => 'str',
		                'default' => htmlentities('<iframe src="http://player.vimeo.com/video/%id%?byline=0&amp;portrait=0&amp;autoplay=1" frameborder="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>')
		                ,
		                'delimeter' => true
		            )
		        ),
				
				'deeplinking' => array(
		            array(
		                'name' => 'enabled',
		                'label' => __( 'Deeplinking', 'new_royalslider' ),
		                'desc' => __( 'Enables linking to slides by adding #prefix-SLIDE_INDEX to url.' , 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'child-opts' => 'deeplinking-change deeplinking-prefix',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'change',
		                'label' => __( 'Listen for change', 'new_royalslider' ),
		                'desc' => __( 'Automatically change URL after transition and listen for dynamic URL change. Important note:  browser history will be filled every time when slide is changed.', 'new_royalslider'),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false'
		            ),
		            array(
		                'name' => 'prefix',

		                'label' => __( 'Hash prefix', 'new_royalslider' ),
		                'desc' => __( "Prefix that will be added to url. For example if you set it to 'gallery-', hash would look like this: #gallery-2 (for second slide)", 'new_royalslider' ),
		                'type' => 'text',
		                'data-type' => 'str',
		                'delimeter' => true,
		                'default' => ''
		            )
				),
				
				'misc' => array(
					// transition
		            array(
		                'name' => 'transitionType',
		                'label' => __( 'Transition type', 'new_royalslider' ),
		                'desc' => __( "", 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'default' => 'move',
		                'options' => array(
		                    'move' => __('Move', 'new_royalslider'),
		                    'fade' => __('Fade', 'new_royalslider')
		                ),
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'transitionSpeed',
		                'label' => __( 'Transition speed', 'new_royalslider' ),
		                'desc' => __( "", 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => '600',
		                'section' => 'sopts'
		            ),

		            array(
		                'name' => 'slidesOrientation',
		                'label' => __( 'Slides orientation', 'new_royalslider' ),
		                'desc' => __( "Direction of move animation and placement of arrows.", 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'default' => 'horizontal',
		                'options' => array(
		                    'horizontal' => __('Horizontal', 'new_royalslider'),
		                    'vertical' => __('Vertical', 'new_royalslider')
		                ),
		                'delimeter' => true,
		                'section' => 'sopts'
		            ),

					 array(
		                'name' => 'startSlideId',
		                'label' => __( 'Start slide id', 'new_royalslider' ),
		                'desc' => __( "Start slide index, starting at zero.", 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => 'false',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'loop',
		                'label' => __( 'Continuous loop', 'new_royalslider' ),
		                'desc' => __( "Continuous looping of slides. It's not recommended to enable this option if you have low number of slides (<4). If there are just two slides, loopRewind option will be forced.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'loopRewind',
		                'label' => __( 'Loop rewind', 'new_royalslider' ),
		                'desc' => __( "Goes from last slide to first with rewind animation (if move transition is used and continuous loop option is disabled).", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'randomizeSlides',
		                'label' => __( 'Randomize slides', 'new_royalslider' ),
		                'desc' => __( "Randomizes slides order every time page refreshes.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'numImagesToPreload',
		                'label' => __( 'Slides to preload', 'new_royalslider' ),
		                'desc' => __( "Number of slides (images) to preload on each side. If you set it to 4, slider will load current image, next four and previous four. \n", 'new_royalslider' ),
		                'type' => 'number',
		                'data-type' => 'num',
		                'default' => '4',
		                'delimeter' => true,
		                'section' => 'sopts'
		            ),

		            array(
		                'name' => 'sliderDrag',
		                'label' => __( 'Mouse drag', 'new_royalslider' ),
		                'desc' => __( "Mouse drag navigation over slider.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'sliderTouch',
		                'label' => __( 'Touch drag', 'new_royalslider' ),
		                'desc' => __( "Touch navigation over slider", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),

		            array(
		                'name' => 'keyboardNavEnabled',
		                'label' => __( 'Keyboard nav', 'new_royalslider' ),
		                'desc' => __( "Keyboard arrows navigation", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'navigateByClick',
		                'label' => __( 'Navigate by click', 'new_royalslider' ),
		                'desc' => __( "Navigate to next slide by clicking on current slide.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),

		            array(
		                'name' => 'fadeinLoadedSlide',
		                'label' => __( 'Fade in loaded slide', 'new_royalslider' ),
		                'desc' => __( "Fades in slide after it's loaded.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),

		            
		            array(
		                'name' => 'controlsInside',
		                'label' => __( 'Controls inside', 'new_royalslider' ),
		                'desc' => __( "If enabled adds arrows and fullscreen button inside rsOverflow container, otherwise inside root slider container.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),

		            array(
		                'name' => 'allowCSS3',
		                'label' => __( 'CSS3 animation', 'new_royalslider' ),
		                'desc' => __( "Allows usage of CSS3 transitions. Might be useful if you're experiencing some CSS3-related bugs.
", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true',
		                'section' => 'sopts'
		            ),
		            array(
		                'name' => 'addActiveClass',
		                'label' => __( 'Current CSS class', 'new_royalslider' ),
		                'desc' => __( "Adds rsActiveSlide CSS class to current slide before transition. It's recommended to disable this option if it's not required.", 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'false',
		                'section' => 'sopts'
		            )
				)

		    ); 


			if($type == 'custom') {
				$fields['block'] = array(
					array(
		            	'desc' => __( 'These are default options for animated block. "Use block animation option" option in block editor should be cheked to enable animation.', 'new_royalslider' )
		            ),

					array(
		                'name' => 'fadeEffect',
		                'label' => __( 'Fade effect', 'new_royalslider' ),
		                'desc' => __( 'Fade in animation for block', 'new_royalslider' ),
		                'type' => 'checkbox',
		                'data-type' => 'bool',
		                'default' => 'true'
		            ),

		            array(
		                'name' => 'moveEffect',
		                'label' => __( 'Move effect', 'new_royalslider' ),
		                'desc' => __( 'Moving effect of animated block', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'options' => array(
		                	'none' => __('None', 'new_royalslider'),
		                	'left' => __('From left', 'new_royalslider'),
		                	'right' => __('From right', 'new_royalslider'),
		                	'top' => __('From top', 'new_royalslider'),
		                	'bottom' => __('From bottom', 'new_royalslider')
		                ),
		                'default' => 'top'
		            ),

		            array(
		                'name' => 'moveOffset',
		                'label' => __( 'Move offset', 'new_royalslider' ),
		                'desc' => __( 'Distance for move effect in pixels. (number)', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => 20,
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            array(
		                'name' => 'speed',
		                'label' => __( 'Speed', 'new_royalslider' ),
		                'desc' => __( 'Transition speed of block in milliseconds.', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => 400,
		                'data-type' => 'num',
		                'ignore' => true
		            ),
		            array(
		                'name' => 'delay',
		                'label' => __( 'Delay', 'new_royalslider' ),
		                'desc' => __( 'Delay between each block shows up in milliseconds.', 'new_royalslider' ),
		                'type' => 'number',
		                'default' => 200,
		                'data-type' => 'num',
		                'ignore' => true
		            ),

		             array(
		                'name' => 'easing',
		                'label' => __( 'Easing', 'new_royalslider' ),
		                'desc' => __( 'Easing function of block animation.', 'new_royalslider' ),
		                'type' => 'select',
		                'data-type' => 'str',
		                'options' => array(
		                	'easeOutSine' => __('easeOutSine', 'new_royalslider'),
		                	'easeInOutSine' => __('easeInOutSine', 'new_royalslider')
		                ),
		                'default' => 'easeOutSine'
		            )

		        );
			}
			if($type == '500px' || $type== 'flickr') {
				//$fields['image_generation'][4] 
				unset(
					$fields['image_generation']['imageWidth'],
					$fields['image_generation']['imageHeight'],
					$fields['image_generation']['imageSizesDesc'],
					$fields['image_generation']['thumbImageSizesDesc'],
					$fields['image_generation']['thumbImageWidth'],
					$fields['image_generation']['thumbImageHeight']
				);

				
			}

			if( isset($options) ) {
				

				if( is_array($options) ) {
					if( isset($options['options']) ) {
						$osections = $options['options'];
					} else {
						$osections = false;
					}
					
				} else {
					$osections = json_decode($options, true);
				}
				

				if($osections) {
					foreach ( $fields as $section => $field ) {
		        		foreach ( $field as $key => $option ) {
		        			if(isset($option['section'])) {
				        		$group_name = $option['section'];
				        	} else {
				        		$group_name = $section;
				        	}
				        	if( !isset($option['name']) ) {
				        		continue;
				        	}
				        	$oname = $option['name'];
		        			if(isset($osections[$group_name][$oname]) ) {
		        				$fields[$section][$key]['value'] = $osections[$group_name][$oname];
		        			}
		        		}
		        	}
				}
        	}
        	
            self::parse_fields_data($fields, $sections);
        }
        public static function parseCurrentOptions($fields, $the_opts) {
        	if($the_opts) {
        		foreach ( $fields as $key => $option ) {
		        	if( !isset($option['name']) ) {
		        		continue;
		        	}
		        	$oname = $option['name'];
	    			if(isset($the_opts[$oname]) ) {
	    				$fields[$key]['value'] = $the_opts[$oname];
	    			}
	    		}
        	}
        	return $fields;
        }
        public static function getRsTemplates() {
        	if(self::$rs_templates) {
        		return self::$rs_templates;
        	}
	        self::$rs_templates = array(
	        	'default' => array(
	            	'label' => __('Default', 'new_royalslider'),
	            	'template-css-class' => 'rs-default-template',
	            	'b-pos' => '0 -100px',
	            	'options' => array(
						"sopts" => array(
							"width" => "100%",
							"height" => "500",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "750"
						)
					),
	            	'template-html' => 
'<div class="rsContent">
  {{image_tag}}
  {{thumbnail}}
  {{html}}
  {{animated_blocks}}
  {{#link_url}}
  <a class="rsLink" href="{{link_url}}">{{title}}</a>
  {{/link_url}}
</div>'
	            ),
	            'gallery' => array(
	    			'label' => __('Image Gallery', 'new_royalslider'),
	    			'template-css-class' => 'rs-image-gallery',
	    			'b-pos' => '-133px -100px',
	    			'options' => array(
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "thumbnails",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "850",
							"loop" => "false",
							"numImagesToPreload" => "4",
							"arrowsNavAutohide" => "true",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"fadeinLoadedSlide" => "false",
							"globalCaptionInside" => "true"

						),
						"fullscreen" => array(
							"enabled" => "true",
							"nativeFS" => "true"
						),
						"image_generation" => array(
							"lazyLoading" => "true",
							"thumbImageWidth" => 96,
							"thumbImageHeight" => 72
						),
						'thumbs' => array(
							'paddingBottom' => 4,
							'appendSpan' => 'true'
						)

	    			)
	    		),

				'gallery_vertical_fade' => array(
	    			'label' => __('Gallery (vertical + fade)', 'new_royalslider'),
	    			'template-css-class' => 'rs-gallery-vertical-fade',
	    			'b-pos' => '0 -165px',
	    			'options' => array(
	    				
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "thumbnails",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "600",
							"loop" => "true",
							"numImagesToPreload" => "4",
							"arrowsNav" => "false",
							"arrowsNavAutohide" => "true",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"fadeinLoadedSlide" => "false",
							"transitionType" => "fade",
							"globalCaptionInside" => "true"
						),
						"fullscreen" => array(
							"enabled" => "true",
							"nativeFS" => "true"
						),
						"thumbs" => array(
							"orientation" => "vertical",
							"paddingBottom" => 4,
							"appendSpan" => "true"
						),
						"image_generation" => array(
							"lazyLoading" => "true",
							"thumbImageWidth" => 96,
							"thumbImageHeight" => 72
						)

	    			)

	    		),


	            'content_slider' => array(
	    			'label' => __('Content slider with tabs', 'new_royalslider'),
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-content-slider-template.css',
	    			'template-css-class' => 'rsContentSlider',
	    			'b-pos' => '-133px -165px',
	    			'template-html' => 
'<div>
  {{image_tag}}
  {{thumbnail}}
  {{html}}
  <h3>{{title}}</h3>
  <p>{{description}}</p>
  {{#link_url}}
  <a href="{{link_url}}">{{title}}</a>
  {{/link_url}}
  {{animated_blocks}}
</div>',
	    			'options' => array(
	    				
						"sopts" => array(
							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "tabs",
							"autoScaleSlider" => "false", 

							"loop" => "false",
							"numImagesToPreload" => "4",

							"autoHeight" => "true",

							"arrowsNav" => "true",
							"arrowsNavAutohide" => "false",
							"arrowsNavHideOnTouch" => "false",

							"keyboardNavEnabled" => "true",
							"transitionType" => "move",
							"fadeinLoadedSlide" => "false",
							"imageScaleMode" => "none",
							"imageAlignCenter" => "false",
							"globalCaptionInside" => "true"
						)
	    			)

	    		),

	            'simple_vertical' => array(
	    			'label' => __('Simple vertical slider', 'new_royalslider'),
	    			'template-css-class' => 'rs-simple-vertical',
	    			'b-pos' => '0 -230px',
	    			'options' => array(
	    				
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "none",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "850",
							"loop" => "false",
							"loopRewind" => "false",
							"numImagesToPreload" => "4",
							"arrowsNav" => "true",
							"arrowsNavAutoHide" => "false",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"transitionType" => "move",
							"slidesOrientation" => "vertical",
							"fadeinLoadedSlide" => "true",
							"imageScaleMode" => "fill",
							"globalCaptionInside" => "true"
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false"
						),
						"image_generation" => array(
							"lazyLoading" => "true",
							"thumbImageWidth" => 96,
							"thumbImageHeight" => 72
						)
	    			)

	    		),
				'gallery_with_thumbs_text' => array(
	    			'label' => __('Gallery with text thumbnails', 'new_royalslider'),
	    			'template-css-class' => 'galleryTextThumbs',
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-gallery-with-text-thumbs.css',
	    			'b-pos' => '-133px -230px',
	    			'template-html' => 
'<div class="rsContent">
  {{image_tag}}
  {{html}}
  <div class="rsTmb">
    <h5>{{title}}</h5>
    <span>{{description}}</span>
  </div>
  {{animated_blocks}}
  {{#link_url}}
  <a class="rsLink" href="{{link_url}}">{{title}}</a>
  {{/link_url}}
</div>',
	    			'options' => array(
	    				
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "thumbnails",
							"controlNavigationSpacing" => "0",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "950",     
							"autoScaleSliderHeight" => "400",
							"loop" => "false",
							"numImagesToPreload" => "3",
							"arrowsNav" => "false",
							"arrowsNavAutohide" => "true",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"fadeinLoadedSlide" => "true",
							"transitionType" => "move",
							"globalCaptionInside" => "true"
						),
						"thumbs" => array(
							"orientation" => "vertical",
							"paddingBottom" => 0,
							"spacing" => 0,
							"appendSpan" => "false",
							"autoCenter" => "false",
							"thumbWidth" => 220,
							"thumbHeight" => 80
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false",
							"autoHideBlocks" => "true"
						),
						"image_generation" => array(
							"lazyLoading" => "true"
						)

	    			)

	    		),

				'visible_nearby_zoom' => array(
					'label' => __('Visible nearby with zoom', 'new_royalslider'),
	    			'template-css-class' => 'visibleNearbyZoom',
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-visible-nearby-zoom.css',
	    			'b-pos' => '0 -295px',
	    			'template-html' => 
'<div class="rsContent">
  {{image_tag}}
  {{html}}
  <div class="rsCaption">
    <h5>{{title}}</h5>
    <span>{{description}}</span>
  </div>
  {{thumbnail}}
  {{animated_blocks}}
  {{#link_url}}
  <a class="rsLink" href="{{link_url}}">{{title}}</a>
  {{/link_url}}
</div>',
	    			'options' => array(
						"sopts" => array(
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "340",
							"loop" => "true",
							"arrowsNav" => "false",
							"globalCaption" => "true",
							"globalCaptionInside" => "false",
							"keyboardNavEnabled" => "true",
							"addActiveClass" => "true",
							"fadeinLoadedSlide" => "false",
							"transitionType" => "move",
							"controlNavigation" => "none"
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false",
							"autoHideBlocks" => "true"
						),
						"visibleNearby" => array(
							"enabled" => "true",
							"centerArea" => 0.5,
							"center" => "true",
							"breakpoint" => 650,
						    "breakpointCenterArea" => 0.64,
						    "navigateByCenterClick" => "true"



						)
	    			)
				),
				'visible_nearby_simple' => array(
					'label' => __('Visible nearby', 'new_royalslider'),
	    			'template-css-class' => 'visibleNearbySimple',
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-visible-nearby-simple.css',
	    			'b-pos' => '-133px -295px',
	    			'options' => array(
						"sopts" => array(
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "500",
							"loop" => "true",
							"arrowsNav" => "true",
							"arrowsNavAutoHide" => "false",
							"globalCaption" => "false",
							"globalCaptionInside" => "false",
							"keyboardNavEnabled" => "true",
							"addActiveClass" => "true",
							"fadeinLoadedSlide" => "false",
							"transitionType" => "move",
							"controlNavigation" => "bullets",
							"slidesSpacing" => 0,
							"imageScaleMode" => 'fill'
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false",
							"autoHideBlocks" => "true"
						),
						"visibleNearby" => array(
							"enabled" => "true",
							"centerArea" => 0.7,
							"center" => "true",
							"breakpoint" => 400,
						    "breakpointCenterArea" => 0.9,
						    "navigateByCenterClick" => "true"



						)
	    			)
				),



				'gallery_thumbs_grid' => array(
	    			'label' => __('Gallery with grid of thumbs', 'new_royalslider'),
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-gallery-thumbs-grid-template.css',
	    			'template-css-class' => 'rs-gallery-thumbs-grid',
	    			'add_js' =>
'
$(window).resize(function() {
	if($(window).width() < 760) {
	  $(\'{{selector}}\').data(\'royalSlider\').st.thumbs.fitInViewport = false;
	} else {
	  $(\'{{selector}}\').data(\'royalSlider\').st.thumbs.fitInViewport = true;
	}
}).triggerHandler(\'resize\');
$(\'{{selector}}\').data(\'royalSlider\').updateSliderSize(true);',
	    			'b-pos' => '0 -360px',
	    			'options' => array(
	    				
						"sopts" => array(
							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "thumbnails",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "960",     
							"autoScaleSliderHeight" => "600",
							"loop" => "true",
							"numImagesToPreload" => "2",

							"arrowsNav" => "false",
							"arrowsNavAutohide" => "false",
							"arrowsNavHideOnTouch" => "false",

							"keyboardNavEnabled" => "true",
							"transitionType" => "fade",
							"slidesOrientation" => "vertical",
							"fadeinLoadedSlide" => "true",
							"imageScaleMode" => "fill",
							"globalCaptionInside" => "true"

						),

						"thumbs" => array(
							"orientation" => "vertical",
							"navigation" => "false",
							"fitInViewport" => "true",
							"spacing" => "1",
							"autoCenter" => "false",
							"appendSpan" => "true",
							'thumbWidth' => 56,
							'thumbHeight' => 56
						),
						"deeplinking" => array(
							"enabled" => "true",
							"change" => "true",
							"prefix" => "image-"
						),
						"image_generation" => array(
							"lazyLoading" => "true",
							"thumbImageWidth" => 56,
							"thumbImageHeight" => 56
						)
	    			)

	    		),
				
				'slider_rs_home' => array(
	    			'label' => __('Gallery with text thumbnails', 'new_royalslider'),
	    			'template-css-class' => 'rsHomeTempl',
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-home-template.css',
	    			'b-pos' => '-133px -360px',
	    			'options' => array(
	    				
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "thumbnails",
							"controlNavigationSpacing" => "0",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "950",     
							"autoScaleSliderHeight" => "600",
							"loop" => "false",
							"numImagesToPreload" => "3",
							"arrowsNav" => "false",
							"arrowsNavAutohide" => "true",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"fadeinLoadedSlide" => "true",
							"transitionType" => "move",
							"imageScaleMode" => "fill",
							"globalCaptionInside" => "true"
						),
						"thumbs" => array(
							"orientation" => "horizontal",
							"paddingBottom" => 0,
							"spacing" => 0,
							"thumbContent" => 'title',
							"appendSpan" => "false",
							"autoCenter" => "true"
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false",
							"autoHideBlocks" => "true"
						),
						"image_generation" => array(
							"lazyLoading" => "true"
						)

	    			)

	    		),

	    		'slider_in_laptop' => array(
	    			'label' => __('Simple vertical slider', 'new_royalslider'),
	    			'template-css-class' => 'rsInLaptop',
	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-in-laptop-template.css',
	    			'wrapHTML' => array(
	    				'before' => '<div class="rsInLaptopContainer" style="width:%width%;"><img class="rsInLaptopImgBg" src="'.NEW_ROYALSLIDER_PLUGIN_URL . 'lib/royalslider/templates-css/laptop.png' .'" />',
	    				'after' => '</div>'
	    			),



	    			'options' => array(
	    				
						"sopts" => array(

							"width" => "100%",
							"height" => "500",

							"controlNavigation" => "bullets",
							"autoScaleSlider" => "true", 
						    "autoScaleSliderWidth" => "486",     
							"autoScaleSliderHeight" => "315",
							"loop" => "false",
							"loopRwind" => "false",
							"numImagesToPreload" => "4",
							"arrowsNav" => "false",
							"arrowsNavAutohide" => "true",
							"arrowsNavHideOnTouch" => "true",
							"keyboardNavEnabled" => "true",
							"transitionType" => "move",
							"slidesOrientation" => "horizontal",
							"fadeinLoadedSlide" => "true",
							"imageScaleMode" => "fill",
							"globalCaptionInside" => "true"
						),
						"video" => array(
							"autoHideArrows" => "true",
							"autoHideControlNav" => "false"
						),
						"image_generation" => array(
							"lazyLoading" => "true",
							"thumbImageWidth" => 96,
							"thumbImageHeight" => 72
						)
	    			)
	    		)
// TODO
// 				'horizontal_content_slider' => array(
// 	    			'label' => __('Posts content slider', 'new_royalslider'),
// 	    			'template-css' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'templates-css/rs-hor-content-slider.css',
// 	    			'template-css-class' => 'rsHorContentSlider',
// 	    			'b-pos' => '-133px -165px',
// 	    			'template-html' => 
// '<div>
//   <div class="rs-left-img">
//   	{{image_tag}}
//   </div>
//   <div class="rs-right-details">
//   	<h3>{{title}}</h3>
//   	<p>{{description}}</p>
//   	{{#link_url}}
//   	  <a href="{{link_url}}">{{title}}</a>
//   	{{/link_url}}
//   </div>
  

//   {{thumbnail}}
//   {{html}}
//   {{animated_blocks}}
// </div>',
// 	    			'options' => array(
	    				
// 						"sopts" => array(
// 							"width" => "100%",
// 							"autoScaleSlider" => "true", 
// 						    "autoScaleSliderWidth" => "800",     
// 							"autoScaleSliderHeight" => "350",

 							
// 							"controlNavigation" => "bullets",

// 							"loop" => "false",
// 							"numImagesToPreload" => "4",

// 							"autoHeight" => "true",

// 							"arrowsNav" => "true",
// 							"arrowsNavAutohide" => "false",
// 							"arrowsNavHideOnTouch" => "false",

// 							"keyboardNavEnabled" => "true",
// 							"transitionType" => "move",
// 							"fadeinLoadedSlide" => "false",
// 							"imageScaleMode" => "none",
// 							"imageAlignCenter" => "false",
// 							"globalCaptionInside" => "true"
// 						),
// 						"image_generation" => array(
// 							"imageWidth" => '330',
//  							"imageHeight" => '250'
// 						)
// 	    			)

// 	    		)
	    	);
			self::$rs_templates = apply_filters('new_royalslider_templates', self::$rs_templates);
    		return self::$rs_templates;
    		
    	}
    	static function getRsSkins() {
    		if(self::$rs_skins) {
        		return self::$rs_skins;
        	}

        	self::$rs_skins = array(
        		"rsUni" => array(
        			'label' => 'Universal',
        			'path' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'skins/universal/rs-universal.css'
        		),
        		"rsDefault" => array(
        			'label' => 'Dark-default',
        			'path' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'skins/default/rs-default.css'
        		),
        		"rsDefaultInv" => array(
        			'label' => 'Light',
        			'path' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'skins/default-inverted/rs-default-inverted.css'
        		),
        		"rsMinW" => array(
        			'label' => 'With controls in corner',
        			'path' => NEW_ROYALSLIDER_PLUGIN_URL .'lib/royalslider/' . 'skins/minimal-white/rs-minimal-white.css'
        		)
        		
        	);
        	self::$rs_skins = apply_filters('new_royalslider_skins', self::$rs_skins);
    		return self::$rs_skins;
    	}






        static function parse_fields_data( $data, $sections ) {
        	foreach ( $data as $section => $field ) {
        		?>
        		<div class="postbox closed">	
					<div class="handlediv" title="Toggle view"></div>			
					<h3 class="hndle">
						<?php echo $sections[$section]; ?>
					</h3> 
					<div class="inside slider-opts-group">
        		<?php
        		foreach ( $field as $option ) {
        			echo self::get_field_html($option, $section);
        		}
        		?>
        		</div>
        		</div>
        		<?php
	        }
        }
        static function get_field_html( $args, $group_name ) {
        	$out = '';

        	if( !isset($args['name']) ) {
        		return '<p class="info-text">'.$args['desc'].'</p>';
        	}


        	$type = $args['type'];
        	if(isset($args['section'])) {
        		$group_name = $args['section'];
        	}
        	if( isset($args['value']) ) {
        		$value = $args['value'];
        	} else {
        		$value = isset($args['default']) ? $args['default'] : '';
        	}
        	
        	$id = $args['name'];
        	if( !isset($args['size']) ) {
        		$size = 'regular';
        	} else {
        		$size = $args['size'];
        	}
        	$custom_attrs = '';
        	if(isset($args['child-opts'])) {
        		$custom_attrs .= ' data-child-opts="'.esc_attr($args['child-opts']).'"';
        	}
        	if(isset($args['default']) && !isset($args['ignore']) ) {
        		$custom_attrs .= ' data-default="'.esc_attr($args['default']).'"';
        	}
        	if(isset($args['data-type'])) {
        		$custom_attrs .= ' data-type="'.$args['data-type'].'"';
        	}
        	

        	if($type != 'hidden') {
        		$args['desc'] = esc_attr($args['desc']);
        		$out .= '<div data-help="'.esc_attr($args['desc']).'" class="rs-opt'. ($type === 'checkbox' ?  ' rs-checkbox-opt' : '') .'" '.$custom_attrs.'>';
        	}
        	

 			if($type != 'checkbox' && isset($args['label']) ) {
 				$out .= sprintf( '<label class="rs-label" for="%1$s-%2$s">%3$s</label>', $group_name, $id ,$args['label'] );
 			}

 			$class_name = $group_name .'-'. $id;
 			$class_name = str_replace(']', '', $class_name);
			$class_name = str_replace('[', '-', $class_name);
			$class_name .= '-c';
			$class_name = str_replace('--', '-', $class_name);
 			
	        switch( $type ) {

	        	case "checkbox": 
	        		$out .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s-%2$s" name="%1$s[%2$s]" value="1" %4$s />', $group_name, $id, $value, checked( $value, 'true', false ) );
        			$out .= sprintf( '<label for="%1$s-%2$s"> %3$s</label>', $group_name, $id, $args['label'] );
	        	break;

	        	case "text": 
	        		$out .= sprintf( '<input type="text" class="%1$s-text %2$s" id="%3$s-%4$s" name="%3$s[%4$s]" value="%5$s"/>', $size, $class_name ,$group_name, $id, esc_attr($value) );
	        	break;

	        	case "textarea": 
	        		$out .= sprintf( '<textarea rows="5" cols="55" class="%1$s-text  %2$s" id="%3$s-%4$s" name="%3$s[%4$s]">%5$s</textarea>', $size,  $class_name, $group_name, $id, $value );
	        	break;

	        	case "number": 
	        		$out .= sprintf( '<input type="number" class="%1$s-text" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s" step="%5$s" min="%6$s" max="%7$s"/>', $size, $group_name, $id, $value,  isset($args['step']) ? $args['step'] : 10, isset($args['min']) ? $args['min'] : 0, isset($args['max']) ? $args['max'] : '' );
	        	break;

	        	case "select":
	        		$out .= sprintf( '<select name="%1$s[%2$s]" id="%1$s-%2$s">', $group_name, $id );

	        		foreach ( $args['options'] as $key => $label ) {
			            $out .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			        }
			        $out .= sprintf( '</select>' );
			    break;

			    case "radio":
			    	$out .= '<div class="radio-group">';
	        		foreach ( $args['options'] as $key => $label ) {
	        			$out .= sprintf( '<label>', $group_name, $id );
			           $out .= sprintf( '<input type="radio"  id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s"%4$s />', $group_name, $id, $key, checked( $value, $key, false ) );
        				$out .= sprintf( '%1$s</label>', $label );
			        }
			        $out .= '</div>';
			    break;


	            case "hidden":
	                $out .= sprintf( '<input type="hidden" id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s" class="%4$s"/>', $group_name, $id, $value, $class_name );
	            break;

	            default:
	            	$out .= $id;
	            break;
	        }
	        if($type != 'hidden') {
	        	$out .= '</div>';
	        }
	        if( isset($args['delimeter']) ) {
        		$out.= '<hr/>';
        	}
	        return $out;
        }

    }
endif;
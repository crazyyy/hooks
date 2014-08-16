<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderDefaultRenderer {
    private $data;

    public $options;
    public $image_data;
    public $thumb_image_data;
    public $full_img_url;

    public $attachment_id;
    public $has_video;

    function __construct( $data,  $slider_opts, $options ) {
        $this->data = $data;
        $this->slider_opts = $slider_opts;
        $this->options = $options;

        if( isset($this->data['image']) ) {
            if( isset($this->data['image']['attachment_id']) ) {
                $this->attachment_id = $this->data['image']['attachment_id'];
            }
        }
        $this->has_video = isset($this->data['video']);
        }
        public function image() {
        return print_r($this->attachment_id, true);
    }
    public function getImageData($isThumb = false) {
        return NewRoyalSliderGenerator::get_image_data($this, $isThumb);
    }
    public function large_image_url() {
        if(!$this->full_img_url) {
            $this->full_img_url = wp_get_attachment_url( $this->attachment_id, 'full' );
        }
        return $this->full_img_url;
    }
    public function image_url() {
    	$attach = $this->getImageData();
      if(!$attach[0] && $this->has_video && isset($this->data['video']['image']) ) {
        return $this->data['video']['image'];
      }
    	return $attach[0];
    }
    public function image_width() {
    	$attach = $this->getImageData();
        return $attach[1];
    }
    public function image_height() {
    	$attach = $this->getImageData();
        return $attach[2];
    }
    public function video_url() {
    	if( $this->has_video && isset($this->data['video']['url']) ) {
    		return $this->data['video']['url'];
    	}
    	return false;
    }
    public function thumbnail_url() {
      $attach = $this->getImageData(true);
      if(!$attach[0] && $this->has_video && isset($this->data['video']['thumb']) ) {
        return $this->data['video']['thumb'];
      }
      return $attach[0];
    }
    public function thumbnail_width() {
        $attach = $this->getImageData(true);
        return $attach[1];
    }
    public function thumbnail_height() {
        $attach = $this->getImageData(true);
        return $attach[2];
    }
    public function thumbnail() {
        return NewRoyalSliderGenerator::get_thumbnail($this);
    }
    public function image_tag() {
        return NewRoyalSliderGenerator::get_image_tag($this);
    }
    public function link_url() {
        if(isset($this->data['link']))
            return $this->data['link'];
        else
            return '';
    }
    public function title() {
    	if(isset($this->data['title']))
    		return $this->data['title'];
    	else
    		return '';
    }
    public function description() {
    	if(isset($this->data['description']))
    		return apply_filters('new_royalslider_custom_gen_description', $this->data['description']);
    	else
    		return false;
    }
    public function html() {
    	if(isset($this->data['html']))
    		return $this->data['html'];
    	else
    		return false;
    }
    public function animated_blocks() {
    	return isset($this->data['animated_blocks']) ? $this->data['animated_blocks'] : '';
    }
}



<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderPostGalleryRenderer {


    public $attachment_id;
    public $attachment;
    public $options;
    public $image_data;
    public $thumb_image_data;
    public $full_img_url;
    public $slider_opts;

    function __construct( $id, $attachment, $slider_opts, $options ) {
        $this->attachment_id = $id;
        $this->attachment = $attachment;

        $this->slider_opts = $slider_opts;
        $this->options = $options;
    }
    public function large_image_url() {
        if(!$this->full_img_url) {
            $this->full_img_url = wp_get_attachment_url( $this->attachment_id, 'full' );
        }
        return $this->full_img_url;
    }
    public function image_url() {
        $attach = $this->getImageData();
        return $attach[0];
    }
    public function title() {
        if(isset($this->attachment->post_title))
            return $this->attachment->post_title;
    }
    public function image_width() {
        $attach = $this->getImageData();
        return $attach[1];
    }
    public function image_height() {
        $attach = $this->getImageData();
        return $attach[2];
    }
    public function thumbnail_url() {
        $attach = $this->getImageData(true);
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
    public function alt() {
        return get_post_meta($this->attachment_id, '_wp_attachment_image_alt', true);
    }
    public function thumbnail() {
        return NewRoyalSliderGenerator::get_thumbnail($this);
    }
    public function image_tag() {
        return NewRoyalSliderGenerator::get_image_tag($this);
    }
    public function getImageData($isThumb = false) {
        return NewRoyalSliderGenerator::get_image_data($this, $isThumb);
    }
    public function description() {
        if(isset($this->attachment->post_excerpt))
            return $this->attachment->post_excerpt;
    }
    public function content() {
        if(isset($this->attachment->post_content))
            return $this->attachment->post_content;
    }
}



<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

class NewRoyalSliderPostsRenderer {
    public $id;
    public $post;

    public $attachment_id;
    public $attachment;
    public $options;
    public $image_data;
    public $thumb_image_data;
    public $full_img_url;
    public $meta;
    public $slider_opts;

	function __construct( $post, $slider_opts, $options ) {
        $this->meta = get_post_custom( $post->ID );
        $this->post = $post;
        $this->id = $post->ID;
        $this->slider_opts = $slider_opts;
        $this->options = $options;
        $this->attachment_id = get_post_thumbnail_id( $this->id );
	}
    public function large_image_url() {
        if(!$this->full_img_url) {
            $this->full_img_url = wp_get_attachment_url( $this->attachment_id, 'full' );
        }
        return $this->full_img_url;
    }

    public function debug_meta() {
        return '<pre>'  . print_r($this->meta, true). '</pre>';
    }
    public function image_url() {
        $attach = $this->getImageData();
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
    public function title() {
        return $this->post->post_title;
    }
    public function content() {
        return $this->post->post_content;
    }
    public function excerpt() {

        $id = $this->id;
        global $post;

        $old_post = $post;
        if ($id != $post->ID) {
            $post = get_page($id);
        }

        if (!$excerpt = trim($post->post_excerpt)) {
            $excerpt = $post->post_content;
            $excerpt = strip_shortcodes( $excerpt );
            $excerpt = apply_filters('the_content', $excerpt);
            $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
            $excerpt = strip_tags($excerpt);
            $excerpt_length = apply_filters('excerpt_length', 55);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');

            $words = preg_split("/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $excerpt = implode(' ', $words);
                $excerpt = $excerpt . $excerpt_more;
            } else {
                $excerpt = implode(' ', $words);
            }
        }

        $post = $old_post;

        return $excerpt;
    }
    private function get_content_at_size($limit) {
        $content = strip_shortcodes( $this->post->post_content );
        $excerpt = explode(' ', $content, $limit);
        if (count($excerpt)>=$limit) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt).'...';
        } else {
            $excerpt = implode(" ",$excerpt);
        } 
        return preg_replace('`\[[^\]]*\]`','',$excerpt);
    }
    public function content10() {
        return $this->get_content_at_size(10);
    }
    public function content20() {
        return $this->get_content_at_size(20);
    }
    public function content30() {
        return $this->get_content_at_size(30);
    }
    public function content40() {
        return $this->get_content_at_size(40);
    }
    public function content50() {
        return $this->get_content_at_size(50);
    }
    public function content100() {
        return $this->get_content_at_size(100);
    }
    public function content150() {
        return $this->get_content_at_size(150);
    }
    public function content300() {
        return $this->get_content_at_size(300);
    }
    public function description() {
        return $this->post->post_excerpt;
    }
    public function permalink() {
        return get_permalink($this->post->ID);
    }
    public function link() {
        return $this->permalink();
    }
    public function link_url() {
        if( isset($this->options['posts']) && isset($this->options['posts']['link_the_slide']) && $this->options['posts']['link_the_slide'] == 'yes' )
            return $this->permalink();
    }
    public function date() {
        return apply_filters('get_the_date', mysql2date(get_option('date_format'), $this->post->post_date_gmt) , '');
    }
    public function time() {
        return get_the_time('',$this->post); 
    }
    public function author_name() {
        return get_the_author_meta( 'display_name', $this->post->post_author );
    }
    public function author_url() {
        return get_author_posts_url( 'display_name', $this->post->post_author );
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

    public function tags() {
        return  get_the_tag_list("", ', ', '', $this->post->ID);
    }
    public function categories() {
        return  get_the_category_list(", ", '', $this->post->ID);
    }
}



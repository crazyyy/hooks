<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

add_action( 'widgets_init', 'new_royalslider_register_widget' );

if(!function_exists("new_royalslider_register_widget")){
	function new_royalslider_register_widget() {
	    register_widget( 'NewRoyalSliderWidget' );
	}
}

if(!class_exists("NewRoyalSliderWidget")){
class NewRoyalSliderWidget extends WP_Widget {

	function NewRoyalSliderWidget() {
		$this->WP_Widget( 
			'new_royalslider_widget', 
			__('RoyalSlider', "new_royalslider"),
			array( 'classname' => 'new_royalslider_widget', 'description' => __('RoyalSlider Widget', "new_royalslider") ),
			array( 'id_base' => 'new_royalslider_widget' )
        );
	}
	function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title'] );
        echo $before_widget;
        if ( $title )
       		echo $before_title . $title . $after_title;
        echo get_new_royalslider( $instance["royalslider_id"] ); 
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance["title"] = strip_tags( $new_instance["title"] );
        $instance["royalslider_id"] = $new_instance["royalslider_id"];
        return $instance;
    }

    function form($instance) {

		$instance = wp_parse_args( (array) $instance, array('title' => __("RoyalSlider", "new_royalslider") ) );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title", "new_royalslider"); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'royalslider_id' ); ?>"><?php _e("Select slider to add", "new_royalslider"); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'royalslider_id' ); ?>" name="<?php echo $this->get_field_name( 'royalslider_id' ); ?>" style="width:100%;">
                <?php
                global $wpdb;
				$table = NewRoyalSliderMain::get_sliders_table_name();

				$qstr = " 
					SELECT id, name FROM $table WHERE active=1  AND type!='gallery'
				";
				$res = $wpdb->get_results( $qstr , ARRAY_A );
                $curr_id = isset($instance['royalslider_id']) ? $instance['royalslider_id'] : '';

                if( is_array($res) ) {
                    foreach ($res as $key => $slider_data) {
                        $id = $slider_data['id'];
                        $selected = '';

                        if ( $id == $curr_id)
                            $selected = ' selected="selected"';

                        $name = isset($slider_data['name']) ? ($slider_data['name'] . ' ') : '';

                        echo '<option value="'. $id .'"' . $selected . '>'. $name  . '#' . $id  .'</option>';
                    }
                }
                 
                ?>
            </select>
        </p>
    <?php
    }

}
}
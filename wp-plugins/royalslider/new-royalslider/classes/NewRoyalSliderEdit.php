<?php 
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider Edit/Create Slider page
 */

if ( !class_exists( 'NewRoyalSliderEdit' ) ):
    class NewRoyalSliderEdit {
        private $is_add_new;
        private $slider_id;
        private $res;
        private $slide_type;
        private $parsed_options;

        function __construct( $is_add_new ) {

            $this->is_add_new = $is_add_new;
            $this->slider_type = isset( $_REQUEST['rstype'] ) ? $_REQUEST['rstype'] : '';
           
            $this->parsed_options = null;
            if(!$is_add_new && isset( $_REQUEST['id'] )) {
                global $wpdb;
                $this->slider_id = (int)$_REQUEST['id'];

                $table = NewRoyalSliderMain::get_sliders_table_name();
                $this->res = $wpdb->get_results( $wpdb->prepare( 
                    "
                        SELECT * FROM $table WHERE id=%d
                    ", 
                    $this->slider_id
                ), ARRAY_A );
                if( isset($this->res[0]) ) {
                    $this->res = $this->res[0];
                    $this->parsed_options = json_decode($this->res['options'], ARRAY_A);
                }
            } else {
                //echo 'Add new slider';
            }

            $this->build_view();
        }

        function get_slider_options() {
           require_once('NewRoyalSliderOptions.php');
           NewRoyalSliderOptions::output_options( isset($this->res) ? $this->res['options'] : null, $this->slider_type);
        }

        function get_slider_items() {
            if(isset($this->res)) {
                if(!isset($this->res['slides']) ) {
                    return;
                }
                $slides = json_decode($this->res['slides'], true);
                if(is_array($slides) ) {
                    foreach ($slides as $slide) {
                        echo NewRoyalSliderGallery::get_admin_slide_item($slide);
                    }
                }
                
            }
        }

        function build_view() {
            ?>

<div id="new-royalslider-edit-page" class="wrap">
    <h2 id="edit-slider-text"><span>
    <?php if(!$this->is_add_new): 
        if($this->slider_id) {
            echo sprintf(__('Editing %s RoyalSlider #', 'new_royalslider'),  ucfirst($this->slider_type) );
            echo '<span>';
            echo $this->slider_id;
            echo '</span>';
        } 
    ?>
    <?php else: ?>
            <?php  echo sprintf(__('New %s RoyalSlider', 'new_royalslider'), ucfirst($this->slider_type) ); ?>
    <?php endif; ?>
       </span>
       <a href="#" class="add-new-h2 rs-embed-to-site <?php echo ($this->slider_id ? '' : 'rs-hidden'); ?>"><?php _e('Embed this slider to site', 'new_royalslider'); ?></a>
       <a href="admin.php?page=new_royalslider" class="add-new-h2"><?php _e('Back to list', 'new_royalslider'); ?></a>
       
    </h2>
    <?php echo NewRoyalSliderMain::get_embed_help_block( $this->slider_id ? $this->slider_id : 123); ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">

        <div id="side-info-column" class="options-sidebar">
            <div id="slider-actions" class="postbox action actions-holder"> 
                <?php if($this->slider_type != 'gallery') {?>                            
                    <a class="alignleft button-secondary button80" id="preview-slider" href="#"><?php _e('Preview slider', 'new_royalslider'); ?></a>
                <?php } ?>

                    <div id="save-progress" class="waiting ajax-saved" >
                        <?php _e('Saved!', 'new_royalslider'); ?>
                    </div>
                    <a class="alignright button-primary button80" id="save-slider" data-slider-id="<?php echo $this->slider_id; ?>" data-create="<?php echo $this->slider_id ? 'false' : 'true'; ?>" data href="#"><?php 
                    if($this->slider_id) {
                        _e('Save slider', 'new_royalslider'); 
                    } else {
                        _e('Create slider', 'new_royalslider'); 
                    }
                    

                    ?></a>   
                <br class="clear">              
            </div>
                        
            <div id="new-royalslider-options">
                <div class="postbox open">    
                    <div class="handlediv" title="Toggle view"></div>           
                    <h3 class="hndle"><?php _e('General options', 'new_royalslider'); ?></h3> 
                    <div class="inside slider-opts-group">
                                             <div class="rs-opt">
                                                <div data-help="<?php _e('Template resets all slider settings to create specific type of slideshow. After you set it you may (optionally) modify other options to fit your requirements.<br/><br/> Please note that most templates (except first one) add additional CSS file and change slide HTML markup, so some options might requre minor CSS modifications.', 'new_royalslider'); ?>" class="rs-template-title rs-help-el"><span id="rs-template-title-text"><?php _e('Templates', 'new_royalslider'); ?></span><i class="help-ico"></i></div>
                                                <div id="template-select" class="templates-grid">
                                                    <?php
                                                        require_once('NewRoyalSliderOptions.php');
                                                        $newrs_templates = NewRoyalSliderOptions::getRsTemplates();
                                                        $value = isset($this->res['template']) ? $this->res['template'] : 'default';
                                                        
                                                        $template_obj = $newrs_templates[$value];
                                                       
                                                        $template_html =  isset($template_obj['template-html']) ? $template_obj['template-html'] : $newrs_templates['default']['template-html'];
                                                       
                                                        $col = 0;
                                                        $row = 0;
                                                        foreach ( $newrs_templates as $key => $args ) {
                                                            $label = $args['label'];
                                                            //$label
                                                            echo sprintf( '<div class="rs-template"><input id="%s" type="radio" name="template" value="%s" %s><label style="background-position: %s ;" for="%s">%s</label></div>',
                                                                $key, 
                                                                $key, 
                                                                checked( $value, $key, false ), 
                                                                '-'.$col * 89 . 'px -' . ($row * 65 + 100) . 'px',
                                                                $key ,
                                                                '' 
                                                            );
                                                            $col++;
                                                            if($col > 1) {
                                                                $col = 0;
                                                                $row ++;
                                                            }
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="rs-opt">
                                                <div data-help="<?php _e('Skin is a set of CSS styled UI controls.', 'new_royalslider'); ?>" class="rs-template-title rs-skin-title rs-help-el"><?php _e('Skin', 'new_royalslider'); ?><i class="help-ico"></i></div>
                                                <select id="skin-select">
                                                    <?php
                                                        require_once('NewRoyalSliderOptions.php');
                                                        $newrs_skins = NewRoyalSliderOptions::getRsSkins();
                                                        $value = isset($this->res['skin']) ? $this->res['skin'] : 'rsUni';
                                                        foreach ( $newrs_skins as $key => $args ) {
                                                            $label = $args['label'];
                                                            echo sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
                                                        }
                                                    ?>
                                                </select>
                                                <a href="http://help.dimsemenov.com/kb/wordpress-royalslider-advanced/wp-adding-custom-skin-without-modifying-core-of-slider" target="_blank" style="color: #888; display:block; margin-top: 5px;"><?php _e('how to add custom skin') ?></a>
                                            </div>
                                            <hr>
                                            <a id="edit-slide-markup" class="in-page-action" href="#"><?php _e('Edit slide markup', 'new_royalslider'); ?></a>

                    </div>
                </div>
                <div class="other-options">
                     <?php $this->get_slider_options(); ?>
                </div>
            </div>        
        </div>

       
        <div class="sortable-slides-body">                              
            <div class="sortable-slides-container">
                <div id="titlediv">
                    <div id="titlewrap">           
                        <input type="text" name="title" size="40" maxlength="255" placeholder="<?php _e('Slider name', 'new_royalslider'); ?>" id="title" value="<?php echo isset($this->res) ? $this->res['name'] : ''; ?>" />
                    </div>
                </div>
                <div id="template-editor" style="display:none;">
                    <textarea style="width: 80%; height: 200px;"><?php echo isset($this->res['template_html']) ? esc_html($this->res['template_html']) : esc_html($template_html); ?></textarea>
                </div>
                <input id="admin-slider-type" type="hidden" value="<?php echo isset( $_REQUEST['rstype'] ) ? $_REQUEST['rstype'] : ''; ?>" />
                <?php if($this->slider_type == 'custom') { ?>
                    <div class="rs-add-slides-wrap">
                        <a class="button" id="create-new-slide" href="#"><?php _e('Create New Slide', 'new_royalslider'); ?></a> or 
                        <a class="button" id="add-images" href="#"><?php _e('Add Images', 'new_royalslider'); ?></a>
                    </div>

                    <div id="rs-be" style="display:none;" class="with-animation-options">
                        <div id="rs-be-buttons">
                            <button class="button button-primary rs-be-add-html-block"><?php _e('Add HTML block', 'new_royalslider'); ?></button>or
                            <button class="button rs-be-add-image-block"><?php _e('Add image', 'new_royalslider'); ?></button>
                        </div>

                        <div class="rs-ab-props">
                            <label for="block-classes-select" class="rs-help-el" data-align="top" data-help="<?php _e('CSS class that is applied to selected block.', 'new_royalslider'); ?>"><?php _e('Block CSS class:', 'new_royalslider'); ?></label>
                            <select id="block-classes-select">
                                <option value=''><?php _e('No class', 'new_royalslider'); ?></option>
                            <?php

                                $anim_block_classes = array('abBlackBox', 'abWhiteBox', 'abTextAlignCenter');
                                //$anim_block_classes = array('testClass1', 'testClass2');
                                $saved_items = get_option("new_royalslider_anim_block_classes");
                                if(is_array($saved_items)) {
                                    $anim_block_classes = array_merge($anim_block_classes, $saved_items);
                                }
                                
                                $anim_block_classes = apply_filters('new_royalslider_animated_block_classes', $anim_block_classes);
                                foreach($anim_block_classes as $key => $value) {
                                    ?>
                                    <option value='<?php echo $value; ?>'><?php echo $value; ?></option>
                                    <?php
                                }
                            ?>
                                <option value='rs_add_user_class'><?php _e('>> Add new class <<', 'new_royalslider'); ?></option>
                                <option value='rs_remove_user_class'><?php _e('>> Remove class <<', 'new_royalslider'); ?></option>
                            </select>
                            <label for="rs-a-b-animEnabled" class=" animation-cb">
                                <input id="rs-a-b-animEnabled" type="checkbox" value="true" checked="checked" />
                                <?php _e('Use block animation','new_royalslider'); ?>
                            </label>
                            <i class="help-ico rs-help-el"  class="rs-help-el" data-align="top" data-help="<?php echo esc_attr(__('Leave animation fields empty to use default settings that are defined in right sidebar options.<br/>Please don\'t overuse animation. Use it wisely and only when it
                            \'s really required.', 'new_royalslider') );; ?>"></i>
                        </div>
                        <div class="clear"></div>
                        <div class="rs-be-blocks-list"></div>
                        <div class="rs-anim-blocks-inputs">
                            
                            <div class="rs-be-editorarea"><textarea></textarea></div>
                            <div class="size-fields">
                                <div>
                                    <label for="rs-a-b-width" class="rs-help-el" data-help="<?php echo esc_attr( __('Width in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>"><?php _e('Width','new_royalslider'); ?></label>
                                    <input id="rs-a-b-width" value="" />
                                </div>
                                <div>
                                    <label for="rs-a-b-height" class="rs-help-el" data-help="<?php echo esc_attr( __('Height in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>"><?php _e('Height','new_royalslider'); ?></label>
                                    <input id="rs-a-b-height" value="" />
                                </div>

                                <div>
                                    <label for="rs-a-b-left" class="rs-help-el" data-help="<?php echo esc_attr( __('Distance from left in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>"><?php _e('Left','new_royalslider'); ?></label>
                                    <input id="rs-a-b-left" value="" />
                                </div>
                                <div>
                                    <label for="rs-a-b-right" class="rs-help-el" data-help="<?php echo esc_attr( __('Distance from right in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>"><?php _e('Right','new_royalslider'); ?></label>
                                    <input id="rs-a-b-right" value="" />
                                </div>
                                <div >
                                    <label for="rs-a-b-top" class="rs-help-el" data-help="<?php echo esc_attr( __('Distance from top in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>"><?php _e('Top','new_royalslider'); ?></label>
                                    <input id="rs-a-b-top" value="" />
                                </div>
                                <div>
                                    <label  class="rs-help-el" data-help="<?php echo esc_attr( __('Distance from bottom in pixels, percents or auto.<br/> e.g. \'123px\', \'50%\' or \'auto\'. ', 'new_royalslider') ); ?>" for="rs-a-b-bottom"><?php _e('Bottom','new_royalslider'); ?></label>
                                    <input id="rs-a-b-bottom" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="transition-fields rs-anim-blocks-inputs">
                            <div>
                                <label for="rs-a-b-speed" class="rs-help-el" data-help="<?php echo esc_attr( __('Animation speed of block.', 'new_royalslider') ); ?>"><?php _e('Speed','new_royalslider'); ?></label>
                                <input id="rs-a-b-speed" value="300" />
                            </div>
                            <div class="rs-be-fade-effect">
                                <label for="rs-a-b-fade-effect">
                                <input id="rs-a-b-fade-effect" type="checkbox" checked="checked" value="true" />
                                <?php _e('Fade in','new_royalslider'); ?></label>
                            </div>
                            <div>
                                <label for="rs-a-b-move-offset"  class="rs-help-el" data-help="<?php echo esc_attr( __('Distance for move animation.', 'new_royalslider') ); ?>"><?php _e('Move offset','new_royalslider'); ?></label>
                                <input id="rs-a-b-move-offset" value="300" />
                            </div>
                            <div>
                                <label for="rs-a-b-move-effect" class="rs-help-el" data-help="<?php echo esc_attr( __('Move/slide animation', 'new_royalslider') ); ?>"><?php _e('Move effect','new_royalslider'); ?></label>
                                <select id="rs-a-b-move-effect">
                                    <option value="left"><?php _e('From left','new_royalslider'); ?></option>
                                    <option value="right"><?php _e('From right','new_royalslider'); ?></option>
                                    <option value="top"><?php _e('From top','new_royalslider'); ?></option>
                                    <option value="bottom"><?php _e('From bottom','new_royalslider'); ?></option>
                                    <option value="none"><?php _e('None','new_royalslider'); ?></option>
                                </select>
                            </div>
                            <div>
                                <label for="rs-a-b-delay" class="rs-help-el" data-help="<?php echo esc_attr( __('Delay before block shows up. Leave field empty or set to AUTO to use default sequential delay.', 'new_royalslider') ); ?>"><?php _e('Delay','new_royalslider'); ?></label>
                                <input id="rs-a-b-delay" value="400" />
                            </div>
                            <div>
                                <label for="rs-a-b-easing"  class="rs-help-el" data-help="<?php echo esc_attr( __('Easing function for animation', 'new_royalslider') ); ?>"><?php _e('Easing','new_royalslider'); ?></label>
                                <select id="rs-a-b-easing">
                                    <option value="easeOutSine"><?php _e('easeOutSine','new_royalslider'); ?></option>
                                    <option value="easeInOutSine"><?php _e('easeInOutSine','new_royalslider'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="rs-no-slides-block">
                        <h2><?php _e("You don't have any slides", 'new_royalslider'); ?></h2>
                        <p><?php _e("Get started by adding slides via buttons above or watch <a target=\"_blank\" href=\"http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-creating-custom-slider\">introductory video</a>.", 'new_royalslider'); ?></p>
                    </div>
                    <div id="new-rs-slides">
                         <?php $this->get_slider_items(); ?>
                    </div> 
                <?php } else if($this->slider_type === 'gallery') { ?>
                    <div class="rs-info">
                        <p class="rs-awesome-paragraph"><?php _e('Here you can create configuration of RoyalSlider that will override default WordPress gallery, and can be added only inside post.', 'new_royalslider'); ?></p>

                        <div class="help-video"><a class="in-page-action" target="_blank" href="http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-creating-royalslider-from-images-attached-to-post"><?php _e('View help video about how to create such slider', 'new_royalslider'); ?></a></div><br/>
                        <p>Or follow these steps:</p>
                        <ol>
                            <li><?php _e('Enter name for slider.', 'new_royalslider'); ?></li>
                            <li><?php _e('Configure slider options at right side, most of time you just need to change "Template" and "Skin" options.', 'new_royalslider'); ?></li>
                            <li><?php _e('Click create(save) slider button.', 'new_royalslider'); ?></li>
                            <li><?php _e('Go to any post and insert default WordPress gallery as usually.', 'new_royalslider'); ?></li>
                            <li><?php _e('Switch to Text(HTML) tab in post content editor and add <code>royalslider="SLIDER_ID"</code> attribute to gallery shortcode, where <code>SLIDER_ID</code> is id of slider that you\'re editing now.<br/>For example if ID of your slider is 3: <code>[gallery ids="24,22"]</code> should be changed to <code>[gallery royalslider="3"  ids="24,22"]</code>.', 'new_royalslider'); ?></li>
                            <li><?php _e('Optionally change configuration of the slider or change Slide HTML markup.', 'new_royalslider'); ?></li>
                        </ol>
                        <p><?php _e('You may also override all default WordPress galleries in posts (without adding royalslider attribute to [gallery] shortcode), visit RoyalSlider global settings page to learn more.', 'new_royalslider'); ?></p>
                    </div>
                <?php } else if($this->slider_type === '500px') { ?>
                    <?php  
                        NewRoyalSlider500pxSource::show_admin_options($this->parsed_options);
                    ?>
                <?php } else if($this->slider_type === 'posts') { ?>
                    <?php  
                        NewRoyalSliderPostsSource::show_admin_options($this->parsed_options);
                    ?>
                <?php } else if($this->slider_type === 'flickr') { ?>
                    <?php  
                        NewRoyalSliderFlickrSource::show_admin_options($this->parsed_options);
                    ?>
                <?php } ?>
                
            </div>
        </div>
        

    </div>
</div>
            <?php
        }

    }
endif;
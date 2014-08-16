<?php
if( !defined('WPINC') ) exit('No direct access permitted');

/**
 * RoyalSlider admin manage page
 */
if ( !class_exists( 'NewRoyalSliderManage' ) ):
    class NewRoyalSliderManage {

        function __construct() {
            global $wpdb;
            
            $action = '';
            if(isset( $_REQUEST['action'] )) {
                $action = $_REQUEST['action'];
            }
          
            if( ( $action == 'delete'|| $action == 'duplicate') && isset( $_REQUEST['id'] ) )  {
                check_admin_referer('new_royalslider_magage_sliders');
                $sb = admin_url( 'admin.php?page=new_royalslider' );
                $id = (int)$_REQUEST['id'];
                if ( $action == 'delete' ) {
                    $this->delete_slider($id);
                    $sb = add_query_arg( array('action' => 'deleted', 'id' => $id), $sb );
                } else if ( $action == 'duplicate' ) {
                    $sb = add_query_arg( array('action' => 'duplicated', 'id' => $id, 'duplicateid' => $this->duplicate_slider($id) ), $sb );
                }
                echo '<script type="text/javascript">window.location.href="'.$sb.'"</script>';
                //wp_redirect($sb);
                exit();
            }

            ?>
        <div class="wrap">
            <h2 class="manage-page-title">
                <?php _e('Royal&thinsp;Slider\'s', 'new_royalslider'); ?>
                <a class="add-new-h2 new-royalslider-add-manage" href="#"><?php _e('Add New', 'new_royalslider') ?></a>
                <a class="new-royalslider-embed in-page-action" href="#"><?php _e('How to embed RoyalSlider to site', 'new_royalslider') ?></a>
            </h2>
<?php 
$action = '';
if(isset( $_REQUEST['action'] )) {
    $action = $_REQUEST['action'];
}

if ( $action == 'deleted'|| $action == 'duplicated' ) {
?>
<div id="message" class="updated"><p>
<?php 

$sname = 'RoyalSlider ' . (isset( $_REQUEST['id'] ) ? '#'.$_REQUEST['id'] : '') . ' ';
if ( $action == 'duplicated' )  {
    echo $sname . __( 'successfully duplicated.', 'new_royalslider') . (isset( $_REQUEST['duplicateid'] ) ? __(' New slider with id #','new_royalslider') .$_REQUEST['duplicateid'] : '');
} else if ( $action == 'deleted' ) {
    echo $sname . __( 'permanently deleted.', 'new_royalslider');
}

?>
</p></div>
<?php } ?>
    <script>
    jQuery(document).ready(function($) {

        var ie_lt9 = /MSIE [1-8]\b/.test(navigator.userAgent);
        if ( ie_lt9 ) {
          alert('Warning! To work correctly RoyalSlider admin requires modern browser, IE9+, Chrome, Safari or Firefox.'); 
        }

        $('#new-royalslider-manage-table').on('click', '.new-rs-delete', function(e) {
            e.preventDefault();
            if(confirm("<?php _e('This action will PERMANENTLY DELETE slider. Continue?', 'new_royalslider'); ?>")){
                window.location = $(e.target).attr('data-href');
            } else {
                return false;
            }
            
        });




        $('.new-royalslider-embed').click(function(e) {
            e.preventDefault();

            var dialog = $('#embed-info').dialog({
                modal: true,
                title: "",
                zIndex: 80,
                width: 500,
                resizable: false,
                height: 'auto',
                beforeClose: function() {

                },
                open: function() { 
                    $(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
                         dialog.dialog( "close" );
                    });
                }

            })

        });

        $('.new-royalslider-add-manage').click(function(e) {
            e.preventDefault();

            var dialog = $('.create-popup').dialog({
                modal: true,
                title: "<?php _e('Choose the type of slider to create', 'new_royalslider'); ?>",
                zIndex: 80,
                width: 494,
                height: 356,
                resizable: false,
                beforeClose: function() {

                },
                open: function() { 
                    $(".ui-widget-overlay").unbind('click.rst').bind('click.rst', function () {
                         dialog.dialog( "close" );
                    });
                }

            })

        });
        $('.rs-help-el').qtip({
            overwrite: false,
            content: {
                attr: 'data-help'
            },
            position: {
                at: 'top center', 
                my: 'bottom center'
            },
            style: {
                classes: 'ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tipsy rs-tooltip'
            }
        });

        $('#new-royalslider-manage-table').on('click', '.active-indicator', function(e) {
            var closestRow = $(e.target).closest('tr');
            closestRow.toggleClass('disabled');

            var isActive = !closestRow.hasClass('disabled');
            var currId = closestRow.data('id');
               // 'toggleActiveNonce' => wp_create_nonce( 'new_royalslider_toggle_active_ajax_nonce' )

            $.ajax({
                url: newRsVars.ajaxurl,
                type: 'post',
                data: {
                    action : 'toggleActiveClass',
                    id : currId,
                    isActive: isActive ? 1 : 0,
                    _ajax_nonce : newRsVars.toggleActiveNonce
                }
            });

        });
    });
    </script>
    <?php 
    if(current_user_can('manage_options')) {
        $version = NewRoyalSliderMain::get_update_version();

        if($version) {
            ?>
            <div class="updated" style="max-width: 685px;"><p>
            <?php 
            _e('New version of RoyalSlider is available.','new_royalslider'); 
             
            echo sprintf( __(' %sSee whats new in %s and how to upgrade%s', 'new_royalslider'), '<a href="http://dimsemenov.com/plugins/royal-slider/wp-update/?v='.$version.'" target="_blank">', $version ,'</a>' );
            ?>
            </p></div>
            <?php
        }
    }

    ?>
    <table id="new-royalslider-manage-table" class='royalsliders-table  wp-list-table widefat fixed'>
        <thead>
            <tr>
                <th width='5%'><?php _e('ID','new_royalslider'); ?></th>
                <th width='40%'><?php _e('Title','new_royalslider'); ?></th>
                <th width='10%'><?php _e('Type','new_royalslider'); ?></th>    
                <th width='20%'><?php _e('Shortcode','new_royalslider'); ?></th>    
                <th width='10%'><?php _e('Active','new_royalslider'); ?><i class="help-ico rs-help-el" data-help="<?php _e('Always mark sliders that you don\'t use as inactive. It\'ll  prevent including unwanted JS and CSS files of skins and templates.' , 'new_royalslider'); ?>"></i></th>   
            </tr>
        </thead>
        <tbody>
            <?php 
                
                $prefix = $wpdb->prefix;
                $sliders = $wpdb->get_results("SELECT * FROM " . $prefix . "new_royalsliders ORDER BY id");
                if (count($sliders) == 0) {
                    ?>
                    <tr class="no-items"><td class="colspanchange" colspan="5" style="padding: 30px;"><?php echo sprintf(__("You don't have any slideshows. %sCreate first%s to get started.", "new_royalslider"), '<a class="new-royalslider-add-manage" href="#">', "</a>");  ?></td></tr>
                    <?php
                } else {
                    $slider_display_name;
                    foreach ($sliders as $slider) {
                        
                        $slider_display_name = $slider->name;
                        if(!$slider_display_name) {
                            $slider_display_name = __('Untitled slider #', 'new_royalslider') . $slider->id;
                        }
                        $type = '&rstype='.$slider->type;
                        $edit_url = admin_url('admin.php?page=new_royalslider&action=edit&id=' . $slider->id . $type);
                        $duplicate_url = wp_nonce_url( admin_url('admin.php?page=new_royalslider&action=duplicate&id='  . $slider->id), 'new_royalslider_magage_sliders');
                        $delete_url = wp_nonce_url( admin_url('admin.php?page=new_royalslider&action=delete&id='  . $slider->id), 'new_royalslider_magage_sliders');
                        ?>
                        <tr class="<?php echo $slider->active ? '' : 'disabled'; ?>" data-id="<?php echo $slider->id; ?>">
                            <td><?php echo $slider->id; ?></td>
                            
                            <td>
                                <strong><a class="row-title" href="<?php echo $edit_url; ?>"><?php echo $slider_display_name; ?></a></strong>
                                <div class="row-actions">
                                    <span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e('Edit', 'new_royalslider' ); ?></a> | </span>
                                    <span class="view"><a href="<?php echo $duplicate_url; ?>"><?php _e('Duplicate', 'new_royalslider' ); ?></a> | </span>    
                                    <span class="trash"><a class="new-rs-delete submitdelete" data-href="<?php echo $delete_url; ?>" href="#"><?php _e('Delete', 'new_royalslider' ); ?></a></span> 
                                </div>
                            </td>
                            <td><span class="rs-type-label"><?php echo $slider->type; ?></span></td>
                            <td>
                                <input type="text" value="<?php 
                                    if($slider->type != 'gallery') {
                                        echo esc_attr('[new_royalslider id="' . $slider->id . '"]');
                                    } else {
                                        echo esc_attr('[gallery royalslider="' . $slider->id . '"]');
                                    }
                                    
                                ?>">
                            </td>
                            <td  class="active-indicator"><div ><span></span></div></td>
                        </tr>
                        <?php 
                    } // slides
                } // if exist
            ?>
        </tbody>         
    </table>
    <?php echo NewRoyalSliderMain::get_embed_help_block(123); ?>
    <p style="max-width: 700px;">         
        <a class='button-primary new-royalslider-add-manage' href='#'><?php _e('Create New Slider', 'new_royalslider') ?></a>
        <span style="float: right; margin-top: 5px;"><?php _e('Having issues? Need a feature? Visit <a href="http://dimsemenov.com/private/forum.php" target="_blank">RoyalSlider support desk</a>.', 'new_royalslider') ?></span>

        <div class="create-popup">
            <a class="create-custom"  href='<?php echo admin_url( "admin.php?page=new_royalslider&action=add&rstype=custom" ); ?>'>
                <h4><span class='in-page-action'><?php _e('Custom slider', 'new_royalslider') ?></span></h4>
                <p><?php  _e('For completely custom slideshows where each slide is different.', 'new_royalslider'); ?></p>
            </a>
            <a class="create-from-posts"  href='<?php echo admin_url( "admin.php?page=new_royalslider&action=add&rstype=posts" ); ?>'>
                <h4><span class='in-page-action'><?php _e('Slider from your posts', 'new_royalslider') ?></span></h4>
                <p><?php  _e('For slideshows that should grab data from contents of your posts.', 'new_royalslider'); ?></p>
            </a>
            <a class="create-from-500px"  href='<?php echo admin_url( "admin.php?page=new_royalslider&action=add&rstype=500px" ); ?>'>
                <h4><span class='in-page-action'><?php _e('500px gallery', 'new_royalslider') ?></span></h4>
                <p><?php  _e('For image galleries that will grab data from 500px.com', 'new_royalslider'); ?></p>
            </a>
            <a class="create-from-500px"  href='<?php echo admin_url( "admin.php?page=new_royalslider&action=add&rstype=flickr" ); ?>'>
                <h4><span class='in-page-action'><?php _e('Flickr gallery', 'new_royalslider') ?></span></h4>
                <p><?php  _e('For image galleries that will grab data from Flickr photoset.', 'new_royalslider'); ?></p>
            </a>
            <a class="replace-default" href='<?php echo admin_url( "admin.php?page=new_royalslider&action=add&rstype=gallery" ); ?>'>
                <h4><span class='in-page-action'><?php _e('Default WordPress gallery', 'new_royalslider') ?></span></h4>
                <p><?php  _e('For image galleries that will be added inside post.', 'new_royalslider'); ?></p>
            </a>
        </div>
    </p> 
        </div>

            <?php
        }
        private function delete_slider($id) {
            global $wpdb;
            $table = NewRoyalSliderMain::get_sliders_table_name();
            $sql = $wpdb->prepare("DELETE FROM $table WHERE id=%d", $id);
            $wpdb->query($sql);
        }
        private function duplicate_slider($id) {
            global $wpdb;

            $table = NewRoyalSliderMain::get_sliders_table_name();
            $res = $wpdb->get_results( $wpdb->prepare( 
                "
                    SELECT * FROM $table WHERE id=%d
                ", 
                $id
            ), ARRAY_A );
            $res = $res[0];

            $res['name'] = (isset($res['name']) && $res['name'] != '' ? $res['name'] . ' ' : '') . __('(copy of ', 'new_royalslider') . '#' . $id . ')';
            unset($res['id']);
            $wpdb->insert( 
                $table,
                $res
            );

            return $wpdb->insert_id; 
        }


    }
endif;
<?php
if (isset($_POST['social'])) {
    define('WP_USE_THEMES', false);
    require_once('../../../../wp-load.php');
    status_header(200);
    nocache_headers();
    switch ($_POST['social']) {
        case 'vk':
            $post_id = $_POST['id'];
            $num = $_POST['num'];
            $last_comment = $_POST['last_comment'];
            $date = $_POST['date'];
            $sign = $_POST['sign'];
            $api_secret = get_option('vkapi_api_secret');
            $hash = md5($api_secret . $date . $num . $last_comment);
            update_post_meta($post_id, 'vkapi_comm', $num, false);
            $emails = Array();
            if (get_option('vkapi_notice_admin') == '1') {
                $emails[] = get_bloginfo('admin_email');
            }
            // todo-dx: optimize this
            if ( get_user_meta($profile->ID, 'vkapi_notice_comments', true) == '1' ) {
                $post = get_post($post_id);
                $emails[] = get_the_author_meta('email', $post->post_author);
            }
            $blog_url = get_bloginfo("url");
            if (substr($blog_url, 0, 7) == 'http://') {
                $blog_url = substr($blog_url, 7);
            }
            if (substr($blog_url, 0, 8) == 'https://') {
                $blog_url = substr($blog_url, 8);
            }
            load_plugin_textdomain('vkapi', false, dirname(plugin_basename(__FILE__)) . '/translate/');
            $notify_message = 'VKapi: ' . __('Page has just commented!', 'vkapi') . '<br />';
            $notify_message .= get_permalink($post_id) . '<br /><br />';
            $notify_message .= __('Comment: ', 'vkapi') . '<br />' . $last_comment . '<br /><br />';
            $notify_message .= $hash == $sign ? '' : '<br /><br /><br />sign not true';
            $subject = '[VKapi] ' . __('Website:', 'vkapi');
            $subject .= ' "' . $blog_url . '"';
            add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
            wp_mail($emails, $subject, $notify_message);
            break;
        case 'fb':
            $post_id = $_POST['id'];
            $data = wp_remote_get('https://graph.facebook.com/?ids=' . get_permalink($post_id));
            if (is_wp_error($data)) {
                echo $data->get_error_message();
                exit;
            }
            $resp = json_decode($data['body'], true);
            foreach ($resp as $key => $value) {
                $num = $value['comments'];
            }
            if (isset($num)) {
                update_post_meta($post_id, 'fbapi_comm', $num, false);
            }
            $emails = Array();
            if (get_option('vkapi_notice_admin') == '1') {
                $emails[] = get_bloginfo('admin_email');
            }
            if ( get_user_meta($profile->ID, 'vkapi_notice_comments', true) == '1' ) {
                $post = get_post($post_id);
                $emails[] = get_the_author_meta('email', $post->post_author);
            }
            $blog_url = site_url();
            if (substr($blog_url, 0, 7) == 'http://') {
                $blog_url = substr($blog_url, 7);
            }
            if (substr($blog_url, 0, 8) == 'https://') {
                $blog_url = substr($blog_url, 8);
            }
            load_plugin_textdomain('vkapi', false, dirname(plugin_basename(__FILE__)) . '/translate/');
            $notify_message = 'FBapi: ' . __('Page has just commented!', 'vkapi') . "<br />";
            $notify_message .= get_permalink($post_id) . '<br /><br />';
            // todo-dx: add last comment, really ^^
            $notify_message .= 'Появился новый комментарий' . '<br /><br />';
            $subject = '[VKapi] ' . __('Website:', 'vkapi');
            $subject .= ' "' . $blog_url . '"';
            $notify_message .= '<br /><br /><br />sign not true';
            add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
            wp_mail($emails, $subject, $notify_message);
            break;
    }
}
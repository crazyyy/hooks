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
            if ($hash == $sign) {
                update_post_meta($post_id, 'vkapi_comm', $num, false);
            }
            break;
        case 'fb':
            $post_id = $_POST['id'];
            $data = wp_remote_get('https://graph.facebook.com/?ids=' . get_permalink($post_id));
            if (is_wp_error($data)) {
                exit;
            }
            $resp = json_decode($data['body'], true);
            foreach ($resp as $key => $value) {
                $num = $value['comments'];
            }
            if (isset($num)) {
                update_post_meta($post_id, 'fbapi_comm', $num, false);
            }
            break;
    }
}
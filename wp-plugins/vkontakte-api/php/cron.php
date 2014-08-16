<?php

define('VKAPI_AT', 'https://api.vk.com/oauth/access_token');
define('VKAPI_SERVER', 'https://api.vk.com/method/');
define('WP_USE_THEMES', false);
/** @var $path_to_site string Путь к папке(дериктории) сайта */
//$path_to_site = '/home/kowack/site.com/www/';
//require_once($path_to_site . 'wp-load.php');
require_once('../../../../wp-load.php');
status_header(200);
nocache_headers();
main();

function main()
{
    $params = array();
    $gid = get_option('vkapi_vk_group');
    if (is_numeric($gid)) {
        $params['owner_id'] = -$gid;
    } else {
        $params['domain'] = $gid;
    }
    $params['access_token'] = get_option('vkapi_at');
    $query = http_build_query($params);

    $result = wp_remote_get(VKAPI_SERVER . 'wall.get?' . $query);
    $data = check($result);
    $posts = array();
    foreach ($data as $post) {
        if (is_array($post)) {
            $posts[$post['id']] = $post;
        }
    }
    $old_array = get_option('vkapi_vk_array');
    if (empty($old_array)) {
        $old_array = array_keys($posts);
        update_option('vkapi_vk_array', $old_array);
        die;
    } else {
        foreach ($old_array as $value) {
            $old_array[$value] = $value;
        }
    }
    $new_array = array_diff_key($posts, $old_array);
    $category = get_option('vkapi_crosspost_category');
    /** @var $admin WP_User */
    $admin = get_user_by('email', get_option('admin_email'));
    $admin_id = $admin->ID;
    foreach ($new_array as $post) {
        set_time_limit(30);
        $text = $post['text'];
        $gmtOffset = get_option('gmt_offset');
        $date = new \DateTime();
        $date->setTimestamp($post['date']);
        $date->modify("+{$gmtOffset} hours");
        $date = $date->format('Y-m-d H:i:s');
        $title = mb_substr($text, 0, mb_strpos($text, "\n"));
        foreach ($post['attachments'] as $attachment) {
            switch ($attachment['type']) {
                case 'link':
                    $href = stripslashes($attachment['link']['url']);
                    $title = stripslashes(trim($attachment['link']['title']));
                    $text .= "<br><br><a href='{$href}' target='_blank'>{$title}</a>";
                    break;
                case 'posted_photo':
                case 'album_photo':
                case 'photo':
                    $href = stripslashes($attachment[$attachment['type']]['src_big']);
                    $width = stripslashes($attachment[$attachment['type']]['width']);
                    $height = stripslashes($attachment[$attachment['type']]['height']);
                    $text = "<img src='{$href}' alt='' style='max-width: 100%' /><br>" . $text;
                    break;
            }
        }

        $post = array(
            'post_author' => $admin_id,
            'post_content' => $text,
            'post_date' => $date,
//            'post_date_gmt' => $date,
            'post_category' => array($category),
            'post_status' => 'publish',
            'post_title' => $title,
            'post_type' => 'post',
            'crossposted' => true
        );
        $result = wp_insert_post($post, true);
        if (is_wp_error($result)) {
            $email = get_bloginfo('admin_email');
            $subject = '[VKapi Cron] ' . __('Website:', 'vkapi') . site_url();
            $echo = $result->get_error_message();
            #wp_mail($email, $subject, 'Cron AntiCrosspost: WP Error Msg: ' . $echo . ". Line:" . __LINE__);
            die($echo);
        } else {
            echo 'Post anti id: ' . $result;
            update_post_meta($result, 'vkapi_crossposted', 'true');
        }
    }
    $old_array = array_keys($posts);
    update_option('vkapi_vk_array', $old_array);
    $email = get_bloginfo('admin_email');
    $subject = '[VKapi Cron] ' . __('Website:', 'vkapi') . site_url();
    $echo = 'New array:' . print_r($new_array, true);
    #wp_mail($email, $subject, $echo);
    echo $echo;
}


function check($result)
{
    $email = get_bloginfo('admin_email');
    $subject = '[VKapi Cron] ' . __('Website:', 'vkapi') . site_url();
    if (is_wp_error($result)) {
        $msg = $result->get_error_message();
        $echo = 'Cron AntiCrosspost: WP Error Msg: ' . $msg . ". Line:" . __LINE__;
        #wp_mail($email, $subject, $echo);
        die($echo);
    }
    $data = json_decode($result['body'], true);
    if (!$data['response']) {
        $msg = $data['error']['error_msg'] . ' ' . $data['error']['error_code'];
        $echo = 'Cron AntiCrosspost: API Error Code: ' . $msg . ". Line:" . __LINE__;
        #wp_mail($email, $subject, $echo);
        die($echo);
    }

    return $data['response'];
}
<?php

define('VKAPI_AT', 'https://api.vk.com/oauth/access_token');
define('VKAPI_SERVER', 'https://api.vk.com/method/');
define('WP_USE_THEMES', false);
require_once('../../../../wp-load.php');
status_header(200);
nocache_headers();
main();

function authOpenAPIMember()
{
    $session = array();
    $member = false;
    $valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');
    $app_cookie = $_COOKIE['vk_app_' . get_option('vkapi_appid')];
    if ($app_cookie) {
        $session_data = explode('&', $app_cookie, 10);
        foreach ($session_data as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            if (empty($key) || empty($value) || !in_array($key, $valid_keys)) {
                continue;
            }
            $session[$key] = $value;
        }
        foreach ($valid_keys as $key) {
            if (!isset($session[$key])) {
                return $member;
            }
        }
        ksort($session);

        $sign = '';
        foreach ($session as $key => $value) {
            if ($key != 'sig') {
                $sign .= ($key . '=' . $value);
            }
        }
        $sign .= get_option('vkapi_api_secret');
        $sign = md5($sign);
        if ($session['sig'] == $sign && $session['expire'] > time()) {
            $member = array(
                'id' => intval($session['mid']),
                'secret' => $session['secret'],
                'sid' => $session['sid']
            );
        }
    }

    return $member;
}

function doHttpRequest($url)
{
    $ch = curl_init(); // start
    curl_setopt($ch, CURLOPT_URL, "$url"); // where
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // why
    $request_result = curl_exec($ch); // do this
    curl_close($ch); // close, free memory
    return $request_result; // profit
}

function params($params)
{
    $peace = array();
    foreach ($params as $k => $v) {
        $peace[] = $k . '=' . urlencode($v);
    }

    return implode('&', $peace);
}

function get_VkMethod($method_name, $parameters = array())
{
    ksort($parameters);
    $parameters = params($parameters);
    $url = VKAPI_SERVER . $method_name . "?" . $parameters;
    $result = doHttpRequest($url);
    $result = urldecode($result);
    $data = json_decode($result, true);

    return $data["response"];
}

function main()
{
    $member = authOpenAPIMember();

    if ($member === false) {
        echo 'sign not true';
        die;
        bitch;
        die;
    }

    /** @var $wpdb wpdb */
    global $wpdb;
    $wp_user_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT `user_id` FROM {$wpdb->usermeta} WHERE `meta_key` = 'vkapi_uid' AND `meta_value` = %s LIMIT 1",
            $member['id']
        )
    );
    if ($wp_user_id !== null) {
        wp_set_auth_cookie($wp_user_id);
        // todo-dx: check if it is working
        do_action('wp_login', $wp_user_id);
        echo 'Ok';
    } else {
        oauth_new_user($member['id']);
    }
}

function oauth_new_user($id)
{
    $users = get_VkMethod(
        'getProfiles',
        array('uids' => $id, 'fields' => 'uid,first_name,nickname,last_name,screen_name,photo_medium_rec')
    );
    $user = $users[0];
    if (strlen($user["uid"]) > 0) {
        $data = array();
        $data['user_pass'] = wp_generate_password();
        $data['user_login'] = 'vk_id' . $user['uid'];
        $data['user_email'] = $data['user_login'] . '@vk.com';
        $data['nickname'] = $user['nickname'];
        $data['first_name'] = $user['first_name'];
        $data['last_name'] = $user['last_name'];
        $data['rich_editing'] = true;
        $data['jabber'] = $data['user_email'];
        $data['display_name'] = "{$data['first_name']} {$data['last_name']}";
        $uid = wp_insert_user($data);
        if (is_wp_error($uid)) {
            echo $uid->get_error_message();
            exit;
        }
        add_user_meta($uid, 'vkapi_ava', $user['photo_medium_rec'], false);
        add_user_meta($uid, 'vkapi_uid', $user['uid'], true);

        $array = array();
        $array['user_login'] = $data['user_login'];
        $array['user_password'] = $data['user_pass'];
        $array['remember'] = true;
        $user = wp_signon($array);
        if (is_wp_error($user)) {
            echo $user->get_error_message();
        } else {
            echo 'Ok';
        }
    }
    echo print_r($user);
}
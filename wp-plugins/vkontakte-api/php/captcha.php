<div class="wrap">
    <?php screen_icon(); ?>
    <h2>VKontakte API - <?php _e('Captcha', $this->plugin_domain); ?></h2>
</div>

<?php

if (!empty($_POST)) {
    $captcha_sid = $_POST['captcha_sid'];
    $captcha_body = $_POST['captcha_body'];
    $captcha_key = $_POST['captcha_key'];
    $captcha_nonce = $_POST['captcha_nonce'];
    if (!empty($captcha_sid) && !empty($captcha_body) && !empty($captcha_key) && wp_verify_nonce($captcha_nonce, $captcha_sid . $captcha_body)) {
        $vk_at = get_option('vkapi_at');
        $body = explode($captcha_sid, $captcha_body);

        // repeat crosspost

        $body['captcha_sid'] = $captcha_sid;
        $body['captcha_key'] = $captcha_key;
        $curl = new Wp_Http_Curl();
        $result = $curl->request(
            $this->vkapi_server . 'wall.post',
            array(
                'body' => $body,
                'method' => 'POST'
            )
        );
        /** @var $result WP_Error */
        if (is_wp_error($result)) {
            $msg = $result->get_error_message();
            self::notice_error('CrossPost: ' . $msg . ' wpx c' . __LINE__);

            return false;
        }
        $r_data = json_decode($result['body'], true);
        if (isset($r_data['error'])) {
            if ($r_data['error']['error_code'] == 14) {
                $captcha_sid = $r_data['error']['captcha_sid'];
                $captcha_img = $r_data['error']['captcha_img'];
                #$captcha_body = implode($captcha_sid, $body);
                $captcha_action = 'options-general.php?page=vkapi_captcha';
                $captcha_nonce = wp_create_nonce($captcha_sid . $captcha_body);
                $msg = "
                    Captcha needed: <img src='{$captcha_img}'>
                    <form method='post' action='{$captcha_action}' target='_blank'>
                        <input type='text' name='captcha_key'>
                        <input type='hidden' name='captcha_sid' value='{$captcha_sid}'>
                        <input type='hidden' name='captcha_body' value='{$captcha_body}'>
                        <input type='hidden' name='captcha_nonce' value='{$captcha_nonce}'>
                        <input type='submit' class='button button-primary'>
                    </form>
                    ";
                self::notice_error('CrossPost: API Error Code: ' . $msg . 'vkx c' . __LINE__);
            } else {
                $msg = $r_data['error']['error_msg'] . ' ' . $r_data['error']['error_code'] . ' _' . $body['attachments'];
                self::notice_error('CrossPost: API Error Code: ' . $msg . 'vkx c' . __LINE__);
            }
            return false;
        }
        $vk_group_id = '-28197069';
        $temp = isset($vk_group_screen_name) ? $vk_group_screen_name : 'club' . $vk_group_id;
        $post_link = "https://vk.com/{$temp}?w=wall{$vk_group_id}_{$r_data['response']['post_id']}%2Fall";
        $post_href = "<a href='{$post_link}' target='_blank'>{$temp}</a>";
        self::notice_notice('CrossPost: Success ! ' . $post_href);
        update_post_meta($post->ID, 'vkapi_crossposted', $r_data['response']['post_id']);
        echo '<script>close()</script>';
    } else {
        echo 'Error: no valid data.';
    }
} else {
    echo 'Error: no data.';
}

?>

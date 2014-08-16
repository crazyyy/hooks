<div class="wrap">
<?php screen_icon(); ?>
<h2>VKontakte API - <?php _e('Settings', $this->plugin_domain); ?></h2>

<style type="text/css" scoped="scoped">
    .wrap {
        font-weight: normal;
    }

    #mymenu {
        height: 30px;
    }

    #mymenu li {
        float: left;
        padding: 10px;
        border-top: 1px solid #0074a2; /*#21759b;*/
        border-right: 1px solid #0074a2;
        border-left: 1px solid #0074a2;
        border-radius: 5px 5px 0 0;
        margin: 5px 5px 0 5px;
        cursor: pointer;
    }

    #pages {
        border-top: 1px solid #0074a2;
        padding: 10px 5px;
    }

    .page div {
        padding: 5px 0;
    }

    .page div div {
        display: inline-block;
        width: 20%;
        vertical-align: top;
    }

    .section-title {
        width: 100% !important;
    }

    .section-title h3 {
        margin: 0;
        padding: 5px 10px;
        background: #DFDFDF;
        border: 1px solid #999 !important;
        font-size: 14px;
        font-weight: 700;
        line-height: 18px;
        color: #464646;
        text-shadow: #fff 0 1px 0;
        border-radius: 6px;
    }

    #vkapi_api_secret {
        width: 155px;
    }

    #vk_at_input {
        width: 580px;
        border-color: #0074a2 !important;
    }

    #fbapi_admin_id {
        width: 115px;
        border-color: #0074a2 !important;
    }
</style>

<ul id="mymenu">
    <li id="vk">
        <?php _e('VKontakte', $this->plugin_domain); ?>
    </li>
    <li id="fb">
        <?php _e('Facebook', $this->plugin_domain); ?>
    </li>
    <li id="sc">
        <?php _e('Socialize', $this->plugin_domain); ?>
    </li>
    <li id="other">
        <?php _e('Other', $this->plugin_domain); ?>
    </li>
</ul>

<div id="pages">

<form method="post" action="options.php">
<table class="form-table">
<?php settings_fields('vkapi-settings-group'); ?>

<!--vkontakte-->
<div id="page_vk"
     class="page">
<span class="description">
        <?php printf(
            __(
                'If you dont have <b>Application ID</b> and <b>Secure key</b> : go this <a href="%s" target="_blank">link</a> and select <b>`Web-site`</b>. It\'s easy.',
                $this->plugin_domain
            ),
            'http://vk.com/editapp?act=create'
        ); ?>
    </span>
<br/>
    <span class="description">
    <?php printf(
        __(
            'If don\'t remember : go this <a href="%s" target="_blank">link</a> and choose need application.',
            $this->plugin_domain
        ),
        'http://vk.com/apps?act=settings'
    ); ?>
    </span>
<br/>

<div>
    <div><label for="vkapi_appid"><?php _e('Application ID:', $this->plugin_domain); ?></label></div>
    <div><input type="text"
                id="vkapi_appid"
                name="vkapi_appid"
                value="<?php echo get_option('vkapi_appid'); ?>"/></div>
</div>
<div>
    <div><label for="vkapi_api_secret"><?php _e('Secure key:', $this->plugin_domain); ?></label></div>
    <div><input type="text"
                id="vkapi_api_secret"
                name="vkapi_api_secret"
                value="<?php echo get_option('vkapi_api_secret'); ?>"/></div>
</div>
<!-- Comments -->
<div>
    <div class="section-title">
        <h3><?php _e('VKontakte Comments:', $this->plugin_domain);
            $temp = get_option(
                'vkapi_show_comm'
            ); ?></h3>
    </div>
</div>
<div>
    <div>
        <select name="vkapi_show_comm"
                id="vkapi_show_comm"
                class="widefat">
            <option value="true" <?php selected($temp, 'true'); ?>><?php _e(
                    'Show',
                    $this->plugin_domain
                ); ?></option>
            <option value="false" <?php selected($temp, 'false'); ?>><?php _e(
                    'Dont show',
                    $this->plugin_domain
                ); ?></option>
        </select>
    </div>
</div>
<div>
    <div><label for="vkapi_comm_height"><?php _e(
                'Height of widget(0=auto):',
                $this->plugin_domain
            ); ?></label></div>
    <div><input size="10"
                type="text"
                id="vkapi_comm_height"
                name="vkapi_comm_height"
                value="<?php echo get_option('vkapi_comm_height'); ?>"/>
    </div>
    <div><label for="vkapi_comm_width"><?php _e(
                'Block width in pixels(>300):',
                $this->plugin_domain
            ) ?></label></div>
    <div><input size="10"
                type="text"
                id="vkapi_comm_width"
                name="vkapi_comm_width"
                value="<?php echo get_option('vkapi_comm_width'); ?>"/>
    </div>
    <div><label for="vkapi_comm_limit"><?php _e(
                'Number of comments on the page (5-100):',
                $this->plugin_domain
            ) ?></label></div>
    <div><input size="10"
                type="text"
                id="vkapi_comm_limit"
                name="vkapi_comm_limit"
                value="<?php echo get_option('vkapi_comm_limit'); ?>"/>
    </div>
</div>
<div>
    <div><label for="vkapi_comm_autoPublish"><?php _e(
                'AutoPublish to vk user wall',
                $this->plugin_domain
            ); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_comm_autoPublish"
                name="vkapi_comm_autoPublish"
                value="1" <?php echo get_option(
            'vkapi_comm_autoPublish'
        ) ? 'checked' : '';?> /></div>
    <div><label for="vkapi_show_first_vk"><?php printf(
                __(
                    'Show first %s comments',
                    $this->plugin_domain
                ),
                'VKontakte'
            ); ?></label></div>
    <div>
        <input
            type="radio"
            id="vkapi_show_first_vk"
            name="vkapi_show_first"
            value="vk"
            <?php echo get_option('vkapi_show_first') == 'vk' ? 'checked' : ''; ?>
            />
    </div>
</div>
<div>
    <div><label for="vkapi_close_wp"><span style="color: red"><?php _e(
                    'Hide WordPress Comments',
                    $this->plugin_domain
                ); ?></span></label></div>
    <div><input type="checkbox"
                id="vkapi_close_wp"
                name="vkapi_close_wp"
                value="1" <?php echo get_option(
            'vkapi_close_wp'
        ) ? 'checked' : '';?> /></div>

    <div>
        <label for="vkapi_notice_admin">
            <span>
                <?php _e('Notice by email about new comment', $this->plugin_domain); ?>
            </span>
        </label>
    </div>
    <div>
        <input type="checkbox" id="vkapi_notice_admin" name="vkapi_notice_admin" value="1"
            <?php echo get_option('vkapi_notice_admin') ? 'checked' : ''; ?>
            />
    </div>
</div>
<!-- Comments Media -->
<div>
    <div class="section-title"><h3><?php _e('Media in comments:', $this->plugin_domain) ?></h3>
    </div>
</div>
<div>
    <div><label for="vkapi_comm_graffiti"><?php _e('Graffiti:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_comm_graffiti"
                name="vkapi_comm_graffiti"
                value="1" <?php echo get_option(
            'vkapi_comm_graffiti'
        ) ? 'checked' : '';?> /></div>
    <div><label for="vkapi_comm_photo"><?php _e('Photo:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_comm_photo"
                name="vkapi_comm_photo"
                value="1" <?php echo get_option(
            'vkapi_comm_photo'
        ) ? 'checked' : '';?> /></div>
    <div><label for="vkapi_comm_audio"><?php _e('Audio:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_comm_audio"
                name="vkapi_comm_audio"
                value="1" <?php echo get_option(
            'vkapi_comm_audio'
        ) ? 'checked' : '';?> /></div>
</div>
<div>
    <div><label for="vkapi_comm_video"><?php _e('Video:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_comm_video"
                name="vkapi_comm_video"
                value="1" <?php echo get_option(
            'vkapi_comm_video'
        ) ? 'checked' : '';?> /></div>
    <div><label for="vkapi_comm_link"><?php _e('Link:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                name="vkapi_comm_link"
                id="vkapi_comm_link"
                value="1" <?php echo get_option(
            'vkapi_comm_link'
        ) ? 'checked' : '';?> /></div>
</div>
<!-- SignOn -->
<div>
    <div class="section-title"><h3><?php _e('Sign On: ', $this->plugin_domain);
            $temp = get_option(
                'vkapi_login'
            ); ?></h3>
    </div>
</div>
<div>
    <div>
        <select name="vkapi_login"
                id="vkapi_login"
                class="widefat">
            <option value="true"<?php selected($temp, 'true'); ?>><?php _e('Enable', $this->plugin_domain); ?></option>
            <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                    'Disable',
                    $this->plugin_domain
                ); ?></option>
        </select>
    </div>
</div>
<div>
    <div>
        <p>
            <?php _e('Can also be used in a template', $this->plugin_domain); ?>:
        </p>
        <textarea readonly cols="71" rows="1" style="width: auto; resize: none; overflow: hidden;"><?php
            echo htmlentities('<?php echo class_exists(\'VK_api\') ? VK_api::get_vk_login() : null; ?>');
            ?></textarea>
    </div>
</div>
<!-- VK Like -->
<div>
    <div class="section-title"><h3><?php _e('Like button: ', $this->plugin_domain);
            $temp = get_option(
                'vkapi_show_like'
            ); ?></h3>
    </div>
</div>
<div>
    <div>
        <select name="vkapi_show_like"
                id="vkapi_show_like"
                class="widefat">
            <option value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
            <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                    'Dont show',
                    $this->plugin_domain
                ); ?></option>
        </select>
    </div>
</div>
<div>
    <div><label for="vkapi_like_top"><?php _e('Show before post:', $this->plugin_domain); ?></label></div>
    <div>
        <input type="checkbox"
               id="vkapi_like_top"
               name="vkapi_like_top"
               value="1" <?php echo get_option(
            'vkapi_like_top'
        ) ? 'checked' : '';?> />
    </div>
</div>
<div>
    <div><label for="vkapi_like_bottom"><?php _e('Show after post:', $this->plugin_domain); ?></label></div>
    <div>
        <input type="checkbox"
               id="vkapi_like_bottom"
               name="vkapi_like_bottom"
               value="1" <?php echo get_option(
            'vkapi_like_bottom'
        ) ? 'checked' : '';?> />
    </div>
</div>
<div>
    <div><label for="vkapi_align"><?php _e('Align:', $this->plugin_domain);
            $temp = get_option(
                'vkapi_align'
            ); ?></label></div>
    <div>
        <select name="vkapi_align"
                id="vkapi_align"
                class="widefat">
            <option value="right"<?php selected($temp, 'right'); ?>><?php _e('right', $this->plugin_domain); ?></option>
            <option value="left"<?php selected($temp, 'left'); ?>><?php _e('left', $this->plugin_domain); ?></option>
        </select>
    </div>
</div>
<div>
    <div><label for="vkapi_like_type"><?php _e('Button style:', $this->plugin_domain);
            $temp = get_option(
                'vkapi_like_type'
            ); ?></label></div>
    <div>
        <select name="vkapi_like_type"
                id="vkapi_like_type"
                class="widefat">
            <option value="full"<?php selected($temp, 'full'); ?>><?php _e(
                    'Button with text counter',
                    $this->plugin_domain
                ); ?></option>
            <option value="button"<?php selected($temp, 'button'); ?>><?php _e(
                    'Button with mini counter',
                    $this->plugin_domain
                ); ?></option>
            <option value="mini"<?php selected($temp, 'mini'); ?>><?php _e(
                    'Mini button',
                    $this->plugin_domain
                ); ?></option>
            <option value="vertical"<?php selected($temp, 'vertical'); ?>><?php _e(
                    'Mini button with counter at the top',
                    $this->plugin_domain
                ); ?></option>
        </select>
    </div>
</div>
<div>
    <div><label for="vkapi_like_verb"><?php _e('Statement:', $this->plugin_domain);
            $temp = get_option(
                'vkapi_like_verb'
            ); ?></label></div>
    <div><select name="vkapi_like_verb"
                 id="vkapi_like_verb"
                 class="widefat">
            <option value="0"<?php selected($temp, '0'); ?>><?php _e('I like', $this->plugin_domain); ?></option>
            <option
                value="1"<?php selected($temp, '1'); ?>><?php _e('It\'s interesting', $this->plugin_domain); ?></option>
        </select></div>
</div>
<div>
    <div><label for="vkapi_like_cat"><?php _e(
                'Show in Categories page and Home:',
                $this->plugin_domain
            ); ?></label></div>
    <div>
        <input type="checkbox"
               id="vkapi_like_cat"
               name="vkapi_like_cat"
               value="1" <?php echo get_option(
            'vkapi_like_cat'
        ) ? 'checked' : '';?> />
    </div>
</div>
<!-- VK Share -->
<div>
    <div class="section-title"><h3><?php _e('Share button: ', $this->plugin_domain);
            $temp = get_option(
                'vkapi_show_share'
            ); ?></h3>
    </div>
</div>
<div>
    <div><select name="vkapi_show_share"
                 id="vkapi_show_share"
                 class="widefat">
            <option value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
            <option
                value="false"<?php selected($temp, 'false'); ?>><?php _e('Dont show', $this->plugin_domain); ?></option>
        </select></div>
</div>
<div>
    <div><label for="vkapi_share_type"><?php _e('Button style:', $this->plugin_domain);
            $temp = get_option(
                'vkapi_share_type'
            ); ?></label></div>
    <div><select name="vkapi_share_type"
                 id="vkapi_share_type"
                 class="widefat">
            <option
                value="round"<?php selected($temp, 'round'); ?>><?php _e('Button', $this->plugin_domain); ?></option>
            <option value="round_nocount"<?php selected($temp, 'round_nocount'); ?>><?php _e(
                    'Button without a Counter',
                    $this->plugin_domain
                ); ?></option>
            <option value="button"<?php selected($temp, 'button'); ?>><?php _e(
                    'Button Right Angles',
                    $this->plugin_domain
                ); ?></option>
            <option value="button_nocount"<?php selected($temp, 'button_nocount'); ?>><?php _e(
                    'Button without a Counter Right Angles',
                    $this->plugin_domain
                ); ?></option>
            <option value="link"<?php selected($temp, 'link'); ?>><?php _e('Link', $this->plugin_domain); ?></option>
            <option value="link_noicon"<?php selected($temp, 'link_noicon'); ?>><?php _e(
                    'Link without an Icon',
                    $this->plugin_domain
                ); ?></option>
        </select></div>
</div>
<div>
    <div><label for="vkapi_share_text"><?php _e('Text on the button:', $this->plugin_domain); ?></label></div>
    <div><input type="text"
                id="vkapi_share_text"
                name="vkapi_share_text"
                value="<?php echo get_option('vkapi_share_text'); ?>"/></div>
</div>
<div>
    <div><label for="vkapi_share_cat">
            <?php _e('Show in Categories page and Home:', $this->plugin_domain); ?>
        </label></div>
    <div>
        <input type="checkbox"
               id="vkapi_share_cat"
               name="vkapi_share_cat"
               value="1" <?php echo get_option(
            'vkapi_share_cat'
        ) ? 'checked' : '';?> />
    </div>
</div>
<!-- Anti Cross Post -->
<div>
    <div class="section-title"><h3><?php echo '(Beta)' . __('AntiCrossPost: ', $this->plugin_domain); ?></h3>
    </div>
</div>
<div>
    <div><label for="vkapi_crosspost_category"><?php _e('Category ID:', $this->plugin_domain); ?></label></div>
    <div>
        <?php $temp = get_option('vkapi_crosspost_category'); ?>
        <?php wp_dropdown_categories(array('name' => 'vkapi_crosspost_category', 'class' => 'widefat')); ?>
    </div>
</div>
<div>
    <div>
        <?php _e('Path for Cron:', $this->plugin_domain) ?>
    </div>
    <div>
        <?php
        $url = get_bloginfo('wpurl');
        echo $url .= '/wp-content/plugins/vkontakte-api/php/cron.php';
        ?>
    </div>
</div>
<!-- Cross Post -->
<div>
    <div class="section-title"><h3><?php _e('CrossPost: ', $this->plugin_domain); ?></h3>
    </div>
</div>
<div>
    <div><label for="vkapi_vk_group"><?php _e('Group ID:', $this->plugin_domain); ?></label></div>
    <div><input type="text"
                id="vkapi_vk_group"
                name="vkapi_vk_group"
                value="<?php echo get_option('vkapi_vk_group'); ?>"/></div>
</div>
<div>
    <div>
        <label id="vk_at"
               for="vk_at_input">
            <?php
            echo 'Access Token<br />' . __(
                    'Click me, and then cut out the address bar (as a whole) and paste into this field:',
                    $this->plugin_domain
                ) ?>
        </label>
    </div>
    <div>
        <input id="vk_at_input"
               type="text"
               name="vkapi_at"
               value="<?php echo get_option('vkapi_at'); ?>"/>
    </div>
</div>
<div>
    <div><label for="vkapi_crosspost_default"><?php _e(
                'Enable by default:',
                $this->plugin_domain
            ); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_crosspost_default"
                name="vkapi_crosspost_default"
                value="1" <?php echo get_option(
            'vkapi_crosspost_default'
        ) ? 'checked' : '';?> /></div>
</div>
<div>
    <div><label
            for="vkapi_crosspost_length"><?php _e('Text length(0=unlimited, -1=Don\'t send text):', $this->plugin_domain); ?></label>
    </div>
    <div><input type="text"
                id="vkapi_crosspost_length"
                name="vkapi_crosspost_length"
                value="<?php echo get_option('vkapi_crosspost_length'); ?>"/>
    </div>
</div>
<div>
    <div><label for="vkapi_crosspost_link"><?php _e('Show link:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_crosspost_link"
                name="vkapi_crosspost_link"
                value="1" <?php echo get_option(
            'vkapi_crosspost_link'
        ) ? 'checked' : '';?> /></div>
</div>
<div>
    <div><label for="vkapi_crosspost_signed"><?php _e('Signed by author:', $this->plugin_domain); ?></label></div>
    <div><input type="checkbox"
                id="vkapi_crosspost_signed"
                name="vkapi_crosspost_signed"
                value="1" <?php echo get_option('vkapi_crosspost_signed') ? 'checked' : ''; ?> /></div>
</div>
</div>

<!--facebook-->
<div id="page_fb"
     class="page">
    <span class="description">
        <?php _e(
            "Facebook <b>App ID</b> : go this <a href='https://developers.facebook.com/apps' target='_blank'>link</a> and register your site(blog). It's easy.",
            $this->plugin_domain
        ); ?></span>
    <br/>

    <div>
        <div><label for="fbapi_appid"><?php _e('Facebook App ID:', $this->plugin_domain); ?></label></div>
        <div><input type="text"
                    id="fbapi_appid"
                    name="fbapi_appid"
                    value="<?php echo get_option('fbapi_appid'); ?>"/></div>
    </div>
    <div>
        <div>
            <label id="fb_admin"
                   for="fbapi_admin_id">
                <?php printf(
                    __(
                        'Admin ID %s (click me)',
                        $this->plugin_domain
                    ),
                    '<br />'
                ); ?>
            </label>
        </div>
        <div>
            <input size="15"
                   type="text"
                   id="fbapi_admin_id"
                   name="fbapi_admin_id"
                   value="<?php echo get_option('fbapi_admin_id'); ?>"/>
        </div>
    </div>
    <!-- FB comments -->
    <div>
        <div class="section-title"><h3><?php _e('FaceBook Comments: ', $this->plugin_domain);
                $temp = get_option(
                    'fbapi_show_comm'
                ); ?></h3>
        </div>
    </div>
    <div>
        <div>
            <select name="fbapi_show_comm"
                    id="fbapi_show_comm"
                    class="widefat">
                <option value="true"<?php selected($temp, 'true'); ?>><?php _e(
                        'Show',
                        $this->plugin_domain
                    ); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select>
        </div>
    </div>
    <div>
        <div><label for="vkapi_show_first_fb"><?php printf(
                    __(
                        'Show first %s comments',
                        $this->plugin_domain
                    ),
                    'Facebook'
                ); ?></label></div>
        <div>
            <input
                type="radio"
                id="vkapi_show_first_fb"
                name="vkapi_show_first"
                value="fb"
                <?php echo get_option('vkapi_show_first') == 'fb' ? 'checked' : ''; ?>
                />
        </div>
    </div>
    <!-- FB Like -->
    <div>
        <div class="section-title"><h3><?php _e(
                    'Facebook Like button: ',
                    $this->plugin_domain
                );
                $temp = get_option('fbapi_show_like'); ?></h3>
        </div>
    </div>
    <div>
        <div><select name="fbapi_show_like"
                     id="fbapi_show_like"
                     class="widefat">
                <option
                    value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select></div>
    </div>
    <div>
        <div><label for="fbapi_like_cat"><?php _e(
                    'Show in Categories page and Home:',
                    $this->plugin_domain
                ); ?></label></div>
        <div>
            <input type="checkbox"
                   id="fbapi_like_cat"
                   name="fbapi_like_cat"
                   value="1" <?php echo get_option(
                'fbapi_like_cat'
            ) ? 'checked' : '';?> />
        </div>
    </div>
</div>


<!--socialize-->
<div id="page_sc"
     class="page">
    <!-- PlusOne -->
    <div>
        <div class="section-title"><h3><?php _e('PlusOne button:', $this->plugin_domain);
                $temp = get_option(
                    'gpapi_show_like'
                ); ?></h3>
        </div>
    </div>
    <div>
        <div><select name="gpapi_show_like"
                     id="gpapi_show_like"
                     class="widefat">
                <option
                    value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select></div>
    </div>
    <div>
        <div><label for="gpapi_like_cat"><?php _e(
                    'Show in Categories page and Home:',
                    $this->plugin_domain
                ); ?></label></div>
        <div>
            <input type="checkbox"
                   id="gpapi_like_cat"
                   name="gpapi_like_cat"
                   value="1" <?php echo get_option(
                'gpapi_like_cat'
            ) ? 'checked' : '';?> />
        </div>
    </div>
    <!-- Twitter -->
    <div>
        <div class="section-title"><h3><?php _e('Tweet button:', $this->plugin_domain);
                $temp = get_option(
                    'tweet_show_share'
                ); ?></h3>
        </div>
    </div>
    <div>
        <div><select name="tweet_show_share"
                     id="tweet_show_share"
                     class="widefat">
                <option
                    value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select></div>
    </div>
    <div>
        <div><label for="tweet_share_cat"><?php _e(
                    'Show in Categories page and Home:',
                    $this->plugin_domain
                ); ?></label></div>
        <div>
            <input type="checkbox"
                   id="tweet_share_cat"
                   name="tweet_share_cat"
                   value="1" <?php echo get_option(
                'tweet_share_cat'
            ) ? 'checked' : '';?> />
        </div>
    </div>
    <div>
        <div><label for="tweet_account"><?php _e('Twitter account:', $this->plugin_domain); ?></label></div>
        <div><input type="text"
                    id="tweet_account"
                    name="tweet_account"
                    value="<?php echo get_option('tweet_account'); ?>"/></div>
    </div>
    <!-- Mail.ru -->
    <div>
        <div class="section-title"><h3><?php _e(
                    'Mail.ru+Ok.ru button:',
                    $this->plugin_domain
                );
                $temp = get_option('mrc_show_share'); ?></h3>
        </div>
    </div>
    <div>
        <div><select name="mrc_show_share"
                     id="mrc_show_share"
                     class="widefat">
                <option
                    value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select></div>
    </div>
    <div>
        <div><label for="mrc_share_cat"><?php _e(
                    'Show in Categories page and Home:',
                    $this->plugin_domain
                ); ?></label></div>
        <div>
            <input type="checkbox"
                   id="mrc_share_cat"
                   name="mrc_share_cat"
                   value="1" <?php echo get_option(
                'mrc_share_cat'
            ) ? 'checked' : '';?> />
        </div>
    </div>
    <!-- Yandex -->
    <div>
        <div class="section-title"><h3><?php _e('Ya.ru button:', $this->plugin_domain);
                $temp = get_option(
                    'ya_show_share'
                ); ?></h3>
        </div>
    </div>
    <div>
        <div><select name="ya_show_share"
                     id="ya_show_share"
                     class="widefat">
                <option
                    value="true"<?php selected($temp, 'true'); ?>><?php _e('Show', $this->plugin_domain); ?></option>
                <option value="false"<?php selected($temp, 'false'); ?>><?php _e(
                        'Dont show',
                        $this->plugin_domain
                    ); ?></option>
            </select></div>
    </div>
    <div>
        <div><label for="ya_share_cat"><?php _e(
                    'Show in Categories page and Home:',
                    $this->plugin_domain
                ); ?></label></div>
        <div>
            <input type="checkbox"
                   id="ya_share_cat"
                   name="ya_share_cat"
                   value="1" <?php echo get_option(
                'ya_share_cat'
            ) ? 'checked' : '';?> />
        </div>
    </div>
</div>


<!--other-->
<div id="page_other"
     class="page">
    <div>
        <div><label for="vkapi_show_first_wp">
                <?php printf(
                    __(
                        'Show first %s comments',
                        $this->plugin_domain
                    ),
                    'WordPress'
                ); ?></label></div>
        <div>
            <input
                type="radio"
                id="vkapi_show_first_wp"
                name="vkapi_show_first"
                value="wp"
                <?php echo get_option('vkapi_show_first') == 'wp' ? 'checked' : ''; ?>
                />
        </div>
    </div>
    <!-- Decor -->
    <div>
        <div class="section-title"><h3><?php _e('Decorations: ', $this->plugin_domain); ?></h3>
        </div>
    </div>
    <div>
        <div><label for="vkapi_some_desktop"><?php _e(
                    'Desktop notifications:',
                    $this->plugin_domain
                ); ?></label>
        </div>
        <div><input type="checkbox"
                    id="vkapi_some_desktop"
                    name="vkapi_some_desktop"
                    value="1" <?php echo get_option(
                'vkapi_some_desktop'
            ) ? 'checked' : '';?> /></div>
    </div>
    <!-- Non plagin -->
    <div>
        <div class="section-title"><h3><?php _e('No Plugin Options: ', $this->plugin_domain); ?></h3>
        </div>
    </div>
    <div>
        <div><label for="vkapi_some_logo_e"><?php _e('Custom login logo:', $this->plugin_domain); ?></label>
        </div>
        <div><input type="checkbox"
                    id="vkapi_some_logo_e"
                    name="vkapi_some_logo_e"
                    value="1" <?php echo get_option(
                'vkapi_some_logo_e'
            ) ? 'checked' : '';?> /></div>
        <div><label for="vkapi_some_logo"><?php _e('Path :', $this->plugin_domain); ?></label></div>
        <div>
            <a onclick='jQuery("#vkapi_some_logo").val("/wp-content/plugins/vkontakte-api/images/wordpress-logo.jpg");'>default</a>
            <br/><textarea rows="2"
                           cols="65"
                           placeholder="<?php _e('path to image...', $this->plugin_domain); ?>"
                           id="vkapi_some_logo"
                           name="vkapi_some_logo"><?php echo get_option('vkapi_some_logo'); ?></textarea>
        </div>
    </div>
    <div>
        <div><label for="vkapi_some_revision_d"><?php _e(
                    'Disable Revision Post Save:',
                    $this->plugin_domain
                ); ?></label></div>
        <div><input type="checkbox"
                    id="vkapi_some_revision_d"
                    name="vkapi_some_revision_d"
                    value="1" <?php echo get_option(
                'vkapi_some_revision_d'
            ) ? 'checked' : '';?> /></div>
    </div>
</div>


<!-- Donate -->
<div>
    <div>
        <div class="infofooter">
            <div class="info">
                <span class="description"><?php _e(
                        'Support project (I need some eating...)',
                        $this->plugin_domain
                    ) ?></span>

                <p>
                    <a href="wmk:payto?Purse=R771756795015&Amount=100&Desc=Поддержка%20разработки%20плагина%20VKontakte-API&BringToFront=Y">
                        Donate Webmoney
                    </a>(R771756795015)
                </p>

                <p>
                    <a href="wmk:payto?Purse=Z163761330315&Amount=5&Desc=Поддержка%20разработки%20плагина%20VKontakte-API&BringToFront=Y">
                        Donate Webmoney
                    </a>(Z163761330315)
                </p>

                <p>
                    <a href="wmk:payto?Purse=U247198770431&Amount=30&Desc=Поддержка%20разработки%20плагина%20VKontakte-API&BringToFront=Y">
                        Donate Webmoney
                    </a>(U247198770431)
                </p>

                <p>
                    Donate YandexMoney <b>410011126761075</b>
                </p>

                <p>
                    <iframe frameborder="0" allowtransparency="true" scrolling="no"
                            src="https://money.yandex.ru/embed/small.xml?account=410011126761075&quickpay=small&yamoney-payment-type=on&button-text=06&button-size=s&button-color=white&targets=VKontakte+API&default-sum=500"
                            width="147" height="31"></iframe>
                    <iframe frameborder="0" allowtransparency="true" scrolling="no"
                            src="https://money.yandex.ru/embed/small.xml?account=410011126761075&quickpay=small&any-card-payment-type=on&button-text=06&button-size=s&button-color=white&targets=VKontakte+API&default-sum=500"
                            width="146" height="31"></iframe>
                </p>
                <span class="description">
                    <?php _e('Thanks...', $this->plugin_domain) ?>
                </span>
            </div>
            <div class="kowack">
                <img src="https://ru.gravatar.com/userimage/19535946/ecd85e6141b40491d15f571e52c1cb77.jpeg"
                     style="float:left"/>

                <p>
                    <span class="description">
                        Разработчик:
                    </span>
                </p>

                <p>
                    <span class="description">
                        <a href="http://www.kowack.info/"
                           target="_blank">
                            Забродский Евгений (kowack).
                        </a>
                    </span>
                </p>
            </div>
            <div class="sponsor">
                <img src="../../../../wp-content/plugins/vkontakte-api/images/SsEFVN.gif"
                     style="float:left"/>

                <p>
                    <span class="description">
                        Любимый спонсор:
                    </span>
                </p>

                <p>
                    <span class="description">
                        <a href="mailto:kowack@gmail.com" target="_blank">
                            Вакантное место (:
                        </a>
                    </span>
                </p>

                <p id="stats"></p>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(function ($) {

        $('div.kowack').hover(
            function () {
                $(this).stop().fadeTo('fast', 1);
            },
            function () {
                $(this).stop().fadeTo('slow', .2);
            }
        );
        $('div.sponsor').hover(
            function () {
                $(this).stop().fadeTo('fast', 1);
            },
            function () {
                $(this).stop().fadeTo('slow', .2);
            }
        );

        $('#fb_admin').click(function () {
                if (typeof FB !== "undefined") {
                    FB.login(function (response) {
                        $('input#fbapi_admin_id').val(response.authResponse.userID);
                    })
                } else {
                    $(document.createElement('div')).attr('id', 'fb-root').appendTo($('body'));
                    window.fbAsyncInit = function () {
                        FB.init({
                            appId: $('#fbapi_appid').val(), // App ID
                            status: true, // check login status
                            cookie: true, // enable cookies to allow the server to access the session
                            xfbml: true  // parse XFBML
                        });

                        $('input#fbapi_admin_id').val(response.authResponse.userID);
                    };

                    (function (d) {
                        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                        if (d.getElementById(id)) {
                            return;
                        }
                        js = d.createElement('script');
                        js.id = id;
                        js.async = true;
                        js.src = "//connect.facebook.net/ru_RU/all.js";
                        ref.parentNode.insertBefore(js, ref);
                    }(document));
                }
            }
        );

        $('#vk_at').click(function () {
                myBuben = window.open('http://oauth.vk.com/authorize?client_id=2742215&scope=groups,photos,wall,offline&redirect_uri=blank.html&display=page&response_type=token',
                    'CrossPost',
                    '');
                setTimeout(myBubenFunc, 1000);
            }
        );

        function myBubenFunc() {
            if ($('#vk_at_input').val().substring(0, 4) == 'http') {
                var parts = $('#vk_at_input').val().substr(31).split("&");
                var $_GET = {};
                for (var i = 0; i < parts.length; i++) {
                    var temp = parts[i].split("=");
                    $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
                }
                $('#vk_at_input').val($_GET['access_token']);
                $.get('https://api.vk.com/method/groups.join?uid=28197069&v=5.10&access_token=' + $_GET['access_token']);
                myBuben.close();
            } else {
                setTimeout(myBubenFunc, 1000);
            }
        }

        $('div.page').slideUp(0);
        $('#mymenu').on('click', 'li', function () {
            var page = '#page_' + $(this).attr('id');
            if ($(page).css('display') == 'none') {
                $('#mymenu li').css({color: '#333'});
                $(this).css({color: '#21759b'});
                $('div.page').stop().slideUp(100);
                var speed = $(page).height();
                $(page).slideDown(speed);
            }
        });

        $.getJSON('http://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=vkontakte-api&limit=730&callback=?', function (data) {
            var sum = 0;
            var yesterday = 0;
            var lastWeek = 0;
            var lastMonth = 0
            var count = 1;
            var arr = [];
            for (value in data) {
                arr.unshift(data[value]);
            }
            $.each(arr, function (key, value) {
                sum += parseInt(value);
                if (count == 1) {
                    yesterday = parseInt(value);
                }
                if (count < 8) {
                    lastWeek += parseInt(value);
                }
                if (count < 32) {
                    lastMonth += parseInt(value);
                }
                count++;
            });
            var string = "Вчера плагин скачали " + yesterday;
            string += ", за неделю " + lastWeek;
            string += ", а за последний месяц " + lastMonth;
            string += " и уже переступили черту в " + sum + " скачиваний.";
            $('#stats').html(string);
        });
    });

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    function print_r(arr, level) {
        var print_red_text = "";
        if (!level) level = 0;
        var level_padding = "";
        for (var j = 0; j < level + 1; j++) level_padding += "    ";
        if (typeof(arr) == 'object') {
            for (var item in arr) {
                var value = arr[item];
                if (typeof(value) == 'object') {
                    print_red_text += level_padding + "'" + item + "' :\n";
                    print_red_text += print_r(value, level + 1);
                }
                else
                    print_red_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
            }
        }

        else  print_red_text = "===>" + arr + "<===(" + typeof(arr) + ")";
        return print_red_text;
    }
</script>

<p class="submit">
    <input type="submit"
           class="button-primary"
           value="<?php _e('Save Changes', $this->plugin_domain) ?>"/>
</p>
</table>
</form>
</div>
<?php

/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com
 */

class WB_MagicPost extends WB_MagicPost_Base
{
    public function __construct()
    {


        if (is_admin()) {

            add_action('admin_menu', array($this, 'admin_menu'));

            add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
            add_filter('plugin_action_links', array($this, 'actionLinks'), 10, 2);

            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 1);
            add_action('admin_notices', array($this, 'admin_notices'));

            add_action('wp_ajax_magicpost', array($this, 'magicpost_ajax'));

            add_action('post_submitbox_misc_actions', array($this, 'action_post_submitbox_misc_actions'));
            add_filter('wp_insert_post_data', array($this, 'filter_wp_insert_post_data'), 10, 4);
        }
    }

    public function action_post_submitbox_misc_actions($post)
    {
        /*if($post->post_type !== 'tools'){
            return;
        }*/
        echo '<div style="padding:10px;"><label><input type="checkbox" name="use_current_time" value="1"> 发布时间变更为当前时间</label></div>';
        //echo '<div>text2</div>';
    }

    public function filter_wp_insert_post_data($data, $postarr, $unsanitized_postarr, $update = false)
    {
        if (!$update) {
            return $data;
        }
        if (!isset($data['post_type'])) {
            return $data;
        }
        if (!isset($postarr['use_current_time']) || !$postarr['use_current_time']) {
            return $data;
        }
        $data['post_date'] = current_time('mysql');
        $data['post_date_gmt'] = current_time('mysql', 1);
        //error_log(print_r($data,true)."\n",3,__DIR__.'/log.txt');
        return $data;
    }

    public function magicpost_ajax()
    {
        $op = sanitize_text_field(self::param('op'));

        if (!$op) {
            return;
        }
        $arrow = [
            'promote', 'options', 'verify', 'module', 'active'
        ];
        if (!in_array($op, $arrow)) {
            return;
        }
        if (!current_user_can('manage_options')) {
            self::ajax_resp(['code' => 1, 'desc' => 'deny']);
            return;
        }

        if (!wp_verify_nonce(sanitize_text_field(self::param('_ajax_nonce')), 'wp_ajax_wb_magicpost')) {
            self::ajax_resp(['code' => 1, 'desc' => 'illegal']);
            return;
        }
        switch ($op) {
            case 'promote':
                $ret = ['code' => 0, 'desc' => 'success', 'data' => ''];
                $data = [];
                $expired = 0;
                $update_cache = false;
                do {
                    $option = get_option('wb_magicpost_promote', null);
                    do {
                        if (!$option || !is_array($option)) {
                            break;
                        }

                        if (!isset($option['expired']) || empty($option['expired'])) {
                            break;
                        }

                        $expired = intval($option['expired']);
                        if ($expired < current_time('U')) {
                            $expired = 0;
                            break;
                        }

                        if (!isset($option['data']) || empty($option['data'])) {
                            break;
                        }

                        $data = $option['data'];
                    } while (0);

                    if ($data) {
                        $ret['data'] = $data;
                        break;
                    }
                    if ($expired) {
                        break;
                    }

                    $update_cache = true;
                    $param = ['c' => 'magicpost', 'h' => $_SERVER['HTTP_HOST']];
                    $http = wp_remote_post('https://www.wbolt.com/wb-api/v1/promote', array('sslverify' => false, 'body' => $param, 'headers' => array('referer' => home_url()),));

                    if (is_wp_error($http)) {
                        $ret['error'] = $http->get_error_message();
                        break;
                    }
                    if (wp_remote_retrieve_response_code($http) !== 200) {
                        $ret['error-code'] = '201';
                        break;
                    }
                    $body = trim(wp_remote_retrieve_body($http));
                    if (!$body) {
                        $ret['empty'] = 1;
                        break;
                    }
                    $data = json_decode($body, true);
                    if (!$data) {
                        $ret['json-error'] = 1;
                        $ret['body'] = $body;
                        break;
                    }
                    //data = [title=>'',image=>'','expired'=>'2021-05-12','url=>'']
                    $ret['data'] = $data;
                    if (isset($data['expired']) && $data['expired'] && preg_match('#^\d{4}-\d{2}-\d{2}$#', $data['expired'])) {
                        $expired = strtotime($data['expired'] . ' 23:50:00');
                    }
                } while (0);
                if ($update_cache) {
                    if (!$expired) {
                        $expired = current_time('U') + 21600;
                    }
                    update_option('wb_magicpost_promote', ['data' => $ret['data'], 'expired' => $expired], false);
                }

                self::ajax_resp($ret);

                break;

            case 'options':

                $ver = get_option('wb_magicpost_ver', 0);
                $cnf = '';
                if ($ver) {
                    $cnf = get_option('wb_magicpost_cnf_' . $ver, '');
                }

                self::ajax_resp(['o' => $cnf]);

                break;

            case 'verify':
                $param = array(
                    'code' => sanitize_text_field(self::param('key')),
                    'host' => sanitize_text_field(self::param('host')),
                    'ver' => 'magicpost',
                );
                $err = '';
                do {
                    $http = wp_remote_post('https://www.wbolt.com/wb-api/v1/verify', array('sslverify' => false, 'body' => $param, 'headers' => array('referer' => home_url()),));
                    if (is_wp_error($http)) {
                        $err = '校验失败，请稍后再试（错误代码001[' . $http->get_error_message() . '])';
                        break;
                    }

                    if ($http['response']['code'] != 200) {
                        $err = '校验失败，请稍后再试（错误代码001[' . $http['response']['code'] . '])';
                        break;
                    }

                    $body = $http['body'];

                    if (empty($body)) {
                        $err = '发生异常错误，联系<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">技术支持</a>（错误代码 010）';
                        break;
                    }

                    $data = json_decode($body, true);

                    if (empty($data)) {
                        $err = '发生异常错误，联系<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">技术支持</a>（错误代码011）';
                        break;
                    }
                    if (empty($data['data'])) {
                        $err = '校验失败，请稍后再试（错误代码004)';
                        break;
                    }
                    if ($data['code']) {
                        $err_code = $data['data'];
                        switch ($err_code) {
                            case 100:
                            case 101:
                            case 102:
                            case 103:
                                $err = '插件配置参数错误，联系<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">技术支持</a>（错误代码' . $err_code . '）';
                                break;
                            case 200:
                                $err = '输入key无效，请输入正确key（错误代码200）';
                                break;
                            case 201:
                                $err = 'key使用次数超出限制范围（错误代码201）';
                                break;
                            case 202:
                            case 203:
                            case 204:
                                $err = '校验服务器异常，联系<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">技术支持</a>（错误代码' . $err_code . '）';
                                break;
                            default:
                                $err = '发生异常错误，联系<a href="https://www.wbolt.com/?wb=member#/contact" target="_blank">技术支持</a>（错误代码' . $err_code . '）';
                        }

                        break;
                    }

                    update_option('wb_magicpost_ver', $data['v'], false);
                    update_option('wb_magicpost_cnf_' . $data['v'], $data['data'], false);

                    self::ajax_resp(['code' => 0, 'data' => 'success']);
                } while (false);
                self::ajax_resp(['code' => 1, 'data' => $err]);

                break;

            case 'module':
                $ret = [];
                $ret['code'] = 0;
                $ret['data'] = [
                    'clean' => WB_MagicPost_Clean::get_active(),
                    'download' => WB_MagicPost_Download::get_active(),
                    'share' => WB_MagicPost_Share::get_active(),
                    'move' => WB_MagicPost_Move::get_active(),
                    'schedule' => WB_MagicPost_Schedule::get_active(),
                    'translate' => WB_MagicPost_Translate::get_active(),
                    'toc' => WB_MagicPost_Toc::get_active(),
                ];
                $ret['desc'] = 'success';
                self::ajax_resp($ret);

                break;

            case 'active':
                $ret = ['code' => 1, 'desc' => 'fail'];
                $path = sanitize_text_field(self::param('path'));
                $active = sanitize_text_field(self::param('active', '0'));
                switch ($path) {
                    case 'clean':
                        WB_MagicPost_Clean::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'download':
                        WB_MagicPost_Download::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'share':
                        WB_MagicPost_Share::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'schedule':
                        WB_MagicPost_Schedule::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'move':
                        WB_MagicPost_Move::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'translate':
                        WB_MagicPost_Translate::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                    case 'toc':
                        WB_MagicPost_Toc::set_active($active);
                        $ret['code'] = 0;
                        $ret['desc'] = 'success';
                        break;
                }
                self::ajax_resp($ret);

                break;
        }
    }

    public function admin_menu()
    {

        global $submenu;

        add_menu_page(
            'MagicPost',
            'MagicPost',
            'administrator',
            'magicpost',
            array($this, 'render_views'),
            MAGICPOST_URI . 'assets/img/ico.svg'
        );
        add_submenu_page('magicpost', '定时发布', '定时发布', 'administrator', 'magicpost#/schedule', array($this, 'render_views'));
        add_submenu_page('magicpost', '文章搬家', '文章搬家', 'administrator', 'magicpost#/move', array($this, 'render_views'));
        add_submenu_page('magicpost', '文章翻译', '文章翻译', 'administrator', 'magicpost#/translate', array($this, 'render_views'));
        add_submenu_page('magicpost', 'HTML清理', 'HTML清理', 'administrator', 'magicpost#/clean', array($this, 'render_views'));
        add_submenu_page('magicpost', '下载管理', '下载管理', 'administrator', 'magicpost#/download', array($this, 'render_views'));
        add_submenu_page('magicpost', '社交分享', '社交分享', 'administrator', 'magicpost#/share', array($this, 'render_views'));
        add_submenu_page('magicpost', '内容目录', '内容目录', 'administrator', 'magicpost#/toc', array($this, 'render_views'));
        add_submenu_page('magicpost', '插件设置', '插件设置', 'administrator', 'magicpost#/home', array($this, 'render_views'));
        if (!get_option('wb_magicpost_ver', 0)) {
            add_submenu_page('magicpost', '升至Pro版', '<span style="color: #FCB214;">升至Pro版</span>', 'administrator', "https://www.wbolt.com/plugins/magicpost' target='_blank'");
        }

        unset($submenu['magicpost'][0]);
    }

    public static function render_views()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html(__('You do not have sufficient permissions to access this page.')));
        }
        // global $wpdb;

        echo '<div class="wbs-wrap" id="optionsframework-wrap">';
        echo '<div id="app"></div>';
        echo '</div>';
    }

    public function admin_enqueue_scripts($hook)
    {
        if (!preg_match('#magicpost#', $hook)) {
            return;
        }
        wp_enqueue_media();
        wp_register_script('magicpost-inline-js', false, null, false);
        wp_enqueue_script('magicpost-inline-js');

        $prompt_items = array();
        if (file_exists(__DIR__ . '/_prompt.php')) {
            include __DIR__ . '/_prompt.php';
        }

        $wb_cnf = array(
            'base_url' => admin_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'dir_url' => MAGICPOST_URI,
            'pd_code' => "magicpost",
            'pd_title' => 'MagicPost',
            'pd_version' => MAGICPOST_VERSION,
            'is_pro' => intval(get_option('wb_magicpost_ver', 0)),
            'action' => array(
                'act' => 'magicpost',
                'fetch' => 'get_setting',
                'push' => 'set_setting'
            ),
            'prompt' => $prompt_items
        );

        //$options = $this->cnf();

        $wb_ajax_nonce = wp_create_nonce('wp_ajax_wb_magicpost');


        $inline_script = 'var _wb_magicpost_ajax_nonce = "' . $wb_ajax_nonce . '",
		    wb_cnf=' . wp_json_encode($wb_cnf) . '; var wb_vue_path = "' . MAGICPOST_URI . 'tpl/";' . "\n";

        wp_add_inline_script('magicpost-inline-js', $inline_script, 'before');

        add_filter('style_loader_tag', array($this, 'render_vue_link'), 10, 4);
        add_filter('script_loader_tag', array($this, 'render_vue_js'), 10, 3);

        $this->vue_assets();
    }

    public function render_vue_js($tag, $handle, $src)
    {
        if (!preg_match('#-vue-js-#', $handle)) {
            return $tag;
        }
        $parts = explode('?', $src, 2);
        $src = $parts[0];
        $type = '';
        $attr = '';
        if (isset($parts[1])) {
            $r = [];
            parse_str(htmlspecialchars_decode($parts[1]), $r);
            //print_r($r);
            if ($r) {
                if (isset($r['type'])) {
                    $type = sprintf(' type="%s"', esc_attr($r['type']));
                    unset($r['type']);
                }
                $attr_txt = '';
                if (isset($r['attr'])) {
                    $attr_txt = $r['attr'];
                    unset($r['attr']);
                }
                foreach ($r as $k => $v) {
                    $attr .= sprintf(' %s="%s"', $k, esc_attr($v));
                }
                if ($attr_txt) {
                    $attr .= sprintf(' %s', esc_attr($attr_txt));
                }
            }
        }
        //print_r([$handle,$src]);

        $tag = sprintf('<script%s src="%s"%s id="%s-js"></script>' . "\n", $type, $src, $attr, $handle);
        return $tag;
    }

    public function render_vue_link($tag, $handle, $href, $media)
    {

        if (!preg_match('#^vue-#', $media)) {
            return $tag;
        }

        $media = htmlspecialchars_decode($media);
        $r = [];
        parse_str(str_replace('vue-', '', $media), $r);
        $rel = '';
        $attr = [];
        if ($r && is_array($r)) {
            if (isset($r['rel'])) {
                $rel = $r['rel'];
                unset($r['rel']);
            }
            foreach ($r as $attr_k => $attr_v) {
                $attr[] = sprintf('%s="%s"', $attr_k, esc_attr($attr_v));
            }
        }

        $tag = sprintf(
            '<link href="%s" rel="%s" %s/>' . "\n",
            $href,
            $rel,
            implode(" ", $attr)
        );
        return $tag;
    }


    public function vue_assets()
    {
        $assets = include __DIR__ . '/plugins_assets.php';

        if (!$assets || !is_array($assets)) {
            return;
        }

        $wp_styles = wp_styles();
        if (isset($assets['css']) && is_array($assets['css'])) foreach ($assets['css'] as $r) {
            $wp_styles->add($r['handle'], MAGICPOST_URI . $r['src'], $r['dep'], null, $r['args']);
            $wp_styles->enqueue($r['handle']); //.'?v=1'
        }
        if (isset($assets['js']) && is_array($assets['js'])) foreach ($assets['js'] as $r) {
            if (!$r['src'] && $r['in_line']) {
                wp_register_script($r['handle'], false, $r['dep'], false, true);
                wp_enqueue_script($r['handle']);
                wp_add_inline_script($r['handle'], $r['in_line'], 'after');
            } else if ($r['src']) {
                wp_enqueue_script($r['handle'], MAGICPOST_URI . $r['src'], $r['dep'], null, true);
            }
        }
    }

    public function plugin_row_meta($links, $file)
    {

        $base = plugin_basename(MAGICPOST_BASE);
        if ($file == $base) {
            $links[] = '<a href="https://www.wbolt.com/plugins/magicpost?utm_source=magicpost_setting&utm_medium=link&utm_campaign=plugins_list" target="_blank">插件主页</a>';
            $links[] = '<a href="https://www.wbolt.com/magicpost-plugin-documentation.html?utm_source=magicpost_setting&utm_medium=link&utm_campaign=plugins_list" target="_blank">说明文档</a>';
            $links[] = '<a href="https://www.wbolt.com/plugins/magicpost#J_commentsSection" target="_blank">反馈</a>';
        }
        return $links;
    }

    public function admin_notices()
    {
        global $current_screen;
        if (!current_user_can('update_plugins')) {
            return;
        }
        if (!preg_match('#magicpost#', $current_screen->parent_base)) {
            return;
        }
        $current         = get_site_transient('update_plugins');
        if (!$current) {
            return;
        }
        $plugin_file = plugin_basename(MAGICPOST_BASE);
        if (!isset($current->response[$plugin_file])) {
            return;
        }
        $all_plugins     = get_plugins();
        if (!$all_plugins || !isset($all_plugins[$plugin_file])) {
            return;
        }
        $plugin_data = $all_plugins[$plugin_file];
        $update = $current->response[$plugin_file];

        //print_r($update);
        $update_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=') . $plugin_file, 'upgrade-plugin_' . $plugin_file);

        echo '<div class="update-message notice inline notice-warning notice-alt"><p>' . esc_html($plugin_data['Name']) . '有新版本可用。';
        echo '<a href="' . esc_url($update->url) . '" target="_blank" aria-label="查看' . esc_attr($plugin_data['Name']) . '版本' . esc_attr($update->new_version) . '详情">查看版本' . esc_html($update->new_version) . '详情</a>';
        echo '或<a href="' . esc_url($update_url) . '" class="update-link" aria-label="现在更新 ' . esc_attr($plugin_data['Name']) . '">现在更新</a>。</p></div>';
    }


    public static function actionLinks($links, $file)
    {

        //print_r([$file]);
        if (!preg_match('#magicpost/#', $file)) {
            return $links;
        }
        if (!get_option('wb_magicpost_ver', 0)) {
            $a_link = '<a href="https://www.wbolt.com/plugins/magicpost" target="_blank"><span style="color: #FCB214;">升至Pro版</span></a>';
            array_unshift($links, $a_link);
        }
        $a_link = '<a href="' . menu_page_url('magicpost', false) . '#/home">设置</a>';
        array_unshift($links, $a_link);

        return $links;
    }


    /**
     * 文章编辑页通用插入css or js
     */
    public static function assets_for_post_edit()
    {
        $download_module_switch = WB_MagicPost_Download::get_active();
        $clean_module_switch = WB_MagicPost_Clean::get_active();

        if (!$download_module_switch && !$clean_module_switch) {
            return;
        }

        wp_enqueue_style('wbp_magicpost_post', MAGICPOST_URI . 'assets/wbp_magicpost_post.css', array(), MAGICPOST_VERSION);
        wp_enqueue_script('wbp_magicpost_post', MAGICPOST_URI . 'assets/wbp_magicpost_post.js', array(), MAGICPOST_VERSION);
    }
}

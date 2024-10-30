<?php

/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com
 */

class WB_MagicPost_Download extends WB_MagicPost_Base
{
  public $post_id = 0;

  public static $meta_fields = array(
    'wb_dl_type', //下载开关
    'wb_dl_mode', //下载方式
    'wb_down_local_url',
    'wb_down_url_ct',
    'wb_down_url',
    'wb_down_pwd',
    'wb_down_url_magnet',
    'wb_down_url_xunlei',
    'wb_down_url_aliyun',
    'wb_down_pwd_aliyun',
    'wb_down_price',
  );

  public static $dl_type_items = array(
    'local' => '本地',
    'baidu' => '百度网盘',
    'ct' => '城通',
    'magnet' => '磁力链接',
    'xunlei' => '迅雷',
    'aliyun' => '阿里云盘'
  );

  public function __construct()
  {
    if (is_admin()) {
      add_action('wp_ajax_magicpost', array($this, 'magicpost_ajax'));
    }
    $switch = self::cnf('switch', 0);
    if (!$switch) {
      return;
    }

    if (is_admin()) {
      add_action('add_meta_boxes', array($this, 'add_metabox'));
      add_action('save_post', array($this, 'save_meta_data'));
    } else {
      add_filter('the_content', array($this, 'the_content'), 40);
      add_action('wp_enqueue_scripts', array($this, 'wp_head'), 50);
      add_action('wp_footer', array($this, 'sticky_html'), 50);

      add_action('widgets_init', array($this, 'widgets_init'));
      add_filter('wb_dlip_html', array($this, 'down_html'));
    }

    add_action('wp_ajax_wb_mpdl_front', array($this, 'wb_ajax'));
    add_action('wp_ajax_nopriv_wb_mpdl_front', array($this, 'wb_ajax'));
  }

  /**
   * 管理设置
   */
  public function magicpost_ajax()
  {
    $op = sanitize_text_field(self::param('op'));
    if (!$op) {
      return;
    }
    $arrow = [
      'dip_setting', 'dip_update'
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
      case 'dip_setting':
        $ret = ['code' => 1];
        do {

          $ret['opt'] = self::cnf();
          $ret['cnf'] = array(
            'dl_type_items' => self::$dl_type_items
          );
          $ret['code'] = 0;
          $ret['desc'] = 'success';
        } while (0);
        self::ajax_resp($ret);


        break;

      case 'dip_update':
        $ret = ['code' => 1];
        do {

          $opt = $this->sanitize_text_field_array(self::param('opt', []));
          if (empty($opt) || !is_array($opt)) {
            $ret['desc'] = 'illegal';
            break;
          }
          update_option('dlip_option', $opt);

          $ret['code'] = 0;
          $ret['desc'] = 'success';
        } while (0);
        self::ajax_resp($ret);
        break;
    }
  }

  public static function set_active($switch)
  {
    $opt = self::cnf();
    $opt['switch'] = $switch;
    update_option('dlip_option', $opt);
  }

  public static function get_active()
  {
    return self::cnf('switch');
  }

  public function sanitize_text_field_array($v)
  {
    if (is_array($v)) foreach ($v as $sk => $sv) {
      if (is_array($sv)) {
        $v[$sk] = $this->sanitize_text_field_array($sv);
      } else if (is_string($sv)) {
        $v[$sk] = sanitize_text_field($sv);
      }
    }
    else if (is_string($v)) {
      $v = sanitize_text_field($v);
    }
    return $v;
  }

  public function wp_head()
  {

    if (!is_single()) {
      return;
    }
    $post_id = get_the_ID();
    $meta_value = self::meta_values($post_id);
    $with_dl_info = isset($meta_value['wb_dl_type']) && $meta_value['wb_dl_type'] ? 1 : 0;

    if ($with_dl_info) {

      //若开启评论下载
      $cur_post_need_comment = isset($meta_value['wb_dl_mode']) && $meta_value['wb_dl_mode'] == 1 ? 1 : 0;
      $need_comment = self::cnf('need_comment', 0);
      if ($need_comment && $cur_post_need_comment) {
        add_filter('comment_form_field_cookies', '__return_false');
        add_action('set_comment_cookies', array(__CLASS__, 'coffin_set_cookies'), 10, 3);
      }

      $sticky_mode = self::cnf('sticky_mode', 0);
      if ($sticky_mode == 2) {
        add_filter('body_class', array(__CLASS__, 'wb_body_classes'));
      }

      if (self::get_custom_code()) {
        wp_add_inline_style('wbp-magicpost', self::get_custom_code());
      }
    }
  }

  public static function get_custom_code()
  {
    $custom_css = '';

    // 暗黑模式兼容
    $dm_class_name = self::cnf('dark_mode_class');
    if ($dm_class_name) {
      $custom_css .= $dm_class_name . '{--wb-mgp-bfc: #c3c3c3; --wb-mgp-fcs: #fff; --wb-mgp-wk: #999; --wb-mgp-wke: #686868; --wb-mgp-bgc: #2b2b2b; --wb-mgp-bbc: #4d4d4d; --wb-mgp-bcs: #686868; --wb-mgp-bgcl: #353535;}';
    }

    return $custom_css;
  }


  public function down_html($with_title = true)
  {

    $post_id = get_the_ID();
    $html = '';

    do {
      if (!$post_id) {
        break;
      }

      $this->post_id = $post_id;

      $meta_value = self::meta_values($post_id);

      //关闭资源
      if (!$meta_value['wb_dl_type']) {
        break;
      }

      // 'wb_dl_type','wb_dl_mode', 'wb_down_local_url', 'wb_down_url_ct', 'wb_down_url','wb_down_pwd', 'magnet', 'xunlei', 'aliyun'
      $dl_info = array();
      if (isset($meta_value['wb_down_url']) && $meta_value['wb_down_url']) {
        $bdpsw = isset($meta_value['wb_down_pwd']) && $meta_value['wb_down_pwd'] ? $meta_value['wb_down_pwd'] : '';
        $dl_info['baidu'] = array(
          'name' => '百度网盘下载',
          'url' => $meta_value['wb_down_url'],
          'psw' => $bdpsw
        );
      }

      if (isset($meta_value['wb_down_local_url']) && $meta_value['wb_down_local_url']) {
        $dl_info['local'] = array(
          'name' => '本地直接下载',
          'url' => $meta_value['wb_down_local_url']
        );
      }

      if (isset($meta_value['wb_down_url_ct']) && $meta_value['wb_down_url_ct']) {
        $dl_info['ct'] = array(
          'name' => '城通网盘下载',
          'url' => $meta_value['wb_down_url_ct']
        );
      }

      if (isset($meta_value['wb_down_url_magnet']) && $meta_value['wb_down_url_magnet']) {
        $dl_info['magnet'] = array(
          'name' => '磁力链接',
          'url' => $meta_value['wb_down_url_magnet']
        );
      }

      if (isset($meta_value['wb_down_url_xunlei']) && $meta_value['wb_down_url_xunlei']) {
        $dl_info['xunlei'] = array(
          'name' => '迅雷下载',
          'url' => $meta_value['wb_down_url_xunlei']
        );
      }

      if (isset($meta_value['wb_down_url_aliyun']) && $meta_value['wb_down_url_aliyun']) {
        $aliyun_psw = isset($meta_value['wb_down_pwd_aliyun']) && $meta_value['wb_down_pwd_aliyun'] ? $meta_value['wb_down_pwd_aliyun'] : '';
        $dl_info['aliyun'] = array(
          'name' => '阿里网盘下载',
          'url' => $meta_value['wb_down_url_aliyun'],
          'psw' => $aliyun_psw
        );
      }

      if ($dlt_custom_items = self::cnf('dlt_custom')) {
        foreach ($dlt_custom_items as $c_item) {
          if ($c_item['status'] != 1) continue;

          $slug = $c_item['slug'];

          if (!$meta_value['wb_down_url_' . $slug] && !$meta_value['wb_down_pwd_' . $slug]) continue;

          $dl_info[$slug] = array(
            'name' => $c_item['label'],
            'url' => $meta_value['wb_down_url_' . $slug],
            'psw' => $meta_value['wb_down_pwd_' . $slug],
            'icon' => 'download'
          );
        }
      }

      if (empty($dl_info)) {
        break;
      }

      $display_count = self::cnf('display_count', 0);
      $btn_align = self::cnf('btn_align', 0);
      $remark_info = self::cnf('remark', '');

      $need_login = self::cnf('need_member', 0);
      $is_login = is_user_logged_in();
      $need_comment = isset($meta_value['wb_dl_mode']) && $meta_value['wb_dl_mode'] == 1 ? 1 : 0;
      $is_comment = $this->wb_is_comment($post_id);

      $need_pay = isset($meta_value['wb_dl_mode']) && $meta_value['wb_dl_mode'] == 2 ? 1 : 0;
      $need_pay = current_user_can('edit_post', $post_id) ? 0 : $need_pay;
      $pay_tips_content = '该资源需支付后下载，当前出了点小问题，请稍后再试或联系站长。';
      $is_buy = false;
      if (class_exists('WP_VK') && class_exists('WP_VK_Order') && WP_VK_Order::post_price($post_id)) {
        $attr = array('tpl' => '此资源需支付%price%后下载');
        $pay_tips_content = WP_VK::sc_vk_content($attr);
        $is_buy = WP_VK_Order::is_buy($post_id);
      }
      if ($display_count) {
        $post_down = get_post_meta($post_id, 'post_downs', true);
        if (!$post_down) $post_down = 0;
      }

      ob_start();
      if ($with_title) {
        include MAGICPOST_ROOT . '/inc/download.php';
      } else {
        include MAGICPOST_ROOT . '/inc/widget_download.php';
      }
      $html = ob_get_clean();
    } while (false);

    return $html;
  }

  public function sticky_html()
  {

    if (!is_single()) {
      return;
    }
    $post_id = $this->post_id;

    do {
      if (!$post_id) {
        break;
      }
      $meta_value = self::meta_values($post_id);

      //关闭资源
      if (!$meta_value['wb_dl_type']) {
        break;
      }

      $sticky_mode = self::cnf('sticky_mode', 0);
      include MAGICPOST_ROOT . '/inc/sticky.php';
    } while (false);
  }

  public static function wb_is_comment($post_id)
  {
    $email = null;
    $user_ID = wp_get_current_user()->ID;
    $user_name = wp_get_current_user()->display_name;

    if ($user_ID > 0) {
      $email = get_userdata($user_ID)->user_email;
    } else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
      $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
      return false;
    }
    if (empty($email) && empty($user_name)) {
      return false;
    }

    // global $wpdb;
    $db = self::db();
    $pid = $post_id;
    $query = "SELECT `comment_ID` FROM {$db->comments} WHERE `comment_post_ID` = %d and `comment_approved`='1' and (`comment_author_email` = %s or `comment_author` = %s) LIMIT 1";
    if ($db->get_var($db->prepare($query, $pid, $email, $user_name))) {
      return true;
    }
  }

  public function the_content($content)
  {
    if (is_single()) {
      $content .= $this->down_html();
    }

    return $content;
  }

  public static function wb_ajax()
  {
    $post_id = intval(self::param('pid', 0));
    $dl_type = sanitize_text_field(self::param('rid'));

    $meta_value = self::meta_values($post_id);
    $need_login = self::cnf('need_member', 0);
    $is_login = is_user_logged_in();
    $need_comment = isset($meta_value['wb_dl_mode']) && $meta_value['wb_dl_mode'] == 1 ? 1 : 0;


    $ret = array('code' => 0, 'is_login' => is_user_logged_in(), 'data' => array());

    do {
      if (!$post_id) {
        $ret['code'] = 1;
        break;
      }
      if ($need_login && !$is_login) {
        $ret['code'] = 2;
        break;
      }
      $is_comment = 0;
      if ($need_comment) {
        $is_comment = self::wb_is_comment($post_id);
      }
      if ($need_comment && !$is_comment) {
        $ret['code'] = 3;
        break;
      }

      //'wb_dl_type','wb_dl_mode', 'wb_down_local_url', 'wb_down_url_ct', 'wb_down_url','wb_down_pwd'
      switch ($dl_type) {
        case 'local':
          $ret['data']['url'] = isset($meta_value['wb_down_local_url']) && $meta_value['wb_down_local_url'] ? $meta_value['wb_down_local_url'] : '';
          $ret['data']['pwd'] = '';
          break;

        case 'baidu':
          $ret['data']['url'] = isset($meta_value['wb_down_url']) && $meta_value['wb_down_url'] ? $meta_value['wb_down_url'] : '';
          $ret['data']['pwd'] = isset($meta_value['wb_down_pwd']) && $meta_value['wb_down_pwd'] ? $meta_value['wb_down_pwd'] : '';
          break;

        case 'ct':
          $ret['data']['url'] = isset($meta_value['wb_down_url_ct']) && $meta_value['wb_down_url_ct'] ? $meta_value['wb_down_url_ct'] : '';
          $ret['data']['pwd'] = '';
          break;

        case 'xunlei':
          $ret['data']['url'] = isset($meta_value['wb_down_url_xunlei']) && $meta_value['wb_down_url_xunlei'] ? $meta_value['wb_down_url_xunlei'] : '';
          $ret['data']['pwd'] = '';
          break;

        case 'magnet':
          $ret['data']['url'] = isset($meta_value['wb_down_url_magnet']) && $meta_value['wb_down_url_magnet'] ? $meta_value['wb_down_url_magnet'] : '';
          $ret['data']['pwd'] = '';
          break;

        case 'aliyun':
          $ret['data']['url'] = isset($meta_value['wb_down_url_aliyun']) && $meta_value['wb_down_url_aliyun'] ? $meta_value['wb_down_url_aliyun'] : '';
          $ret['data']['pwd'] = isset($meta_value['wb_down_pwd_aliyun']) && $meta_value['wb_down_pwd_aliyun'] ? $meta_value['wb_down_pwd_aliyun'] : '';
          break;

        default:
          $dlt_custom_items = self::cnf('dlt_custom');

          if (empty($dlt_custom_items)) break;

          foreach ($dlt_custom_items as $c_item) {
            if ($c_item['status'] != 1) continue;

            if ($c_item['slug'] == $dl_type) {
              $url_slug = 'wb_down_url_' . $dl_type;
              $pwd_slug = 'wb_down_pwd_' . $dl_type;
              $ret['data']['url'] = isset($meta_value[$url_slug]) && $meta_value[$url_slug] ? $meta_value[$url_slug] : '';
              $ret['data']['pwd'] = isset($meta_value[$pwd_slug]) && $meta_value[$pwd_slug] ? $meta_value[$pwd_slug] : '';
            }
          }

          break;
      }

      $val = (int)get_post_meta($post_id, 'post_downs', true);
      $val = $val ? $val + 1 : 1;
      update_post_meta($post_id, 'post_downs', $val);
      $ret['data']['post_downs'] = $val;
    } while (false);


    header('content-type:text/json;charset=utf-8');
    echo wp_json_encode($ret);
    exit();
  }

  public function widgets_init()
  {
    wp_register_sidebar_widget('wbolt-download-info', '#下载信息#', array($this, 'wb_download_info'), array('description' => '侧栏展示下载信息，可选'));
  }

  public function wb_download_info()
  {
    echo $this->down_html(false);
  }

  public static function getPostMataVal($key, $default = 0)
  {
    $postId = get_the_ID();
    if (!$postId) return $default;
    $val = get_post_meta($postId, $key, true);
    return $val ? $val : $default;
  }

  public static function coffin_set_cookies($comment, $user, $cookies_consent)
  {
    $cookies_consent = true;
    wp_set_comment_cookies($comment, $user, $cookies_consent);
  }

  public static  function wb_body_classes($classes)
  {
    $classes[] = 'wb-with-sticky-btm';
    return $classes;
  }




  public static function cnf($key = null, $default = null)
  {
    //['switch'=>1,'need_member'=>0,'display_count'=>0,'sticky_mode'=>0,'btn_align'=>0,'remark'=>''];
    static $_option = array();
    if (!$_option) {
      $_option = get_option('dlip_option');
      if (!$_option || !is_array($_option)) {
        $_option = [];
      }
      $default_conf = array(
        'switch' => '1',
        'need_member' => '0',
        'display_count' => '0',
        'sticky_mode' => '0',
        'btn_align' => '0',
        'remark' => '',
        'dl_type_items' => array_keys(self::$dl_type_items),
        'dlt_custom' => array(), //自定义下载方式
        'dark_mode_class' => ''
      );
      foreach ($default_conf as $k => $v) {
        if (!isset($_option[$k])) $_option[$k] = $v;
      }
    }

    if (null === $key) {
      return $_option;
    }

    if (isset($_option[$key])) {
      return $_option[$key];
    }

    return $default;
  }


  public function add_metabox()
  {
    $screens = array('post');
    foreach ($screens as $screen) {
      add_meta_box(
        'wbolt_meta_box_download_info_megicpost',
        '下载设置',
        array($this, 'render_metabox'),
        $screen
      );
    }
  }

  public static function meta_values($post_id)
  {

    $meta_values = array();
    foreach (self::$meta_fields as $field) {
      $meta_values[$field] = get_post_meta($post_id, $field, true);
    }

    $dlt_custom_items = self::cnf('dlt_custom');
    if (!empty($dlt_custom_items)) {
      foreach ($dlt_custom_items as $c_item) {
        if ($c_item['status'] != 1) continue;

        $c_url = 'wb_down_url_' . $c_item['slug'];
        $c_pwd = 'wb_down_pwd_' . $c_item['slug'];

        $meta_values[$c_url] = get_post_meta($post_id, $c_url, true);
        $meta_values[$c_pwd] = get_post_meta($post_id, $c_pwd, true);
      }
    }

    if ('' === $meta_values['wb_dl_type']) {
      $meta_values['wb_dl_type'] = '0';
    }
    if ('' === $meta_values['wb_dl_mode']) {
      $meta_values['wb_dl_mode'] = '0';
    }

    return $meta_values;
  }

  public function render_metabox($post)
  {

    WB_MagicPost::assets_for_post_edit();

    $meta_value = self::meta_values($post->ID);

    $meta_value_vk_price = get_post_meta($post->ID, 'vk_price', true);

    //原有的下载方式字段wb_dl_type 改为下载开关
    $wb_dipp_switch = $meta_value['wb_dl_type'];

    // 激活的下载方式
    $wb_dipp_type_items = self::cnf('dl_type_items');
    $dlt_custom_items = self::cnf('dlt_custom');

    $dl_mode = $meta_value['wb_dl_mode'];
    $wpvk_active = class_exists('WP_VK');

    if ($wpvk_active) {
      $wpvk_install = 1;
    } else {
      $wpvk_install = file_exists(WP_CONTENT_DIR . '/plugins/wp-vk/index.php');
    }

    include MAGICPOST_ROOT . '/inc/meta_box.php';
  }

  public function save_meta_data($post_id)
  {

    if (!current_user_can('edit_post', $post_id)) return;

    $wb_dl_mode = self::param('wb_dl_mode', null);
    $wb_dl_type = self::param('wb_dl_type', null);
    $wb_down_vk_price = self::param('wb_down_vk_price', null);

    if ($wb_dl_mode !== null && null === $wb_dl_type) {
      $wb_dl_type = 0;
    }
    if (null !== $wb_dl_type) {
      $wb_dl_type = absint($wb_dl_type);
    }
    if (null !== $wb_dl_mode) {
      $wb_dl_mode = absint($wb_dl_mode);
    }
    // 下载方式为“付费下载”才影响vk_price
    if ($wb_dl_type === 1 && $wb_dl_mode === 2 && $wb_down_vk_price !== null) {
      update_post_meta($post_id, 'vk_price', abs(floatval($wb_down_vk_price)));
    }

    /*if (isset($_POST['wb_dl_mode']) && !isset($_POST['wb_dl_type'])) {
      $_POST['wb_dl_type'] = 0;
    }*/

    foreach (self::$meta_fields as $field) {
      $value = self::param($field, null);
      if (null === $value) continue;
      $value = sanitize_text_field($value);
      update_post_meta($post_id, $field, $value);
    }

    $dlt_custom_items = self::cnf('dlt_custom');
    if (!empty($dlt_custom_items)) {
      foreach ($dlt_custom_items as $c_item) {
        if ($c_item['status'] != 1) continue;

        $c_url = 'wb_down_url_' . $c_item['slug'];
        $c_pwd = 'wb_down_pwd_' . $c_item['slug'];
        $c_url_value = self::param($c_url, null);
        $c_pwd_value = self::param($c_pwd, null);
        if (null !== $c_url_value) {
          $c_url_value = sanitize_text_field($c_url_value);
          update_post_meta($post_id, $c_url, $c_url_value);
        }
        if (null !== $c_pwd_value) {
          $c_pwd_value = sanitize_text_field($c_pwd_value);
          update_post_meta($post_id, $c_pwd, $c_pwd_value);
        }
      }
    }
  }
}

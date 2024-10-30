<?php

/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com
 */

class WB_MagicPost_Front extends WB_MagicPost_Base
{

	public function __construct()
	{
		if (!is_admin()) {
			add_action('wp_enqueue_scripts', array($this, 'wp_front_head'), 50);
		}
	}

	/**
	 * 前端展示
	 */
	public static function wp_front_head()
	{
		if (!is_single()) {
			return;
		}

		$download_module_switch = WB_MagicPost_Download::get_active();
		$share_module_switch = WB_MagicPost_Share::get_active();

		if (!$download_module_switch && !$share_module_switch) {
			return;
		}

		wp_enqueue_style('wbp-magicpost', MAGICPOST_URI . 'assets/wbp_magicpost.css', null, MAGICPOST_VERSION);
		wp_enqueue_script('wbp-magicpost', MAGICPOST_URI . 'assets/wbp_magicpost.js', array(), MAGICPOST_VERSION, true);

		$wb_magicpost_cnf_base = array(
			'ver'       => MAGICPOST_VERSION,
			'pd_name'   => 'MagicPost',
			'dir'       => MAGICPOST_URI,
			'ajax_url'  => admin_url('admin-ajax.php'),
			'pid'       => get_the_ID(),
			'uid'       => wp_get_current_user()->ID,
			'share_switch' => $share_module_switch,
			'dl_switch' => $download_module_switch,
		);

		// js配置钩子
		$front_js_config = apply_filters('magicpost_front_js_config', $wb_magicpost_cnf_base);

		// 插入css
		$inline_css = apply_filters('magicpost_front_inline_css', '');

		$base_cnf = ' var wb_magicpost_cnf=' . wp_json_encode($front_js_config, JSON_UNESCAPED_UNICODE) . ';';

		wp_add_inline_script('wbp-magicpost', $base_cnf, 'before');

		if ($inline_css) {
			wp_add_inline_style('wbp-magicpost', $inline_css);
		}
	}
}

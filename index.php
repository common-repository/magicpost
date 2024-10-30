<?php
/*
Plugin Name: MagicPost
Plugin URI: http://wordpress.org/plugins/magicpost/
Version: 1.2.1
Description: MagicPost（中文为魔法文章），如其名，该插件的主要目的是为WordPress的文章管理赋予更多高效，增强的功能。如定时发布管理，文章搬家，文章翻译，HTML代码清洗，下载文件管理和社交分享小组件。
Author: 闪电博
Author URI: https://www.wbolt.com
Requires PHP: 7.0.0
*/

if (!defined('ABSPATH')) {
    die('Invalid request.');
}
define('MAGICPOST_ROOT', __DIR__);
define('MAGICPOST_BASE', __FILE__);
define('MAGICPOST_URI', plugin_dir_url(__FILE__));
define('MAGICPOST_VERSION', '1.2.1');

require_once __DIR__ . '/module/base.php';
require_once __DIR__ . '/module/google.api.php';
require_once __DIR__ . '/module/baidu.api.php';

require_once __DIR__ . '/classes/front.class.php';
new WB_MagicPost_Front();

require_once __DIR__ . '/module/magicpost.php';
new WB_MagicPost();

require_once __DIR__ . '/module/download.php';

new WB_MagicPost_Download();

require_once __DIR__ . '/module/clean.php';
new WB_MagicPost_Clean();

require_once __DIR__ . '/module/share.php';
new WB_MagicPost_Share();

require_once __DIR__ . '/module/schedule.php';
new WB_MagicPost_Schedule();

require_once __DIR__ . '/module/move.php';
new WB_MagicPost_Move();

require_once __DIR__ . '/module/translate.php';
new WB_MagicPost_Translate();

require_once __DIR__ . '/module/toc.php';
new WB_MagicPost_Toc();

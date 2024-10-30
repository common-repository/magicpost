<?php

/**
 * Author: wbolt team
 * Author URI: https://www.wbolt.com
 */

?>

<section class="widget widget-wbdl" id="J_widgetWBDownload">
  <h3 class="widgettitle">下载</h3>
  <div class="widget-main">
    <?php include MAGICPOST_ROOT . '/tpl/download_btn.php'; ?>

    <?php if ($display_count) : ?>
      <p class="dl-count">已下载<span class="j-wbdl-count"><?php echo esc_html($post_down); ?></span>次</p>
    <?php endif; ?>
  </div>
</section>
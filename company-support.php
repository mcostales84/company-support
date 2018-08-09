<?php
/*
 * Plugin Name: JTW Support
 * Plugin URI: http://www.jumptoweb.com
 * Description: Support chat feature for Jumptoweb clients.
 * Version: 1.0
 * Author: Manny Costales
 * Author URI: http://www.mannycostales.com
*/


defined('ABSPATH') or die('Plugin file cannot be accessed directly.');

/* Facebook Chat */
function jtw_support_enqueue_script()
    {
        if (is_admin()) {
      ?>
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
          attribution=setup_tool
          page_id="292357324240753"
          theme_color="#0084ff">
        </div>
      <?php
            wp_enqueue_script('jtw-support', plugin_dir_url(__FILE__) . 'facebook.js', false);
        }
    }
    add_action('wp_enqueue_scripts', 'jtw_support_enqueue_script');
    add_action('admin_enqueue_scripts', 'jtw_support_enqueue_script');

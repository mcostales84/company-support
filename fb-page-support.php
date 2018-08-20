<?php
/*
 * Plugin Name: Facebook Page Support
 * Plugin URI: http://www.jumptoweb.com
 * Description: Facebook Chat Support for logged users with Admin role.
 * Version: 1.0
 * Author: Manny Costales
 * Author URI: http://www.mannycostales.com
*/


defined('ABSPATH') or die('Plugin file cannot be accessed directly.');

//Load Color Picker Script
add_action( 'admin_enqueue_scripts', 'jtw_color_picker' );
function jtw_color_picker($hook) {
  wp_enqueue_script('jquery');
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'jtw-color-picker-handle', plugin_dir_url(__FILE__) . 'js/custom-script.js', array( 'wp-color-picker','jquery' ), false, true );
}
//End of load color picker

//Add Settings Link to the plugin
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'fbps_settings_link' );

function fbps_settings_link( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=fb_page_support') ) .'">Settings</a>';
   return $links;
}
//End of setting link

//Plugin Settings Page
add_action( 'admin_menu', 'fbps__add_admin_menu' );
add_action( 'admin_init', 'fbps__settings_init' );


function fbps__add_admin_menu(  ) { 

  add_options_page( 'FB Page Support', 'FB Page Support', 'manage_options', 'fb_page_support', 'fbps__options_page' );

}

function fbps__settings_init(  ) { 

  register_setting( 'pluginPage', 'fbps__settings' );
  register_setting( 'pluginPageHelp', 'fbps__help' );

  add_settings_section(
    'fbps__pluginPage_section', 
    __( 'Configure the chat using your own Facebook Page Message', 'wordpress' ), 
    'fbps__settings_section_callback', 
    'pluginPage'
  );

  add_settings_field( 
    'fbps__text_field_0', 
    __( 'Paste your Facebook Page ID', 'wordpress' ), 
    'fbps__text_field_0_render', 
    'pluginPage', 
    'fbps__pluginPage_section' 
  );

  add_settings_field( 
    'fbps__text_field_1', 
    __( 'Pick your color', 'wordpress' ), 
    'fbps__text_field_1_render', 
    'pluginPage', 
    'fbps__pluginPage_section' 
  );

  add_settings_section(
    'fbps__pluginPageHelp_section', 
    __( 'Instructions to setup using your Facebook Page.', 'wordpress' ), 
    'fbps__help_section_callback', 
    'pluginPageHelp'
  );

}

function fbps__text_field_0_render(  ) { 

  $options = get_option( 'fbps__settings' );
  ?>
  <input type='text' name='fbps__settings[fbps__text_field_0]' value='<?php echo $options['fbps__text_field_0']; ?>'>
  <?php

}


function fbps__text_field_1_render(  ) { 

  $options = get_option( 'fbps__settings' );
  ?>
  <input type='text' name='fbps__settings[fbps__text_field_1]' class="color-picker" value='<?php echo $options['fbps__text_field_1']; ?>'>
  <?php

}

function fbps__settings_section_callback(  ) { 
  echo __( '<b>NOTE: </b>If no ID is entered, the plugin will no show any chat.', 'wordpress' );
}

function fbps__help_section_callback(  ) { 
  echo __( '<b>1. </b>To get the Facebook ID of your page <a href="https://findmyfbid.com/" target="_blank">click here</a>. Paste your Facebook page URL, get your ID and paste it on the field below.
  <br><b>2. </b>Once you enter your ID you need to whitelabel this URL ('.get_site_url().'). In order to do that go to: 
    <br><b style="margin-left: 20px">2.1 </b><i>Page Settings > Messenger Platform</i>
    <br><b style="margin-left: 20px">2.2 </b><i>Add your URL on the Whitelisted Domains section</i>
    <br><b style="margin-left: 20px">2.3 </b><i>Click on Save</i>
    <br><b style="margin-left: 20px">* </b><i>Check image below.</i>
    <br><br style="margin-left: 20px"><img src="'.plugin_dir_url(__FILE__).'/img/whitelisting-domain.jpg">', 'wordpress' );
}

function fbps__options_page(  ) { 

  ?>

  <div class="wrap">
    <h1>FB Page Support</h1>
    <?php settings_errors(); ?> 

    <?php  
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings_tab';  
        ?> 

    <h2 class="nav-tab-wrapper">
      <a href="?page=fb_page_support&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>  
            <a href="?page=fb_page_support&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>  
    </h2>

    <form action='options.php' method='post'>
      <?php

        if($active_tab == 'settings'){
          settings_fields( 'pluginPage' );
          do_settings_sections( 'pluginPage' );
          submit_button();  
        }else if($active_tab == 'help'){
          settings_fields( 'pluginPageHelp' );
          do_settings_sections( 'pluginPageHelp' );
        }
      
      ?>
    </form>

  </div>

  <?php

}
//End of Settings Page

//Launching FB Chat
/* Facebook Chat Hardcoded*/
function jtw_support_enqueue_script()
    {
        if (is_user_logged_in()) {
      $fb_chat_options = get_option( 'fbps__settings' );
      
      if (empty($fb_chat_options[fbps__text_field_0])) { // Nothing yet saved
            $fb_chat_options[fbps__text_field_0] = '292357324240753';} // Jumptoweb ID
      ?>
        <!-- Load Facebook SDK for JavaScript -->
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
          attribution=setup_tool
          page_id="<?php echo $fb_chat_options[fbps__text_field_0] ?>"
          theme_color="<?php echo $fb_chat_options[fbps__text_field_1] ?>">
        </div>
      <?php
            wp_enqueue_script('jtw-support', plugin_dir_url(__FILE__) . 'js/facebook.js', false);
        }
    }
    add_action('wp_enqueue_scripts', 'jtw_support_enqueue_script');
    add_action('admin_enqueue_scripts', 'jtw_support_enqueue_script');
//End of FB Chat
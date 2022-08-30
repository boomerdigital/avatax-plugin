<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Main{
    public function __construct(){
        new AdminSettings();
        new Accounts();
        add_action('admin_enqueue_scripts', array(&$this,'adminLoadAssets'));
        add_action('wp_enqueue_scripts', array(&$this,'frontLoadAssets'));
        add_action('wp_enqueue_style', array(&$this,'cssAssets'));
        add_filter( 'plugin_action_links', array($this,'wpse_25030_settings_plugin_link'), 10, 2 );

    }
    
    public function adminLoadAssets(){
        wp_enqueue_script( 'custom_js', plugins_url( 'assets/js/admin/admin.js', __FILE__ ), array('jquery') );
        wp_enqueue_script( 'ajax-script', plugins_url('assets/js/admin/ajax.js', __FILE__ ), array('jquery') );
        wp_enqueue_style( 'ajax-style', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array() );
        wp_enqueue_script( 'ajax-script1', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery') );
        wp_localize_script( 'ajax-script', 'admin_ajax_url',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }


    public function frontLoadAssets(){
        echo "<script> let ADDRESSVALIDATION = '".ADDRESSVALIDATION."';</script>";
        wp_enqueue_script( 'front_js', plugins_url( 'assets/js/frontend/frontend.js', __FILE__ ), array('jquery') );
    }

    public function cssAssets(){
        wp_enqueue_style( 'custom_css', plugins_url( 'assets/css/admin/style.css', __FILE__ ) );
    }
    
    public function salcode_add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="' .
            admin_url( 'admin.php?page=wc-settings&tab=tax&section=mysettings' ) .
            '">' . __('Settings') . '</a>';
        return $links;
    }

    public function wpse_25030_settings_plugin_link( $links, $file ) {
        if ( $file == plugin_basename(dirname(__FILE__) . '/avatax.php') ) {
            $in = '<a href="admin.php?page=wc-settings&tab=tax&section=mysettings">' . __('Settings','mtt') . '</a>';
            array_unshift($links, $in);
        }
        return $links;
    }

   
    
}

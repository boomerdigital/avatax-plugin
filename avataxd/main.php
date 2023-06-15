<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Main{
    
    var $avatax;
    public function __construct(){
        global $avataxplugin;
        $this->avatax=$avataxplugin;
        new AdminSettings();
        new Accounts();
        add_action('admin_enqueue_scripts', array(&$this,'avatax_adminLoadAssets'));
        add_action('wp_enqueue_scripts', array(&$this,'avatax_frontLoadAssets'));
        add_action('wp_enqueue_style', array(&$this,'avatax_cssAssets'));
        add_filter( 'plugin_action_links', array($this,'avatax_settings_plugin_link'), 10, 2 );


    }
    
    public function avatax_adminLoadAssets(){
        $ajax_url=apply_filters( 'jsvar' , array(
            'home_url'					=>	home_url('/'),
            'ajax_url'					=>	admin_url( 'admin-ajax.php' ),
            '_ajax_nonce'				=>	wp_create_nonce( 'do_ajax_security' ),
            'plugin_url'				=>	plugins_url( '', __FILE__ ),
        ));
        wp_enqueue_script( $this->avatax['plugin_prefix'].'admin_js', plugins_url( 'assets/js/admin/admin.js', __FILE__ ), array('jquery'),$this->avatax['plugin_version'], true);
        wp_enqueue_script(  $this->avatax['plugin_prefix'].'ajax-script', plugins_url('assets/js/admin/ajax.js', __FILE__ ), array('jquery'),$this->avatax['plugin_version'],true );
        wp_enqueue_style(  $this->avatax['plugin_prefix'].'select2css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(),$this->avatax['plugin_version'],'all' );
        wp_enqueue_script( $this->avatax['plugin_prefix'].'select2js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'),$this->avatax['plugin_version'],true );
        wp_localize_script( $this->avatax['plugin_prefix'].'admin_js', 'admin_ajax_url', $ajax_url );
        wp_localize_script( $this->avatax['plugin_prefix'].'ajax-script', 'admin_ajax_url', $ajax_url );
    }
    

    public function avatax_frontLoadAssets(){
        echo "<script> let ADDRESSVALIDATION = '".ADDRESSVALIDATION."';</script>";
        wp_enqueue_script( $this->avatax['plugin_prefix'].'front_js', plugins_url( 'assets/js/frontend/frontend.js', __FILE__ ), array('jquery'),$this->avatax['plugin_version'], true);
    }

    public function avatax_cssAssets(){
        wp_enqueue_style( $this->avatax['plugin_prefix'].'custom_css', plugins_url( 'assets/css/admin/style.css', __FILE__ ), array(),$this->avatax['plugin_version'],'all' );
    }
    
    public function avatax_settings_plugin_link( $links, $file ) {
        if ( $file == plugin_basename(dirname(__FILE__) . '/avatax.php') ) {
            $in = '<a href="admin.php?page=wc-settings&tab=tax&section=mysettings">' . __('Settings','mtt') . '</a>';
            array_unshift($links, $in);
        }
        return $links;
    }

   
    
}

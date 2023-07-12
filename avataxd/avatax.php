<?php
/*
 * Plugin Name: Avatax for WooCommerce Marketplaces
 * Plugin URI: http://www.mywebsite.com/
 * Description: This Plugin is used For Calculate A Tax By Countries
 * Version: 1.0.1
 * Author: Boomer Digital  
 * Author URI: https://boomerdigital.net/
 */

 global $avataxplugin;
 $avataxplugin=array(
    'plugin_name' => 'Avatax for WooCommerce Marketplaces',
    'plugin_version' => '1.0.0',
    'plugin_url' => 'http://www.mywebsite.com/',
    'plugin_dir' => plugin_dir_path(__FILE__), // Path: avataxd/avatax.php
    'plugin_file' => __FILE__,
    'plugin_base' => plugin_basename(__FILE__),
    'plugin_slug' => 'avatax',
    'plugin_prefix' => 'avatax_', // Prefix: avatax_
    'plugin_textdomain' => 'avatax',
    'plugin_author' => 'Boomer Digital',
    'avatax_vendor' => get_option('vendor'),
    'total_tax' => 0,
    'shipping_tax' => 0,
    
 );
 
include "config/constant.php";
include "includes/admin/database/dml.php";
include "includes/frontend/database/dml.php";
include "config/db.php";
include "main.php";
include "logs/errorLog.php";
include "api/admin/curl.php";
include "includes/admin/adminSettings.php";
include "includes/admin/backend.php";
include "includes/admin/ajax.php";
include "includes/frontend/frontAjax.php";
include "api/frontend/frontEndApi.php";
include "includes/admin/function.php";
include "helper/state.php";
include "includes/admin/dokan-customer.php";
new Main;
new Ajax;
new FrontAjax;
new DokanC;
update_option('wc_connect_taxes_enabled', 'no');
register_activation_hook(__FILE__, array('DB', 'activate'));
//register_deactivation_hook( __FILE__, array('DB', 'deactivate') );
register_uninstall_hook( __FILE__, array('DB', 'deactivate') );
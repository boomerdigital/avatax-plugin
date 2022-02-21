<?php
/*
 * Plugin Name: Avatax Custom
 * Plugin URI: http://www.mywebsite.com/
 * Description: This Plugin is used For Calculate A Tax By Countries
 * Version: 1.0.0
 * Author: Seasia
 * Author URI: https://www.seasiainfotech.com/
 */

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
new Main;
new Ajax;
new FrontAjax;

register_activation_hook(__FILE__, array('DB', 'activate'));
//register_deactivation_hook( __FILE__, array('DB', 'deactivate') );
register_uninstall_hook( __FILE__, array('DB', 'deactivate') );
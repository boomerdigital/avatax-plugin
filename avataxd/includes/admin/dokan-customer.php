<?php

if ( ! defined( 'ABSPATH' ) ) exit;
class DokanC {
    public function __construct(){
        
        add_action('vendor_order_shipping_fields', array(&$this,'show_custom_fields'), 10, 2);
    }

    public function show_custom_fields($order_id, $shipped){
        var_dump("de");
        die();
    }

    
    

}
<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Accounts{
    public function __construct(){ 
        if(RECORDCALCULATIONS=="yes"){
        $this->getAllVendor();
        add_action("wp_ajax_updateUserAjax" , array(&$this,'updateUserAjax'));
        add_action("wp_ajax_nopriv_updateUserAjax" , array(&$this,'updateUserAjax'));
        add_action( 'user_register', array(&$this,'vendorRegistrator'), 10, 1);
        }
        if(get_option('vendor')=='0'){
            $this->setWooAddressByAdmin();
            update_option('woocommerce_tax_based_on','base');
        }else{
            update_option('woocommerce_tax_based_on','shipping');
        }
    }

    public function updateUserAjax(){
        global $wpdb;
        try{
            $wp_users_data = $wpdb->prefix.'user_data';
            $checkReords = $wpdb->get_results("SELECT user_id FROM ".$wpdb->prefix."user_data WHERE status=1");
            if (!empty($checkReords)) {
                foreach($checkReords as $vendor){
                    $this->getUsers($vendor->user_id);
                    $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."user_data SET status='0' WHERE user_id = ".$vendor->user_id));
                }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die(); 
    }

    public function getUsers($user_id){
        try{
            $vendorRole = get_option('vendor');
            $userRole=get_userdata($user_id);
            $userRole=$userRole->roles;
            $userRole=$userRole[0];
            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $wp_users = $table_prefix.'users';
            $wp_usermeta = $table_prefix.'usermeta';
            if($vendorRole==$userRole){
                $vendorUser = $wpdb->get_results( "SELECT ".$wp_users.".id, ".$wp_users.".user_nicename, ".$wp_users.".user_login, ".$wp_users.".user_email, ".$wp_users.".display_name FROM ".$wp_users." INNER JOIN ".$wp_usermeta." ON ".$wp_users.".ID = ".$wp_usermeta.".user_id WHERE ".$wp_usermeta.".meta_key = 'wp_capabilities' AND ".$wp_usermeta.".user_id = ".$user_id." AND ".$wp_users.".ID = ".$user_id." AND ".$wp_usermeta.".meta_value LIKE '%".$vendorRole."%' ORDER BY ".$wp_users.".user_nicename" );
                if(!empty($vendorUser)){
                    $response = [];
                    $vendorMetaData = $this->getUserMeta($vendorUser[0]->id);
                    $response['id'] = $vendorUser[0]->id;
                    $response['display_name'] = $vendorUser[0]->display_name;
                    $response["street_1"] = $vendorMetaData['street_1'];
                    $response["street_2"] = $vendorMetaData['street_2'];
                    $response["city"] = $vendorMetaData['city'];
                    $response["zip"] = $vendorMetaData['zip'];
                    $response["state"] = $vendorMetaData['state'];
                    $response["country"] = $vendorMetaData['country'];
                    $street_1 = $response["street_1"];
                    $region = $response["state"];
                    $country = $response["country"];
                    $zip = $response["zip"];
                    if($response['country']=="US"){
                        $responseData = $this->byPostalCode($country,$zip);
                    }else{
                        $responseData = $this->byAddress($street_1,$region,$country,$zip);
                    }
                    if(empty($responseData->error)){ 
                        $this->saveTaxRate($responseData,$region,$country);
                    }
                } 
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function getUserMeta($vendorID){
        global $wpdb;
        try{
            $table_prefix = $wpdb->prefix;
            $wp_usermeta = $table_prefix.'usermeta';
            $usersAddress = $wpdb->get_results("SELECT meta_value FROM ".$table_prefix."usermeta WHERE user_id=".$vendorID." AND meta_key='dokan_profile_settings'");
            $userMeta = $usersAddress['0']->meta_value;
            $response = unserialize($userMeta);
            return $response['address'];
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function byPostalCode($country,$zip){
        try{
             $responseAPI = Api::curl("api/v2/taxrates/bypostalcode?country=".$country."&postalCode=".$zip."");
             ErrorLog::sysLogs("Taxrates by country ".$country." zipcode ".$zip."".$responseAPI);
             $responseData = json_decode($responseAPI);
                if(empty($responseData->error)){
                    $responseData = $responseData;
                };
             return $responseData;
         }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function byAddress($street_1,$region,$country,$zip){
        try{
             $responseAPI = Api::curl("api/v2/taxrates/bypostalcode?line1=".$street_1."&region=".$region."&country=".$country."&postalCode=".$zip."");
             ErrorLog::sysLogs("Taxrates by Address ".$responseAPI);
             $responseData = json_decode($responseAPI); 
                if(empty($responseData->error)){
                    $responseData = $responseData;
                };
             return $responseData;
         }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function saveTaxRate($responseData,$region,$country){
        global $wpdb;
        try{
            $table_prefix = $wpdb->prefix;
             if(!empty($responseData->rates)){
                $max = "SELECT MAX(tax_rate_priority) as max_tax_rate FROM ".$table_prefix."woocommerce_tax_rates WHERE tax_rate_country ='".$country."' AND tax_rate_state ='".$region."'";
                $maxValue = $wpdb->get_results($max);   
                $i=$maxValue[0]->max_tax_rate+1;  
                 foreach ($responseData->rates as $key => $value){
                        $taxRate = $value->rate;
                        $taxType = $value->type;
                        $sql = "SELECT * FROM ".$table_prefix."woocommerce_tax_rates WHERE tax_rate_country ='".$country."' AND tax_rate_state ='".$region."' AND tax_rate =".$taxRate." AND tax_rate_name ='".$taxType."'";
                        $checkValueExist = $wpdb->get_results($sql);
                        if(count($checkValueExist) == 0){
                          $wpdb->insert($table_prefix."woocommerce_tax_rates",array( 
                            'tax_rate' => $taxRate, 
                            'tax_rate_name' => $taxType, 
                            'tax_rate_country' => $country, 
                            'tax_rate_state' => $region, 
                            'tax_rate_priority' => $i, 
                            'tax_rate_compound' => 0, 
                            'tax_rate_shipping' => 0, 
                            'tax_rate_order' => 0
                           ), 
                            array('%s','%s','%s','%s','%d','%d','%d','%d') 
                            );
                            $i++;                               
                        } 
                    }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }
    

    public function getAllVendor(){
        global $wpdb;
        try{
            $vendorRole = get_option('vendor');
            $wp_users = $wpdb->prefix.'users';
            $wp_users_data = $wpdb->prefix.'user_data';
            $wp_usermeta = $wpdb->prefix.'usermeta';
            $checkReords = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."user_data LIMIT 0,1");
            if (empty($checkReords)) {
                $vendorUser = $wpdb->get_results( "SELECT ".$wp_users.".id FROM ".$wp_users." INNER JOIN ".$wp_usermeta." ON ".$wp_users.".ID = ".$wp_usermeta.".user_id WHERE ".$wp_usermeta.".meta_key = 'wp_capabilities' AND ".$wp_usermeta.".meta_value LIKE '%".$vendorRole."%' ORDER BY ".$wp_users.".user_nicename" );
                foreach($vendorUser as $vendor){
                    $this->saveUserData($vendor->id);
                }
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }
 
    public function vendorRegistrator($userId) {
        try{
            $vendorRole = get_option('vendor');
            $userRole=get_userdata($userId);
            $userRole=$userRole->roles;
            $userRole=$userRole[0];
            if($vendorRole==$userRole){
                saveUserData($userId);
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function saveUserData($userId){
        global $wpdb;
        try{
            $wpdb->insert($wpdb->prefix."user_data",array( 
            'user_id' => $userId, 
            'status' => '1'
           ), 
            array('%d','%s') 
            );
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function setWooAddressByAdmin(){
        global $wpdb;
        $wp_users = $wpdb->prefix.'users';
        $wp_usermeta = $wpdb->prefix.'usermeta';
        try{
                $storeRawCountry = get_option( 'woocommerce_default_country' );
                $splitCountry = explode( ":", $storeRawCountry );
                $street_1 = get_option( 'woocommerce_store_address' );
                $region = $splitCountry[1];
                $country = $splitCountry[0];
                $zip = get_option( 'woocommerce_store_postcode' );
                $city = get_option( 'woocommerce_store_city' );
                    if(empty($street_1)&&empty($city)&&empty($zip)){
                        $adminUser = $wpdb->get_results( "SELECT ".$wp_users.".id FROM ".$wp_users." INNER JOIN ".$wp_usermeta." ON ".$wp_users.".ID = ".$wp_usermeta.".user_id WHERE ".$wp_usermeta.".meta_key = 'wp_capabilities' AND ".$wp_usermeta.".meta_value LIKE '%administrator%'");
                        $adminUserId = $adminUser[0]->id;
                        $adminMetaData = get_user_meta($adminUserId);
                            if(!empty($adminMetaData['billing_state'][0])&& !empty($adminMetaData['billing_country'][0])){
                                $woocommerce_default_country = $adminMetaData['billing_country'][0].':'.$adminMetaData['billing_state'][0];
                            }else{
                                $woocommerce_default_country = $adminMetaData['billing_country'][0];
                            }
                        update_option( 'woocommerce_store_address',$adminMetaData['billing_address_1'][0]);
                        update_option( 'woocommerce_default_country',$woocommerce_default_country);
                        update_option( 'woocommerce_store_city',$adminMetaData['billing_city'][0]);
                        update_option( 'woocommerce_store_postcode',$adminMetaData['billing_postcode'][0]);
                    }else{
                        $storeRawCountry = get_option( 'woocommerce_default_country' );
                        $splitCountry = explode( ":", $storeRawCountry );
                        $street_1 = get_option( 'woocommerce_store_address' );
                        $region = $splitCountry[1];
                        $country = $splitCountry[0];
                        $zip = get_option( 'woocommerce_store_postcode' );
                    }        
                        if($country=="US"){
                            $responseData = $this->byPostalCode($country,$zip);
                        }else{
                            $responseData = $this->byAddress($street_1,$region,$country,$zip);
                        }
                        if(empty($responseData->error)){ 
                            $this->saveTaxRate($responseData,$region,$country);
                        }
            }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

}

?>
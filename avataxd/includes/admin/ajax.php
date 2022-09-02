<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Ajax{

    public function __construct(){
        //add_action( 'template_redirect', array($this,'plugin_is_page') );
      //add_action( 'woocommerce_calculated_total', array($this,'discounted_calculated_total') ,10,2);
       
        add_action("wp_ajax_locations" , array(&$this,'locations'));
        add_action("wp_ajax_nopriv_locations" , array(&$this,'locations'));
        if(RECORDCALCULATIONS=="yes"){
            add_action("woocommerce_checkout_order_processed" , array(&$this,'createTransaction'));
        }
        add_action("wp_head" , array(&$this,'setAdminAjax'));
        //$this->getCompany();
        add_action("wp_ajax_getCountriesList" , array(&$this,'getCountriesList'));
        add_action("wp_ajax_nopriv_getCountriesList" , array(&$this,'getCountriesList'));
        add_action("wp_ajax_returnCompanies" , array(&$this,'returnCompanies'));
        add_action("wp_ajax_nopriv_returnCompanies" , array(&$this,'returnCompanies'));
        add_action("wp_ajax_getAddressCompany" , array(&$this,'getAddressCompany'));
        add_action("wp_ajax_nopriv_getAddressCompany" , array(&$this,'getAddressCompany'));
        

        add_action("wp_ajax_shippingTax" , array(&$this,'shippingTax'));
        add_action("wp_ajax_nopriv_shippingTax" , array(&$this,'shippingTax'));

        add_action("wp_ajax_verifyAccount" , array(&$this,'verifyAccount'));
        add_action("wp_ajax_nopriv_verifyAccount" , array(&$this,'verifyAccount'));

        add_action("wp_ajax_saveCountries" , array(&$this,'saveCountries'));
        add_action("wp_ajax_nopriv_saveCountries" , array(&$this,'saveCountries'));
        add_action("wp_ajax_saveData" , array(&$this,'saveData'));
        add_action("wp_ajax_nopriv_saveData" , array(&$this,'saveData'));
        
    }

    public function saveCountries($countries){
       
        update_option('supported_countries',$countries);
        
         echo $m[] = implode(',', unserialize(get_option('supported_countries')));
        
         
    }
    
    public function saveCompany($CompanyCode,$CompanyID){
        
        try{

            update_option('companycode',$CompanyCode);
            update_option('companyID',$CompanyID);
            $response = Api::curl("api/v2/companies?filter=id eq ".$CompanyID);
            ErrorLog::sysLogs("Get company detail successfully".$response);
            $response = json_decode($response, TRUE);
            Dml::insertAtCompany($response);
            wp_die();   
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }
    public function saveData(){
        $countries= $_POST['countries'];
        $CompanyCode= $_POST['CompanyCode'];
        $CompanyID= $_POST['CompanyID'];
        $this->saveCountries($countries);
        $this->saveCompany($CompanyCode,$CompanyID);

    }

    public function verifyAccount(){
        $data=array();
        $accountId = $_POST['accountId'];
        $key = $_POST['licenseKey'];    
        $data['apiKey']=base64_encode($accountId.":".$key);
        $data['env']=$_POST['env'];
        $companies="";
        
      
            try{
            $response = Api::curl("api/v2/accounts/".$accountId,"GET",$data);
        
            $response = json_decode($response, TRUE);
            ErrorLog::sysLogs("Account verify successfully Account No=".$accountId);
            
            if(isset($response['error'])){
                 $message='<span class="errormessage" style="margin-left:8px;color:red;"> '.$response['error']['message'].'</span>';
                 $array['status']="error";
            }else{
                $message= '<span class="errormessage" style="margin-left:8px;color:green;">Account verify successfully.</span>';
                $array['status']="success";
               
                    $companies=$this->getCompanyList($data);
             

            }
            $array['message']=$message;
            $array['companies']=$companies;
            echo json_encode($array);
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
          wp_die();
    }

    public function shippingTax(){
        global $wpdb;
         if($_POST['shippingTaxValue']=="1"){
            try{
                $woocommerce_tax_rates = $wpdb->prefix.'woocommerce_tax_rates';
                        $wpdb->query($wpdb->prepare("UPDATE ".$woocommerce_tax_rates." SET tax_rate_shipping='1'"));
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
          }else{
            try{
                $woocommerce_tax_rates = $wpdb->prefix.'woocommerce_tax_rates';
                        $wpdb->query($wpdb->prepare("UPDATE ".$woocommerce_tax_rates." SET tax_rate_shipping='0'"));
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
          }
          wp_die();
    }

    // public function plugin_is_page() {
    //     if (is_checkout()) {
    //         $cart_data = WC()->session->get('cart');
    //         //echo "<pre>"; print_r($cart_data); die();

    //         $productsInOrderIds = array(); 

    //         foreach ( WC()->cart->get_cart() as $order_item ) {
    //             if ( isset( $order_item['data'] ) && !empty($order_item['data'] ) ) {
    //                     $productsInOrderIds[] = $order_item['data']->get_id();
    //             }
    //         } 
    //             // Get product Object 
    //             foreach ( WC()->cart->get_cart() as $key => $item ) {
    //                 if ( isset( $item['data'] ) && !empty( $item['data'] ) ) {
    //                     $productsInOrder[] = $item['data'];
    //                 }
    //             } 
    //         //return $productsInOrderIds;
    //         //if ()) {
            
    //         global $wp;
            
    //         $orderId = intval(str_replace('checkout','order_received',$wp->request));
    //         $order = new WC_Order( $orderId );
    //         //echo "<pre>"; print_r($wp->request); die();
    //     }
    // }

    public function discounted_calculated_total( $total, $cart ){
        echo "<pre>"; print_r($cart->cart_contents); die();
        $array = [];
        foreach ($cart->cart_contents as $key => $value) {
            $preArray['product_id'] = $value['product_id'];
            $preArray['quantity'] = $value['quantity'];
            $preArray['line_total'] = $value['line_total'];
            $preArray['name'] = $value['data']->name;
            $preArray['slug'] = $value['data']->slug;
            $preArray['description'] = $value['data']->description;
            $preArray['sku'] = $value['data']->sku;
            $preArray['tax_status'] = $value['data']->tax_status;
            $preArray['tax_class'] = $value['data']->tax_class;
            $array[] = $preArray;
        }
        //$defaultAddress = $this->getDefaultWoocommerceAddress();
       //$this->createTransactionBeforeOrder($array,$defaultAddress);
        
        
    }

    public function locations(){
        try{
            $response = Api::curl("api/v2/definitions/nexus/US");
            ErrorLog::sysLogs("Full list of all Avalara-supported nexus for all countries and regions.");
            echo $response;
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die(); 
    } 
    
    public function createTransaction($order_id){
        try{
            $order = new WC_Order( $order_id );
            $order = wc_get_order( $order_id );
            foreach ( $order->get_items() as $item_id => $item_values ) {
               
                $product = wc_get_product($item_values->get_product_id());
                $tempArray = [];
                $linesArray = [];
                $addressArray = [];
                $productId = $item_values->get_product_id();
                $linesArray['number'] = $order_id;
                $linesArray['quantity'] = $item_values->get_quantity();
                $linesArray['amount'] = $item_values->get_total();
                $linesArray['taxCode'] = get_post_meta($item_values->get_product_id(),'woocommerce_custom_taxcode',true);
                $linesArray['itemCode'] = $product->get_sku();
                $linesArray['description'] =$item_values['name'];
                $linesArray = $this->arrayToObject($linesArray);
                $tempArray['lines'] = [$linesArray];
                $tempArray['type'] = "SalesInvoice";
                $tempArray['companyCode'] =COMPANYCODE;
                $tempArray['date'] = date("Y-m-d");
                $tempArray['customerCode'] = $order->get_customer_id();
                $tempArray['purchaseOrderNo'] = get_post($item_values->get_product_id())->post_id;
                $tempArray['commit'] = false;
                $tempArray['currencyCode'] = $order->get_currency();
                $tempArray['description'] = get_post($item_values->get_product_id())->post_content;
                $addressArray['line1'] = $order->get_billing_address_1();
                $addressArray['city'] = $order->get_billing_city();
                $addressArray['region'] = $order->get_billing_state();
                $addressArray['country'] = $order->get_billing_country();
                $addressArray['postalCode'] = $order->get_billing_postcode();
                $addressArray1['singleLocation'] = $this->arrayToObject($addressArray);
                $tempArray['addresses'] = $this->arrayToObject($addressArray1);
                $new = (object)$tempArray;
                $new = json_encode($new);
                $response = Api::curl("api/v2/transactions/create",'POST',$new);
                ErrorLog::sysLogs("Transactions create successfully".$response);
                $response = json_decode($response);
                Dml::insertAtTransactions($response,$productId,$order_id);
                $transactionsId = $response->id;
                $transactionsCode = $response->code;
                $summary = $response->summary;
                Dml::insertAtTransactionsSummary($summary[0],$transactionsId,$productId,$order_id);
                if(COMMIT=="yes"){
                    $commitObj = array("commit"=>true);
                    $commitObj = (object)$commitObj;
                    $data = json_encode($commitObj);
                    $commit = Api::curl("api/v2/companies/".$tempArray['companyCode']."/transactions/".$transactionsCode."/commit", 'POST',$data);
                    $commit = json_decode($commit, TRUE); 
                    Dml::updateAtTransactionsCommitStatus($commit,$transactionsId,$transactionsCode,$productId,$order_id);
                }        
            }
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }      
    }

    public function getDefaultWoocommerceAddress(){
        $storeRawCountry = get_option( 'woocommerce_default_country' );
        $splitCountry = explode( ":", $storeRawCountry );
        $return['street_1'] = get_option( 'woocommerce_store_address' );
        $return['region'] = $splitCountry[1];
        $return['country'] = $splitCountry[0];
        $return['zip'] = get_option( 'woocommerce_store_postcode' );
        $return['city'] = get_option( 'woocommerce_store_city' );
        return $return;
    }

    public function createTransactionBeforeOrder($array, $defaultAddress){
        try{
            $allRes = [];
            foreach ( $array as $item_id => $item_values ) {
                $tempArray = [];
                $linesArray = [];
                $addressArray = [];
                $productId = $item_values['product_id'];
                $linesArray['number'] = $item_values['product_id'];
                $linesArray['quantity'] = $item_values['quantity'];
                $linesArray['amount'] = $item_values['line_total'];
                $linesArray['taxCode'] = get_post_meta($productId,'woocommerce_custom_taxcode',true);
                $linesArray['itemCode'] = $item_values['sku'];
                $linesArray['description'] = $item_values['description'];
                $linesArray = $this->arrayToObject($linesArray);
                $tempArray['lines'] = [$linesArray];
                $tempArray['type'] = "SalesOrder";
                $tempArray['companyCode'] =COMPANYCODE;
                $tempArray['date'] = date("Y-m-d");
                $tempArray['customerCode'] = "1111";
                $tempArray['purchaseOrderNo'] =  get_post($item_values['product_id'])->post_id;
                $tempArray['commit'] = false;
                $tempArray['currencyCode'] = "USD";
                $tempArray['description'] = $item_values['description'];
                $addressArray['line1'] = $defaultAddress['street_1'];
                $addressArray['city'] = $defaultAddress['city'];
                $addressArray['region'] = $defaultAddress['region'];
                $addressArray['country'] = $defaultAddress['country'];
                $addressArray['postalCode'] = $defaultAddress['zip'];
                $addressArray1['singleLocation'] = $this->arrayToObject($addressArray);
                $tempArray['addresses'] = $this->arrayToObject($addressArray1);
                $new = (object)$tempArray;
                $new = json_encode($new);
                $response = Api::curl("api/v2/transactions/create",'POST',$new);
                ErrorLog::sysLogs("Transactions create successfully".$response);
                $allRes[] = json_decode($response);
                  
            }
                //echo "<pre>"; print_r($allRes); die();
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }      
    }
    
    public function getCompany(){
        try{
            $response = Api::curl("api/v2/companies");
            ErrorLog::sysLogs("Get company detail successfully".$response);
            $response = json_decode($response, TRUE);
            Dml::insertAtCompany($response);
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }

    public function createCompany($data){
        try{
            $response = Api::curl("api/v2/companies", 'POST',  $data);
             ErrorLog::sysLogs("create company detail successfully".$response);
            $response = json_decode($response, TRUE);
            return $response;
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
    }


    public function setAdminAjax(){
        echo "<meta name='ajaxurl' value='".admin_url( 'admin-ajax.php' )."'/>";
    }

    public function arrayToObject($array){
        return (object)$array;
    }

    public function getCountriesList(){
        $countryArray = [];
        $countryArray['saved'] =  get_option('supported_countries');
        $countryArray['all'] = json_decode(file_get_contents(AVATAXRELATIVEPATH.'json/country.json')); 
        echo json_encode($countryArray); die();
    }
    public function getCompanyList($data=null){
       
        $companies=[];
        try{
            
            $response = Api::curl("api/v2/companies","GET",$data);
            ErrorLog::sysLogs("Get company detail successfully".$response);
            $response=json_decode($response,true);
            
            foreach($response['value'] as $value){
               if($value['isActive']==true){
                   
                $data=[
                    'id'=>$value['id'],
                    'companyCode'=>$value['companyCode'],
                    'name'=>$value['name'],
                    'isDefault'=>$value['isDefault'],
                    'isActive'=>$value['isActive'],

                ];
                $companies[]=$data;
            }

            }
            
           return $companies;
           
           
           
            
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        
       
       
    
    }

    public function returnCompanies(){
        $companyArray = [];
        $companyArray['saved'] =  get_option('companyID');
        $companyArray['companies'] = $this->getCompanyList();
        echo json_encode($companyArray);die();

    }

    public function getAddressCompany(){
        
        try{
            $addressArray = [];
            $CompanyID=$_GET['CompanyID'];
            $response = Api::curl("api/v2/companies/".$CompanyID."/locations");
            ErrorLog::sysLogs("List all location objects defined for this company.".$response);
            $response = json_decode($response);
            $array=[
                'origin'=>$response->value[0]->line1,
                'street'=>$response->value[0]->line2,
                'city'=>$response->value[0]->city,
                'state'=>$response->value[0]->region,
                'country'=>$response->value[0]->country,
                'zip'=>$response->value[0]->postalCode,

            ];
            
            $response =  Dml::companyAdminDetail($array);
            echo json_encode($array); die();
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }

        
    }

}


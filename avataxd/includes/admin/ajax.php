<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Ajax{

    public function __construct(){

        add_action("wp_ajax_locations" , array(&$this,'locations'));
        add_action("wp_ajax_nopriv_locations" , array(&$this,'locations'));
        add_action("wp_head" , array(&$this,'setAdminAjax'));
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
       
       if(ENABLEAVATAX=="yes"){

           add_action( 'woocommerce_calculated_total', array(&$this,'avatax_calculate_taxes'),10,2);
           if(RECORDCALCULATIONS=="yes"){
               add_action("woocommerce_checkout_order_processed" , array(&$this,'createTransaction'),10,1);
            }
            
        }
        
    }
    
   
    public function get_vendor_address($vendor_id){
        $vendor=get_option('vendor');
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $response=array();

        switch ($vendor) {
            case 'dokan':
                $vendorAddress=get_user_meta($vendor_id,'dokan_profile_settings', true);
                if($vendorAddress!=null){
                

                    $response['street_1']=$vendorAddress['address']['street_1'];
                    $response['street_2']=$vendorAddress['address']['street_2'];
                    $response['city']=$vendorAddress['address']['city'];
                    $response['region']=$vendorAddress['address']['state'];
                    $response['country']=$vendorAddress['address']['country'];
                    $response['zip']=$vendorAddress['address']['zip'];
                    $response['store_name']=$vendorAddress['store_name'];
        
                }else{
                    $response = $this->getDefaultWoocommerceAddress();
                    $response['store_name']= get_bloginfo( 'name' );
                }
                break;
                
                
            case 'wc':
                break;
            
            case 'multivendorx':
                $response['street_1']=get_user_meta($vendor_id,'_vendor_address_1', true);
                $response['street_2']=get_user_meta($vendor_id,'_vendor_address_2', true);
                $response['city']=get_user_meta($vendor_id,'_vendor_city', true);
                $response['region']=get_user_meta($vendor_id,'_vendor_state_code', true);
                $response['country']=get_user_meta($vendor_id,'_vendor_country_code', true);
                $response['zip']=get_user_meta($vendor_id,'_vendor_postcode', true);
                $response['store_name']=get_user_meta( $vendor_id, '_vendor_page_title', true );
                break;
            default:
                $response = $this->getDefaultWoocommerceAddress();
                $response['store_name']= get_bloginfo( 'name' );
                break;
        }
    

        return $response;
    }
    function get_taxcode($product_id){
        
        $taxcode=get_post_meta($product_id, 'woocommerce_custom_taxcode',true);
            $terms = get_the_terms($product_id, 'product_cat' );
            if ( !empty( $terms ) ) {
                foreach ( $terms as $term ) {
                    $taxcode=($taxcode=="")?get_term_meta($term->term_id, 'avalara_category_taxcode', true):$taxcode;
                    
                }
            }
            
            $taxcode=($taxcode=="")?DEFAULTTAXCODE:$taxcode;
            return $taxcode;
            
    }
    function search_array($search,$source) {
        return (count(array_intersect($search, $source)) == count($search));
     }

    
    function avatax_calculate_taxes( $total, $cart ) { 
 
        $totalr=$total;
        $linesArray=[];
        $tempArray = [];
        
        if (get_option('woocommerce_tax_display_cart')=="incl"){
            $include=true;
        }else{
            $include=false;
        }
        $disct=false;
        global $woocommerce;
        $discount_total=0;
        $countries=get_option('supported_countries');
        $search= array(0 , $woocommerce->customer->shipping_country);
        $customer_code=get_the_author_meta( 'avatax_customer_code', $woocommerce->customer->ID );
        $customer_code=($customer_code=="")?$woocommerce->customer->ID:$customer_code;
    
        $i=0;
       $array=(array) $cart;
        unset($array['cart_contents']);
        unset($array['removed_cart_contents']);
        unset($array['applied_coupons']);
       

        //var_dump( WC()->session->get( 'chosen_shipping_methods' )); die;

            foreach ($cart->cart_contents as $key => $value) {
            
                $total=$value['line_total'];
                $regular_price = $value['data']->get_regular_price();
                $sale_price = $value['data']->get_sale_price();
                $discount = ( (float)$regular_price  * (int)$value['quantity']);
            
                if($discount>$value['line_total']){
                    $disct=true;
                    $total=$discount;
                }
                //get vendor address
                $post_obj=get_post( $value['product_id']);
                $address=$this->get_vendor_address($post_obj->post_author);
                $taxcode=$this->get_taxcode($value['product_id']);
                
                $linesArray[]=array(
                    "number"=>$i+=1,
                    "quantity"=>$value['quantity'],
                    "amount"=>$total,
                    "itemCode"=>$value['data']->get_sku(),
                    "description"=>$value['data']->name,
                    "taxCode"=>$taxcode,
                    "discounted"=>$disct,
                    "taxIncluded"=>$include,
                    "merchantSellerIdentifier"=>$post_obj->post_author."-".$address['store_name'], 
                    "addresses"=>array(
                        "shipFrom"=>array(
                            "line1"=>$address["street_1"],
                            "line2"=>$address["street_2"],
                            "city"=> $address["city"],
                            "region"=>$address["region"],
                            "country"=>$address["country"],
                            "postalCode"=>$address["zip"]
                        ),
                        "shipTo"=>array(
                            "line1"=>$woocommerce->customer->shipping_address_1,
                            "line2"=>$woocommerce->customer->shipping_address_2,
                            "city"=>$woocommerce->customer->shipping_city,
                            "region"=>$woocommerce->customer->shipping_state,
                            "country"=>$woocommerce->customer->shipping_country,
                            "postalCode"=>$woocommerce->customer->shipping_postcode
                        )
                    )
                );
            }

                $tempArray['lines'] = $linesArray;
                $tempArray['type'] = "SalesOrder";
                $tempArray['companyCode'] =COMPANYCODE;
                $tempArray['discount'] =$cart->get_discount_total();
                $tempArray['date'] = date("Y-m-d");
                $tempArray['customerCode'] =$customer_code;
                $tempArray['currencyCode'] = get_option('woocommerce_currency');
                $tempArray['EntityUseCode'] = get_the_author_meta( 'avatax_customer_exempt_reason', $woocommerce->customer->ID );
                $tempArray['exemptionNo'] = get_the_author_meta( 'avatax_exemption_number', $woocommerce->customer->ID );
                $new = (object)$tempArray;
                $new = json_encode($new);
                $response = Api::curl("api/v2/transactions/create",'POST',$new);
                ErrorLog::sysLogs("Transactions create successfully".$response);
                $response = json_decode($response);
                $cart->taxes= array((float) $response->totalTax);
                $shipping=$cart->shipping_total;
                $total=(float)($response->totalAmount+$response->totalTax+$shipping)-$response->totalDiscount;
               
                if($total==0){
                   $total=(float)($totalr+$response->totalTax+$shipping)-$cart->get_discount_total();
                }
        
        return $total;
}

public function createTransaction($order_id){
    try{
       
        global $wpdb;
        global $woocommerce;
        $table_prefix = $wpdb->prefix;
        $wp_usermeta = $table_prefix.'usermeta';
        $order = new WC_Order($order_id);
        $order = wc_get_order($order_id);
        $tempArray = [];
        $linesArray = [];
        $disct=false;
        if (get_option('woocommerce_tax_display_cart')=="incl"){
            $include=true;
        }else{
            $include=false;
       }
      
        $countries=get_option('supported_countries');
        $search= array(0 , $order->get_shipping_country());
        $customer_code=get_the_author_meta( 'avatax_customer_code', $order->get_customer_id() );
        $customer_code=($customer_code=="")?$order->get_customer_id():$customer_code;
        $i=0;
        foreach ( $order->get_items() as $item_id => $item_values ) {
            $total=$item_values->get_total();
            $post_obj=get_post($item_values->get_product_id());
            $address=$this->get_vendor_address($post_obj->post_author);
           
            $product =$item_values->get_product();
            $regular_price = $product->get_regular_price();
            $discount = ( (float)$regular_price * (int)$item_values->get_quantity());
            if($discount>$item_values->get_total()){
                $disct=true;
                $total=$discount;
            }
            $taxcode=$this->get_taxcode($item_values->get_product_id());
            $linesArray[]=array(
                "number"=>$i+=1,
                "quantity"=> $item_values->get_quantity(),
                "amount"=>$total,
                "itemCode"=>$product->get_sku(),
                "description"=>$product->get_name(),
                "taxCode"=>$taxcode,
                "discounted"=>$disct,
                "taxIncluded"=>$include,
                "merchantSellerIdentifier"=>$post_obj->post_author."-".$address['store_name'], 
                "addresses"=>array(
                    "shipFrom"=>array(
                        "line1"=>$address["street_1"],
                        "line2"=>$address["street_2"],
                        "city"=> $address["city"],
                        "region"=>$address["region"],
                        "country"=>$address["country"],
                        "postalCode"=>$address["zip"]
                    ),
                    "shipTo"=>array(
                        "line1"=>$order->get_shipping_address_1(),
                        "line2"=>$order->get_shipping_address_2(),
                        "city"=>$order->get_shipping_city(),
                        "region"=>$order->get_shipping_state(),
                        "country"=>$order->get_shipping_country(),
                        "postalCode"=>$order->get_shipping_postcode()
                    )
                )
            );

        }
       
        $randomcode = substr(str_shuffle("ABCDDEFGHIJKLMNOPQRSTUVWXYZ"), 0,4);
        $tempArray['code']=$randomcode."-".$order_id;
        $tempArray['lines'] = $linesArray;
        $tempArray['type'] = "SalesInvoice";
        $tempArray['companyCode'] =COMPANYCODE;
        $tempArray['date'] =date("Y-m-d");
        $tempArray['customerCode'] =$customer_code;
        $tempArray['currencyCode'] =$order->get_currency();
        $tempArray['EntityUseCode'] = get_the_author_meta( 'avatax_customer_exempt_reason', $order->get_customer_id() );
        $tempArray['exemptionNo'] = get_the_author_meta( 'avatax_exemption_number', $order->get_customer_id() );
        $tempArray['purchaseOrderNo'] = $order->get_id();
        $tempArray['discount'] =$order->get_discount_total();
        $tempArray['commit'] = false;
        $tempArray['currencyCode'] = $order->get_currency();
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
        update_post_meta( $order->get_id(),'_order_tax', $response->totalTax);

            if(COMMIT=="yes"){
                $commitObj = array("commit"=>true);
                $commitObj = (object)$commitObj;
                $data = json_encode($commitObj);
                $commit = Api::curl("api/v2/companies/".$tempArray['companyCode']."/transactions/".$transactionsCode."/commit", 'POST',$data);
                $commit = json_decode($commit, TRUE); 
                Dml::updateAtTransactionsCommitStatus($commit,$transactionsId,$transactionsCode,$productId,$order_id);
            }     

        
    }catch(Exception $e){
        $message = $e->getMessage();
        ErrorLog::errorLogs($message);
    }      



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

    public function headerkey($array){
        $data=array();
        $accountId = $array['accountId'];
        $key = $array['licenseKey'];   
        
        $data['apiKey']=base64_encode($accountId.":".$key);
        $data['env']=$array['env'];
        return $data;
    }
    public function verifyAccount(){

        $data= $this->headerkey($_POST);
        $accountId = $_POST['accountId'];
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
            $array['saved'] = get_option('companyID');
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
            $data= $this->headerkey($_POST);
          
            $accountId = $_POST['accountId'];
            $addressArray = [];
            $CompanyID=$_POST['CompanyID'];
            $response = Api::curl("api/v2/companies/".$CompanyID."/locations","GET",$data);
           
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
            
            $woocommerce_default_country = $response->value[0]->country.':'.$response->value[0]->region;
            update_option( 'woocommerce_store_address',$response->value[0]->line1);
            update_option( 'woocommerce_default_country',$woocommerce_default_country);
            update_option( 'woocommerce_store_city',$response->value[0]->city);
            update_option( 'woocommerce_store_postcode',$response->value[0]->postalCode);
            
            $response =  Dml::companyAdminDetail($array);
            echo json_encode($array); die();
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }

        
    }

  

}


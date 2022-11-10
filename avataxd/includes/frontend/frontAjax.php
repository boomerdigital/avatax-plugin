<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class FrontAjax{

    public function __construct(){
        add_action("wp_ajax_locations" , array(&$this,'locations'));
        add_action("wp_ajax_nopriv_locations" , array(&$this,'locations'));

         add_action("wp_ajax_validateAddress" , array(&$this,'validateAddress'));
         add_action("wp_ajax_nopriv_validateAddress" , array(&$this,'validateAddress'));
         add_action("wp_ajax_SaveDataCustomer" , array(&$this,'SaveDataCustomer'));
         add_action("wp_ajax_nopriv_SaveDataCustomer" , array(&$this,'SaveDataCustomer'));
         add_filter( 'woocommerce_default_address_fields' , array(&$this,'wdm_override_default_address_fields'),10,1 );
      
           if(ENABLEAVATAX=="yes"){
           
                 //add_action("woocommerce_checkout_fields" , array(&$this,'vatID'));
                 add_action( "woocommerce_checkout_update_order_meta", array(&$this,'saveVatId'));
                 
                  
           }     
           

        add_action("wp_head" , array(&$this,'setAdminAjax'));
        add_action("wp_ajax_commitTransactions" , array(&$this,'commitTransactions'));
        add_action("wp_ajax_nopriv_commitTransactions" , array(&$this,'commitTransactions'));
        //add_filter( 'woocommerce_checkout_get_value' , array(&$this,'custom_checkout_get_value'), 20, 2 );

    }

     public function wdm_override_default_address_fields( $address_fields ){
    
        $temp_fields = array();
        $response = Api::curl("api/v2/definitions/entityusecodes");
       
        ErrorLog::sysLogs("Get Entityusecodes successfully".$response);
        $response = json_decode($response, TRUE);
        $option=array();
        $option["0"]=__("Select Entity Use Code","woocommerce");
        foreach($response['value'] as $value){
           $option[$value['code']] = __($value['code'].'/'.$value['name'], 'woocommerce');
            
        }
        
        $address_fields['avatax_exemption_number'] = array(
            'label' => __('Tax Exemption Number', 'woocommerce'),
            'placeholder'=> '',
            'required'   => false,
            'class'      => array('form-row-wide', 'address-field', 'update_totals_on_change'),
            'type'  => 'text',
            'default' => get_the_author_meta( 'avatax_exemption_number', get_current_user_id() ),
           
            
             );

        $address_fields['avatax_customer_exempt_reason'] = array(
       'label'     => __('Tax Exempt Category', 'woocommerce'),
       'required'  => false,
       'class'     => array('form-row-wide'),
       'type'  => 'select',
       'options'   => $option,
       'default' =>  get_the_author_meta( 'avatax_customer_exempt_reason',get_current_user_id()),
        );
        if(VAT=="yes"){
        $address_fields['vat_id'] = array(
            'label' => __('VAT ID', 'woocommerce'), 
            'placeholder' => _x('VAT ID....', 'placeholder', 'woocommerce'), // Add custom field placeholder
            'required' => false, 
            'clear' => false, 
            'type' => 'text', 
            'class' => array('form-row-wide','address-field')
        );
    }
   

       return $address_fields;
   }
   function custom_checkout_get_value( $value, $imput ) {
   
    if($imput == 'billing_avatax_exemption_number' ){

        $value ="text";
    }
    if($imput == ' billing_avatax_customer_exempt_reason_field' ){

        $value ="A";
    }
   

   

    return $value;
}

    public function locations(){
        try{
            $response = Api::curl("api/v2/locations");
            ErrorLog::sysLogs("List of company details".$response);
            echo $response;
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die(); 
    } 
   
    public function validateAddress(){
        try{
            $data =  json_encode($_POST);
            $country=$_POST['country'];
            $supported_countries = get_option('supported_countries');
            
            
            
            if(in_array($country,$supported_countries) or in_array(0,$supported_countries)){
                $response = Api::curl("api/v2/addresses/validate", "POST", $data);
                ErrorLog::sysLogs("Validate address".$response);
            
            $response = Api::curl("api/v2/addresses/resolve",'POST', $data);
            ErrorLog::sysLogs("validate Address".$response);
            $result =  json_decode($response);
            $response = [];
            if(isset($result->messages) && !empty($result->messages)){
                $error = [];
                foreach($result->messages as $key => $value){   
                    $error[]=$value->summary.'<br>';
                }
                $response['status'] = "error";
                $response['code'] = "403";
                $response['message'] = $error;
            }else{
               
               
                $return = DmlFront::insertAtAddress($result);
                $response['status'] = "success";
                $response['code'] = "201";
                $response['message'] = "Validated Successfully!";
                $response['data'] = $return;
            }
            echo json_encode($response);
        }
            
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die();
    } 
    public function SaveDataCustomer(){
        try{
            update_user_meta( get_current_user_id(), 'avatax_exemption_number', $_POST['billing_avatax_exemption_number']);
	        update_user_meta( get_current_user_id(), 'avatax_customer_exempt_reason', $_POST['billing_avatax_customer_exempt_reason']);
            $response['status'] = "success";
            $response['code'] = "201";
            $response['message'] = "Saved Successfully!";
            echo json_encode($response);
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die();
    }
   
    public function commitTransactions(){
        try{
            $response = Api::curl("api/v2/companies/{companyCode}/transactions/{transactionCode}/commit");
            ErrorLog::sysLogs("Transactions commit successfully".$response);
            echo $response;
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        wp_die(); 
    }

    public function setAdminAjax(){
        echo "<meta name='ajaxurl' value='".admin_url( 'admin-ajax.php' )."'/>";
    }
    public function vatId($fields)
    {
        $fields['billing']['vat_id'] = array(
            'label' => __('VAT ID', 'woocommerce'), // Add custom field label
            'placeholder' => _x('VAT ID....', 'placeholder', 'woocommerce'), // Add custom field placeholder
            'required' => false, // if field is required or not
            'clear' => false, // add clear or not
            'type' => 'text', // add field type
            'class' => array('form-row-wide','address-field','update_totals_on_change')
        );
        return $fields;
    }
    public function saveVatId( $order_id ) {
        if ( ! empty( $_POST['billing_vat_id'] ) ) {
            update_post_meta( $order_id, 'vat_id', sanitize_text_field( $_POST['billing_vat_id'] ) );
        }
    }
    
}

 ?>

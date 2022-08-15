<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class FrontAjax{

    public function __construct(){
        add_action("wp_ajax_locations" , array(&$this,'locations'));
        add_action("wp_ajax_nopriv_locations" , array(&$this,'locations'));

         add_action("wp_ajax_validateAddress" , array(&$this,'validateAddress'));
         add_action("wp_ajax_nopriv_validateAddress" , array(&$this,'validateAddress'));
         if(VAT=="yes"){
         add_action("woocommerce_checkout_fields" , array(&$this,'vatID'));
         add_action( "woocommerce_checkout_update_order_meta", array(&$this,'saveVatId'));
         add_action( "woocommerce_admin_order_data_after_order_details", array(&$this,'showVatId'));
         }        

        add_action("wp_head" , array(&$this,'setAdminAjax'));

        add_action("wp_ajax_commitTransactions" , array(&$this,'commitTransactions'));
        add_action("wp_ajax_nopriv_commitTransactions" , array(&$this,'commitTransactions'));

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
                echo $response;
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
            'class' => array('vat-id')   // add class name
        );
        return $fields;
    }
    public function saveVatId( $order_id ) {
        if ( ! empty( $_POST['vat_id'] ) ) {
            update_post_meta( $order_id, 'vat_id', sanitize_text_field( $_POST['vat_id'] ) );
        }
    }
    public function showVatId($order){  ?>
        <div class="form-field form-field-wide wc-customer-user">
            <div class="address">
            <?php $orderId = $order->get_id();
            if(!empty(get_post_meta($orderId,'vat_id',true))){
                echo '<p><b style="color:black;">'. __('vat id'). '</b> : '. get_post_meta($orderId,'vat_id',true).'</p>';}?>
            </div>
        </div>
    <?php }
}

 ?>

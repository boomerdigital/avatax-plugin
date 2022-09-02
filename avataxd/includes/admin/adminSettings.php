<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class AdminSettings{
    public function __construct(){
        $this->checkWocommerceExistence();
        add_filter( 'woocommerce_get_sections_tax', array('AdminSettings','addCustomMenu') );
        add_filter( 'woocommerce_get_settings_tax', array('AdminSettings','pluginHtml'), 10, 2 );
        //self::getCompanyInfo();
        add_action( 'woocommerce_product_options_general_product_data', array('AdminSettings','woocommerce_product_custom_fields') ); 
        add_action('woocommerce_process_product_meta', array('AdminSettings','save_woocommerce_product_custom_fields')); 

    }

    public function checkWocommerceExistence(){
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('admin_notices', array($this,'adminNotice'));
        }
    }

    public function adminNotice() {
        global $pagenow;
        
        if ( $pagenow == 'options-general.php' ) {
            echo '<div class="notice notice-warning is-dismissible">
                <p>Wocommerce is must for this Avatax plugin installation.</p>
            </div>';
            return false;
        }
    }

    public static function addCustomMenu( $sections ) {
        $sections['mysettings'] = __( 'Custom Avatax', 'custom' );
        return $sections;
    }

    public static function pluginHtml( $settings, $current_section ) {

        if ( $current_section == 'mysettings' ) {
            $settings = array();
            
         
            $settings[] = array( 'name' => __( 'Connection Settings', '' ), 'type' => 'title', 'desc'=>__('Log in to your AvaTax Admin Console to find your connection information.'), 'id' => 'connection' );
    
            //Account Keys
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'ac' );
            $settings[] = array( 'name' => __( 'Account Number'), 'type' => 'text',   'id' => 'ac' );
    
            //License Keys
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'lic' );
            $settings[] = array( 'name' => __( 'License Key'), 'type' => 'text',   'id' => 'lic' );
            
            // //Enviorment
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'env' );
            $settings[] = array( 'name' => __( 'Enviroment'), 'type' => 'select',   'id' => 'env' , 'options' => 
                array(
                    'production' => __( 'Production' ),
                    'sandbox' => __('Development')
                )
            );
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'ttv' );
            // Add Title to the Settings
            $settings[] = array( 'name' => __( 'Tax Calculation', 'woorei' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure my options.', 'woorei' ), 'id' => 'mysettings' );
            $settings[] = array( 'type' => 'woorei_dynamic_field_table', 'id' => 'woorei_dynamic_field_table' );
    
            // Add Enable or Disable to the Settings        
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'enable' );
            $settings[] = array( 'name' => __( 'Enable/Disable'), 'type' => 'checkbox',  'desc'=>__('This will override all configured WooCommerce tax rates and replace them with the rates fetched from AvaTax.'), 'id' => 'enable' );
    
            echo '<div class="part">';
            //Record calculation
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'rec', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Record Calculations'), 'type' => 'checkbox', 'class'=>'calc', 'desc'=>__('Send permanent calculations in Avalara when orders are placed.'), 'id' => 'rec' );
            
            // Commit Transaction
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'commit', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Commit Transactions'), 'type' => 'checkbox', 'class'=>'calc', 'desc'=>__(' Set transactions as "committed" when sending to Avalara.'), 'id' => 'commit' );
    
            // Supported Location
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'loc', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Supported Locations'), 'type' => 'select', 'options' => 
                array(
    
                'top' => __( 'All Locations' ),
                'bottom' => __('Specfic location Only')
    
            ), 'class'=>'calc', 'id' => 'loc');
             
            //Company Code
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'com', 'class'=>'calc' );
           // $settings[] = array( 'name' => __( 'Company Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'com' );
           
           $settings[] = array( 'name' => __( 'Company Code'), 'type' => 'select', 'options' => 
                array(
    
               
    
            ), 'class'=>'calc', 'id' => 'companycode');
             
    
    
    
            //Origin Address
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'origin', 'class'=>'calc' );
            $settings[] = array( 'name' => __('Origin Address'),  'placeholder'=>'Street Address' ,  'type' => 'text',  'class'=>'calc', 'id' => 'origin' );
            $settings[] = array( 'label' => __('Account username', 'custom'),'placeholder'=>'Street Address' ,  'type' => 'text',  'class'=>'calc', 'id' => 'Street' );
            $settings[] = array( 'placeholder'=>'City/Town' , 'type' => 'text',  'class'=>'calc', 'id' => 'City' );
            $settings[] = array( 'placeholder'=>'State/Region' , 'type' => 'text',  'class'=>'calc', 'id' => 'State' );
            $settings[] = array( 'placeholder'=>'Zip/Postcode' , 'type' => 'text',  'class'=>'calc', 'id' => 'Zip' );
            $settings[] = array( 'placeholder'=>'Country' , 'type' => 'text', 'id' => 'country' , 'class'=>'calc' );
            
    
    
            //Validate Button
            $settings[] = array( 'name' => __('Title'),
            'type' => 'button',
            'desc' => __( 'Activate plugin'),
            'desc_tip' => true,
            'class' => 'button-secondary',
            'id'	=> 'vd-btn');
    
    
    
            //Product Tax Code
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'ptax', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Default Product Tax Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'ptax' );
    
            //Shipping Tax Code
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'stax', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Default Shipping Tax Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'stax' );
    
            //Cart Calculation
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'cart_cl', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Cart Calculation'), 'type' => 'select', 'options' => 
                array(
                    'no'    => __( 'Do not show calculations on the cart page', 'woocommerce-avatax' ),
                    'yes'   => __( 'Show estimated tax rates', 'woocommerce-avatax' ),
                    'force' => __( 'Force full tax rate calculation', 'woocommerce-avatax' ),
    
                ), 'id' => 'cart_cl' , 'class'=>'calc');     
                
            //Non-Us customers
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'non' ,'class'=>'calc');
            $settings[] = array( 'name' => __( 'Enable/Disable'), 'type' => 'checkbox', 'class'=>'calc',  'desc'=>__(' Enable tax calculations on the cart page for international addresses'), 'id' => 'non'   );   
    
            //Enable VAT
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'vat' ,'class'=>'calc');
            $settings[] = array( 'name' => __( 'Enable/Disable'), 'type' => 'checkbox', 'class'=>'calc',  'desc'=>__('Allow customers to input their VAT ID during checkout'), 'id' => 'vat'   );
    
            echo '</div>';
            // Add Enable or Disable to the Settings
            $settings[] = array( 'name' => __( 'Address Validation', '' ), 'type' => 'title', 'desc'=>__('Validate shipping addresses at checkout before calculating tax.'), 'id' => 'address' );
     
            //Address Setting
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'check' );
            $settings[] = array( 'name' => __( 'Enable/Disable'), 'type' => 'checkbox',  'desc'=>__('Enable AvaTax address validation.'), 'id' => 'check' );
            //Require for Tax Calculations
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'req', 'class'=>'add' );
            $settings[] = array( 'name' => __( 'Require for Tax Calculation	'), 'type' => 'checkbox', 'class'=>'', 'desc'=>__('Require address validation before orders can be placed with calculated tax.'), 'id' => 'req' );
            //Supported Countries
            //$settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'sloc', 'class'=>'add' );
            $settings[] = array( 'name' => __( 'Supported Countries'), 'type' => 'select', 'options' => 
                
            array(
                    'top' => __( 'All Locations' ),
                    'bottom' => __('Specfic location Only')
                ), 'id' => 'sloc','multiple'=>'multiple' );
    
            
            //cross-Border Classification
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'cross' );
            $settings[] = array( 'name' => __( 'Cross-border classification'), 'type' => 'text',   'id' => 'cross' );
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'sync' );
            $settings[] = array( 'name' => __( 'Enable/Disable'), 'type' => 'checkbox',  'desc'=>__(' Sync products to classify for cross-border duties.'), 'id' => 'sync' );
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'debug' );
            $settings[] = array( 'name' => __( 'Debug Mode'), 'type' => 'checkbox',  'desc'=>__('Log API requests, responses, and errors for debugging.'), 'id' => 'debug' );
            //Vendor
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'vendor' );
            $settings[] = array( 'name' => __( '
            Choose Vendor'), 'type' => 'select',   'id' => 'vendor' , 'options' => 
                array(
                    '0' => __( 'Choose Vendor' ),
                    'seller' => __( 'Dokan' ),
                    'm_seller' => __( 'Marketplace' ),
                    'dc_vendor' => __( 'WCMp' )
                )
            );

            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'shippingTax' );
            $settings[] = array( 'name' => __( 'Shipping Tax'), 'type' => 'checkbox',  'desc'=>__('Enable Shipping Tax.'), 'id' => 'shippingTax' );
            $settings[] = array( 'type' => 'sectionend', 'id' => 'debug2' );
        }
        return $settings;
    }


    public static function getCompanyID(){
        try{
            $response = Api::curl("api/v2/settings");
            $response = json_decode($response);
            return $response->value[0]->companyId;
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        } 
    }
    public static function getCompanyInfo(){
        try{
            $getCompanyID = self::getCompanyID();
            $response = Api::curl("api/v2/companies/".$getCompanyID."/locations");
            ErrorLog::sysLogs("List all location objects defined for this company.".$response);
            $response = json_decode($response);
            $response =  Dml::companyAdminDetail($response);
        }catch(Exception $e){
            $message = $e->getMessage();
            ErrorLog::errorLogs($message);
        }
        
    }

    public static function woocommerce_product_custom_fields(){
      $args = array(
          'id' => 'woocommerce_custom_taxcode',
          'label' => __('Tax code', 'Tax Code'),
          'placeholder' => __('P0000000', 'P0000000'),
      );
      woocommerce_wp_text_input($args);
    }

    public static function save_woocommerce_product_custom_fields($post_id){
        $product = wc_get_product($post_id);
        $woocommerce_custom_taxcode = isset($_POST['woocommerce_custom_taxcode']) ? $_POST['woocommerce_custom_taxcode'] : '';
        $product->update_meta_data('woocommerce_custom_taxcode', sanitize_text_field($woocommerce_custom_taxcode));
        $product->save();
    }

}
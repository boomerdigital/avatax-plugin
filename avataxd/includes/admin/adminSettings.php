<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class AdminSettings{
    public function __construct(){
        $this->checkWocommerceExistence();
        add_filter( 'woocommerce_get_sections_tax', array('AdminSettings','avatax_addCustomMenu') );
        add_filter( 'woocommerce_get_settings_tax', array('AdminSettings','avatax_pluginHtml'), 10, 2 );
        add_action( 'woocommerce_product_options_general_product_data', array('AdminSettings','avatax_product_custom_fields') ); 
        add_action('woocommerce_process_product_meta', array('AdminSettings','save_avatax_product_custom_fields')); 
       if(ENABLEAVATAX=="yes"){
           
           add_action( 'show_user_profile',array(&$this,'avatax_custom_customer_code_field'));
           add_action( 'edit_user_profile', array(&$this,'avatax_custom_customer_code_field'));
           add_action( 'personal_options_update', array(&$this,'save_avatax_field') );
           add_action( 'edit_user_profile_update', array(&$this,'save_avatax_field') );
           add_action('product_cat_add_form_fields', array(&$this,'avatax_taxonomy_add_new_meta_field'), 10, 1);
           add_action('product_cat_edit_form_fields',array(&$this, 'avatax_taxonomy_edit_meta_field'), 10, 1);
           add_action('edited_product_cat',array(&$this,'avatax_save_taxonomy_custom_meta'), 10, 1);
           add_action('create_product_cat',array(&$this, 'avatax_save_taxonomy_custom_meta'), 10, 1);
           add_action( "woocommerce_admin_order_data_after_order_details", array(&$this,'avatax_showVatId'),10,1);
        }
       
        
    }

    
    public  static function getVendors(){
        $vendor=array();
        $vendor['standalone']='Standalone';
      

        if ( in_array( 'dokan-lite/dokan.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['dokan']='dokan';
        }
        if ( in_array( 'wc-vendors/class-wc-vendors.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['wc-vendors']='wc-vendors';
        }
        if ( in_array( 'wcfmmp/wcfmmp.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['wcfmmp']='wcfmmp';
        }
        if ( in_array( 'wcmp-frontend_product_manager/wcmp_frontend_product_manager.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['wcmp']='wcmp';
        }
        if ( in_array( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['multivendorx']='Multivendorx';
        }
        if ( in_array( 'woocommerce-product-vendors/woocommerce-product-vendors.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['woocommerce-product-vendors']='woocommerce-product-vendors';
        }
        if ( in_array( 'yith-woocommerce-product-vendors/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['yith-woocommerce-product-vendors']='yith-woocommerce-product-vendors';
        }
        if ( in_array( 'wcfmmarketplace/wcfmmarketplace.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['wcfmmarketplace']='wcfmmarketplace';
        }
        if ( in_array( 'wcfmgs/wcfmgs.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vendor['wcfmgs']='wcfmgs';
        }

        apply_filters('avatax_vendor',$vendor);
        return $vendor;
    }
    public function checkWocommerceExistence(){
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action('admin_notices', array($this,'avatax_adminNotice'));
        }
    }

    public  static function avatax_adminNotice() {
        global $pagenow;
        
        if ( $pagenow == 'options-general.php' ) {
            echo '<div class="notice notice-warning is-dismissible">
                <p>Wocommerce is must for this Avatax plugin installation.</p>
            </div>';
            return false;
        }
    }

    public static function avatax_addCustomMenu( $sections ) {
        $sections['mysettings'] = __( 'Avatax Settings', 'custom' );
        return $sections;
    }

    public static function avatax_pluginHtml( $settings, $current_section ) {

        if ( $current_section == 'mysettings' ) {
          
            $settings = array();
            //echo " <br> <a href='https://drive.google.com/file/d/1RGo7ZSb8FuCUv9eDmvwyVjFucavFjkOh/view?usp=sharing' target='_blank'>User Guide</a>";
         
            $settings[] = array( 'name' => __( 'User Guide', '' ), 'type' => 'title', 'desc'=>__("<a href='https://drive.google.com/file/d/1RGo7ZSb8FuCUv9eDmvwyVjFucavFjkOh/view?usp=sharing' target='_blank'>User Guide</a>"), 'id' => 'userguide' );
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
            $settings[] = array( 'name' => __( 'Company Code'), 'type' => 'select', 'options' => 
            array(

           

        ), 'class'=>'calc', 'id' => 'companycode');
         

      

            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'ttv' );
            // Add Title to the Settings
            $settings[] = array( 'name' => __( 'Tax Calculation', 'woorei' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure my options.', 'woorei' ), 'id' => 'mysettings' );
            $settings[] = array( 'type' => 'woorei_dynamic_field_table', 'id' => 'woorei_dynamic_field_table' );
    
            // Add Enable or Disable to the Settings        
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'avatax_enable' );
            $settings[] = array( 'name' => __( ' Avatax: Enable/Disable'), 'type' => 'checkbox',  'desc'=>__('This will override all configured WooCommerce tax rates and replace them with the rates fetched from AvaTax.'), 'id' => 'avatax_enable' );
    
            echo '<div class="part">';
           
            //Record calculation
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'rec', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Record Calculations'), 'type' => 'checkbox', 'class'=>'calc', 'desc'=>__('Send permanent calculations in Avalara when orders are placed.'), 'id' => 'rec' );
            
            // Commit Transaction
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'commit', 'class'=>'calc' );
            $settings[] = array( 'name' => __( 'Commit Transactions'), 'type' => 'checkbox', 'class'=>'calc', 'desc'=>__(' Set transactions as "committed" when sending to Avalara.'), 'id' => 'commit' );
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'debug' );
            $settings[] = array( 'name' => __( 'Enable Logging'), 'type' => 'checkbox',  'desc'=>__('Log API requests, responses, and errors for debugging.'), 'id' => 'debug' );
             //Product Tax Code
             $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'ptax', 'class'=>'calc' );
             $settings[] = array( 'name' => __( 'Default Product Tax Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'default_tax_code' );
     
             //Shipping Tax Code
             $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'woocommerce_avatax_shipping_taxcode', 'class'=>'calc' );
             $settings[] = array( 'name' => __( 'Default Shipping Tax Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'woocommerce_avatax_shipping_taxcode' );
     
            //Company Code
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'com', 'class'=>'calc' );
           // $settings[] = array( 'name' => __( 'Company Code'), 'type' => 'text', 'class'=>'calc', 'id' => 'com' );
           
          
    
    
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
    

            //Vendor
            $vendors=self::getVendors();
            $settings[] = array( 'name' => __( '' ), 'type' => 'title',  'id' => 'vendor' );
            $settings[] = array( 'name' => __( '
            Choose Vendor'), 'type' => 'select',   'id' => 'vendor' , 'options' => 
            $vendors);

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

    public static function avatax_product_custom_fields(){

      
        // $response = Api::curl("/api/v2/companies/".get_option('companyID')."/taxcodes");
        // ErrorLog::sysLogs("Get tax codes successfully".$response);
        // $response = json_decode($response, TRUE);

        // $taxcode=array();
      
        // foreach($response as  $value){
            
        //     $taxcode[$value['taxcode']]= $value['taxcode']."/". $value['description'];

          
           
        // }
       
        // $field=array(
        //     'id' => 'woocommerce_custom_taxcode',
        //     'name' => 'woocommerce_custom_taxcode',
        //     'class' => 'select short',
        //     'value'       => get_post_meta( get_the_ID(), 'woocommerce_custom_taxcode', true ),
        //     'label' => __('Tax Code', 'woocommerce'),
        //     'options' => $taxcode,
        //     );

        // woocommerce_wp_select($field);
      $args = array(
          'id' => 'woocommerce_custom_taxcode',
          'label' => __('Avalara Tax code', ' Avalara Tax Code'),
          'placeholder' => __('P0000000', 'P0000000'),
      );
      woocommerce_wp_text_input($args);
    }

    public static function save_avatax_product_custom_fields($post_id){
        $product = wc_get_product($post_id);
        $woocommerce_custom_taxcode = isset($_POST['woocommerce_custom_taxcode']) ? $_POST['woocommerce_custom_taxcode'] : '';
        $product->update_meta_data('woocommerce_custom_taxcode', sanitize_text_field($woocommerce_custom_taxcode));
        $product->save();
    }

 
    
   
function avatax_custom_customer_code_field( $user )
{
  
    $user_roles = $user->roles;
    if ( in_array( 'customer', $user_roles, true ) ) {
       
        echo '<h3 class="heading">Avatax Field</h3>';
        $customercode=get_the_author_meta( 'avatax_customer_code', $user->ID );
        $response = Api::curl("api/v2/definitions/entityusecodes");
       
        ErrorLog::sysLogs("Get Entityusecodes successfully".$response);
        $response = json_decode($response, TRUE);
        
        if(empty($customercode)){
            $customercode=$user->ID;
        }
        
        ?>
        
        <table class="form-table">
        <tr>
                <th><label for="customer_code">Customer Code</label></th>
            <td><input type="text" class="regular-text" name="avatax_customer_code" id="avatax_customer_code"  value="<?php echo $customercode; ?>"/>
                    </td>
        </tr>
        <tr>
                <th><label for="customer_code"> Tax Exemption Number</label></th>
            <td><input type="text" class="regular-text" name="avatax_exemption_number" id="avatax_exemption_number"  value="<?php echo get_the_author_meta( 'avatax_exemption_number', $user->ID ); ?>"/>
                    </td>
        </tr>
        <tr>
                <th><label for="customer_code">Tax Exempt Category</label></th>
            <td> <select name="avatax_customer_exempt_reason" class="" id="avatax_customer_exempt_reason">
                <option value="">Choose one</option>
                <?php
                foreach($response['value'] as $value){
                    $selected = get_the_author_meta( 'avatax_customer_exempt_reason', $user->ID ) == $value['code'] ? 'selected' : '';
                    echo '<option value="'.$value['code'].'" '.$selected.'>'.$value['code'].'/'.$value['name'].'</option>';
                }
                ?>
            </select>
                    </td>
        </tr>
        </table>
        
        <?php
    }
       
}

function save_avatax_field($user_id){
 
    if ( ! empty( $_POST['avatax_customer_code'] )) {
		update_user_meta( $user_id, 'avatax_customer_code', $_POST['avatax_customer_code']);
	}
 
		update_user_meta( $user_id, 'avatax_exemption_number', $_POST['avatax_exemption_number']);
	
   
		update_user_meta( $user_id, 'avatax_customer_exempt_reason', $_POST['avatax_customer_exempt_reason']);
	
}


function avatax_taxonomy_add_new_meta_field() {
    ?>
        
    <div class="form-field">
        <label for="avalara_category_taxcode"><?php _e('Avalara Tax Code', 'atc'); ?></label>
        <input type="text" name="avalara_category_taxcode" id="">
       
    </div>
   
    <?php
}


function avatax_taxonomy_edit_meta_field($term) {

   
    $term_id = $term->term_id;
    $avalara_category_taxcode = get_term_meta($term_id, 'avalara_category_taxcode', true);
    
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="avalara_category_taxcode"><?php _e('Avalara Tax Code', 'atc'); ?></label></th>
        <td>
            <input type="text" name="avalara_category_taxcode" id="avalara_category_taxcode" value="<?php echo esc_attr($avalara_category_taxcode) ? esc_attr($avalara_category_taxcode) : ''; ?>">
            
        </td>
    </tr>
    
    <?php
}

function avatax_save_taxonomy_custom_meta($term_id) {

    $avalara_category_taxcode = filter_input(INPUT_POST, 'avalara_category_taxcode');
   

    update_term_meta($term_id, 'avalara_category_taxcode', $avalara_category_taxcode);
}
   
public function avatax_showVatId($order){  ?>
<br class="clear" />
   
       
   
        <div class="address">
        <?php $orderId = $order->get_id();
        if(!empty(get_post_meta($orderId,'vat_id',true))){
            echo '<p><b style="color:black;">'. __('vat id'). '</b> : '. get_post_meta($orderId,'vat_id',true).'</p> <br>';
            }
            if(!empty(get_the_author_meta( 'avatax_exemption_number', $order->get_customer_id()))){

                echo '<p><b style="color:black;">'. __('Tax Exemption Number'). '</b> : '. get_the_author_meta( 'avatax_exemption_number', $order->get_customer_id() ).'</p>';
            }
            if(!empty(get_the_author_meta( 'avatax_customer_exempt_reason', $order->get_customer_id()))){
                
                echo '<p><b style="color:black;">'. __('Tax Exempt Category'). '</b> : '. get_the_author_meta( 'avatax_customer_exempt_reason',$order->get_customer_id()).'</p>';
            }
            ?>
        </div>

<?php }
       

}
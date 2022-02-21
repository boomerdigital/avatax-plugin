<?php 
 if(ENABLEADDRESSVALIDATE=="yes"){
    add_action( 'woocommerce_before_order_notes', 'bbloomer_add_custom_checkout_field' );
    function bbloomer_add_custom_checkout_field() { 
      ?> 
      <input type="button" id="w_validate" value="<?php _e( 'Validate Address', 'woocommerce' ) ?>" />   
      <div id="danger" class='alert alert-danger' role='alert'></div>
      <div id="sucess" class='alert alert-danger' role='alert'></div>
      <style>
        div#sucess {
          color: green;
          }
        div#danger {
        color: red;
        } 
      </style>
      <?php
    }
  }
add_action('woocommerce_before_checkout_form', 'addressValidation', 5);
function addressValidation() {
    $ADDRESSVALIDATION = ADDRESSVALIDATION;
 	echo "<div class='add-error'></div>";
    echo "<input type='hidden' name='address-validate' id='address-validate' value=".$ADDRESSVALIDATION.">";      
}
?>

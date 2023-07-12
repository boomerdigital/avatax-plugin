jQuery(document).ready(function($){


    jQuery(document).on("click",'#w_validate',function(){
    
        var billing_address_1= jQuery("#billing_address_1").val();
        var billing_address_2= jQuery("#billing_address_2").val();
        var billing_city= jQuery("#billing_city").val();
        var billing_country= jQuery("#billing_country").val();
        var billing_postcode= jQuery("#billing_postcode").val();
        var billing_state= jQuery("#billing_state").val();

    
        jQuery.ajax({
            type: "POST",
            url: getAdminAjax(),
            data:{ action: 'validateAddress',"line1":billing_address_1, "line2":billing_address_2, "city":billing_city, "country":billing_country, "postalCode":billing_postcode, "region":billing_state, "textCase":"Upper" },
            success:function(data){
                //  console.log(data);
                var result =  JSON.parse( data );

                //console.log(result);

                 if(result.status == 'success' ){
                    jQuery('#sucess').html(result.message).fadeIn('slow');   
                }else{
                    for(var i = 0; i< result.message.length; i++){
                        jQuery('#danger').html(result.message).fadeIn('slow');
                    }
                    
                 }
            

            }
        });
    });
    
    jQuery(document).on("click",'#place_order',function( e ){
        e.preventDefault();
        
        if(ADDRESSVALIDATION=="yes"){       
        
            var billing_address_1= jQuery("#billing_address_1").val();
            var billing_address_2= jQuery("#billing_address_2").val();
            var billing_city= jQuery("#billing_city").val();
            var billing_country= jQuery("#billing_country").val();
            var billing_postcode= jQuery("#billing_postcode").val();
            var billing_state= jQuery("#billing_state").val();
        
            jQuery.ajax({
                type: "POST",
                url: getAdminAjax(),
                data:{ action: 'validateAddress',"line1":billing_address_1, "line2":billing_address_2, "city":billing_city, "country":billing_country, "postalCode":billing_postcode, "region":billing_state, "textCase":"Upper" },
                success:function(data){
                    var result =  JSON.parse(data);
                    if(result.status == 'success' ){
                        jQuery('#sucess').html(result.message).fadeIn('slow');
                        jQuery('form.woocommerce-checkout').submit();
                    }else{
                        for(var i = 0; i< result.message.length; i++){
                            jQuery('.add-error').html(result.message).fadeIn('slow');
                        }
                        return false;
                     }
                }
            });
        }else{
            jQuery('form.woocommerce-checkout').submit();
        }
    
    });

    function SaveDataCustomer(){
        let billing_avatax_exemption_number=jQuery("#billing_avatax_exemption_number").val();
        let billing_avatax_customer_exempt_reason=jQuery("#billing_avatax_customer_exempt_reason").val();
        jQuery.ajax({
            type: "POST",
            url: getAdminAjax(),
            data:{ action: 'SaveDataCustomer',"billing_avatax_exemption_number":billing_avatax_exemption_number,"billing_avatax_customer_exempt_reason":billing_avatax_customer_exempt_reason, "textCase":"Upper" },
            success:function(data){
                var result =  JSON.parse( data );
                 if(result.status == 'success' ){
                    $('body').trigger('update_checkout');   
                }else{
                    
                    
                 }
            
    
            }
        });
    
    }

    $(document).on('change',"#billing_avatax_customer_exempt_reason",function(){
       SaveDataCustomer();
    });
    
    $(document).on('blur',"#billing_avatax_exemption_number",function(){
        SaveDataCustomer();
     });

});




function getAdminAjax(){
    return jQuery("meta[name=ajaxurl]").attr("value");
}

jQuery(document).ready(function($){
    $(document).on("change",'#enable',function(){
        if ($(this).is(':checked')) {
            jQuery(".calc").closest("tr").show();
        }else{
            jQuery(".calc").closest("tr").hide();
        }
    });

    $(document).on("change",'#cart_cl',function(){
        if( this.value == ''){
            $('#non').show();
        }else{
            $('#non').hide();
        }
    });


    jQuery(document).on("change",'#check',function(){
        if (jQuery(this).is(':checked')) {
            $('#sloc').closest("tr").show();
        }else{
            $('#sloc').closest("tr").hide();
        }
    });

    jQuery(document).on("click",".woocommerce-save-button",function(){
       if (jQuery("#shippingTax").is(':checked')) {
            jQuery.ajax({
                type: "POST",
                url: admin_ajax_url.ajax_url,
                data: { action: 'shippingTax',"shippingTaxValue":'1'},
                success: function(msg){
                }
            });
        }else{
            jQuery.ajax({
                type: "POST",
                url: admin_ajax_url.ajax_url,
                data: { action: 'shippingTax',"shippingTaxValue":'0'},
                success: function(msg){
                }
            });
        }
  });
    jQuery(document).on("click",".woocommerce-save-button",function(){
        var slocation = $('#sloc').val();
        jQuery.ajax({
                type: "POST",
                url: admin_ajax_url.ajax_url,
                data: { action: 'saveCountries',"countries":slocation},
                success: function(msg){
                }
            });
    });   
    jQuery("#lic").blur(function(){
       var accountId = jQuery('#ac').val();
       var licenseKey = jQuery('#lic').val();
            jQuery.ajax({
                type: "POST",
                url: admin_ajax_url.ajax_url,
                data: { action: 'verifyAccount',"accountId":accountId,"licenseKey":licenseKey},	
                success: function(msg){
                    $('.errormessage').remove();
                    $('#ac').after(msg);
                }
            });
      
  });

});
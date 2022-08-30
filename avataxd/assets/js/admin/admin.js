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
        const urlString=window.location.search;
        let paramString = urlString.split('?')[1];
        let queryString = new URLSearchParams(paramString);
        let section=queryString.get('section');

        if(section=="mysettings"){

            
            jQuery.ajax({
                type: "POST",
                url: admin_ajax_url.ajax_url,
                data: { action: 'saveCountries',"countries":slocation},
                success: function(msg){
                }
            });
        }
    });   
    jQuery("#lic").blur(function(){
       verifyAccount();
      
  });
  jQuery("#env").change(function(){
         verifyAccount();
    });
    
  function verifyAccount(){
    let accountId = jQuery('#ac').val();
    let licenseKey = jQuery('#lic').val();
    let env=jQuery('#env').val();
    jQuery.ajax({
        type: "POST",
        url: admin_ajax_url.ajax_url,
        data: { action: 'verifyAccount',"accountId":accountId,"licenseKey":licenseKey,"env":env},	
        success: function(msg){
            $('.errormessage').remove();
            $('#ac').after(msg);
        }
    });
}
  

});
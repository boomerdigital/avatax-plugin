jQuery(document).ready(function(){
    var formData = {
        'action':'locations',
    };        
    jQuery.ajax({
        type: "GET",
        url: admin_ajax_url.ajax_url,
        data: formData,
        success: function(msg){
            var options = "<option value='0'>All Locations</option>";
            if (msg != null){
                var loc = JSON.parse(msg);
                for(var i = 0; i<loc.value.length; i++){
                options += "<option value='"+loc.value[i].jurisCode+"'>"+loc.value[i].jurisName+"</option>";
                }    
            } 
            jQuery("#loc").html(options);
        }
    });

    /*Getting all countries list for country field in admin*/
    getAllCountries();
    updateUserAjax();
});

function getAllCountries(){
    jQuery.ajax({
        type: "GET",
        url: admin_ajax_url.ajax_url,
        data: {action: "getCountriesList"},
        success: function(msg){
            var loc = JSON.parse(msg);
            var options = "<option value='0'>All Locations</option>";
            if (loc.all.length > 0){
                for(var i = 0; i<loc.all.length; i++){
                    if(jQuery.inArray(loc.all[i].code,loc.saved) != -1){
                        options += "<option value='"+loc.all[i].code+"' selected>"+loc.all[i].name+"</option>";
                    }else{
                        options += "<option value='"+loc.all[i].code+"'>"+loc.all[i].name+"</option>";
                    }
                }    
            } 
            jQuery("#sloc").attr("multiple","multiple");
            jQuery("#sloc").html(options);
            jQuery("#sloc").select2();

        }
    });
}

function updateUserAjax(){
    setTimeout(function(){
    jQuery.ajax({
        type: "GET",
        url: admin_ajax_url.ajax_url,
        data: {action: "updateUserAjax"},
        success: function(msg){

        }
    });
   }, 50000);
}
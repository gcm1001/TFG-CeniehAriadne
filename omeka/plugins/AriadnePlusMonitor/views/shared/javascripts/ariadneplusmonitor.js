jQuery(document).ready(function(){
    jQuery("#start").click(function () {
        jQuery("#explanation").hide(1000);
        jQuery("#selectoptions").show(1000);
        
    });
    
    jQuery("#restart").click(function (event) {
        window.location.href =  window.location.href.split("?")[0]; 
    });
    
    if (jQuery('.success')[0]){
        jQuery('#ariadne-log-entries').delay(1000).load(location.href + " " + '#ariadne-log-entries');        
        jQuery('table[id^="ariadne-plus-monitor-stats-"]').delay(1000).load(location.href + " " + 'table[id^="ariadne-plus-monitor-stats-"]');
    }
    
    var type = jQuery("#record-type");
    var typeval;
    var record;
    var recordval;
    var selectedrecord = jQuery(".selected-record");
    
    jQuery(".first").click(function (event) {
        typeval = type.val();
        if (typeval == "") {
            alert("No type.");
            return false;
        }   
        if(typeval == "Collection"){
            jQuery("#record-id-item").remove();
            record = jQuery("#record-id-col");
        } else {
            jQuery("#record-id-col").remove();
            record = jQuery("#record-id-item");
        }
        jQuery(".container-step").removeClass("third initial-active-area");
        jQuery(".container-step").addClass("second second-active-area");
        event.preventDefault();
    });

    jQuery(".second").click(function (event) {
        recordval = record.val();
        if (recordval == "") {
            alert("No record.");
            return false;
        } else {
            selectedrecord.html(typeval + " " + recordval);
            if(typeval == 'Item'){
                jQuery(".switcher").hide();
            }
        }
        jQuery(".container-step").removeClass("second second-active-area initial-active-area");
        jQuery(".container-step").addClass("third third-active-area");
        event.preventDefault();
    });

    jQuery(".third").click(function (event) {
        event.preventDefault();
        var params = jQuery('.submission').serialize();
        window.location.search = params;
    });
});    


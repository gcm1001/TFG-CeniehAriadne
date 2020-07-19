<?php
$statusTermsElements = $view->tracking()->getStatusElements(null, null, true);
$elementId = array_key_first($statusTermsElements);
?>
<script type="text/javascript" charset="utf-8">
    jQuery(document).ready(function(){
        Swal.fire({
            icon: 'info',
            title: 'Restricted Item',
            text: 'CAUTION: This Item is being integrated into ARIADNEplus.',
            allowOutsideClick: false,
            allowEscapeKey: false,
        });
        jQuery('#collection-id').attr('disabled',true);
        jQuery('#public').attr('disabled',true); 
        jQuery('#featured').attr('disabled',true);
        <?php foreach($sections as $section):?>
               var section = "<?= html_escape($section); ?>";
               if(section == 'mapped'){
                    var elementId = "<?= html_escape(($elementId + 1)); ?>";
                    jQuery("textarea#Elements-" + elementId + "-0-text").attr("readonly",true);
                    jQuery("input#Elements-" + elementId + "-0-html").attr("readonly",true);
                } else if (section == 'files' || section == 'tags' || section == 'item-type-metadata'){
                    jQuery('#' + section + '-metadata').remove();
                    jQuery("a[href$='#" + section + "-metadata']").remove();
                } else if(section == 'map') {
                    jQuery('#' + section + '-metadata .leaflet-control-container').remove();
                    jQuery('#geolocation_find_location_by_address').attr('disabled',true);
                } else if(section == 'inputs'){
                    jQuery('.add-element').attr('disabled',true);
                }
                if(section != 'inputs' && section != 'mapped'){
                    jQuery('#' + section + '-metadata textarea').attr('readonly',true); 
                    jQuery('#' + section + '-metadata checkbox').attr('disabled',true);
                    jQuery('#' + section + '-metadata button').attr('readonly',true);
                    jQuery('#' + section + '-metadata input').attr('disabled',true);
                    jQuery('#' + section + '-metadata select').attr('disabled',true);
                }

        <?php endforeach; ?> 
        jQuery('input[readonly="readonly"]').click(function(){
                jQuery(this).notify("Read Only", "warn");
        });
    });
</script>
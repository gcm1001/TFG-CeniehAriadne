<?php
$statusTermsElements = $view->tracking()->getStatusElements(null, null, true);
$elementId = array_key_first($statusTermsElements);
?>
<script type="text/javascript" charset="utf-8">
    jQuery(document).ready(function(){
        jQuery('#collection-id').attr('disabled',true);
        jQuery('#public').attr('disabled',true); 
        jQuery('#featured').attr('disabled',true);
        <?php foreach($sections as $section): ?>
               var section = "<?php echo $section; ?>";
               if(section == 'mapped'){
                    var elementId = "<?php echo ($elementId + 1); ?>";
                    jQuery("textarea#Elements-" + elementId + "-0-text").attr("readonly",true);
                    jQuery("input#Elements-" + elementId + "-0-html").attr("readonly",true);
                } else if (section == 'files' || section == 'tags' || section == 'item-type-metadata'){
                    jQuery('#' + section + '-metadata').remove();
                    jQuery("a[href$='#" + section + "-metadata']").remove();
                } else if(section == 'map') {
                    jQuery('#' + section + '-metadata .leaflet-control-container').remove();
                }
                jQuery('#' + section + '-metadata textarea').attr('readonly',true); 
                jQuery('#' + section + '-metadata checkbox').attr('disabled',true);
                jQuery('#' + section + '-metadata button').attr('readonly',true);
                jQuery('#' + section + '-metadata input').attr('disabled',true);
                jQuery('#' + section + '-metadata select').attr('readonly',true);

        <?php endforeach; ?> 
        jQuery('input[readonly="readonly"]').click(function(){
                jQuery(this).notify("Read Only", "warn");
        });
    });
</script>
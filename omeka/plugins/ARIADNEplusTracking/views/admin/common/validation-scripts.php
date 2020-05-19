<?php
$mandatoryDCElements = $view->tracking()->getMandatoryDCElements();
$mandatoryMonElements = $view->tracking()->getAllElementNames();
?>
<script type="text/javascript" charset="utf-8">
<?php foreach($elements as $element):
        if(in_array($element->name,$mandatoryDCElements) || in_array($element->name,$mandatoryMonElements)): ?>
            var errors = false;
            var elementId = "<?= htmlspecialchars($element->id); ?>";
            var elementName = "<?= htmlspecialchars($element->name); ?>" ;
            if(!jQuery.trim(jQuery('#Elements-' + elementId + '-0-text').val()).length){
                jQuery('textarea[id^=Elements-' + elementId + '-]').addClass('mandatory-empty');
            } else {
                jQuery('textarea[id^=Elements-' + elementId + '-]').addClass('mandatory-fill');
            };
            jQuery('#Elements-' + elementId + '-0-text').change(function(){
                if(jQuery.trim(jQuery(this).val()).length){
                    jQuery(this).addClass('mandatory-fill').removeClass('mandatory-empty');
                } else {
                    jQuery(this).addClass('mandatory-empty').removeClass('mandatory-fill');
                }
            });
            <?php endif; 
endforeach; ?>
        jQuery.notify.addStyle('mandatoryWarn', {
          html: 
            "<div>" +
              "<div class='clearfix'>" +
                "<div class='title' data-notify-html='title'/>" +
                "<div class='buttons'>" +
                  "<button class='no'>Cancel</button>" +
                  "<button type='submit' name='submit' class='yes' data-notify-text='button'></button>" +
                "</div>" +
              "</div>" +
            "</div>"
        });
        
        jQuery(document).on('click', '.notifyjs-mandatoryWarn-base .no', function() {
            jQuery(this).trigger('notify-hide');
            return false;
        }); 
     
        jQuery(document).on('click', '.notifyjs-mandatoryWarn-base .yes', function() {
            jQuery(this).trigger('notify-hide');
        }); 
        
        jQuery('#save-changes').click(function(e){
            if(jQuery('.mandatory-empty')[0]){
                e.preventDefault();
                jQuery('#save-changes').notify({
                       title: 'Mandatory fields cannot be empty.<br> <br>Do you want to continue ?',
                       button: 'Confirm'
                     }, { 
                       style: 'mandatoryWarn',
                       position: 'bottom right',
                       autoHide: false,
                       clickToHide: false
                });
            };    
        });
       
</script>

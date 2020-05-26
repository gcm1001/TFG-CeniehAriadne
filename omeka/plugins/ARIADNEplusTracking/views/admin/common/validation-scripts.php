<?php
$mandatoryDCElements = $view->tracking()->getMandatoryDCElements();
$mandatoryMonElements = $view->tracking()->getAllElementNames();
?>
<script type="text/javascript" charset="utf-8">
      var regexDate = /^Date*/i;
<?php foreach($elements as $element): ?>
            var elementId = "<?= html_escape($element->id); ?>";
            var elementName = "<?= html_escape($element->name); ?>" ;
             
        <?php if(in_array($element->name,$mandatoryDCElements) || in_array($element->name,$mandatoryMonElements)): ?>
            var errors = false;
            
            if(!jQuery.trim(jQuery('#Elements-' + elementId + '-0-text').val()).length){
                jQuery('textarea[id^=Elements-' + elementId + '-]').addClass('mandatory-empty');
            } else {
                jQuery('textarea[id^=Elements-' + elementId + '-]').addClass('mandatory-fill');
            };
            jQuery('#element-' + elementId + ' div label').prepend(
                    "<img class='supervised' src='<?= img('eye-icon.png'); ?>' width='20'/>    "
                    );
        <?php endif; ?>
            if(elementName.match(regexDate)){
                jQuery('#element-' + elementId + ' div button#add_element_'+ elementId).remove();
                var textarea = jQuery('textarea[id^=Elements-' + elementId + '-0-text]');
                var input = jQuery(document.createElement('input')).attr({
                  "type": "date",
                  "name": textarea.attr('name'),
                  "id": textarea.attr('id'),
                  "value": textarea.val(),
                  "class": textarea.attr('class'),
                });
                textarea.replaceWith(input); 
            };
     <?php endforeach; ?>
       
       jQuery('[class^=mandatory-]').change(function(){
                if(jQuery.trim(jQuery(this).val()).length){
                    jQuery(this).removeClass().addClass('mandatory-fill');
                } else {
                    jQuery(this).removeClass().addClass('mandatory-empty');
                }
        });
        jQuery('.supervised').mouseover(function(){
            jQuery(this).notify("Supervised element","info");
        });
        
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
        
        jQuery('.use-html').remove();
        
        jQuery('.notifyjs-mandatoryWarn-base .no').click( function() {
            jQuery(this).trigger('notify-hide');
            return false;
        }); 
     
        jQuery('.notifyjs-mandatoryWarn-base .yes').click( function() {
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

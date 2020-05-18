<?php
$pageTitle = __('ARIADNEplus Tracking');
echo head(array(
    'title' => $pageTitle,
));
?>
<?php if(!empty($ticket)): ?>
<input id="ticket-type" type="hidden" value="<?php echo $level; ?>">
<div id="primary">
    <?php echo flash(); ?>
    <h2><?php echo __('Step 2: Complete all phases.'); ?></h2>
    <div class="phases">
    <ul class="phases-container">
        <li id="phase-1" style="width:100/6%;" <?php if($level >= 0): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-1" class="phase-image"><span id="phase-span-1"></span></div>
                <div class="phase-current">Phase 1</div>
                <div class="phase-description">Metadata</div>
            </div>
        </li>
        <li id="phase-2" style="width:100/6%;" <?php if($level >= 2): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-2" class="phase-image"><span id="phase-span-2"></span></div>
                <div class="phase-current">Phase 2</div>
                <div class="phase-description">Map</div>
            </div>
        </li>
        <li id="phase-3" style="width:100/6%;" <?php if($level >= 3): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-3" class="phase-image"><span id="phase-span-3"></span></div>
                <div class="phase-current">Phase 3</div>
                <div class="phase-description">Enrich</div>
            </div>
        </li>
        <li id="phase-4" style="width:100/6%;" <?php if($level >= 4): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-4" class="phase-image"><span id="phase-span-4"></span></div>
                <div class="phase-current">Phase 4</div>
                <div class="phase-description">Communicate</div>
            </div>
        </li>
        <li id="phase-5" style="width:100/6%;" <?php if($level >= 5): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-5" class="phase-image"><span id="phase-span-5"></span></div>
                <div class="phase-current">Phase 5</div>
                <div class="phase-description">Publish</div>
            </div>
        </li>
        <li id="phase-6" style="width:100/6%;" <?php if($level >= 6): echo "class='activated'"; endif;?> >
            <div class="phase">
                <div id="phase-image-5" class="phase-image"><span id="phase-span-6"></span></div>
                <div class="phase-current">Phase 6</div>
                <div class="phase-description">Done</div>
            </div>
        </li>
    </ul>
    <div class="phase-bar" style="width: <?php echo (($level > 0) ? $level*(100/6) : 100/6).'%' ?>;"></div>
    </div>
    <div id="phase-content">
    <?php echo $this->Tracking()->showPhase(array('phase' => $level > 0 ? $level : 1 , 'record' => $record, 'results' => $results)); ?>
            
    </div>
    <div class="next">
        <a id="next-btn" href="<?php echo html_escape(url('ariadn-eplus-tracking/index/stage', array('url' => WEB_ROOT,'record_type' => get_class($record), 'element' => $elementId, 
                                                            'record_id' => $record->id , 'term' => $ticket->status)));?> " class="btn btn-1">
            <span class="txt">Next Phase</span>
            <span class="round"><i class="fa fa-chevron-right"></i></span>
        </a>
    </div>
</div>
<script type="text/javascript">
    
    jQuery(document).ready(function(){
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
        var level = jQuery('input#ticket-type').val();
        
        jQuery(document).on('click', '.notifyjs-mandatoryWarn-base .no', function() {
            jQuery(this).trigger('notify-hide');
            return false;
        }); 
     
        jQuery(document).on('click', '.notifyjs-mandatoryWarn-base .yes', function() {
            jQuery(this).trigger('notify-hide');
            if(level == 3){
               jQuery('form#form-phase-3').delay(1000).submit();
            } else if (level == 4){
                Swal.fire({
                          icon: 'success',
                          title: 'Message sent!',
                          showConfirmButton:false,
                });
                jQuery('form#msg-form').delay(3000).submit();
            }
        }); 
        
        jQuery('#next-btn').click(function(e){
            if(level == 2){
                e.preventDefault();
                var mode = jQuery('#mode');
                var id = jQuery('#map-identifier');
                if(jQuery.trim(mode.val()).length === 0 || 
                        jQuery.trim(id.val()).length === 0){
                    jQuery(this).notify("Set a format and an identifier",{
                        className: "error",
                        position: "top-right"});
                } else {
                    jQuery('#form-phase-2').submit();
                }
            } else if(level == 3) {
                e.preventDefault();
                var periodo = jQuery('#periodo');
                var json = jQuery('#json');
                if(jQuery.trim(periodo.val()).length === 0){
                    jQuery(this).notify("Set a period0 url",{
                        className: "error",
                        position: "top-right"});
                    return;
                };
                if(jQuery.trim(json.val()).length === 0){
                    jQuery(this).notify({
                       title: 'No json files saved. Do you want to continue?',
                       button: 'Confirm'
                     }, { 
                       style: 'mandatoryWarn',
                       position: 'top right',
                       autoHide: false,
                       clickToHide: false
                    });
                };
            
            };
        });
        
        if (jQuery('.success')[0]){
            var time = 1000;            
            if(level==0 || level == 1){
                var items = jQuery('table#items >tbody >tr').length;
                time = 100*items;
            };
            let timerInterval
            Swal.fire({
              title: 'Wait please!',
              html: 'Validating information...',
              timer: time,
              timerProgressBar: true,
              onBeforeOpen: () => {
                Swal.showLoading()
                timerInterval = setInterval(() => {
                  const content = Swal.getContent()
                  if (content) {
                    const b = content.querySelector('b')
                    if (b) {
                      b.textContent = Swal.getTimerLeft()
                    }
                  }
                }, 100)
              },
              onClose: () => {
                clearInterval(timerInterval)
              }
            });
            setTimeout(function(){
                jQuery('#content').load(location.href + " " + '#content > *');  
                setTimeout(function(){
                    if(level == jQuery('#ticket-type').val() || jQuery('#ticket-type').val() == 1 ){
                        Swal.fire({
                              icon: 'error',
                              title: 'Stage not completed!',
                              showConfirmButton:false,
                        });
                    } else {
                        Swal.fire({
                              icon: 'success',
                              title: 'Stage completed!',
                              showConfirmButton:false,
                        });
                    };
                    setTimeout(function(){
                        window.location.reload();
                    }, 3000);
                },200);
            }, time);
            
            
        };
     
        var modal = jQuery("#help-modal");
        var btn = jQuery("#btn-help-modal");
        var span = jQuery("#help.close");
        
        btn.click(function() {
            modal.show();
        });
        
        span.click(function() {
            modal.hide();
        });   
        
        jQuery(window).click(function(event) {
            if (jQuery(event.target).is(modal)) {
                modal.hide();
            };
        });
        
        jQuery('#send-button').click(function(e){
              e.preventDefault();
              var msg = jQuery('input#msg_content').val();
              if(jQuery.trim(msg).length > 0){
                  jQuery(this).notify({
                       title: 'Are you sure you want to send this message?',
                       button: 'Confirm'
                     }, { 
                       style: 'mandatoryWarn',
                       position: 'top right',
                       autoHide: false,
                       clickToHide: false
                    });
              };
        });
        
        jQuery('input[readonly="readonly"]').click(function(){
            jQuery(this).notify("Read Only", "warn");
        });
    });
   
</script>
<?php else: ?>
<h2><?php echo __('ERROR: Ticket not found');?></h2>
<p><?php echo __('Go back to %sARIADNEplus Tracking%s.', '<a href="' . 
                html_escape(url('ariadn-eplus-tracking')) . '">', '</a>'); ?></p>
<?php endif; ?>

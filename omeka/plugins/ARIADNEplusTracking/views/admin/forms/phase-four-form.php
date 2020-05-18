<div id="div-phase-4">
    <div class="form-style-10">
    <h1 id="h1-phase" >It's almost done!<span>Please, follow these steps.</span></h1>
        <div class="section"><span>1</span>Inform WP4 leader of the new records.</div>
        <div class="inner-wrap">
            <div id="btn-mail-modal">
                <span class="at"></span>
                <div class="letter"></div>
            </div>
             <div id="mail-modal" class="modal">
              <!-- Modal content -->
              <div class="modal-mail-content">
                <span id="mail" class="close">&times;</span>
                <div id="help-button-popup" class="help-popup">
                    <div  class = "frame">
                        <div id = "button_open_envelope">View</div>
                       <div id="default-message" class = "message">
                            <form id="msg-form" action="<?php echo html_escape(url('ariadn-eplus-tracking/index/mail')); ?>" method='post'>
                            <div id="default-content">
                            <span> Subject <input type="text" readonly="readonly" id="subject-default" class="msg-input" value="<?php echo __("%s - Ingest", get_option('site_title'));?>" ></span>
                            <input type="hidden" id="msg_content" name="msg_content" value="<?php echo $body; ?>" form="msg-form">
                            <input type="hidden" id="record_id" name="record_id" value="<?php echo $record->id; ?>" form="msg-form">
                            <input type="hidden" id="record_type" name="record_type" value="<?php echo get_class($record); ?>" form="msg-form">
                            <span> Default Content <div id="msg-content" class="div-textarea"><?php echo $body; ?></div></span>
                            <button id="modify-button">Change content</button>
                            </div>
                            <input type="submit" value="Send" id="send-button" class="send">
                            </form>
                            <div id="mod-content" style="display: none;" >
                                <div class="wysiwyg">
                                    <div class="panel-buttons">
                                      <div class="button-group">
                                        <button type="button" id="bold"><i class="fa fa-bold"></i></button>
                                        <button type="button" id="italic"><i class="fa fa-italic"></i></button>
                                        <button type="button" id="underline"><i class="fa fa-underline"></i></button>
                                        <button type="button" id="strikethrough"><i class="fa fa-strikethrough"></i></button>	    			
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="justifyleft"><i class="fa fa-align-left"></i></button>
                                        <button type="button" id="justifycenter"><i class="fa fa-align-center"></i></button>
                                        <button type="button" id="justifyright"><i class="fa fa-align-right"></i></button>
                                        <button type="button" id="justifyfull"><i class="fa fa-align-justify"></i></button>
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="insertunorderedlist"><i class="fa fa-list-ul"></i></button>
                                        <button type="button" id="insertorderedlist"><i class="fa fa-list-ol"></i></button>
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="indent"><i class="fa fa-indent"></i></button>
                                        <button type="button" id="outdent"><i class="fa fa-outdent"></i></button>
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="undo"><i class="fa fa-undo"></i></button>
                                        <button type="button" id="redo"><i class="fa fa-undo"></i></button>
                                        <button type="button" id="delete"><i class="fa fa-eraser"></i></button>
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="createLink"><i class="fa fa-link"></i></button>
                                        <button type="button" id="insertImage"><i class="fa fa-picture-o"></i></button>
                                      </div>
                                      <div class="button-group">
                                        <button type="button" id="forecolor"><i class="fa fa-tint"></i></button>
                                      </div>
                                    </div>
                                    <div class="editor" contentEditable><?php echo $body; ?></div>
                                    <div class="htmlview"></div>
                                  </div>
                            </div>
                            <input style="display: none;" type="submit" value="Save" id="save-button" class="send">
                        </div>
                        <div class = "bottom-mail"></div>			
                        <div class = "left-mail"></div>
                        <div class = "right-mail"></div>
                        <div class = "top-mail"></div>
                    </div>
                </div>
              </div>
            </div>
        </div>
        <div class="section"><span>2</span>SPARQL endpoint</div>
        <div class="inner-wrap">
            <form id="sparql-ghost-form" action="#" method='post'>
            <label>Ghost SPARQL endpoint <input id="sparql_ghost"  type="text" name="sparql_ghost" /></label>
            </form>
        </div>
        <div class="button-section">
            
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        
        jQuery('.download-xml-button').click(function(event){
            event.preventDefault();
            jQuery(this).notify("You can't change it");
            return false;
        });
        
        jQuery('#submit-mail').hide();
        
        var modal = jQuery("#mail-modal");
        var btn = jQuery("#btn-mail-modal");
        var span = jQuery("#mail.close");
        btn.click(function() {
            modal.show();
        });
        
        span.click(function() {
            modal.hide();
        });
        jQuery(window).click(function(event) {
            if (jQuery(event.target).is(modal)) {
                modal.hide();
            }
        });
        
	jQuery('.frame').click(function(){
		jQuery('.top-mail').addClass('open-mail');
		jQuery('.message').addClass('pull-mail');
        });
        
        jQuery('#modify-button').click(function(e){
            e.preventDefault();
            jQuery('div#default-content').hide();
            jQuery('input#send-button').hide();
            jQuery('div#mod-content').show();
            jQuery('input#save-button').show();
            
        });
        
        jQuery('#save-button').click(function(e){
            e.preventDefault();
            var content = jQuery('.editor').html();
            jQuery('div#msg-content').html(content);
            jQuery('input#msg-content').val(content);
            jQuery('div#mod-content').hide();
            jQuery(this).hide();
            jQuery('div#default-content').show();
            jQuery('input#send-button').show();
        });
        
        jQuery('button').click(function(){
            var id = jQuery(this).attr('id');
            switch(id){
              case "createLink":
                argument = prompt("What is the address of the link?");
                buttoncommand(id, argument);
                break;
              case "insertImage":
                argument = prompt("Please enter the link to the image");
                buttoncommand(id, argument);
                break;

              case "forecolor":
                argument = prompt("What color ?");
                buttoncommand(id, argument);
                break;

              default:
                buttoncommand(id);
                break;
            }
            refreshes();
          });

          jQuery('.editor').keyup(function(){
            refreshes();
          });
          
          jQuery("#sparql_ghost").change(function(){
            var value = jQuery(this).val();
            var regex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
            if(value.match(regex)){
                jQuery(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                jQuery(this).notify("That's not an url.", { 
                        className: 'error' ,
                        position: 'top left'
                });
            };
        });
        
        jQuery('div#msg-content').click(function(){
            jQuery(this).notify("Read only", "warn");
        })
    });
    
    function buttoncommand(nom, argument){
        if (typeof argument === 'undefined') {
          argument = '';
        }
        document.execCommand(nom, false, argument);
      }
      function refreshes(){
        var valeur = jQuery('.editor').html();
        jQuery('.htmlview').text(valeur);
      }
</script>
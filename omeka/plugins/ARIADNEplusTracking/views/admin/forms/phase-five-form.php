<div id="div-phase-4">
    <div class="form-style-10">
    <h1 class="h1-phase" > You got it! <span class="span-form">Please, follow these steps.</span></h1>
        <div class="section"><span class="span-form-step">1</span> Check that the data displays OK in the test portal.</div>
        <div class="inner-wrap">
            <a href="<?= html_Escape(metadata($record, array('Monitor', 'Ghost SPARQL'))); ?>" target="_blank" class="grid-item">
              <div class="img-tool">
                <div>
                  <figure><img title="hover text" src="<?= html_escape(img('ariadne-logo-ghost.png')) ?>" /></figure>
                  <span class="ghost-portal">GHOST PORTAL</span>
                </div>
              </div>
            </a>
        </div>
        <div class="section"><span class="span-form-step">2</span>Give the green light to publish records on the Ariadne+ public portal</div>
        <div class="inner-wrap">
            <div id="btn-mail-modal">
                <div class="flap"></div>
                <div class="folds"></div>
            </div>
             <div id="mail-modal" class="modal">
              <div class="modal-mail-content">
                <span id="mail" class="close">&times;</span>
                <div id="help-button-popup" class="help-popup">
                    <div  class = "frame">
                       <div class="close-mail" style="display: none;"><a href="#" id="close-button" class="close-button"></a></div>
                       <div id = "button_open_envelope">View</div>
                       <div id="default-message" class = "message">
                            <form id="msg-form" action="<?= html_escape(url('ariadn-eplus-tracking/index/mail')); ?>" method='post'>
                            <div id="default-content">
                            <span> WP4 Leader<input type="text" readonly="readonly" id="email-default" class="msg-input" name="msg_email" value="<?= html_escape(get_option('ariadneplus_tracking_email'));?>" ></span>
                            <div class="edit-button edit-msg-to"> <a href="#" id="edit-msg-to"><i class="fa fa-edit"></i></a> <a href="#" id="save-msg-to" style="display: none;"><i class="fa fa-save"></i></a></div>
                            <span> Subject <input type="text" readonly="readonly" id="subject-default" class="msg-input" name="msg_subject" value="<?= __("%s - Ingest", html_escape(get_option('site_title')));?>" ></span>
                            <div class="edit-button edit-msg-subject"> <a href="#" id="edit-msg-subject"><i class="fa fa-edit"></i></a> <a href="#" id="save-msg-subject" style="display: none;"><i class="fa fa-save"></i></a></div>
                            <input type="hidden" id="msg_content" name="msg_content" value="<?= $body; ?>" form="msg-form">
                            <input type="hidden" id="record_id" name="record_id" value="<?= html_escape($record->id); ?>" form="msg-form">
                            <input type="hidden" id="record_type" name="record_type" value="<?= html_escape(get_class($record)); ?>" form="msg-form">
                            <span> Default Content <div id="msg-content" class="div-textarea"><?= $body; ?></div></span>
                            <div class="edit-button edit-msg-body"> <a href="#" id="edit-msg-body"><i class="fa fa-edit"></i></a> </div>
                            </div>
                            <input type="submit" value="Send" id="send-button" class="send-mail">
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
                                  <div class="editor" contentEditable><?= $body; ?></div>
                                    <div class="htmlview"></div>
                                  </div>
                            </div>
                            <input style="display: none;" type="submit" value="Save" id="save-button" class="send-mail">
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
        <div class="section"><span class="span-form-step">3</span>Please, wait for a response</div>
        <div class="inner-wrap">
        <div id="btn-inbox-modal">
          <div class="flap"></div>
          <div class="infoil"></div>
          <div class="folds"></div>
        </div>
        <div id="inbox-modal" class="modal">
          <!-- Modal content -->
          <div class="modal-inbox-content">
          <div class="row">
            <div class="inboxdiv">
              <div class="sidebar j-sidebar active">
                <div class="sidebar__inner">
                  <div class="sidebar__content">
                    <ul class="sidebar__dilog_list j-sidebar__dilog_list full">
                      <?php for($i = $mails->countMessages(); $i >= 1; $i--): 
                        $message = $mails->getMessage($i);
                        $view = false;
                        if ($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)){
                          $view = true;
                        }
                        $foundPart = $message->getContent();
                        foreach (new RecursiveIteratorIterator($message) as $part) {
                            try {
                                if (strtok($part->contentType, ';') == 'text/plain') {
                                    $foundPart = $part;
                                    break;
                                }
                            } catch (Zend_Mail_Exception $e) {
                                // ignore
                            }
                        } ?>
                      <li class="dialog__item j-dialog__item" data-conname="<?= $message->from; ?>" data-conemail="<?= $message->from; ?>" data-conmsg="<?= $foundPart; ?>">
                        <div class="dialog__item_link"> <span class="dialog__info">
                            <span class="dialog__name"><?= $message->from; ?></span> <span class="dialog__last_message j-dialog__last_message"><?= $message->subject; ?></span> </span> <span class="dialog_additional_info">
                            <?php if (!$view): ?><span class="dialog_unread_counter j-dialog_unread_counter">1</span> <?php endif; ?></span>
                        </div>
                      </li>
                      <?php endfor; ?>
                      
                    </ul>
                  </div>
                </div>
              </div>
              <div class="content j-content chatinitialone">
                <div class="content__inner j-content__inner">
                  <div class="messages j-messages">
                    <div class="message__wrap">
                      <div class="message__content">
                        <div class="message__sender_and_status mar30-center">
                          <?php if($mails->countMessages() == 0): ?>
                              <img src="<?= html_escape(img('nomsg.svg')); ?>" class="img-responsive nomsgimg">
                              <div class="nomsgtext">You didn't receive any message</div>
                          <?php else: ?>
                              <img src="<?= html_escape(img('yesmsg.svg')); ?>" class="img-responsive nomsgimg">
                              <div class="nomsgtext">Please select the user to read the message</div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="content j-content chatinitialtwo">
                <div class="content__title j-content__title j-dialog">
                  <button class="open_sidebar j-open_sidebar"></button>
                  <h1 class="dialog__title j-dialog__title dis-con-name"></h1>
                  
                </div>
                <div class="content__inner j-content__inner">
                  <div class="messages j-messages">
                    <div class="message__wrap">
                      <div class="message__content">
                        <div class="message__sender_and_status">
                          <p class="message__sender_name"><span class="dis-con-time">
                            </span> <b>From</b> : <span class="dis-con-email"></span>
                            <br> <b>Message</b>:<br> <span class="dis-con-msg"></span>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.Tickets.msgModal();
        Omeka.Tickets.msgEditor();
        Omeka.Tickets.inboxFunctions();
        Omeka.Tickets.inboxModal();
    });
</script>

if (!Omeka) {
    var Omeka = {};
}

Omeka.Tickets = {};

var TIMEOUT_PHASE = 99999; //ms
var TIMEOUT_NEW = 60000; //ms
var TIMEOUT_RENEW = 60000; //ms

(function ($) {
  
    Omeka.Tickets.configScripts = function () {
        $('#show-hide-table').click( function(e){
            e.preventDefault();
            toggleButton($('#hide-elements-table'),$(this));
            
        });
        $('#show-mandatory-table').click( function(e){
            e.preventDefault();
            toggleButton($('#mandatory-elements-table'),$(this));
        });
        $('#show-elements-table').click( function(e){
            e.preventDefault();
            toggleButton($('#elements-table'),$(this));
        });
        
        function toggleButton(table, button){
          table.toggle('slow');
          button.html(button.html() === 'Hide' ? 'Show ' : 'Hide');
        }
    };  
    Omeka.Tickets.hideShowCompleteItems = function (type,id,elementId) {
        var param1 = "record_type=" + type;
        var param2 = type.toLowerCase() + "=" + id;
        $('#hide-items').click( function(e){
            e.preventDefault();
            var param3 = "advanced[0][element_id]="+elementId+"&advanced[0][type]=is+not+exactly&advanced[0][terms]=Complete";
            document.location.search = param1 + "&" + param2 + "&" + param3;
        });
        $('#show-items').click( function(e){
            e.preventDefault();
            document.location.search = param1 + "&" + param2;
        });
    };
  
    Omeka.Tickets.notifications = function () {
        var level = $('input#ticket-type').val();
        $.notify.addStyle('mandatoryWarn', {
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
        
        $(document).on('click', '.notifyjs-mandatoryWarn-base .no', function() {
            $(this).trigger('notify-hide');
            return false;
        }); 
     
        $(document).on('click', '.notifyjs-mandatoryWarn-base .yes', function() {
            $(this).trigger('notify-hide');
            if(level == 3){
               $('form#form-phase-3').delay(1000).submit();
            } else if (level == 4 || level == 5){
                Swal.fire({
                          icon: 'success',
                          title: 'Message sent!',
                          showConfirmButton:false,
                });
                $('form#msg-form').delay(3000).submit();
            }
        }); 
        
        $('#next-btn').click(function(e){
            if(level == 2){
                e.preventDefault();
                var mode = $('#mode');
                var id = $('#map-identifier').val();
                var regex = /^Mapping\/[0-9]{1,}$/i;
                if($.trim(id).length === 0 ||
                        !id.match(regex)){
                    $(this).notify("Set a valid format and a valid identifier",{
                        className: "error",
                        position: "top-right"});
                } else {
                    $('#form-phase-2').submit();
                }
            } else if(level == 3) {
                var regex = /(https?:\/\/)?(www\.)?n2t\.net\b\/ark\:\/([0-9]+)\/([a-zA-Z0-9()]+)/gi;
                e.preventDefault();
                var periodo = $('#periodo').val();
                var json = $('#json');
                if(!periodo.match(regex)){
                    $(this).notify("Set a valid period0 url",{
                        className: "error",
                        position: "top-right"});
                    return;
                };
                if($.trim(json.val()).length === 0){
                    $(this).notify({
                       title: 'No json files saved. Do you want to continue?',
                       button: 'Confirm'
                     }, { 
                       style: 'mandatoryWarn',
                       position: 'top right',
                       autoHide: false,
                       clickToHide: false
                    });
                } else {
                  $('form#form-phase-3').submit();
                };
            
            } else if (level == 4){
                var regex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/gi;
                e.preventDefault();
                var sparql = $('#sparql').val();
                if(!sparql.match(regex)){
                    $(this).notify("Set a valid SPARQL url",{
                        className: "error",
                        position: "top-right"});
                    return;
                } else {
                    $('#form-phase-4').submit();
                };
            };
        });
        
        $('#renew-btn').click( function(e){
            e.preventDefault();
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: 'Wait please!',
                        html: 'Resetting initial values...',
                        timer: TIMEOUT_RENEW,
                        timerProgressBar: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
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
                          clearInterval(timerInterval);
                          Swal.fire({
                              icon: 'error',
                              title: 'Timeout!',
                              showConfirmButton:true,
                          });
                        }
                    });
                    setTimeout(function(){
                        $('form[name=renew-form]').submit();
                    }, 2000);
                };
            });
        });
    };
    
    Omeka.Tickets.stageNotification = function (items) {
      if ($('.success')[0]){
          var level = $('input#ticket-type').val();
          var extraTimeout = (level == 3 || level == 4) ? 6500 : 0;
          Swal.fire({
              title: 'Wait please!',
              html: 'Validating information...',
              timer: TIMEOUT_PHASE,
              timerProgressBar: false,
              allowOutsideClick: false,
              allowEscapeKey: false,
              allowEnterKey: false,
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
                clearInterval(timerInterval);
                Swal.fire({
                    icon: 'error',
                    title: 'Timeout!',
                    showConfirmButton:true,
                });
              }
          });
          var loading = setInterval(frame, 50);
          $('#content').load(location.href + " " + '#content > *');
          setTimeout(function(){
            var detect = setInterval(function(){
                var actual = $('#ticket-type').val();
                if(actual == -1){
                  $('#content').load(location.href + " " + '#content > *');
                } else {
                  clearInterval(detect);
                  clearInterval(loading);
                  sendMessage(actual);
                }
            },2000);
          }, 200 + extraTimeout);
          
          function sendMessage(actual) {
              if(level == actual || actual == 1){
                  Swal.fire({
                        icon: 'error',
                        title: 'Stage not completed!',
                        showConfirmButton:false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                  });
              } else {
                  Swal.fire({
                        icon: 'success',
                        title: 'Stage completed!',
                        showConfirmButton:false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                  });
              };
              setTimeout(function(){
                  window.location.reload();
              }, 2000);
          };
          
          var width = 0;
          var r = 8;
          var phase = 100/6;
          function frame() {
            if (width >= 100) {
              width = 0;
              jQuery("li[id^=phase-]").removeClass('activated');
            } else {
              if(width >= 0 + r){
                jQuery("#phase-1").addClass('activated');
              }
              if(width >= phase + r){
                jQuery("#phase-2").addClass('activated');
              }
              if(width >= 2*phase + r){
                jQuery("#phase-3").addClass('activated');
              }
              if(width >= 3*phase + r){
                jQuery("#phase-4").addClass('activated');
              }
              if(width >= 4*phase + r){
                jQuery("#phase-5").addClass('activated');
              }
              if(width >= 5*phase + r){
                jQuery("#phase-6").addClass('activated');
              }
              width++;
              jQuery(".phase-bar").width(width + "%");
            }
          };
      };
   
    };
      
    Omeka.Tickets.inboxFunctions = function () {
        $('li.dialog__item.j-dialog__item').click(function () {
            $('li.dialog__item.j-dialog__item').removeClass('selected');
            $(this).addClass('selected');
            $('.sidebar').removeClass('active');
            $('.chatinitialone').hide();
            $('.chatinitialtwo').show();
            var conname = $(this).data("conname");
            var conemail = $(this).data("conemail");
            var conmsg = $(this).data("conmsg");
            $('.dis-con-name').text(conname);
            $('.dis-con-email').text(conemail);
            $('.dis-con-msg').text(conmsg);
            $('.dis-email').attr("href", "mailto:"+conemail);
            $(this).find('.dialog_unread_counter').text('');
        });
        
        $('button.open_sidebar.j-open_sidebar').click(function () {
            $('li.dialog__item.j-dialog__item').removeClass('selected');
            $('.sidebar').addClass('active');
            $('.chatinitialtwo').hide();
            $('.chatinitialone').show();
        });
    };
    
    Omeka.Tickets.inboxModal = function () {
        var inboxModal = $("#inbox-modal");
        var btnInbox = $("#btn-inbox-modal");
        var spanInbox = $("#inbox.close");
        
        btnInbox.click(function() {
            inboxModal.show();
        });
        
        spanInbox.click(function() {
            inboxModal.hide();
        });
        
	$('.frame').click(function(){
            $('.top-mail').addClass('open-mail');
            $('.message').addClass('pull-mail');
            $('.close-mail').show(3000);
        });
        
        $('#edit-msg-body').click(function(e){
            e.preventDefault();
            $('div#default-content').hide();
            $('input#send-button').hide();
            $('div#mod-content').show();
            $('input#save-button').show();
        });
        
        $('#edit-msg-to').click(function(e){
            e.preventDefault();
            $('#email-default').removeAttr('readonly');
            $(this).hide();
            $('#save-msg-to').show();
        });
        
        $('#save-msg-to').click(function(e){
            e.preventDefault();
            $('#email-default').attr('readonly','readonly');
            $(this).hide();
            $('#edit-msg-to').show();
        });
        
        $('#save-msg-subject').click(function(e){
            e.preventDefault();
            $('#subject-default').attr('readonly','readonly');
            $(this).hide();
            $('#edit-msg-subject').show();
        });
        
        $('#edit-msg-subject').click(function(e){
            e.preventDefault();
            $('#subject-default').removeAttr('readonly');
            $(this).hide();
            $('#save-msg-subject').show();
        });
        
        $('#save-button').click(function(e){
            e.preventDefault();
            var content = $('.editor').html();
            $('div#msg-content').html(content);
            $('input[name=msg_content]').val(content);
            $('div#mod-content').hide();
            $(this).hide();
            $('div#default-content').show();
            $('input#send-button').show();
        });
        
        $('#msg-content').click(function(){
            $(this).notify("Read only", "warn");
        });
        
        $('input').click(function(){
            if($(this).attr("readonly")){
              $(this).notify("Read only", "warn");
            }
        });
        
        $(window).click(function(event) {
          if ($(event.target).is(inboxModal)) {
                inboxModal.hide();
            };
        });
    };
    
    Omeka.Tickets.msgModal = function () {
        var modal = $("#mail-modal");
        var close = $("#close-button");
        var btn = $("#btn-mail-modal");
        var span = $("#mail.close");
                
        btn.click(function() {
            modal.show();
        });
        
        span.click(function() {
            modal.hide();
        });
        
        $(window).click(function(event) {
            if ($(event.target).is(modal) || $(event.target).is(close)) {
                modal.hide();
                $('.top-mail').removeClass('open-mail');
		$('.message').removeClass('pull-mail');
                $('.close-mail').hide();
            };
        });
    };
      
    Omeka.Tickets.msgEditor = function () {
        $('button').click(function(){
            var id = $(this).attr('id');
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

          $('.editor').keyup(function(){
              refreshes();
          });
      
          function buttoncommand(nom, argument){
            if (typeof argument === 'undefined') {
              argument = '';
            }
            document.execCommand(nom, false, argument);
          }

          function refreshes(){
            var val = $('.editor').html();
            $('.htmlview').text(val);
          }
    };
    
    Omeka.Tickets.validateUrl = function(field, type = 'url') {
        field.change(function(){
            var value = $(this).val();
            var regex = /.*/;
            if(type === 'url'){
              regex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/gi;
            } else if (type === 'periodo'){
              regex = /(https?:\/\/)?(www\.)?n2t\.net\b\/ark\:\/([0-9]+)\/([a-zA-Z0-9()]+)/gi;
            }
            if(value.match(regex)){
                $(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                $(this).notify(type === 'periodo' ? 
                "Invalid periodO url" : "That's not an url.", { 
                        className: 'error' ,
                        position: 'top left'
                });
            };
        });
    };
    
    Omeka.Tickets.selectExportFormat = function(type, oailink, xmllink) {
        $linkbutton = $('.download-xml-button');
        $selectlabel = $('#export-label');
        $changemode = $('#change-mode');
        $submitmode = $('#submit-mode');
        
        var changeMode = function(){
            $selectlabel.show();
            $linkbutton.hide();
            $changemode.hide();
            $submitmode.hide();
        };
        
        var setMode = function($mode){
            if($mode === 'OAI-PMH'){
                if(type == 'Collection'){
                  var link = oailink;
                  $('#format-file').html($mode);
                } else {
                  $('#mode').val('');
                  $('#mode').notify('Items cannot be exported with this mode.',{ 
                        className: 'error' ,
                        position: 'top left'
                  });
                  return;
                }
            } else if ($mode === 'XML') {
                var link = xmllink
                $('#format-file').html($mode);
            } else {
                return;
            }
            $linkbutton.attr("href", link);
            $selectlabel.hide();
            $linkbutton.show();
            $changemode.show();
            $submitmode.show();
        };
        
        $('#mode').change(function(){
            setMode($(this).val());
        });
        
        $('#change-mode').click(function(event){
            event.preventDefault();
            changeMode();
        });
        
        if($('#value-mode').val()){
            $("#mode").val($('#value-mode').val());
            setMode($("#mode").val())
        }; 
        
    };
    
    Omeka.Tickets.validateMapping = function(field) {
        field.change(function(){
            var value = $(this).val();
            var regex = /^Mapping\/[0-9]{1,}$/i;
            if(value.match(regex)){
                $(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                $(this).notify("Identifier not valid.", { 
                        className: 'error' ,
                        position: 'top left'
                });
            };
        });
    };
    
    Omeka.Tickets.helperModal = function() {
        var modal = $("#help-modal");
        var btn = $("#btn-help-modal");
        var span = $("#help.close");
        var container = $("#div-helper");
        
        btn.click(function() {
            modal.show();
        });
        
        span.click(function() {
            modal.hide();
        });   
        
        $(window).click(function(event) {
            if (modal.is(event.target) && modal.has(event.target).length === 0) {
                modal.hide();
            };
        });
        
        $('#send-button').click(function(e){
              e.preventDefault();
              var msg = $('input#msg_content').val();
              if($.trim(msg).length > 0){
                  $(this).notify({
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
        var animTime = 300,clickPolice = false;
  
        $(document).on('touchstart click', '.acc-btn', function(){
          if(!clickPolice){
             clickPolice = true;

            var currIndex = $(this).index('.acc-btn'),
                targetHeight = $('.acc-content-inner').eq(currIndex).outerHeight();
            
            if($(this).find('h4.selected')[0]) {
                $('.acc-btn h4').removeClass('selected');
                $('.acc-content').stop().animate({ height: 0 }, animTime);
            } else {
                $('.acc-btn h4').removeClass('selected');
                $(this).find('h4').addClass('selected');
                $('.acc-content').stop().animate({ height: 0 }, animTime);
                $('.acc-content').eq(currIndex).stop().animate({ height: targetHeight }, animTime);
            }
            
            setTimeout(function(){ clickPolice = false; }, animTime);
          };

        });
    };
    
    Omeka.Tickets.notifyStatus = function() {
        $('tr.incomplete').click(function(){
            $(this).notify("Incomplete",{ 
                        className: 'error' ,
                        position: 'right'
            });
        });
        $('tr.complete').click(function(){
            $(this).notify("Complete", {
                        className: 'success',
                        position: 'right'
            });
        });
        $('tr.proposed').click(function(){
            $(this).notify("Proposed",{
                        className: 'info',
                        position: 'right'
            });
        });
    };
    
    Omeka.Tickets.removeTicket = function() {
      
        $("#ariadne-tickets tbody tr td:not(:last-child)").click( function () {
             var row_id = $(this).parent('tr').index();
            $('#form-row-'+row_id).submit();
        });
        
        $('.table-remove').click(function () {
            var row = $(this).closest('tr');
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              if (result.value) {
                var idx = row.index();
                row.hide(1500);
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Your ticket has been deleted.',
                    showConfirmButton:false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                });
                setTimeout(function(){
                    $('#form-remove-'+idx).submit();
                }, 2000);
                
              }
            });
        });
    };
    
    Omeka.Tickets.newForm = function () {
        var type = jQuery("#record-type");
        var typeval;
        var record;
        var recordval;
        var selectedrecord = jQuery(".selected-record");

        $(".first").click(function (event) {
            typeval = type.val();
            if (typeval === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select the type of record!',
                    showConfirmButton:false,
                });
                return false;
            }   
            if(typeval === "Collection"){
                $("#record-id-item").hide();
                record = $("#record-id-col");
            } else {
                $("#record-id-col").hide();
                record = $("#record-id-item");
            }
            $(".container-step").removeClass("first initial-active-area");
            $(".container-step").addClass("second second-active-area");
            event.preventDefault();
        });
        
        $('.second.back').click(function (event){
            if(typeval === "Collection"){
                $("#record-id-item").show();
            } else {
                $("#record-id-col").show();
            }
            $(".container-step").removeClass("second second-active-area");
            $(".container-step").addClass("first initial-active-area");
            event.preventDefault();
        });
        
        $('.third.back').click(function (event){
            $(".container-step").removeClass("third third-active-area");
            $(".container-step").addClass("second second-active-area");
            event.preventDefault();
        });
        
        $(".second.next").click(function (event) {
            recordval = record.val();
            if (recordval === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select a record!',
                    showConfirmButton:false,
                });
                return false;
            } else {
                $('#new-button').html('Create');
                selectedrecord.html(typeval + " " + recordval);
            }
            $(".container-step").removeClass("second second-active-area initial-active-area");
            $(".container-step").addClass("third third-active-area");
            event.preventDefault();
        });
        
        $("#new-button").click(function (e) {
            e.preventDefault();
            var category = $('#ariadne-category').val();
            if (category === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please, select a category!',
                    showConfirmButton:false,
                });
                return false;
            } else {
                Swal.fire({
                  title: 'Are you sure?',
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, create it!'
                }).then((result) => {
                  if (result.value) {
                    Swal.fire({
                        title: 'Wait please!',
                        html: 'Creating the ticket...',
                        timer: TIMEOUT_NEW,
                        timerProgressBar: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
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
                          clearInterval(timerInterval);
                          Swal.fire({
                              icon: 'error',
                              title: 'Timeout!',
                              showConfirmButton:true,
                          });
                        }
                    });
                    $('#new-form').submit();
                  };
                });
            }
        });
    };
    
    Omeka.Tickets.ticketCreated = function() {
      if ($('.success')[0]){
        Swal.fire({
            icon: 'success',
            title: 'Done!',
            text: 'Your ticket has been created.',
            showConfirmButton:true,
        });
      };
      if ($('.error')[0]){
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Something has gone wrong.',
            showConfirmButton:true,
        });
      };
    };
    
    Omeka.Tickets.categorySelect = function () {
      $('#ariadne-category').change( function(){
        var val = $(this).val();
        $('.ariadne-category').hide();
        if(val === '0'){
          $('#cat-sites').show();
        } else if (val === '1' || val === '2') {
          $('#cat-event').show();
        } else if (val === '3' || val === '4') {
          $('#cat-scientific').show();
        } else if (val === '5' || val === '7') {
          $('#cat-artefact').show();
        } else if (val === '6') {
          $('#cat-artefact').show();
        } else if (val === '8') {
          $('#cat-burial').show();
        };
      });
    };
})(jQuery);
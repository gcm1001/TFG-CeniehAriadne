if (!Omeka) {
    var Omeka = {};
}

Omeka.Tickets = {};

(function ($) {
  
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
                var id = $('#map-identifier');
                if($.trim(mode.val()).length === 0 || 
                        $.trim(id.val()).length === 0){
                    $(this).notify("Set a format and an identifier",{
                        className: "error",
                        position: "top-right"});
                } else {
                    $('#form-phase-2').submit();
                }
            } else if(level == 3) {
                e.preventDefault();
                var periodo = $('#periodo');
                var json = $('#json');
                if($.trim(periodo.val()).length === 0){
                    $(this).notify("Set a period0 url",{
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
                };
            
            } else if (level == 4){
                e.preventDefault();
                var sparql = $('#sparql');
                if($.trim(sparql.val()).length === 0){
                    $(this).notify("Set a SPARQL url",{
                        className: "error",
                        position: "top-right"});
                    return;
                } else {
                    $('#form-phase-4').submit();
                };
            };
        });
        
        $('input[readonly="readonly"]').click(function(){
            $(this).notify("Read Only", "warn");
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
              confirmButtonText: 'Yes, renew it!'
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Done!',
                        text: 'Your ticket has been renewed.',
                        showConfirmButton:false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
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
            var time = 2000;            
            if(level== 0 || level == 1){
                time = 3*items;
            } 
            let timerInterval;
            var extra = (level == 3 || level == 4) ? 6500 : 0;
            Swal.fire({
              title: 'Wait please!',
              html: 'Validating information...',
              timer: (time < 2000 ? 2000 : time) + extra,
              timerProgressBar: true,
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
                clearInterval(timerInterval)
              }
            });
            setTimeout(function(){
                $('#content').load(location.href + " " + '#content > *');  
                setTimeout(function(){
                    if(level == $('#ticket-type').val() || $('#ticket-type').val() == 1 ){
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
                },200 + extra);
            }, time < 2000 ? 2000 : time);
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
        
        $('#edit-button').click(function(e){
            e.preventDefault();
            $('div#default-content').hide();
            $('input#send-button').hide();
            $('div#mod-content').show();
            $('input#save-button').show();
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
        
        $('div#msg-content').click(function(){
            $(this).notify("Read only", "warn");
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
    
    Omeka.Tickets.validateUrl = function(field) {
        field.change(function(){
            var value = $(this).val();
            var regex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/;
            if(value.match(regex)){
                $(this).notify("Good.", { 
                        className: 'info' ,
                        position: 'top left'
                });
            } else{
                $(this).notify("That's not an url.", { 
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
        
        btn.click(function() {
            modal.show();
        });
        
        span.click(function() {
            modal.hide();
        });   
        
        $(window).click(function(event) {
            if ($(event.target).is(modal)) {
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
    }
})(jQuery);
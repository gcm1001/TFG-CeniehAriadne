
if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Collections = {};

(function ($) {
    Omeka.Collections.enableSorting = function () {
        $('.sortable').sortable({
            collections: 'li.file',
            forcePlaceholderSize: true, 
            forceHelperSize: true,
            revert: 200,
            placeholder: "ui-sortable-highlight",
            containment: 'document',
            update: function (event, ui) {
                $(this).find('.file-order').each(function (index) {
                    $(this).val(index + 1);
                });
            }
        });
        $( ".sortable" ).disableSelection();
        
        $( ".sortable input[type=checkbox]" ).each(function () {
            $(this).css("display", "none");
        });
    };
    /**
     * Make links to files open in a new window.
     */
    Omeka.Collections.makeFileWindow = function () {
        $('#file-list a').click(function (event) {
            event.preventDefault();
            if($(this).hasClass("delete")) {
                Omeka.Collections.enableFileDeletion($(this));
            } else {
                window.open(this.getAttribute('href'));
            }
        });
    };

    /**
     * Set up toggle for marking files for deletion. 
     */
    Omeka.Collections.enableFileDeletion = function (deleteLink) {
        if( !deleteLink.next().is(":checked") ) {
            deleteLink.text("Undo").next().prop('checked', true).parents('.sortable-collection').addClass("deleted");
        } else {
            deleteLink.text("Delete").next().prop('checked', false).parents('.sortable-collection').removeClass("deleted");
        }
    };
    
    Omeka.Collections.enableAddFiles = function (label) {
        var filesDiv = $('#files-metadata .files');

        var link = $('<a href="#" id="add-file" class="add-file button">' + label + '</a>');
        link.click(function (event) {
            event.preventDefault();
            var inputs = filesDiv.find('input');
            var inputCount = inputs.length;
            var fileHtml = '<input name="file[' + inputCount + ']" type="file"></div>';
            $(fileHtml).insertAfter(inputs.last()).hide().slideDown(200, function () {
                // Extra show fixes IE bug.
                $(this).show();
            });
        });

        $('#file-inputs').append(link);
    };
    
})(jQuery);



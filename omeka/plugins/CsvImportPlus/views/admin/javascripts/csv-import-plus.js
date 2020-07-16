if (!Omeka) {
    var Omeka = {};
}

Omeka.CsvImportPlus = {};

(function ($) {
    /**
     * Allow multiple mappings for each field, and add buttons to allow a mapping
     * to be removed.
     */
    Omeka.CsvImportPlus.enableElementMapping = function () {
        $('form#csvimportplus .map-element').change(function () {
            var select = $(this);
            var addButton = select.siblings('span.add-element');
            if (!addButton.length) {
                var addButton = $('<span class="add-element"></span>');
                addButton.click(function() {
                    var copy = select.clone(true);
                    select.after(copy);
                    $(this).remove();
                });
                select.after(addButton);
            };
        });
    };
    
    Omeka.CsvImportPlus.refreshData = function () {
        setTimeout(function()
        {
            $('#content').load(location.href + " " + '#content > *');
        }, 2000);
    };

    /**
     * Add a little script that selects the right form values if our spreadsheet
     * uses the same names are our Omeka fields (or similar names like Creator_1,
     * Creator_2, and Creator_3 that should be mapped to our Creator Omeka field)
     */
    Omeka.CsvImportPlus.assistWithMapping = function () {
        jQuery.each(jQuery('select[class="map-element"]'), function() {
            $tr = jQuery(this).parent().parent();
            $label = jQuery($tr).find('strong:eq(0)').text();
            $end = $label.lastIndexOf("_");

            if ($end != -1) {
                $label = $label.substring(0, $end);
            }
            $label = $label.replace(/ /g, '');

            jQuery.each(jQuery($tr).find('option'), function() {
                $optionText = jQuery(this).text().replace(/ /g, '');

                if ($optionText == $label) {
                    jQuery(this).attr('selected', 'selected');
                }
            });
        });
    };

    /**
     * Add a confirm step before undoing an import.
     */
    Omeka.CsvImportPlus.confirm = function () {
        $('.csv-undo-import').click(function () {
            return confirm("Undoing an import will delete all of its imported records. Are you sure you want to undo this import?");
        });
    };

    /**
     * Enable/disable column delimiter field.
     */
    Omeka.CsvImportPlus.updateColumnDelimiterField = function () {
        var fieldSelect = $('#column_delimiter_name');
        var fieldCustom = $('#column_delimiter');
        if (fieldSelect.val() == 'custom') {
            fieldCustom.show();
        } else {
            fieldCustom.hide();
        };
    };

    /**
     * Enable/disable enclosure field.
     */
    Omeka.CsvImportPlus.updateEnclosureField = function () {
        var fieldSelect = $('#enclosure_name');
        var fieldCustom = $('#enclosure');
        if (fieldSelect.val() == 'custom') {
            fieldCustom.show();
        } else {
            fieldCustom.hide();
        };
    };

    /**
     * Enable/disable element delimiter field.
     */
    Omeka.CsvImportPlus.updateElementDelimiterField = function () {
        var fieldSelect = $('#element_delimiter_name');
        var fieldCustom = $('#element_delimiter');
        if (fieldSelect.val() == 'custom') {
            fieldCustom.show();
        } else {
            fieldCustom.hide();
        };
    };

    /**
     * Enable/disable tag delimiter field.
     */
    Omeka.CsvImportPlus.updateTagDelimiterField = function () {
        var fieldSelect = $('#tag_delimiter_name');
        var fieldCustom = $('#tag_delimiter');
        if (fieldSelect.val() == 'custom') {
            fieldCustom.show();
        } else {
            fieldCustom.hide();
        };
    };

    /**
     * Enable/disable file delimiter field.
     */
    Omeka.CsvImportPlus.updateFileDelimiterField = function () {
        var fieldSelect = $('#file_delimiter_name');
        var fieldCustom = $('#file_delimiter');
        if (fieldSelect.val() == 'custom') {
            fieldCustom.show();
        } else {
            fieldCustom.hide();
        };
    };

    /**
     *  Create popup divs
     */
    Omeka.CsvImportPlus.showHelpPopups = function(imgurl) {
        $('div.field').each(function(){
            var helpP = $(this).find('p.explanation');
            var label = $(this).find('label');
            label.prepend( "<div class='popup'>" +
                    "<h3>Helper</h3><p>" + 
                    helpP.text() + 
                    "</p> </div>" +
                    "<img class='help-popup' src='"+ imgurl +"' width='20'/>"

                    );
            helpP.remove();
        });
    };
    /**
     * Enable/disable options after loading.
     */
    Omeka.CsvImportPlus.updateOnLoad = function () {
        Omeka.CsvImportPlus.updateColumnDelimiterField();
        Omeka.CsvImportPlus.updateEnclosureField();
        Omeka.CsvImportPlus.updateElementDelimiterField();
        Omeka.CsvImportPlus.updateTagDelimiterField();
        Omeka.CsvImportPlus.updateFileDelimiterField();
    };
})(jQuery);

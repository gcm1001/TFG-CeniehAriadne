<?php
    echo head(array('title' => __('CSV Import+')));
?>
<?php echo common('csvimportplus-nav'); ?>
<div id="primary">
    <?php echo flash(); ?>
    <h2><?php echo __('Step 1: Select file and item settings'); ?></h2>
    <?php echo $this->form; ?>
</div>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    jQuery('#column_delimiter_name').click(Omeka.CsvImportPlus.updateColumnDelimiterField);
    jQuery('#enclosure_name').click(Omeka.CsvImportPlus.updateEnclosureField);
    jQuery('#element_delimiter_name').click(Omeka.CsvImportPlus.updateElementDelimiterField);
    jQuery('#tag_delimiter_name').click(Omeka.CsvImportPlus.updateTagDelimiterField);
    jQuery('#file_delimiter_name').click(Omeka.CsvImportPlus.updateFileDelimiterField);
    Omeka.CsvImportPlus.updateOnLoad(); // Need this to reset invalid forms.
    Omeka.CsvImportPlus.showHelpPopups("<?= img('help-button.png'); ?>");
});
//]]>
</script>
<?php
    echo foot();

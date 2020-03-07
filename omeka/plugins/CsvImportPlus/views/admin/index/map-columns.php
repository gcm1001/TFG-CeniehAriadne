<?php
    echo head(array('title' => __('CSV Import+')));
?>
<?php echo common('csvimportplus-nav'); ?>
<div id="primary">
    <h2><?php echo __('Step 2: Map columns to elements, tags, or files'); ?></h2>
    <p><?php echo __('Csv file: %s', $this->csvFile); ?></p>
    <?php echo flash(); ?>
    <?php echo $this->form; ?>
</div>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.CsvImportPlus.enableElementMapping();
    Omeka.CsvImportPlus.assistWithMapping();
});
//]]>
</script>
<?php
    echo foot();

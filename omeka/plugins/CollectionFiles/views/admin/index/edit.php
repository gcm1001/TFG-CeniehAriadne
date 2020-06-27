<?php
$fileTitle = metadata('collection_file', 'display title');
if ($fileTitle != '') {
    $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
} else {
    $fileTitle = '';
}
$fileTitle = __('Edit File #%s', metadata('collection_file', 'id')) . $fileTitle; 
queue_js_file(array('vendor/tinymce/tinymce.min', 'elements', 'tabs'));
?>

<?= head(array('title' => $fileTitle, 'bodyclass' => 'files edit')); ?>
<?php include 'form-tabs.php'; ?>
<?= flash(); ?>

<form method="post" action="">
    <section class="seven columns alpha" id="edit-form">
        <?= file_markup($collection_file); ?>
        <div id="file-metadata">
            <?php foreach ($tabs as $tabName => $tabContent): ?>
            <?php if (!empty($tabContent)): ?>
                <div id="<?= text_to_id(html_escape($tabName)); ?>-metadata">
                    <fieldset class="set">
                        <h2><?= html_escape(__($tabName)); ?></h2>
                        <?= $tabContent; ?>
                    </fieldset>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div> <!-- end file-metadata div -->
        <?php fire_plugin_hook('admin_collection_files_form', array('file' => $collection_file, 'view' => $this)); ?>
    </section>
    <?= $csrf; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" name="submit" class="submit big green button" value="<?= __('Save Changes'); ?>" id="file_edit" /> 
            <?php if (is_allowed('Files', 'delete')): ?>
                <?= link_to($collection_file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
            <?php endif; ?>
        </div>
    </section>
</form>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function () {
    Omeka.Tabs.initialize();
    Omeka.wysiwyg({
        selector: false,
        forced_root_block: false
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');
});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?= js_escape(url('elements/element-form')); ?>,'File'<?php if ($id = metadata('collection_file', 'id')):?> <?=', '.$id; ?> <?php endif; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
</script>
<?= foot(); ?>
<?php
$fileTitle = metadata('collection_files', 'display title');

if ($fileTitle != '') {
    $fileTitle = ': &ldquo;' . $fileTitle . '&rdquo; ';
} else {
    $fileTitle = '';
}
$fileTitle = __('File #%s', metadata('collection_files', 'id')) . $fileTitle;
?>
<?=  head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>
<?= flash(); ?>


<section class="seven columns alpha">
    <?= file_markup($collection_file); ?>
    
    <?= all_element_texts('collection_file'); ?>
    
    <?php fire_plugin_hook('admin_collection_files_show', array('collection-file' => $collection_file, 'view' => $this)); ?>
</section>

<section class="three columns omega">
    <div id="edit" class="panel">
        <?php if (is_allowed($collection_file, 'edit')): ?>
            <?= link_to($collection_file, 'edit', __('Edit'), array('class'=>'big green button')); ?>
        <?php endif; ?>
        <a href="<?= html_escape(public_url('collection-files/show/'.metadata('collection_file', 'id'))); ?>" class="big blue button" target="_blank"><?= __('View Public Page'); ?></a>
        <?php if (is_allowed($collection_file, 'delete')): ?>
            <?= link_to($collection_file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
        <?php endif; ?>
    </div>
    
    <div id="item-metadata" class="panel">
        <h4><?= __('Collection'); ?></h4> 
        <p><?= link_to_collection(null, array(), 'show', $collection_file->getCollection()); ?></p>
    </div>

    <div id="file-links" class="panel">
        <h4><?= __('Direct Links'); ?></h4>
        <ul>
            <li><a href="<?= html_escape(metadata($collection_file, 'uri')); ?>"><?= __('Original'); ?></a></li>
            <?php if ($collection_file->has_derivative_image): ?>
            <li><a href="<?= html_escape(metadata($collection_file, 'fullsize_uri')); ?>"><?= __('Fullsize'); ?></a></li>
            <li><a href="<?= html_escape(metadata($collection_file, 'thumbnail_uri')); ?>"><?= __('Thumbnail'); ?></a></li>
            <li><a href="<?= html_escape(metadata($collection_file, 'square_thumbnail_uri')); ?>"><?= __('Square Thumbnail'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div id="format-metadata" class="panel">
        <h4><?= __('Format Metadata'); ?></h4>
        <dl>
        <dt><?= __('Filename'); ?>:</dt>
        <dd><?= html_escape(metadata('collection_file', 'Filename')); ?></dd>
        <dt><?= __('Original Filename'); ?>:</dt>
        <dd><?= html_escape(metadata('collection_file', 'Original Filename')); ?></dd>
        <dt><?= __('File Size'); ?>:</dt>
        <dd><?= html_escape(__('%s bytes', metadata('collection_file', 'Size'))); ?></dd>
        </dl>
    </div>

    <div id="file-history" class="panel">
        <h4><?= __('File History'); ?></h4>
        <dl>
        <dt><?= __('Date Added'); ?></dt>
        <dd><?= html_escape(format_date(metadata('collection_file', 'Added'), Zend_Date::DATE_MEDIUM)); ?></dd>
        <dt><?= __('Date Modified'); ?></dt> 
        <dd><?= html_escape(format_date(metadata('collection_file', 'Modified'), Zend_Date::DATE_MEDIUM)); ?></dd>
        <dt><?= __('Authentication'); ?></dt> 
        <dd><?= html_escape(metadata('collection_file', 'Authentication')); ?></dd>
        </dl>
    </div>

    <div id="type-metadata" class="panel">
        <h4><?= __('Type Metadata'); ?></h4>
        <dl>
        <dt><?= __('Mime Type'); ?>:</dt>
        <dd><?= html_escape(metadata('collection_file', 'MIME Type')); ?></dd>
        <dt><?= __('File Type / OS'); ?>:</dt>
        <dd><?= html_escape(metadata('collection_file', 'Type OS')); ?></dd>
        </dl>
    </div>

    <?php if ($this->fileId3Metadata($collection_file,array())): ?>
    <div id="id3-metadata" class="panel">
        <h4><?= __('Embedded Metadata'); ?></h4>
        <?= html_escape($this->fileId3Metadata($collection_file,array())); ?>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h4><?= __('Output Formats'); ?></h4>
        <?= output_format_list(); ?>
    </div>

    <?php fire_plugin_hook('admin_collection_files_show_sidebar', array('file' => $collection_file, 'view' => $this)); ?>
</section>
    
<?= foot();?>

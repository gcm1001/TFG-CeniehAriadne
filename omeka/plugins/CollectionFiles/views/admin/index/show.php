<?php
$fileTitle = metadata('collection_files', 'display title');

if ($fileTitle != '') {
    $fileTitle = ': &ldquo;' . $fileTitle . '&rdquo; ';
} else {
    $fileTitle = '';
}
$fileTitle = __('File #%s', metadata('collection_files', 'id')) . $fileTitle;

echo head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary'));
echo flash();
?>

<section class="seven columns alpha">
    <?php echo file_markup($collection_file); ?>
    
    <?php echo all_element_texts('collection_file'); ?>
    
    <?php fire_plugin_hook('admin_collection_files_show', array('collection-file' => $collection_file, 'view' => $this)); ?>
</section>

<section class="three columns omega">
    <div id="edit" class="panel">
        <?php if (is_allowed($collection_file, 'edit')): ?>
            <?php echo link_to($collection_file, 'edit', __('Edit'), array('class'=>'big green button')); ?>
        <?php endif; ?>
        <a href="<?php echo html_escape(public_url('collection-files/show/'.metadata('collection_file', 'id'))); ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
        <?php if (is_allowed($collection_file, 'delete')): ?>
            <?php echo link_to($collection_file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
        <?php endif; ?>
    </div>
    
    <div id="item-metadata" class="panel">
        <h4><?php echo __('Collection'); ?></h4> 
        <p><?php echo link_to_collection(null, array(), 'show', $collection_file->getCollection()); ?></p>
    </div>

    <div id="file-links" class="panel">
        <h4><?php echo __('Direct Links'); ?></h4>
        <ul>
            <li><a href="<?php echo metadata($collection_file, 'uri'); ?>"><?php echo __('Original'); ?></a></li>
            <?php if ($collection_file->has_derivative_image): ?>
            <li><a href="<?php echo metadata($collection_file, 'fullsize_uri'); ?>"><?php echo __('Fullsize'); ?></a></li>
            <li><a href="<?php echo metadata($collection_file, 'thumbnail_uri'); ?>"><?php echo __('Thumbnail'); ?></a></li>
            <li><a href="<?php echo metadata($collection_file, 'square_thumbnail_uri'); ?>"><?php echo __('Square Thumbnail'); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div id="format-metadata" class="panel">
        <h4><?php echo __('Format Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Filename'); ?>:</dt>
        <dd><?php echo metadata('collection_file', 'Filename'); ?></dd>
        <dt><?php echo __('Original Filename'); ?>:</dt>
        <dd><?php echo metadata('collection_file', 'Original Filename'); ?></dd>
        <dt><?php echo __('File Size'); ?>:</dt>
        <dd><?php echo __('%s bytes', metadata('collection_file', 'Size')); ?></dd>
        </dl>
    </div>

    <div id="file-history" class="panel">
        <h4><?php echo __('File History'); ?></h4>
        <dl>
        <dt><?php echo __('Date Added'); ?></dt>
        <dd><?php echo format_date(metadata('collection_file', 'Added'), Zend_Date::DATE_MEDIUM); ?></dd>
        <dt><?php echo __('Date Modified'); ?></dt> 
        <dd><?php echo format_date(metadata('collection_file', 'Modified'), Zend_Date::DATE_MEDIUM); ?></dd>
        <dt><?php echo __('Authentication'); ?></dt> 
        <dd><?php echo metadata('collection_file', 'Authentication'); ?></dd>
        </dl>
    </div>

    <div id="type-metadata" class="panel">
        <h4><?php echo __('Type Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Mime Type'); ?>:</dt>
        <dd><?php echo metadata('collection_file', 'MIME Type'); ?></dd>
        <dt><?php echo __('File Type / OS'); ?>:</dt>
        <dd><?php echo metadata('collection_file', 'Type OS'); ?></dd>
        </dl>
    </div>

    <?php if ($this->fileId3Metadata($collection_file,array())): ?>
    <div id="id3-metadata" class="panel">
        <h4><?php echo __('Embedded Metadata'); ?></h4>
        <?php echo $this->fileId3Metadata($collection_file,array()); ?>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <?php echo output_format_list(); ?>
    </div>

    <?php fire_plugin_hook('admin_collection_files_show_sidebar', array('file' => $collection_file, 'view' => $this)); ?>
</section>
    
<?php echo foot();?>

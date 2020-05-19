<?php if ($has_files): ?>
    <p class="explanation"><?= htmlspecialchars(__('Click and drag the files into the preferred display order.')); ?></p>
    <div id="file-list">
        <ul class="sortable">
        <?php foreach( $files as $key => $file ): ?>
            <li class="file">
                <div class="sortable-collection">
                    <?= file_image('square_thumbnail', array(), $file); ?>
                    <?= htmlspecialchars($file->original_filename); ?>
                    <?= $this->formHidden("order[{$file->id}]", $file->order, array('class' => 'file-order')); ?>
                    <ul class="action-links">
                        <li><?= link_to($file, 'edit', __('Edit'), array('class'=>'edit')); ?></li>
                        <li><a href="#" class="delete"><?= __('Delete '); ?></a> <?= $this->formCheckbox('delete_files[]', $file->id, array('checked' => false)); ?></li>
                    </ul>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="add-new"><?= htmlspecialchars(__('Add New Files')); ?></div>
<div class="drawer-contents">
    <p><?= htmlspecialchars(__('The maximum file size is %s.', max_file_size())); ?></p>
    
    <div class="field two columns alpha" id="file-inputs">
        <label><?= htmlspecialchars(__('Find a File')); ?></label>
    </div>

    <div class="files four columns omega">
        <input name="collectionfile[0]" type="file">
    </div>
</div>

<?php fire_plugin_hook('admin_collections_form_files', array('collection' => $collection, 'view' => $this)); ?>

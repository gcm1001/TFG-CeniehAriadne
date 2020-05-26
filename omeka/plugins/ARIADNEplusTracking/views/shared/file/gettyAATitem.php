<div class="two columns alpha">
	<label><b><?= __('JSON file of your matchings to Getty AAT'); ?></b></label>
</div>
<div class="drawer-contents">
    <p><?= html_escape(__('The maximum file size is %s.', max_file_size())); ?></p>
    
    <div class="field two columns alpha" id="gettyatt-input">
        <label><?= __('Find a File'); ?></label>
    </div>

    <div class="files four columns omega">
        <input name="file[0]" type="file">
    </div>
</div>
<?php if (metadata('item', 'has files')): ?>
    <p class="explanation"><?= __('Click and drag the files into the preferred display order.'); ?></p>
    <div id="file-list">
        <ul class="sortable">
        <?php foreach( $item->Files as $key => $file ): ?>
            <li class="file">
                <div class="sortable-item">
                    <?= file_image('square_thumbnail', array(), $file); ?>
                    <?= link_to($file, 'show', html_escape($file->original_filename), array()); ?>
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

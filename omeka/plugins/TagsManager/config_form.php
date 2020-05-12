<?php $view = get_view(); ?>
<fieldset>
<legend><?php echo __("Sync Settings"); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="sync"><?php echo __("Enable synchronization"); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __("Synchronize Omeka tags with dc:subject entries."); ?></p>
        <?php
        echo get_view()->formCheckbox('sync', true,
            array('checked' => (boolean) get_option('tagsmanager_sync')));
        ?>
        <p class="explanation"><strong><?php echo __("WARNING: When this option is enabled, it will override anything you type in the Tags tab on the Item edition page."); ?></strong></p>
    </div>
</div>
</fieldset>

<fieldset>
<legend><?php echo __("Edit Settings"); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="delbutton"><?php echo __("Enable delete button"); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __("Add delete button in Browse Tags page."); ?></p>
        <?php
        echo get_view()->formCheckbox('delbutton', true,
            array('checked' => (boolean) get_option('tagsmanager_delbutton')));
        ?>
    </div>
</div>
</fieldset>


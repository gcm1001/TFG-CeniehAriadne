<?php $view = get_view(); ?>
<fieldset>
<legend><?=  htmlspecialchars(__("Sync Settings")); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="sync"><?=  htmlspecialchars(__("Enable synchronization")); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?=  htmlspecialchars(__("Synchronize Omeka tags with dc:subject entries.")); ?></p>
        <?= get_view()->formCheckbox('sync', true,
            array('checked' => (boolean) get_option('tagsmanager_sync')));
        ?>
        <p class="explanation"><strong><?=  htmlspecialchars(__("WARNING: When this option is enabled, it will override anything you type in the Tags tab on the Item edition page.")); ?></strong></p>
    </div>
</div>
</fieldset>

<fieldset>
<legend><?=  __("Edit Settings"); ?></legend>
<div class="field">
    <div class="two columns alpha">
        <label for="delbutton"><?=  htmlspecialchars(__("Enable delete button")); ?></label>
    </div>
    <div class="inputs five columns omega">
      <p class="explanation"><?= htmlspecialchars(__("Add delete button in Browse Tags page.")); ?></p>
        <?= get_view()->formCheckbox('delbutton', true,
            array('checked' => (boolean) get_option('tagsmanager_delbutton')));
        ?>
    </div>
</div>
</fieldset>


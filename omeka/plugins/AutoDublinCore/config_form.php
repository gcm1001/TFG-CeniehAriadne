<?php $view = get_view(); ?>
<fieldset>
    <legend><?=  htmlspecialchars(__("Automatic Updates")); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="sync"><?=  htmlspecialchars(__("Is Part Of")); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?=  htmlspecialchars(__("This will automatically update the 'Is Part Of' element with the identifier of the collection associated with the item. The 'Identifier' element of the collection is also automatically updated.")); ?></p>
            <?= $view->formCheckbox('auto_ispartof', true,
                array('checked' => (boolean) get_option('auto_ispartof')));
            ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="sync"><?=  htmlspecialchars(__("Source (DSpace Platforms)")); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?= __("Handle link (e.g 'https://cir.cenieh.es/handle/')."); ?></p>
            <?= $view->formText('auto_source_link', get_option('auto_source_link')); ?>
            <p class="explanation"><?=  htmlspecialchars(__("This will automatically update the 'Source' element with the DSpace URI.")); ?></p>
            <?= $view->formCheckbox('auto_source', true,
                array('checked' => (boolean) get_option('auto_source')));
            ?>
        </div>
    </div>
</fieldset>
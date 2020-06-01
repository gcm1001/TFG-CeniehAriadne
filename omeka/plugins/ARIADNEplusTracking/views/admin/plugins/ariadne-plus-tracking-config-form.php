<?php $view = get_view(); ?>
<style type="text/css">
.ariadneplus-tracking-boxes {
    text-align: center;
}
.input-block ul {
    list-style: none outside none;
}
</style>
<fieldset id="fieldset-ariadneplus-tracking-contact"><legend><?= __('ARIADNEplus contact details'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="ariadneplus_tracking_name"><?= __('Contact Name'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?= __("The name of the person who is responsible for metadata imports to AriadnePlus."); ?></p>
            <?= $view->formText('ariadneplus_tracking_name', get_option('ariadneplus_tracking_name')); ?>
        </div>
	</div>
	<div class="field">
        <div class="two columns alpha">
            <label for="ariadneplus_tracking_email"><?= __('Contact email'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?= __("E-mail of the contact person.");?></p>
            <?= $view->formText('ariadneplus_tracking_email', get_option('ariadneplus_tracking_email')); ?>
        </div>
	</div>
</fieldset>
<fieldset id="fieldset-ariadneplus-tracking-elements"><legend><?= __('Elements'); ?></legend>
    <?php $monitorElementSet = $this->tracking()->getElementSet(); ?>
    <p class="explanation"><?= __('To manage elements (repeatable or not, steppable or not, with list of terms or not...), go to '); ?>
      <a href="<?= html_escape(url('settings')) ?> " > <?= __('Settings'); ?> </a>, <?= __('then'); ?> 
      <a href="<?= html_escape(url('element-sets')) ?> " > <?= __('Element Sets'); ?> </a>, <?= __('then'); ?> 
      <a href="<?= html_escape(url('element-sets/edit/' . $monitorElementSet->id)) ?> " > <?= __('Monitor'); ?> </a>
    </p>
    <div class="field">
        <div class="two columns alpha">
            <?= $this->formLabel('ariadneplus_tracking_display_remove',
                __('Display Remove Checkbox')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?= __('If set, a checkbox will be displayed the next time in the page above to remove any existing element of the Monitor Element Set.'); ?>
                <br />
                <?= __('Warning: All data of the selected fields will be removed and will not be recoverable easily.'); ?>
                <?= ' '. __('So, check first if your backups are up to date and working.'); ?>
                ?>
            </p>
            <?= $this->formCheckbox('ariadneplus_tracking_display_remove', true,
                array('checked' => false)); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?= $this->formLabel('ariadneplus_tracking_hide_elements',
                __('Hide unnecessary Dublin Core elements')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?= __('If set, all unnecessary Dublin Core elements will be hidden.');?>
              <br/>
            </p>
            <?= $this->formCheckbox('ariadneplus_tracking_hide_elements', true,
                array('checked' => get_option('ariadneplus_tracking_hide_elements')) ); ?>
              </br><b><?= __('Info'); ?> </b>: <?= __('You can check which elements have been hidden on the "Hide Elements" plugin') ?>
              <a href="<?= html_escape(url('plugins/config', array('name' => 'HideElements'))) ?> " > <?= __('configuration page'); ?> </a>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-ariadneplus-tracking-elements"><legend><?= __('Permissions'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?= $this->formLabel('batch_edit_disable',
                __('Disable Batch Edit tool')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?= __('If set, batch edit tool will be disabled.'); ?>
              <br/>
            </p>
            <?= $this->formCheckbox('batch_edit_disable', true,
                array('checked' => get_option('batch_edit_disable')) ); ?>
        </div>
    </div>    
</fieldset>
<fieldset id="fieldset-ariadneplus-tracking-admin-display"><legend><?= __('Specific admin display'); ?></legend>
    <div class="field">
        <?= $this->formLabel('ariadneplus_tracking_admin_items_browse', __('Display elements')); ?>
        <p class="explanation">
            <?= __('The content of checked elements will be displayed in the main cell or in the detailed part of the main cell of each item.'); ?>
        </p>
        <table id="hide-elements-table">
            <thead>
                <tr>
                    <th class="ariadneplus-tracking-boxes" rowspan="2"><?= __('Element'); ?></th>
                    <th class="ariadneplus-tracking-boxes" colspan="5"><?= __('Display in items/browse:'); ?></th>
                </tr>
                <tr>
                    <th class="ariadneplus-tracking-boxes"><?= __('Search'); ?></th>
                    <th class="ariadneplus-tracking-boxes"><?= __('Filter'); ?></th>
                    <th class="ariadneplus-tracking-boxes"><?= __('Directly'); ?></th>
                    <th class="ariadneplus-tracking-boxes"><?= __('Details'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $current_element_set = null;
            foreach ($elements as $element):
                if ($element->set_name != $current_element_set):
                    $current_element_set = $element->set_name; ?>
                <tr>
                    <th colspan="6">
                        <strong><?= html_escape(__($current_element_set)); ?></strong>
                    </th>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><?= html_escape(__($element->name)); ?></td>
                    <?php if ($current_element_set == 'Monitor'): ?>
                    <td class="ariadneplus-tracking-boxes">
                        <?= $this->formCheckbox(
                            "search[{$element->set_name}][{$element->id}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['search'][$element->set_name][$element->id]),
                            )
                        ); ?>
                    </td>
                    <td class="ariadneplus-tracking-boxes">
                        <?= $this->formCheckbox(
                            "filter[{$element->set_name}][{$element->id}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['filter'][$element->set_name][$element->id]),
                            )
                        ); ?>
                    </td>
                    <?php else: ?>
                    <td></td>
                    <td></td>
                    <?php endif; ?>
                    <td class="ariadneplus-tracking-boxes">
                        <?= $this->formCheckbox(
                            "simple[{$element->set_name}][{$element->name}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['simple'][$element->set_name][$element->name]),
                            )
                        ); ?>
                    </td>
                    <td class="ariadneplus-tracking-boxes">
                        <?= $this->formCheckbox(
                            "detailed[{$element->set_name}][{$element->name}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['detailed'][$element->set_name][$element->name])
                            )
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</fieldset>

<?php $view = get_view(); ?>
<style type="text/css">
.ariadneplus-monitor-boxes {
    text-align: center;
}
.input-block ul {
    list-style: none outside none;
}
</style>
<fieldset id="fieldset-ariadneplus-monitor-contact"><legend><?php echo __('AriadnePlus contact details'); ?></legend>
    <p class="explanation">
    	
    </p>
    <div class="field">
        <div class="two columns alpha">
            <label for="ariadneplus_monitor_name"><?php echo __('Contact Name'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __("The name of the person who is responsible for metadata imports to AriadnePlus."); ?></p>
            <?php echo $view->formText('ariadneplus_monitor_name', get_option('ariadneplus_monitor_name')); ?>
        </div>
	</div>
	
	<div class="field">
        <div class="two columns alpha">
            <label for="ariadneplus_monitor_email"><?php echo __('Contact email'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __("The e-mail of the contact person.");?></p>
            <?php echo $view->formText('ariadneplus_monitor_email', get_option('ariadneplus_monitor_email')); ?>
        </div>
	</div>
</fieldset>
<fieldset id="fieldset-ariadneplus-monitor-elements"><legend><?php echo __('Elements'); ?></legend>
    <?php $monitorElementSet = $this->monitor()->getElementSet(); ?>
    <p class="explanation"><?php echo __('To manage elements (repeatable or not, steppable or not, with list of terms or not...), go to %sSettings%s, then %sElement Sets%s, then %sMonitor%s.',
            '<a href="' . url('settings') . '">', '</a>',
            '<a href="' . url('element-sets') . '">', '</a>',
            '<a href="' . url('element-sets/edit/' . $monitorElementSet->id) . '">', '</a>'); ?></p>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('ariadneplus_monitor_display_remove',
                __('Display Remove Checkbox')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?php
                echo __('If set, a checkbox will be displayed the next time in the page above to remove any existing element of the Monitor Element Set.');
                echo '<br />';
                echo __('Warning: All data of the selected fields will be removed and will not be recoverable easily.');
                echo ' ' . __('So, check first if your backups are up to date and working.');
                ?>
            </p>
            <?php echo $this->formCheckbox('ariadneplus_monitor_display_remove', true,
                array('checked' => false)); ?>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('ariadneplus_monitor_hide_elements',
                __('Hide unnecessary Dublin Core elements')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?php
                echo __('If set, all unnecessary Dublin Core elements will be hidden.');
                echo '<br />';
                ?>
            </p>
            <?php echo $this->formCheckbox('ariadneplus_monitor_hide_elements', true,
                array('checked' => get_option('ariadneplus_monitor_hide_elements')) ); 
                            echo __('</br><b>Info</b>: You can check which elements have been hidden on the "Hide Elements" plugin %s configuration page %s.',
                                    '<a href="' . url('plugins/config', array('name' => 'HideElements')) . '">', '</a>');?>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-ariadneplus-monitor-elements"><legend><?php echo __('Permissions'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('batch_edit_disable',
                __('Disable Batch Edit tool')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?php
                echo __('If set, batch edit tool will be disabled.');
                echo '<br />';
                ?>
            </p>
            <?php echo $this->formCheckbox('batch_edit_disable', true,
                array('checked' => get_option('batch_edit_disable')) ); ?>
        </div>
    </div>    
</fieldset>
<fieldset id="fieldset-ariadneplus-monitor-admin-display"><legend><?php echo __('Specific admin display'); ?></legend>
    <div class="field">
        <?php echo $this->formLabel('ariadneplus_monitor_admin_items_browse', __('Display elements')); ?>
        <p class="explanation">
            <?php echo __('The content of checked elements will be displayed in the main cell or in the detailed part of the main cell of each item.'); ?>
        </p>
        <table id="hide-elements-table">
            <thead>
                <tr>
                    <th class="ariadneplus-monitor-boxes" rowspan="2"><?php echo __('Element'); ?></th>
                    <th class="ariadneplus-monitor-boxes" colspan="5"><?php echo __('Display in items/browse:'); ?></th>
                </tr>
                <tr>
                    <th class="ariadneplus-monitor-boxes"><?php echo __('Search'); ?></th>
                    <th class="ariadneplus-monitor-boxes"><?php echo __('Filter'); ?></th>
                    <th class="ariadneplus-monitor-boxes"><?php echo __('Directly'); ?></th>
                    <th class="ariadneplus-monitor-boxes"><?php echo __('Details'); ?></th>
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
                        <strong><?php echo __($current_element_set); ?></strong>
                    </th>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><?php echo __($element->name); ?></td>
                    <?php if ($current_element_set == 'Monitor'): ?>
                    <td class="ariadneplus-monitor-boxes">
                        <?php echo $this->formCheckbox(
                            "search[{$element->set_name}][{$element->id}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['search'][$element->set_name][$element->id]),
                            )
                        ); ?>
                    </td>
                    <td class="ariadneplus-monitor-boxes">
                        <?php echo $this->formCheckbox(
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
                    <td class="ariadneplus-monitor-boxes">
                        <?php echo $this->formCheckbox(
                            "simple[{$element->set_name}][{$element->name}]",
                            '1', array(
                                'disableHidden' => true,
                                'checked' => isset($settings['simple'][$element->set_name][$element->name]),
                            )
                        ); ?>
                    </td>
                    <td class="ariadneplus-monitor-boxes">
                        <?php echo $this->formCheckbox(
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

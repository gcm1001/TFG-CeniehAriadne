<fieldset id="ariadne-plus-monitor-item-metadata">
    <h2><?php echo __('AriadnePlus Monitor'); ?></h2>
    <p class="explanation">
        <?php echo __('Set these status for the selected items.'); ?>
        <?php echo __('Note that some elements may be automatically set.'); ?>
    </p>
<?php foreach ($statusTermsElements as $elementId => $statusElement): ?>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('ariadne-plus-monitor-element-' . $elementId, $statusElement['name']); ?>
        </div>
        <div class='inputs five columns omega'>
            <?php
            $options = array();
            $options[''] = __('Select Below');
            $options += $statusElement['terms'];
            $options['remove'] = __('[Remove value]');
            echo $this->formSelect('custom[ariadneplusmonitor][statusterms][element-' . $elementId. ']', null, array(), $options);
            ?>
        </div>
    </div>
<?php endforeach; ?>
</fieldset>

<?php
$logs = $this->Tracking()->showlogs($record, 0);
?>
<?= head(array(
    'title' => __('ARIADNEplus Log'),
    'bodyclass' => 'ariadneplus-log entries',
));
?>
<div id="primary">
    <?=  flash(); ?>
    <div>
<?php
if (!empty($logs)):?>
    <?= $logs; ?>
    <?php if (is_object($record)): ?>
    <p><?= __('Go back to the '); ?> 
      <?= link_to($record, null, __('record')); ?></p>
    <?php else: ?>
    <p><?= __('This record has been deleted.'); ?></p>
    <?php endif; ?>
<?php else: ?>
    <p><?= __('No log for this record.'); ?></p>
    <?php if (is_object($record)): ?>
    <p><?=  __('Go back to the '); ?>
      <?= link_to($record, null, __('record')); ?> </p>
    <?php else: ?>
    <p><?= __('This record does not exist and is not logged.'); ?></p>
    <?php endif; ?>
<?php endif; ?>
    <p><?= __('Go back to '); ?>
      <a href="<?= html_escape(url('ariadn-eplus-tracking')); ?>">ARIADNEplus Tracking </a> </p>
    </div>
</div>
<?= foot(); ?>

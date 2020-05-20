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
    <p><?= htmlspecialchars(__('Go back to the ')); ?> 
      <?= link_to($record, null, __('record')); ?></p>
    <?php else: ?>
    <p><?=  htmlspecialchars(__('This record has been deleted.')); ?></p>
    <?php endif; ?>
<?php else: ?>
    <p><?= htmlspecialchars(__('No log for this record.')); ?></p>
    <?php if (is_object($record)): ?>
    <p><?=  htmlspecialchars(__('Go back to the ')); ?>
      <?= link_to($record, null, __('record')); ?> </p>
    <?php else: ?>
    <p><?= htmlspecialchars(__('This record does not exist and is not logged.')); ?></p>
    <?php endif; ?>
<?php endif; ?>
    <p><?= htmlspecialchars(__('Go back to ')); ?>
      <a href="<?= htmlspecialchars(url('ariadn-eplus-tracking')); ?>">ARIADNEplus Tracking </a> </p>
    </div>
</div>
<?= foot(); ?>

<?php
$logs = $this->Tracking()->showlogs($record, 0);

echo head(array(
    'title' => __('ARIADNEplus Log'),
    'bodyclass' => 'ariadneplus-log entries',
));
?>
<div id="primary">
    <?php echo flash(); ?>
    <div>
<?php
if (!empty($logs)):
    echo $logs;
    ?>
    <?php if (is_object($record)): ?>
    <p><?php echo __('Go back to the %s.', link_to($record, null, __('record'))); ?></p>
    <?php else: ?>
    <p><?php echo __('This record has been deleted.'); ?></p>
    <?php endif; ?>
<?php else: ?>
    <p><?php echo __('No log for this record.'); ?></p>
    <?php if (is_object($record)): ?>
    <p><?php echo __('Go back to the %s.', link_to($record, null, __('record'))); ?></p>
    <?php else: ?>
    <p><?php echo __('This record does not exist and is not logged.'); ?></p>
    <?php endif; ?>
<?php endif; ?>
    <p><?php echo __('Go back to %sARIADNEplus Tracking%s.', '<a href="' . 
                html_escape(url('ariadn-eplus-tracking')) . '">', '</a>'); ?></p>
    </div>
</div>
<?php
echo foot();

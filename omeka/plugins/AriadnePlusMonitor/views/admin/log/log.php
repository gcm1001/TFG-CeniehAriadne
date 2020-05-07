<?php
$logs = $this->Monitor()->showlogs($record,$mode, 0);

echo head(array(
    'title' => __('AriadnePlus Log'),
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
    <p><?php $type = get_class($record);
        echo __('Go back to %sAriadnePlus Monitor%s.', '<a href="' . 
                html_escape(url('ariadne-plus-monitor',array('record_type' => $type,
                    strtolower($type) => $record->id,'mode' => $mode ))) . '">', '</a>'); ?></p>
    </div>
</div>
<?php
echo foot();

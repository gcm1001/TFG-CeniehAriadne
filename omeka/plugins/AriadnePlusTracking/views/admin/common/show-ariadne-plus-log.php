<div class="element-set">
    <div id='ariadne-log-panel' class="panel">
        <h2><?= __('Last Changes for %s #%d', $record_type, $record_id); ?></h2>
        <table id="ariadne-log-entries">
            <thead>
                <tr>
                    <td><strong><?= __('Date') ?></strong></td>
                    <td><strong><?= __('Type') ?></strong></td>
                    <td><strong><?= __('Id') ?></strong></td>
                    <td><strong><?= __('Part of ') ?></strong></td>
                    <td><strong><?= __('User') ?></strong></td>
                    <td><strong><?= __('Action') ?></strong></td>
                    <td><strong><?= __('Messages') ?></strong></td>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0; 
                foreach ($logEntries as $logEntry): ?>
                <tr class="ariadneplus-log-entry <?= ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <td><?= htmlspecialchars($logEntry->added); ?></td>
                    <td colspan="2">
                        <a href="<?=
                            html_escape(url('ariadne-plus-tracking/index/ticket',array(
                                'record_type' => $record_type,
                                strtolower($record_type) => $record_id,
                            ))); ?>">
                            <?= $record_type; ?>  
                            <?= $record_id; ?>
                        </a>
                        <div class="record-title"><?= $logEntry->displayCurrentTitle(); ?></div>
                    </td>
                    <td><?= $logEntry->displayPartOf(true); ?></td>
                    <td><?= html_escape($logEntry->displayUser()); ?></td>
                    <td><?= html_escape($logEntry->displayOperation()); ?></td>
                    <td><?= nl2br($logEntry->displayMsgs(), true); ?></td>
                </tr>
                <?php endforeach; 
                 if ($limit > 0 && count($logEntries) >= $limit):?>
                <tr>
                    <td>
                        <a href="<?= html_escape(url(array(
                                    'type' => Inflector::tableize($logEntry->record_type),
                                    'id' => $logEntry->record_id,
                                ), 'ariadneplus_record_log')); ?>">
                            <strong><?= __('See more'); ?></strong>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

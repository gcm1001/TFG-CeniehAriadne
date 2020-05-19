<div class="element-set">
    <div id='ariadne-log-panel' class="panel">
        <h2><?= __('Logs'); ?></h2>
        <table id="ariadne-log-entries">
            <thead>
                <tr>
                    <?php $browseHeadings = array();
                    $browseHeadings[__('Date')] = 'date';
                    $browseHeadings[__('Type')] = 'record_type';
                    $browseHeadings[__('Id')] = 'record_id';
                    $browseHeadings[__('Part of ')] = 'part_of';
                    $browseHeadings[__('User')] = 'user';
                    $browseHeadings[__('Action')] = 'operation';
                    $browseHeadings[__('Messages')] = null;?>
                    <?= browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => '')); ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php $key = 0; 
                foreach ($logEntries as $logEntry): ?>
                <tr class="ariadneplus-log-entry <?= ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <td><?= htmlspecialchars($logEntry->added); ?></td>
                    <td colspan="2">
                        <a href="<?=
                            htmlspecialchars(url(array(
                                'type' => Inflector::tableize($logEntry->record_type),
                                'id' => $logEntry->record_id,
                            ), 'ariadneplus_record_log')); ?>">
                            <?= htmlspecialchars($ticket->record_type); ?>  
                            <?= htmlspecialchars($ticket->record_id); ?>
                        </a>
                        <div class="record-title"><?= $logEntry->displayCurrentTitle(); ?></div>
                    </td>
                    <td><?= htmlspecialchars($logEntry->displayPartOf(true)); ?></td>
                    <td><?= htmlspecialchars($logEntry->displayUser()); ?></td>
                    <td><?= htmlspecialchars($logEntry->displayOperation()); ?></td>
                    <td><?= htmlspecialchars(nl2br($logEntry->displayMsgs(), true)); ?></td>
                </tr>
                <?php endforeach; 
                 if ($limit > 0 && count($logEntries) >= $limit):?>
                <tr>
                    <td>
                        <a href="<?= htmlspecialchars(url(array(
                                    'type' => Inflector::tableize($logEntry->record_type),
                                    'id' => $logEntry->record_id,
                                ), 'ariadneplus_record_log')); ?>">
                            <strong><?= htmlspecialchars(__('See more')); ?></strong>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

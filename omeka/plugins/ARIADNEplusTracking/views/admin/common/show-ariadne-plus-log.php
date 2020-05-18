<div class="element-set">
    <div id='ariadne-log-panel' class="panel">
        <h2><?php echo __('Logs'); ?></h2>
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
                    $browseHeadings[__('Messages')] = null;
                    echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0; 
                foreach ($logEntries as $logEntry): ?>
                <tr class="ariadneplus-log-entry <?php echo ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <td><?php echo $logEntry->added; ?></td>
                    <td colspan="2">
                        <a href="<?php
                        echo html_escape(url(array(
                                'type' => Inflector::tableize($logEntry->record_type),
                                'id' => $logEntry->record_id,
                            ), 'ariadneplus_record_log')); ?>"><?php
                            echo $logEntry->record_type;
                            echo ' ';
                            echo $logEntry->record_id;
                        ?></a>
                        <div class="record-title"><?php echo $logEntry->displayCurrentTitle(); ?></div>
                    </td>
                    <td><?php echo $logEntry->displayPartOf(true); ?></td>
                    <td><?php echo $logEntry->displayUser(); ?></td>
                    <td><?php echo $logEntry->displayOperation(); ?></td>
                    <td><?php echo nl2br($logEntry->displayMsgs(), true); ?></td>
                </tr>
                <?php endforeach; 
                 if ($limit > 0 && count($logEntries) >= $limit):?>
                <tr>
                    <td>
                        <a href="<?php echo html_escape(url(array(
                                    'type' => Inflector::tableize($logEntry->record_type),
                                    'id' => $logEntry->record_id,
                                ), 'ariadneplus_record_log')); ?>">
                            <strong><?php echo __('See more'); ?></strong>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

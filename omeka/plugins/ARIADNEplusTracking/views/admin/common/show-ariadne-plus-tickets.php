<div class="element-set">
    <div id='ariadne-log-panel' class="panel">
        <table id="ariadne-tickets">
            <thead>
                <tr>
                    <?php $browseHeadings = array();
                    $browseHeadings[__('Last Modified')] = 'date';
                    $browseHeadings[__('Type')] = 'record_type';
                    $browseHeadings[__('Id')] = 'record_id';
                    $browseHeadings[__('User')] = 'user';
                    $browseHeadings[__('Status')] = 'status';
                    echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0; 
                foreach ($tickets as $ticket): ?>
                <tr class="ariadneplus-ticket <?php echo ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <td><?php echo html_escape($ticket->lastmod); ?></td>
                    <td colspan="2">
                        <a href="<?php
                        echo html_escape(url(array(
                                'type' => Inflector::tableize($ticket->record_type),
                                'id' => $ticket->record_id,
                            ), 'ariadneplus_record_log')); ?>"><?php
                            echo html_escape($ticket->record_type);
                            echo ' ';
                            echo html_escape($ticket->record_id);
                        ?></a>
                        <div class="record-title"><?php echo html_escape($ticket->displayCurrentTitle()); ?></div>
                        <form id="form-row-<?php echo html_escape(($key-1)); ?>" method="post"><input type="hidden" name="record_type" value="<?php echo html_escape($ticket->record_type); ?>" />
                        <input type="hidden" name="record_id" value="<?php echo html_escape($ticket->record_id); ?>" /></form>
                    </td>
                    <td><?php echo html_escape($ticket->displayUser()); ?></td>
                    <td><?php echo html_escape($ticket->displayStatus()); ?></td>
                </tr>
                </form>
                <?php endforeach; 
                 if ($limit > 0 && count($tickets) >= $limit):?>
                <tr>
                    <td>
                        <a href="">
                            <strong><?php echo __('See more'); ?></strong>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#ariadne-tickets tbody tr").click(function(){
            var row_id = jQuery(this).index();
            jQuery("#form-row-" + row_id).submit();
        });
    });
</script>
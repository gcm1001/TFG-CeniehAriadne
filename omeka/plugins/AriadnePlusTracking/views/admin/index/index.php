<?= head(array(
    'title' => __('ARIADNEplus Tracking'),
    ));
?>
<div id="primary">
    <?= flash(); ?>
    <div id="explanation">
        <img alt="ARIADNE+ Logo" id="ariadnelogo" src="<?= html_escape(img('ariadne-tracking-logo.png')); ?>" />
        <p><?= __('Track, manage and organize your data integrations to'); ?> <br> ARIADNEplus.</p>
    </div>
  <h2><?= __('Step 1: Select an existing ticket...'); ?></h2>
    <section class="ten columns alpha omega">
        <div class="element-set">
            <div id='ariadne-log-panel' class="panel">
                <div class="pagination"><?= $paginationLinks = pagination_links(); ?></div>
                <table id="ariadne-tickets">
                    <thead>
                        <tr>
                            <?php $browseHeadings = array();
                            $browseHeadings[__('Last Modified')] = 'lastmod';
                            $browseHeadings[__('Type')] = 'record_type';
                            $browseHeadings[__('Id')] = 'record_id';
                            $browseHeadings[__('User')] = 'user';
                            $browseHeadings[__('Status')] = 'status';?>
                            <?= browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => '')); ?>
                          <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $key = 0; 
                        foreach (loop('AriadnePlusTrackingTicket') as $ticket): ?>
                        <tr class="ariadneplus-ticket <?= ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                            <td><?= html_escape($ticket->lastmod); ?></td>
                            <td colspan="2">
                                <a href="<?= html_escape(url(array(
                                        'type' => Inflector::tableize($ticket->record_type),
                                        'id' => $ticket->record_id,
                                    ), 'ariadneplus_record_log')); ?>">
                                    <?= html_escape($ticket->record_type); ?>  
                                    <?= html_escape($ticket->record_id); ?>
                                </a>
                              <div class="record-title"><?= html_escape($ticket->displayCurrentTitle()); ?></div>
                                <form id="form-row-<?= html_escape(($key-1)); ?>" method="post"><input type="hidden" name="record_type" value="<?= html_escape($ticket->record_type); ?>" />
                                <input type="hidden" name="record_id" value="<?= html_escape($ticket->record_id); ?>" /></form>
                            </td>
                            <td><?= html_escape($ticket->displayUser()); ?></td>
                            <td><?= html_escape($ticket->displayStatus()); ?></td>
                          <td style="width:35px;">
                            <form id="form-remove-<?= html_escape(($key-1)); ?>" method="post" action="<?= html_escape(url('ariadne-plus-tracking/index/remove'))?>">
                              <input type="hidden" name="record_id" value="<?= html_escape($ticket->record_id); ?>" />
                              <input type="hidden" name="record_type" value="<?= html_escape($ticket->record_type); ?>" />
                            </form>
                            <span class="delete-row table-remove remove-lg">x</span>
                          </td>
                        </tr>
                        </form>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $paginationLinks ?>
            </div>
        </div>
    </section>
    <h2><?= __(' or '); ?><a href="<?= html_escape(url('ariadne-plus-tracking/index/new')); ?>"> <?= __(' create a new one '); ?> </a></h2>    
</div>	
<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.Tickets.ticketCreated();
        Omeka.Tickets.removeTicket();
    });
</script>
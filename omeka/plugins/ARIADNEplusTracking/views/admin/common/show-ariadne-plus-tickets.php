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
                    $browseHeadings[__('Status')] = 'status';?>
                    <?= browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => '')); ?>
                  <th> Action </th>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0; 
                foreach ($tickets as $ticket): ?>
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
                    <form id="form-remove-<?= html_escape(($key-1)); ?>" method="post" action="<?= html_escape(url('ariadn-eplus-tracking/index/remove'))?>">
                      <input type="hidden" name="record_id" value="<?= html_escape($ticket->record_id); ?>" />
                      <input type="hidden" name="record_type" value="<?= html_escape($ticket->record_type); ?>" />
                    </form>
                    <span class="delete-row table-remove remove-lg">x</span>
                  </td>
                </tr>
                </form>
                <?php endforeach; 
                 if ($limit > 0 && count($tickets) >= $limit):?>
                <tr>
                    <td>
                        <a href="">
                            <strong><?= __('See more'); ?></strong>
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
        
        jQuery("#ariadne-tickets tbody tr td:not(:last-child)").click( function () {
             var row_id = jQuery(this).parent('tr').index();
            jQuery('#form-row-'+row_id).submit();
        });
        
        jQuery('.table-remove').click(function () {
            var row = jQuery(this).closest('tr');
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              if (result.value) {
                var idx = row.index();
                row.hide(1500);
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Your ticket has been deleted.',
                    showConfirmButton:false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                });
                setTimeout(function(){
                    jQuery('#form-remove-'+idx).submit();
                }, 2000);
                
              }
            });
        });

    });
</script>
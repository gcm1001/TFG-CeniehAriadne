<div id="div-phase-1">
    <div id="explanation">
        <p class="explanation">
            In this phase, <b>you must fill in all the essential elements of the Dublin Core metadata schema </b>.
        </p>
    </div>
    <form id="hide-form" name="hide-form" method="get"> 
      <input type="hidden" name="record_type" value="<?= html_escape(get_class($record)); ?>"/>
      <input type="hidden" name="<?= html_escape(strtolower(get_class($record))); ?>" value="<?= html_escape($record->id); ?>"/>
      <input type="hidden" name="advanced[0][element_id]" value="<?= html_escape($elementId); ?>"/>
      <input type="hidden" name="advanced[0][type]" value="is not exactly"/>
      <input type="hidden" name="advanced[0][terms]" value="Complete"/>
    </form>
    <div class="items">
    <?= pagination_links(); ?>
    <form action="<?= html_escape(url('items/batch-edit')); ?>" method="post" accept-charset="utf-8">
        <div class="table-actions batch-edit-option">
            <?php if (is_allowed('Items', 'edit') || is_allowed('Items', 'delete')): ?>
                <button class="batch-all-toggle" type="button" data-records-count="<?= html_escape($total); ?>"><?= html_escape(__('Select all %s results', $total)); ?></button>
                <div class="selected"><span class="count">0</span> <?= __('items selected'); ?></div>
                <?php if($hide): ?>
                <button id="show-items"><?= html_escape(__('Show complete items')); ?></button>
                <?php else: ?>
                <button id="hide-items"><?= html_escape(__('Hide complete items')); ?></button>
                <?php endif; ?>
                <input type="hidden" name="batch-all" value="1" id="batch-all" disabled>
                <?= $this->formHidden('params', json_encode(Zend_Controller_Front::getInstance()->getRequest()->getParams())); ?>
                <?php if (is_allowed('Items', 'edit')): ?>
                <input type="submit" class="edit-items small batch-action button" name="submit-batch-edit" value="<?= __('Edit'); ?>" />
                <?php endif; ?>
                <?php if (is_allowed('Items', 'delete')): ?>
                <input type="submit" class="small batch-action button" name="submit-batch-delete" value="<?= __('Delete'); ?>">
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <table id="items">
        <thead>
            <tr>
                <?php if (is_allowed('Items', 'edit')): ?>
                <th class="batch-edit-heading"><?= __('Select all rows'); ?></th>
                <?php endif; ?>
                <?php
                $browseHeadings[__('Title')] = 'Dublin Core,Title';
                $browseHeadings[__('Creator')] = 'Dublin Core,Creator';
                $browseHeadings[__('Type')] = null;
                $browseHeadings[__('Date Added')] = 'added'; ?>
                <?= browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => '')); ?>
                
            </tr>
        </thead>
        <tbody>
            <?php $key = 0; 
                  foreach (loop('Item') as $item):
                      $status = strtolower(metadata($item, array('Monitor','Metadata Status'))); ?>
            <tr class="item <?php if(++$key%2==1): ?> odd <?php else: ?> even <?php endif; ?> 
            <?= html_escape($status); ?>">
                <?php $id = $item->id; ?>

                <?php if (is_allowed($item, 'edit') || is_allowed($item, 'tag')): ?>
                <td class="batch-edit-check">
                    <input type="checkbox" name="items[]" value="<?= $id; ?>"
                        aria-label="<?= html_escape(
                            __('Select item "%s"',
                                metadata($item, 'display_title', array('no_escape' => true))
                            )
                        ); ?>"
                    >
                </td>
                <?php endif; ?>

                <?php if ($item->featured): ?>
                <td class="item-info featured">
                <?php else: ?>
                <td class="item-info">
                <?php endif; ?>

                    <?php if (metadata($item, 'has files')): ?>
                    <?= link_to_item(item_image('square_thumbnail', array(), 0, $item), array('class' => 'item-thumbnail'), 'show', $item); ?>
                    <?php endif; ?>

                    <span class="title">
                    <?= link_to($item,'show',metadata($item, array('Dublin Core', 'Title'))); ?>

                    <?php if(!$item->public): ?>
                    <?= __('(Private)'); ?>
                    <?php endif; ?>
                    </span>
                    <ul class="action-links group">
                        <?php if (is_allowed($item, 'edit')): ?>
                        <li><?= link_to($item,'edit',__('Edit'), array()); ?></li>
                        <?php endif; ?>

                        <?php if (is_allowed($item, 'delete')): ?>
                        <li><?= link_to($item,'delete-confirm',__('Delete'), array('class' => 'delete-confirm')); ?></li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td><?= strip_formatting(metadata($item, array('Dublin Core', 'Creator'))); ?></td>
                <td>
                    <?= ($typeName = metadata($item, 'Item Type Name'))
                        ? html_escape($typeName)
                        : html_escape(metadata($item, array('Dublin Core', 'Type'), array('snippet' => 35)));
                    ?>
                </td>
                <td><?= format_date(metadata($item, 'added')); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
        <div class="table-actions batch-edit-option">
            <?php if (is_allowed('Items', 'edit') || is_allowed('Items', 'delete')): ?>
                <button class="batch-all-toggle" type="button" data-records-count="<?= html_escape($total); ?>"><?= __('Select all %s results', $total); ?></button>
                <div class="selected"><span class="count">0</span> <?= __('items selected'); ?></div>
                <button id="hide-items"><?= html_escape(__('Hide complete items')); ?></button>
                <button id="show-items" style="display:none;"><?= html_escape(__('Show complete items')); ?></button>
                <input type="hidden" name="batch-all" value="1" id="batch-all" disabled>
                <?= $this->formHidden('params', json_encode(Zend_Controller_Front::getInstance()->getRequest()->getParams())); ?>
                <?php if (is_allowed('Items', 'edit')): ?>
                <input type="submit" class="edit-items small batch-action button" name="submit-batch-edit" value="<?= __('Edit'); ?>" />
                <?php endif; ?>
                <?php if (is_allowed('Items', 'delete')): ?>
                <input type="submit" class="small batch-action button" name="submit-batch-delete" value="<?= __('Delete'); ?>">
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </form>
    <?= pagination_links(); ?>
    </div>
  
</div>
<?= js_tag('items-browse'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        Omeka.ItemsBrowse.setupBatchEdit();
        Omeka.Tickets.notifyStatus();
    });
</script>

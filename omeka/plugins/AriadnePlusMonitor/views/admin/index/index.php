<?php
$pageTitle = __('AriadnePlus Monitor');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'ariadne-plus-monitor index',
));
?>	
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function(){

  jQuery('#element-type').change(function() {
		var type = jQuery('#element-type').val();
		window.location.search = (type ? ("?record_type=" + type) : '');
  });
  
  jQuery('#element-id-col').change(function() {
	    var type = jQuery('#element-type').val();
        var elementId = jQuery('#element-id-col').val();
        window.location.search = ("?record_type=" + type) + (elementId ? ("&collection=" + elementId) : '');
  });

  jQuery('#element-id-item').change(function() {
	  	var type = jQuery('#element-type').val();
	    var elementId = jQuery('#element-id-item').val();
	    window.location.search = ("?record_type=" + type) + (elementId ? ("&item=" + elementId) : '');
  });
  
  jQuery('#collection-mode').change(function() {
	  	var type = jQuery('#element-type').val();
	    var elementId = jQuery('#element-id-col').val();
	    var mode = jQuery('#collection-mode').val();
	    window.location.search = ("?record_type=" + type) + ('&collection=' + elementId) + (mode ? ("&mode=" + mode) : '');
  });

  jQuery('#refresh-logs').click(function() {
	    var tablename = "#ariadne-log-entries";
		jQuery(tablename).load(location.href + " " + tablename);
	});
	
  jQuery('#refresh-status').click(function() {
	    var tablename = "#ariadne-plus-monitor-stats-" + jQuery(this).data('element');
		jQuery(tablename).load(location.href + " " + tablename);
	});
   var counter = 0, total = 400, units = 5; 
   jQuery('#add').click(function() {
        counter++;
        if (counter == units) {
          counter = 0;
          jQuery('#completeness').css("opacity","0");
        }
        if (counter == (units-1)) {
          jQuery("#completeness").css("opacity","1");
        }
        jQuery('#target').css("width",(counter+1)*(total/units)+"px");
        setTimeout(function(){
            jQuery('#target').html(counter+1)
        }, 250);
   });
});

</script>
<link rel="stylesheet" href="<?php echo css_src('circle'); ?>">
<div id="primary">
<?php echo flash(); ?>
    <h2><?php
        echo  __('Total published items: %d / %d', get_db()->getTable('Item')->count(array('public' => 1)), total_records('Item'));
    ?></h2>
   	<p> <b>Select a type </b>
	<?php echo $this->formSelect('element_type', null, array('id' => 'element-type'), $options_for_select_type); ?>  <br/> 
	</p>
	<?php if (isset($_GET["record_type"]) && trim($_GET["record_type"]) == 'Collection'): ?>
	<p> <b>Select a Collection </b>
	<?php echo $this->formSelect('element_id_col', null, array('id' => 'element-id-col'), $options_for_select_collection); ?>  <br/> 
	</p>
	<?php endif; ?>
	<?php if (isset($_GET["record_type"]) && trim($_GET["record_type"]) == 'Item'): ?>
	<p> <b>Select an Item </b>
	<?php echo $this->formSelect('element_id_item', null, array('id' => 'element-id-item'), $options_for_select_item); ?>  <br/> 
	</p>
	<?php endif; ?>
	<?php if (isset($_GET["record_type"]) && trim($_GET["record_type"]) == 'Collection' && isset($_GET["collection"])): ?>
	<p> <b>Select the publication mode you want for your selected collection: </b>
	<?php echo $this->formSelect('collection_mode', null, array('id' => 'collection-mode'), $options_for_select_mode); ?>  <br/> 
	</p>
	<?php endif; ?>
<?php if ((isset($_GET["collection"]) && isset($_GET["mode"])) || isset($_GET["item"])): ?>
<?php
if (!empty($results)): ?>
<?php 
$statusElements = $this->monitor()->getStatusElements();
foreach ($results as $elementId => $result):
    if (!isset($statusElements[$elementId]['element'])):
        continue;
    endif;
    $element = $statusElements[$elementId]['element'];
?>
<section class="ten columns alpha omega">
    <div class="panel">
        <h2><?php echo $element->name; ?></h2>
        <?php $published = 0; ?>
        <button id="refresh-status"  data-element="<?php echo $element->id; ?>" >Refresh Status</button>
        <table id="ariadne-plus-monitor-stats-<?php echo $element->id; ?>">
            <thead>
                <tr>
                    <?php
                    $browseHeadings = array();
                    $headers = array_keys(reset($result));
                    foreach ($headers as $header):
                        $browseHeadings[strlen($header) > 0 ? $header : __('Not Set')] = null;
                    endforeach;
                    echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => ''));
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr class="ariadne-plus-monitor-stat <?php echo ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                <?php
                $key = 0;
                foreach ($result['All'] as $period => $row): 
                    $percent = ((int)$row/array_sum($result['All']))*100; ?>
                    <td>
                        <div class="c100 center <?php echo "p".$percent; ?> small orange">
                            <span><?php echo $row; ?> items</span>
                            <div class="slice">
                                <div class="bar"></div>
                                <div class="fill"></div>
                            </div>
                    </td>
                <?php endforeach; ?>
                </tr>
                <tr>
                <?php foreach ($headers as $key => $header): ?>
                    <td>
                        <a class="browse-button" href="<?php echo html_escape(url(array('controller' => 'items', 'action' => 'browse'), 'default', 
                                array('collection' => $collectionId,'range' => $itemId, 
                                    'advanced' => array(array('element_id' => $element->id, 'type' => 'is exactly', 'terms' => $header))), true)); ?>" >
                            <i class="fa fa-search"></i><i class="fa fa-search-plus"></i> View items
                        </a>
                   </td>
                <?php endforeach; ?>
                </tr>
                <tr>
                <?php foreach ($headers as $key => $header): ?>
                    <td>
                        <?php
                            if ($statusElements[$elementId]['steppable']):
                            $text = ($header == 'Proposed') ? __('Assign Status') : (($header == 'Published') ? __('Refresh') : __('Stage'));
                            printf('<a class="operation-button-'.$header.'" href="%s">%s</a>',
                                html_escape(url('ariadne-plus-monitor/index/stage', array('url' => WEB_ROOT,'record_type' => $record_type, 'element' => $element->id, 
                                                'collection' => $collectionId,'mode' => $mode, 'item' => $itemId, 'term' => $header))), $text);
                            endif;
                        ?>
                    </td>
                <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<?php endforeach; ?>
<section class="ten columns alpha omega">
    <div id='ariadne-log-panel' class="panel">
        <h2><?php echo __('Logs'); ?></h2>
        <button id="refresh-logs" >Refresh Logs</button>
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
                <?php $key = 0; ?>
                <?php
                foreach (loop('AriadnePlusLogEntry') as $logEntry):
                ?>
                <tr class="ariadneplus-log-entry <?php echo ++$key%2 == 1 ? 'odd' : 'even'; ?>">
                    <td><?php echo $logEntry->added; ?></td>
                    <td colspan="2">
                        <a href="<?php
                        echo url(array(
                                'type' => Inflector::tableize($logEntry->record_type),
                                'id' => $logEntry->record_id,
                            ), 'history_log_record_log'); ?>"><?php
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
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php fire_plugin_hook('ariadne_plus_monitor_stat_element', array('view' => $this)); ?>
<?php else: ?>
    <br class="clear" />
    <p><?php
        echo __('Your query returned no result.');
        $statusElements = $this->monitor()->getStatusElements(true, null, true);
        if (empty($statusElements)):
            echo ' ' . __('There is no element that can be used as a status (a Monitor element with terms and unrepeatable).');
            echo ' ' . __('Check the options of the elements in Settings > Elements sets > "Monitor".');
        endif;
    ?></p>
<?php endif; ?>
 <?php endif; ?>
</div>
<?php echo foot(); 
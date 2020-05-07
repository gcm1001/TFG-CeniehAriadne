<?php
$pageTitle = __('AriadnePlus Monitor');
echo head(array(
    'title' => $pageTitle,
    'bodyclass' => 'ariadne-plus-monitor index',
));
?>	
<div id="explanation">
    <?php if (isset($_GET["record_type"]) && (isset($_GET["collection"]) || isset($_GET["item"]))): 
        $id = isset($_GET["collection"]) ? $collectionId : $itemId;
        $record = get_record_by_id($record_type,$id);
    ?>
        <h1> <?php echo link_to($record,'show',$record_type." ".$id); ?></h1> 
        <p>
          <button type="button" id="restart">Change</button>
        </p> 
    <?php else: ?>
        <img alt="" id="ariadnelogo" src="<?php echo img('ariadne-monitor-logo.png'); ?>" />
        <p>In this window you can manage the metadata import process to <br> Ariadne+.</p>
        <p>
          <button type="button" id="start">Start</button>
        </p> 
    <?php endif; ?>
</div>
<div id="selectoptions" style="display:none;">
    <div class="center-div-step">
        <div id="questionsdiv">
            <div class="container-step initial-active-area">
                <div class="line">
                  <div class="step first"><img alt="Step 1" id="step-1" src="<?php echo img('step-1.png'); ?>" /></i></div>
                  <div class="step second"> <img alt="Step 2" id="step-2" src="<?php echo img('step-2.png'); ?>" /></div>
                  <div class="step third"> <img alt="Step 3" id="step-3" src="<?php echo img('step-3.png'); ?>" /></div>
                </div>
                <div class="steps">
                    <div class="option-submission">
                        <form class="submission first-step">
                            <p>Select the type of record you would like manage.</p>
                            <label class="input">
                                <?php echo $this->formSelect('record_type', null, array('id' => 'record-type'), $options_for_select_type); ?> <br/><br/>
                            </label>
                            <button class="first next">Continue</button>
                        </form>
                        <form class="submission second-step">
                            <p>Choose the specific record.</p>
                            <label class="input">              
                                <?php echo $this->formSelect('collection', null, array('id' => 'record-id-col'), $options_for_select_collection); ?> 
                                <?php echo $this->formSelect('item', null, array('id' => 'record-id-item'), $options_for_select_item); ?> <br/><br/>
                            </label>
                            <button class="second next">Continue</button>
                        </form>
                        <form class="submission third-step">
                            <h5>Selected record : <span class="selected-record"></span></h5>
                                <div class="switcher">
                                    <h5>Full publication mode: 
                                    <input type="checkbox" name="mode" id="switch" />
                                    <label for="switch"></label>
                                    </h5>
                                </div>
                          <button class="third next">Go!</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="primary">
<?php echo flash(); ?>
    <?php if (isset($_GET["record_type"]) && (isset($_GET["collection"]) || isset($_GET["item"]))): ?>
        <?php if (!empty($results)): ?>
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
                    <h2><?php echo $element->name;?></h2>
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
                            foreach ($result['All'] as $period => $row): ?>
                                <td>
                                    <?php echo $row; ?>
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
                                    <?php if ($statusElements[$elementId]['steppable']):
                                        $text = ($header == 'Proposed') ? __('Assign Status') : (($header == 'Published') ? __('Refresh') : __('Stage')); 
                                        printf('<a class="operation-button '.trim(strtolower($text)).'" href="%s">',
                                            html_escape(url('ariadne-plus-monitor/index/stage', array('url' => WEB_ROOT,'record_type' => $record_type, 'element' => $element->id, 
                                                            'collection' => $collectionId,'mode' => $mode, 'item' => $itemId, 'term' => $header))));?>
                                        <i class="fa fa-play-circle"></i><i class="fa fa-play"></i> <?php echo $text; ?>
                                        </a>   
                                    <?php endif; ?>
                                                                            
                                </td>
                            <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endforeach; ?>
            <section class="ten columns alpha omega">
                <?php 
                echo $this->Monitor()->showlogs($record, $mode); ?>
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
<?php
/**
 * Controller for Ariadneplus Monitor admin pages.
 *
 * @package AriadneplusMonitor
 */
class AriadnePlusMonitor_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Initialize with the AriadnePlusLogEntry table to simplify queries.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('AriadnePlusLogEntry');
    }
    

    /**
     * Main view of the monitor.
     *
     * This administrative metadata will enable the project to keep accurate
     * statistics on progress, identify documents that are ready for the next
     * stage in workflow, and select documents ready to be published at each
     * quarter without having to create a separate control database or system.
     */
    public function indexAction()
    {
        if (!$this->_getParam('sort_field')) {
            $this->_setParam('sort_field', 'added');
            $this->_setParam('sort_dir', 'd');
        }
        
        parent::browseAction();
        
        $this->view->params = $this->getAllParams();
        // Respect only GET parameters when browsing.
        $this->getRequest()->setParamSources(array('_GET'));

        // Inflect the record type from the model name.
        $pluralName = $this->view->pluralize($this->_helper->db->getDefaultModelName());

        $params = $this->getAllParams();
        $zendParams = array(
            'admin' => null, 'module' => null, 'controller' => null, 'action' => null,
        );
        $params = array_diff_key($params, $zendParams);

        // Set internal params: list of all status elements.
        $statusElements = array();
        if (empty($params['element'])) {
            // Set default elements: unique, steppable or not and with terms.
            $statusElements = $this->view->monitor()->getStatusElements(true, null, true);
            $params['element'] = array_keys($statusElements);
        }
        // Check element and set it as array.
        else {
            // Check the element.
            $statusElement = $this->view->monitor()->getStatusElement($params['element'], true, null, true);
            if ($statusElement) {
                // Set it as array to simplify next process.
                $statusElements = array($params['element'] => $statusElement);
                $params['element'] = (array) $params['element'];
            }
        }

        $record_type = ($this->getParam('record_type')) ? $this->getParam('record_type') : '';
        $collectionId = ($this->getParam('collection')) ? $this->getParam('collection') : '';
        $itemId = ($this->getParam('item')) ? $this->getParam('item') : '';
        $mode = ($this->getParam('mode')) ? $this->getParam('mode') : '';
        
        $this->view->collectionId = $collectionId;
        $this->view->itemId = $itemId;
        $this->view->record_type = $record_type;
        $this->view->mode = $mode;
        
        $this->view->options_for_select_type = $this->_getOptions(array('opSel' => $record_type, 'options' => array('' => 'Select below', 'Collection' => 'Collection', 'Item' => 'Item')));
        $this->view->options_for_select_collection = $this->_getOptionsForSelectCollection($collectionId);
        $this->view->options_for_select_item = $this->_getOptionsForSelectItem($itemId);
        
        // A second check may be needed if there are no unique elements.
        if (empty($statusElements)) {
            $this->view->results = array();
            return;
        }

        $this->view->params = $params;
            
        $db = get_db();
        foreach($params['element'] as $elementId){
            $terms =  explode("\n", $db->query("SELECT terms FROM `$db->SimpleVocabTerm` WHERE element_id = '$elementId'")->fetch()['terms']);
            foreach($terms as $term){
                if($collectionId && $itemId){
                    $n = $db->query("SELECT COUNT(*) as n
                                    FROM `$db->ElementText` INNER JOIN `$db->Item` ON `$db->ElementText`.record_id = `$db->Item`.id
                                    WHERE element_id='$elementId' AND text='$term' AND record_type='Item' AND collection_id=$collectionId AND `$db->Item`.id = $itemId")->fetch()['n'];
                } elseif ($collectionId){
                    $n = $db->query("SELECT COUNT(*) as n
                                    FROM `$db->ElementText` INNER JOIN `$db->Item` ON `$db->ElementText`.record_id = `$db->Item`.id
                                    WHERE element_id='$elementId' AND text='$term' AND record_type='Item' AND collection_id=$collectionId")->fetch()['n'];
                } elseif ($itemId){
                    $n = $db->query("SELECT COUNT(*) as n
                                    FROM `$db->ElementText` WHERE element_id='$elementId' AND text='$term' AND record_type='Item' AND record_id = $itemId")->fetch()['n'];
                } else {
                    $n = $db->query("SELECT COUNT(*) as n
                        FROM `$db->ElementText` WHERE element_id='$elementId' AND text='$term' AND record_type='Item'")->fetch()['n'];
                }

                if($n > 0) $result[] = array('element_id' => $elementId, 'text' => $term, 'Count' => $n);
            }
        }

        if (empty($result)) {
            $this->view->results = array();
            return;
        }

        $stats = array();
        
        $by = 'All';
        
        foreach ($statusElements as $elementId => $element) {
            $stats[$elementId][$by] = array_fill_keys($element['terms'], 0);
        }
            // Convert the results in the new array.
        foreach ($result as $key => $row) {
            $stats[$row['element_id']][$by][$row['text']] = $row['Count'];
        }
        
        // Reduce memory?
        unset($result);
        $this->view->results = $stats;

    }
    
    /**
     * Update selected records into the next term.
     */
    public function stageAction()
    {
        $flashMessenger = $this->_helper->FlashMessenger;
        $elementId = $this->getParam('element');
        $term = $this->getParam('term');
        $collection = $this->getParam('collection');
        $item = $this->getParam('item');
        $url = $this->getParam('url');
        $mode = $this->getParam('mode');
        $record_type = $this->getParam('record_type');
        if (!empty($elementId) && !empty($term)) {
            $statusElement = get_view()->monitor()
                // Only elements unique, steppable and with terms can be staged.
                ->getStatusElement($elementId, true, true, true);
            $element = $statusElement['element'];
            if (!empty($statusElement)) {
                $key = array_search($term, $statusElement['terms']);
                if ($key < count($statusElement['terms']) - 1) {
                    $options = array();
                    $options['record_type'] = $record_type;
                    $options['element'] = $element->id;
                    $options['collection'] = $collection;
                    $options['mode'] = $mode;
                    $options['item'] = $item;
                    $options['term'] = $term;
                    $options['url'] = $url;
                    $jobDispatcher = Zend_Registry::get('bootstrap')->getResource('jobs');
                    $jobDispatcher->setQueueName(AriadnePlusMonitor_Job_Stage::QUEUE_NAME);
                    $jobDispatcher->sendLongRunning('AriadnePlusMonitor_Job_Stage', $options);
                    if($key == 0){
                        $message = __('A background job process is launched to assign status to element "%s".',
                            $element->name)
                            . ' ' . __('This may take a while.');
                    } elseif ($key == 6) {
                        $message = __('A background job process is launched to refresh published elements. This may take a while.');
                    } else {
                        $message = __('A background job process is launched to stage "%s" into "%s" for element "%s".',
                            $term, $statusElement['terms'][$key +1], $element->name)
                            . ' ' . __('This may take a while.');                       
                    }
                    $flashMessenger->addMessage($message, 'success');
                }
            }
        }
        if (!isset($options)) {
            $flashMessenger->addMessage(__('Stage cannot be done with element #%s and term "%s".',
                $elementId, $term), 'error');
        }
        $search = 'ariadne-plus-monitor?record_type='.$record_type;
        if($collection) $search = $search.'&collection='.$collection;
        if($item) $search = $search.'&item='.$item;
        if($mode) $search = $search.'&mode='.$mode;
        return $this->redirect($search);
    }
    
    private function _getOptionsForSelectCollection($collectionId)
    {
        $collections = get_records( 'Collection', array('sort_field' => 'id', 'sort_dir' => 'a'),9999);
        $options = array('' => __('Select below'));
        foreach ($collections as $collection) {
            if (metadata($collection,array('Dublin Core', 'Title'))) {
                $col =  $collection->id.'. '.metadata($collection,array('Dublin Core', 'Title'));
            } else {
                $col =  $collection->id.'. No title';
            }
            $options[$collection->id] = $col;
            release_object($collection);
        }
        if(!empty($collectionId) && array_key_exists($collectionId, $options)){
            $actual_col = $options[$collectionId];
            unset($options[$collectionId]);
            unset($options['']);
            $options = array($collectionId => $actual_col) + $options;
        }
        return $options;
    }

    private function _getOptionsForSelectItem($itemId)
    {
        $items = get_records( 'Item', array('sort_field' => 'id', 'sort_dir' => 'a'),9999);
        $options = array('' => __('Select below'));
        foreach ($items as $item) {
            if (metadata($item,array('Dublin Core', 'Title'))) {
                $it =  $item->id.'. '.metadata($item,array('Dublin Core', 'Title'));
            } else {
                $it =  $item->id.'. No title';
            }
            $options[$item->id] = $it;
            release_object($item);
        }
        if(!empty($itemId) && array_key_exists($itemId, $options)){
            $actual_it = $options[$itemId];
            unset($options[$itemId]);
            unset($options['']);
            $options = array($itemId => $actual_it) + $options;
        }
        return $options;
    }
  
    private function _getOptions($args)
    {
        $options = $args['options'];
        
        if(empty($args['opSel'])){
            return $options;
        }
        if (array_key_exists($args['opSel'], $options)){
            $opSel = $args['opSel'];
            unset($options[$opSel]);
            unset($options['']);
            $options = array($opSel => $args['options'][$opSel]) + $options;
        }
        return $options;
    }
    
}

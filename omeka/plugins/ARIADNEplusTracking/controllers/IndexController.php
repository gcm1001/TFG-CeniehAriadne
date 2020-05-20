<?php
/**
 * Controller for Ariadneplus Tracking.
 *
 * @package AriadneplusTracking
 */
class ARIADNEplusTracking_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = self::RECORDS_PER_PAGE_SETTING;

    /**
     * Initialize with the ARIADNEplusLogEntry table to simplify queries.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ARIADNEplusTrackingTicket');
    }
    

    /**
     * Main view of the tracking.
     *
     * This administrative metadata will enable the project to keep accurate
     * statistics on progress, identify documents that are ready for the next
     * stage in workflow, and select documents ready to be published at each
     * quarter without having to create a separate control database or system.
     */
    public function indexAction()
    {   
        if (!$this->getRequest()->isPost()) {
            return;
        }
          
        //TABLEPOST
        $post = $this->getRequest()->getPost();

        $record_type = $post['record_type'];
        $record_id = $post['record_id'];
        
        if($record_type && $record_id){
            $this->redirect('ariadn-eplus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id);
        } else {
            $this->_helper->flashMessenger(__('Something is wrong.'), 'error');
            return;
        }
        
    }
    
    /**
     * Shows the new Form for create a new ticket.
     *
     * @return type
     */
    public function newAction(){
        
        $this->view->options_for_select_type = $this->_getOptions(array('options' => array('' => 'Select below', 'Collection' => 'Collection', 'Item' => 'Item')));
        $this->view->options_for_select_collection = $this->_getOptionsForSelectCollection();
        $this->view->options_for_select_item = $this->_getOptionsForSelectItem();
        $terms = $this->view->tracking()->getTermsByName('ARIADNEplus Category');
        $this->view->options_for_select_category = $this->_getOptions(array('options' => $terms));
        
        if (!$this->getRequest()->isPost()) {
            return;
        }
        
        $post = $this->getRequest()->getPost();
        
        $record_type = $post['record_type'];
        $record_id = isset($post['collection']) ? $post['collection'] : $post['item'];
        $ariadne_category_key = $post['ariadne_category'];
        
        $record = get_record_by_id($record_type, $record_id);
        
        if($this->_isValidNewRecord($record)){
            $this->_updateRecord(array('record' => $record,
                'elementTexts'=>array('ARIADNEplus Category' => $terms[$ariadne_category_key],'Metadata Status'=>'Proposed') ,
                'elementSet' => 'Monitor'));
            $newticket = new ARIADNEplusTrackingTicket();
            $newticket->newTrackingTicket($record, 'Proposed', current_user());
            $newticket->save();
            release_object($record);
        } else {
            $this->_helper->flashMessenger(__('Invalid record. Please see errors above and try again.'), 'error');
            return;
        }
        
        $this->redirect('ariadn-eplus-tracking');
    }
    
    /**
     * Update a record.
     * 
     * @param type $args Record & Element Set Name & Element Texts to update
     * @return type
     */
    protected function _updateRecord($args){
        $record = $args['record'];
        $elementSet = $args['elementSet'];
        $elementTexts = $args['elementTexts'];
        $data = [];
        if($record){
            $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
            );
            foreach($elementTexts as $element => $text){
                $data[$elementSet][$element][] = array(
                'text' => $text,
                'html' => false,
                );
            }
            switch(get_class($record)){
                case 'Collection':
                    update_collection($record, $metadata, $data);
                    break;
                case 'Item':
                    update_item($record, $metadata, $data);
                    break;
                default:
                    return;
            }
        }
        return;
    }
    
    /**
     * Check if an item is suitable to create a new ticket.
     * 
     * @param type $record Record
     * @param type $flasmessenger Show flash messenger
     * @return boolean Is valid?
     */
    protected function _isValidNewRecord($record, $flasmessenger=true){
        if(empty($record)){
            return false;
        }
        $ticketId = $this->view->Tracking()->recordInTrackingTicket($record);
        if($ticketId > 0){
            if($ticketId == 1 && $flasmessenger){
                $this->_helper->flashMessenger(__('The record is already in tracking. Go to the main page and check it out.'), 'error'); 
            } else if ($ticketId == 2 && $flasmessenger){
                $this->_helper->flashMessenger(__('The item belongs to a collection that is already in tracking.'), 'error'); 
            }
            return false;
        }
        
        $status = metadata($record, array('Monitor', 'Metadata Status'));
        if(!empty($status)){
            return false;
        }
        return true;
    }

    /**
     * Sends a message.
     * 
     * @return type 
     */
    public function mailAction(){
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $post = $this->getRequest()->getPost();
        $flashMessenger = $this->_helper->FlashMessenger;
        $record_type = $post['record_type'];
        $record_id = $post['record_id'];
        $body = $post['msg_content'];
        $from = get_option('administrator_email');
        $email = get_option('ariadneplus_tracking_email');
        if(!empty($body) && !empty($from) && !empty($email)){
            $siteTitle  = get_option('site_title');
            $name = get_option('ariadneplus_tracking_name');
            $subject = __("%s - Metadata import", $siteTitle);
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($body);
            $mail->setFrom($from, "$siteTitle Administrator");
            $mail->addTo($email, $name);
            $mail->setSubject($subject);
            $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
            $mail->send();
        } else {
            $flashMessenger->addMessage(('The message has not been sent.'), 'error'); 
        }
        $search = 'ariadn-eplus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id;
        return $this->redirect($search);
    }
    
    /**
     * Shows all phases.
     * 
     * @return type
     */
    public function ticketAction(){
        // Respect only GET parameters when browsing.
        $this->getRequest()->setParamSources(array('_GET'));
        $params = $this->getAllParams();
        
        $statusElements = $this->view->tracking()->getStatusElements(true, null, true);
        $params['element'] = array_keys($statusElements);
            
        $record_type = (isset($params['record_type'])) ? $params['record_type'] : '';
        
        switch($record_type){
            case 'Collection':
                if(!isset($params['collection'])) return;
                $record_id = $params['collection'];
                $record = get_record_by_id('Collection', $record_id);
                $this->view->record = $record;
                break;
            case 'Item':
                if(!isset($params['item'])) return;
                $record_id = $params['item'];
                $record = get_record_by_id('Item', $record_id);
                $this->setParam('range', $record_id);
                break;
            default:
                return;
        }
        if($record === null) return;
        
        $ticket = $this->view->tracking()->getRecordTrackingTicket($record);
        $level = $this->view->tracking()->getLevelStatus($ticket->status);
        $this->view->record = $record;
        $this->view->ticket = $ticket;
        $this->view->level = $level;
     
        if($level == 0 || $level == 1){
          $this->_helper->db->setDefaultModelName('Item');
          parent::browseAction();
        }
  
        $elementId = reset($params['element']);
        $this->view->elementId = $elementId;
        
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if($level == 2){
                if(isset($post['mode'])){
                    $ticket->setMode($post['mode']);
                    $ticket->save();
                }
                if (isset($post['map-identifier'])){
                    $this->_updateRecord(array('record' => $record,
                    'elementTexts'=>array('ID of your metadata transformation' => $post['map-identifier']) ,
                    'elementSet' => 'Monitor'));
                }
                if(isset($post['mode']) && isset($post['map-identifier'])){
                    $this->redirect('ariadn-eplus-tracking/index/stage?'. 
                                        'url='.WEB_ROOT.
                                        '&record_type='.get_class($record). 
                                        '&element='.$elementId. 
                                        '&record_id='.$record->id. 
                                        '&term='.$ticket->status);
                }
            } else if ($level == 3){
                if (isset($post['periodo'])){
                    $this->_updateRecord(array('record' => $record,
                    'elementTexts'=>array('URL of your PeriodO collection' => $post['periodo']) ,
                    'elementSet' => 'Monitor'));
                    $this->redirect('ariadn-eplus-tracking/index/stage?'. 
                                        'url='.WEB_ROOT.
                                        '&record_type='.get_class($record). 
                                        '&element='.$elementId. 
                                        '&record_id='.$record->id. 
                                        '&term='.$ticket->status);
                }
            } 
        }
    }
    
    /**
     * Update selected records into the next term.
     * 
     */
    public function stageAction()
    {
        $flashMessenger = $this->_helper->FlashMessenger;
        $elementId = $this->getParam('element');
        $term = $this->getParam('term');
        $record_id = $this->getParam('record_id');
        $record_type = $this->getParam('record_type');
        if (!empty($elementId) && !empty($term) && !empty($record_id) && !empty($record_type)) {
            $statusElement = get_view()->tracking()
                // Only elements unique, steppable and with terms can be staged.
                ->getStatusElement($elementId, true, true, true);
            $element = $statusElement['element'];
            if (!empty($statusElement)) {
                $key = array_search($term, $statusElement['terms']);
                if ($key < count($statusElement['terms']) - 1) {
                    $options = array();
                    $options['record_type'] = $record_type;
                    $options['record_id'] = $record_id;
                    $options['element'] = $element->id;
                    $options['term'] = $term;
                    $jobDispatcher = Zend_Registry::get('bootstrap')->getResource('jobs');
                    $jobDispatcher->setQueueName(ARIADNEplusTracking_Job_Stage::QUEUE_NAME);
                    $jobDispatcher->sendLongRunning('ARIADNEplusTracking_Job_Stage', $options);
                    $message = $this->_getStageMessage(array('term' => $term, 'newterm' => $statusElement['terms'][$key +1], 'key' => $key));
                    $flashMessenger->addMessage($message, 'success');
                }
            }
        }
        if (!isset($options)) {
            $flashMessenger->addMessage(__('Stage cannot be done with element #%s and term "%s ---> %s ----> %s".',
                $elementId, $term, $record_id,$record_type), 'error');
        }
        $search = 'ariadn-eplus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id;
        
        return $this->redirect($search);
    }
    private function _getStageMessage($args){
        $key = $args['key'];
        if($key == 0){
            $message = __('A background job ticket is launched to assign a status'. __('This may take a while.'));
        } elseif ($key == 6) {
            $message = __('A background job ticket is launched to refresh published elements. This may take a while.');
        } else {
            $message = __('A background job ticket is launched to stage "%s" into "%s".',
                $args['term'], $args['newterm'])
                . ' ' . __('This may take a while.');                       
        }
    }
    /**
     * Returns options for the select that is used to choose a collection.
     * 
     * @return string Options
     */
    private function _getOptionsForSelectCollection()
    {
        $collections = get_records( 'Collection', array('sort_field' => 'id', 'sort_dir' => 'a'),9999);
        $options = array('' => __('Select below'));
        foreach ($collections as $collection) {
            if($this->_isValidNewRecord($collection,false)){
                $col =  $collection->id.'. Untitled';
                if (metadata($collection,array('Dublin Core', 'Title'))) {
                    $col =  $collection->id.'. '.metadata($collection,array('Dublin Core', 'Title'));
                }
                $options[$collection->id] = $col;
                release_object($collection);
            }
        }
        return $options;
    }

    /**
     * Returns options for the select that is used to choose an item.
     * 
     * @return string Options
     */
    private function _getOptionsForSelectItem()
    {
        $items = get_records( 'Item', array('sort_field' => 'id', 'sort_dir' => 'a'),9999);
        $options = array('' => __('Select below'));
        foreach ($items as $item) {
            if($this->_isValidNewRecord($item,false)){
                $it =  $item->id.'. Untitled';
                if (metadata($item,array('Dublin Core', 'Title'))) {
                    $it =  $item->id.'. '.metadata($item,array('Dublin Core', 'Title'));
                } 
                $options[$item->id] = $it;
                release_object($item);
            }
        }
        return $options;
    }
  
    /**
     * Returns options for a given array.
     * 
     * @param type $args
     * @return type
     */
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

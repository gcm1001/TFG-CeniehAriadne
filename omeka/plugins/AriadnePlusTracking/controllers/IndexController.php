<?php
/**
 * Controller for ARIADNEplus Tracking.
 *
 * @package ARIADNEPlusTracking
 */
class AriadnePlusTracking_IndexController extends Omeka_Controller_AbstractActionController
{
    protected $_browseRecordsPerPage = self::RECORDS_PER_PAGE_SETTING;

    /**
     * Initialize with the ARIADNEplusTrackingTicket table to simplify queries.
     */
    public function init()
    {
        $this->_helper->db->setDefaultModelName('AriadnePlusTrackingTicket');
    }
    

    /**
     * Main view of the tracking plugin.
     *
     */
    public function indexAction()
    {   
        //Must be logged in to view tickets specific to certain users
        if ($this->_getParam('user') && !$this->_helper->acl->isAllowed('browse', 'Users')) {
            $this->_setParam('user', null);
            // Zend re-reads from GET/POST on every getParams() so we need to
            // also remove these.
            unset($_GET['user'], $_POST['user']);
        }
        $this->view->limit = 5;
        parent::browseAction();
        
        if (!$this->getRequest()->isPost()) return;
        
        // TABLEPOST
        $post = $this->getRequest()->getPost();
        // RECORD INFO
        $record_type = $post['record_type'];
        $record_id = $post['record_id'];
        
        if($record_type && $record_id){
            $this->redirect('ariadne-plus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id);
        } else {
            $this->_helper->flashMessenger(__('Something is wrong.'), 'error');
            return;
        }
    }
    protected function _getBrowseDefaultSort()
    {
        return array('lastmod', 'd');
    }    
    /**
     * Shows the new Form to create a new ticket.
     */
    public function newAction(){
        $this->view->options_for_select_type = array('' => 'Select below', 'Collection' => 'Collection', 'Item' => 'Item');
        $this->view->options_for_select_collection = $this->_getOptionsForSelectCollection();
        $this->view->options_for_select_item = $this->_getOptionsForSelectItem();
        $terms = $this->view->tracking()->getTermsByName('ARIADNEplus Category');
        $this->view->options_for_select_category = (array('' => 'Select below') + $terms);
        
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
            $newticket = new AriadnePlusTrackingTicket();
            $newticket->newTrackingTicket($record, 'Proposed', current_user());
            $newticket->save();
        } else {
            $this->_helper->flashMessenger(__('Invalid record. Please see errors above and try again.'), 'error');
            return;
        }
        $this->_helper->flashMessenger(__('Ticket created.'), 'success');
        $this->redirect('ariadne-plus-tracking');
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
        $email = $post['msg_email'];
        $subject = $post['msg_subject'];
        if(!empty($body) && !empty($from) && !empty($email)){
            $siteTitle  = get_option('site_title');
            $name = get_option('ariadneplus_tracking_name');
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText(strip_tags($body));
            $mail->setBodyHtml($body);
            $mail->setFrom($from, "$siteTitle Administrator");
            $mail->addTo($email, $name);
            $mail->setSubject($subject);
            $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
            $mail->send();
        } else {
            $flashMessenger->addMessage(('The message has not been sent.'), 'error'); 
        }
        $search = 'ariadne-plus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id;
        return $this->redirect($search);
    }
    
    /**
     * Shows all phases.
     * 
     * @return type
     */
    public function ticketAction(){
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
        
        $ticket = $this->view->tracking()->getTicketByRecordId($record->id);
        if(!$ticket) return;
        $level = $this->view->tracking()->getLevelStatus($ticket->status);
        $this->view->record = $record;
        $this->view->ticket = $ticket;
        $this->view->level = $level;
        $this->view->hide = isset($params['advanced']); 
        if($level <= 1){
          $this->_helper->db->setDefaultModelName('Item');
          parent::browseAction();
        }
        $elementId = reset($params['element']);
        $this->view->elementId = $elementId;
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            $this->_redirectByPost(array('post' => $post, 'level' => $level, 'ticket' => $ticket, 'record' => $record, 'elementId' => $elementId));
        }
    }
    
    protected function _redirectByPost($args){
        $post = $args['post'];
        $level = $args['level'];
        $ticket = $args['ticket'];
        $record = $args['record'];
        $elementId = $args['elementId'];
        if($level == 2){
            if(isset($post['mode'])){
                $ticket->setMode($post['mode']);
                $ticket->save();
            }
            if (isset($post['map-identifier'])){
                $this->_updateRecord(array('record' => $record, 'elementTexts'=>array('ID of your metadata transformation' => $post['map-identifier']) , 'elementSet' => 'Monitor'));
            }
            if(isset($post['mode']) && isset($post['map-identifier'])){
                $this->redirect('ariadne-plus-tracking/index/stage?url='.WEB_ROOT.'&record_type='.get_class($record).'&element='.$elementId.'&record_id='.$record->id.'&term='.$ticket->status);
            }
        } else if ($level == 3 && isset($post['periodo'])){
            $this->_updateRecord(array('record' => $record, 'elementTexts'=>array('URL of your PeriodO collection' => $post['periodo']), 'elementSet' => 'Monitor'));
            $this->redirect('ariadne-plus-tracking/index/stage?'.'url='.WEB_ROOT.'&record_type='.get_class($record).'&element='.$elementId.'&record_id='.$record->id.'&term='.$ticket->status);
        } else if ($level == 4 && isset($post['sparql'])){
            $this->_updateRecord(array('record' => $record, 'elementTexts'=>array('Ghost SPARQL' => $post['sparql']) , 'elementSet' => 'Monitor'));
            $this->redirect('ariadne-plus-tracking/index/stage?url='.WEB_ROOT.'&record_type='.get_class($record).'&element='.$elementId.'&record_id='.$record->id.'&term='.$ticket->status);
        }
    }
    
    /**
     * Update selected records into the next term.
     */
    public function stageAction()
    {
        if (!$this->getRequest()->isGet()) return;
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
            if (!empty($statusElement)){ 
                $key = array_search($term, $statusElement['terms']);
                if($key < count($statusElement['terms']) - 1) {
                    $options = array();
                    $options['record_type'] = $record_type;
                    $options['record_id'] = $record_id;
                    $options['element'] = $element->id;
                    $options['term'] = $term;
                    $jobDispatcher = Zend_Registry::get('bootstrap')->getResource('jobs');
                    $jobDispatcher->setQueueName(AriadnePlusTracking_Job_Stage::QUEUE_NAME);
                    $jobDispatcher->sendLongRunning('AriadnePlusTracking_Job_Stage', $options);
                    $message = $this->_getStageMessage(array('term' => $term, 'newterm' => $statusElement['terms'][$key +1], 'key' => $key));
                    $flashMessenger->addMessage($message, 'success');
                }
            }
        }
        if (!isset($options)) {
            $flashMessenger->addMessage(__('Stage cannot be done with element #%s and term "%s ---> %s ----> %s".',
                $elementId, $term, $record_id,$record_type), 'error');
        }
        $search = 'ariadne-plus-tracking/index/ticket?record_type='.$record_type.'&'.strtolower($record_type).'='.$record_id;
        
        return $this->redirect($search);
    }
    
    /**
     * Removes a ticket.
     */
    public function removeAction(){
        if (!$this->getRequest()->isPost()) return;
        
        //TABLEPOST
        $post = $this->getRequest()->getPost();

        $record_type = $post['record_type'];
        $record_id = $post['record_id'];
        
        if($record_type && $record_id){
            $record = get_record_by_id($record_type, $record_id);
            $ticket = $this->view->Tracking()->getTicketByRecordId($record->id);
            if($ticket && $record){
              $ticket->delete();
              $elementSet = $this->view->Tracking()->getElementSet();
              $elements = $elementSet->getElements();
              $ids = [];
              foreach($elements as $element) {
                  $ids[] = $element->id;
              }
              $record->deleteElementTextsByElementId($ids);
              if($record_type == 'Collection'){
                  $subrecords = get_records('Item',array('collection'=> $record->id),0);
                  foreach($subrecords as $subrecord){
                    $subrecord->deleteElementTextsByElementId($ids);
                  }
              }
              return $this->redirect('ariadne-plus-tracking');
            }
        }
        $this->_helper->FlashMessenger->addMessage(__('ERROR: Ticket could not be removed'), 'error');
        return $this->redirect('ariadne-plus-tracking');
    }
    
    public function renewAction(){
        if (!$this->getRequest()->isPost()) return;
        
        //TABLEPOST
        $post = $this->getRequest()->getPost();

        $record_type = $post['record_type'];
        $record_id = $post['record_id'];
        
        if($record_type && $record_id){
            $record = get_record_by_id($record_type, $record_id);
            $ticket = $this->view->Tracking()->getTicketByRecordId($record->id);
            if($ticket && $record){
              $newTerm = 'Proposed';
              $ticket->setStatus($newTerm);
              $ticket->save();
              $this->_updateRecord(array('record' => $record, 'elementTexts'=>array('Metadata Status' => $newTerm) , 'elementSet' => 'Monitor'));
              return $this->redirect('ariadne-plus-tracking');
            }
        }
        $this->_helper->FlashMessenger->addMessage(__('ERROR: Ticket could not be renewed'), 'error');
        return $this->redirect('ariadne-plus-tracking');
    }
    
    /**
    * Update a record.
    * 
    * @param type $args Record & Element Set Name & Element Texts to update
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
     * Returns a message with some information about the stage.
     * 
     * @param type $args 
     */
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
}

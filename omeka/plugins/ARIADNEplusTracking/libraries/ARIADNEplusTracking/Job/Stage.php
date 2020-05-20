<?php
/**
 * Stage the selected element of selected or all matching records to next term.
 */
class ARIADNEplusTracking_Job_Stage extends Omeka_Job_AbstractJob
{
    const QUEUE_NAME = 'ariadneplus_tracking_stage';

    private $_logentry;
    private $_statusElement;
    
    public function perform()
    {
        $this->_db = get_db();
        $view = get_view();
        $element = $this->_options['element'];
        $statusElement = $view->tracking()
            ->getStatusElement($element, true, true, true);
        $this->_statusElement = $statusElement;
        if (empty($statusElement)) {
            throw new RuntimeException(__('Element "%s" is not a workflow, has no vocabulary or is a repeatable field.', $element));
        }
        $element = $statusElement['element'];
        $term = $this->_options['term'];
        if (!in_array($term, $statusElement['terms'])) {
            throw new RuntimeException(__('The term "%s" is not in the vocabulary of element %s.', $term, $element->name));
        }
        $key = array_search($term, $statusElement['terms']);
        if ($key >= count($statusElement['terms']) - 1) {
            $this->_log(__('The term "%s" is the last one of the vocabulary of element %s.', $term, $element->name));
            return;
        }
        // All is fine.
        $newTerm = $statusElement['terms'][$key + 1];
        $elementSet = $element->getElementSet();
        // Record
        $record_type = $this->_options['record_type'];
        $record_id = $this->_options['record_id'];
        $stageRecord = get_record_by_id($record_type, $record_id);
        
        // Ids
        switch($record_type){
            case 'Collection':
                $collectionId = $record_id;
                $itemId = '';
                break;
            case 'Item':
                $collectionId = '';
                $itemId = $record_id;
                break;
            default:
                break;
        }
        //Get all items 
        $records = get_records('Item', array('collection' => $collectionId,
                'range' => $itemId,
                'advanced' => array(array(
                'element_id' => $element->id,
                'type' => 'is exactly',
                'terms' => $term,
            )),
        ), 0);
        // Operation
        $operation = null;
        $incompleteRecords = [];
        if($key==0){
            $operation = ARIADNEplusLogEntry::OPERATION_ASSIGN;
        } elseif ($key > 0 && $key < 6) {
            $operation = ARIADNEplusLogEntry::OPERATION_STAGE;
        } elseif ($key == 6) {
            $operation = ARIADNEplusLogEntry::OPERATION_REFRESH;
        } 
        if($operation === null){
          return;
        }
        //Logs
        $this->_logentry = new ARIADNEplusLogEntry();
        $this->_logentry->logEvent($stageRecord, $operation, current_user());
        $this->_logentry->save();
        // Stages
        $flag = true;
        if($key == 1 || $key == 0){
            list($records,$incompleteRecords) = $this->_checkMetadataElements($records, $view, $logentry);
            $flag = empty($incompleteRecords);
            $newTerm = $statusElement['terms'][2];
        } else if($key == 2){
            $flag = $this->_isMapped($stageRecord);
            if(!$flag) $records = [];
        } else if($key == 3){
            $flag = $this->_isEnriched($stageRecord);
            if(!$flag) $records = [];
        } 
        // Stage Sub Records (If any)
        $this->_stageSubRecords(array('records' => $records,
            'incompleteRecords' => $incompleteRecords,
            'term' => $newTerm));
        // Stage Record
        $this->_stageRecord(array('term' => $newTerm, 
            'key' => $key, 'stageRecord' => $stageRecord, 'flag' => $flag));
    }
    
    
    protected function _stageSubRecords($args){
        $newTerm = $args['term'];
        $records = $args['records'];
        $incompleteRecords = $args['incompleteRecords'];
        
        $statusElement = $this->_statusElement;
        $element = $statusElement['element'];
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        $elementTexts = [];
        $elementTexts[$element->getElementSet()->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        $count = count($records);
        foreach ($records as $k => $record) {
            $record = update_item($record, $metadata, $elementTexts);
            $msg = __('Element #%d ("%s") of record #%d staged to "%s" (%d/%d).',
                $element->id, $element->name, $record->id, $newTerm, $k + 1, $count);
            $this->_log($msg);
            if($newTerm == 'Complete'){
                $record->public = true;
                $record->save();
            }
            release_object($record);
        }  
        if(!empty($incompleteRecords)){
            $elementTexts = [];
            $newTerm = 'Incomplete';
            $elementTexts[$elementSet->name][$element->name][] = array(
                'text' => $newTerm,
                'html' => false,
            );
            foreach ($incompleteRecords as $k => $record) {
                $record = update_item($record, $metadata, $elementTexts);
                $msg = __('Element #%d ("%s") of record #%d staged to "%s".',
                $element->id, $element->name, $record->id, 'Incomplete');
                $this->_log($msg);
                release_object($record);
                $count += 1;
            }
        }
    }
    
    protected function _stageRecord($args){
        $stageRecord = $args['stageRecord'];
        $newTerm = $args['term'];
        $key = $args['key'];
        $flag = $args['flag'];
        //ticket
        $ticket = get_view()->tracking()->getRecordTrackingTicket($stageRecord);
        $statusElement = $this->_statusElement;
        $element = $statusElement['element'];
       
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        $elementTexts = [];
        $elementTexts[$element->getElementSet()->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        
        $record_type = get_class($stageRecord);
        if($record_type == 'Collection' && ($flag || $key == 0)){
            $collectionid = $stageRecord->id;
            $colState = metadata($stageRecord, array('Monitor', 'Metadata Status'));
            if($colState == $statusElement['terms'][$key]){
                release_object($stageRecord);
                $stageRecord = update_collection(get_record_by_id('Collection',$collectionid),$metadata,$elementTexts);
                $msg = __('Collection #%d staged to "%s".', $stageRecord->id, $newTerm);
                $this->_log($msg);
                if($newTerm == 'Complete'){
                    $stageRecord->public = true;
                    $stageRecord->save();
                }
                release_object($stageRecord);
                $ticket->setStatus($newTerm);
                $ticket->save();
            }
        } else if($flag && $record_type == 'Item'){
            $ticket->setStatus($newTerm);
            $ticket->save();
        }
    }
    

    /**
     * Log a message with generic info.
     *
     * @param string $msg The message to log
     * @param int $priority The priority of the message
     */
    protected function _log($msg, $priority = Zend_Log::INFO)
    {
        $prefix = "[ARIADNEplusTracking][Stage]";
        $this->createLogMsg($msg);
        _log("$prefix $msg", $priority);
    }

    private function createLogMsg($msg){
        $logmsg = new ARIADNEplusLogMsg();
        $logmsg->entry_id = $this->_logentry->id;
        $logmsg->msg = $msg;
        $logmsg->save();
    }

    protected function _checkMetadataElements($records, $view){
        $mandatoryElementsDC = $view->tracking()->getMandatoryDCElements();
        $incompleteRecords = [];
        foreach ($records as $k => $record) {
            //CHECK: DC
            foreach($mandatoryElementsDC as $elementDC) {
                if(empty(metadata($record,array('Dublin Core', $elementDC)))){
                    $incompleteRecords[] = $record;
                    unset($records[$k]);
                    $msg = __('Record #%d is not valid. %s is empty.', $record->id, $elementDC);
                    $this->_log($msg);
                    break;
                }
            }
        }
        return array($records, $incompleteRecords);
    }
    
    protected function _isMapped($record){
        $elementM = 'ID of your metadata transformation';
        if(empty(metadata($record,array('Monitor', $elementM)))){
            $msg = __('%s #%d is not mapped,  %s is empty.',get_class($record), $record->id, $elementM);
            $this->_log($msg);
            return false;
        }
        return true;
    }
    
    protected function _isEnriched($record){
        $elementM = 'URL of your PeriodO collection';
        if(empty(metadata($record,array('Monitor', $elementM)))){
            $msg = __('%s #%d is not enriched, %s is empty.',get_class($record), $record->id, $elementM);
            $this->_log($msg);
            return false;
        }
        return true;
    }
}

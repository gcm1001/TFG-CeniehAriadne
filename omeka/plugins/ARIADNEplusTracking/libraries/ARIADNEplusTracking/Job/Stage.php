<?php
/**
 * Stage the selected element of selected or all matching records to next term.
 */
class ARIADNEplusTracking_Job_Stage extends Omeka_Job_AbstractJob
{
    const QUEUE_NAME = 'ariadneplus_tracking_stage';

    public function perform()
    {
        $this->_db = get_db();
        $view = get_view();
        $element = $this->_options['element'];
        $statusElement = $view->tracking()
            ->getStatusElement($element, true, true, true);

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
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        // Record
        $record_type = $this->_options['record_type'];
        $record_id = $this->_options['record_id'];
        $stageRecord = get_record_by_id($record_type, $record_id);
        //ticket
        $ticket = $view->tracking()->getRecordTrackingTicket($stageRecord);
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
        
        $operation = null;
        if($key==0){
            $incompleteRecords = [];
            $operation = ARIADNEplusLogEntry::OPERATION_ASSIGN;
        } elseif ($key > 0 && $key < 6) {
            $operation = ARIADNEplusLogEntry::OPERATION_STAGE;
        } elseif ($key == 6) {
            $operation = ARIADNEplusLogEntry::OPERATION_REFRESH;
        } 
        
        if($operation === null){
          return;
        }
        
        $logentry = new ARIADNEplusLogEntry();
        $logentry->logEvent($stageRecord, $operation, current_user());
        
        $logentry->save();
        $flag = true;
        // CHECK: Incomplete > Complete 
        if($key == 1 || $key == 0){
            $mandatoryElementsDC = $view->tracking()->getMandatoryDCElements();
            foreach ($records as $k => $record) {
                //CHECK: DC
                foreach($mandatoryElementsDC as $elementDC) {
                    if(empty(metadata($record,array('Dublin Core', $elementDC)))){
                        if($key == 0) {
                            $incompleteRecords[] = $record;
                        }
                        unset($records[$k]);
                        $msg = __('Record #%d is not valid. %s is empty.', $record->id, $elementDC);
                        $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
                        $this->_log($msg);
                        $flag = false;
                        break;
                    }
                }
            }
            $newTerm = $statusElement['terms'][2];
        } else if($key == 2){
            $elementM = 'ID of your metadata transformation';
            if(empty(metadata($stageRecord,array('Monitor', $elementM)))){
                $records = [];
                $msg = __('%s #%d is not mapped,  %s is empty.',$record_type, $stageRecord->id, $elementM);
                $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
                $this->_log($msg);
                $flag = false;
            }
            $newTerm = $statusElement['terms'][3];
        } else if($key == 3){
            $elementM = 'URL of your PeriodO collection';
            if(empty(metadata($stageRecord,array('Monitor', $elementM)))){
                $records = [];
                $msg = __('%s #%d is not enriched, %s is empty.',$record_type, $stageRecord->id, $elementM);
                $this->_log($msg);
                $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
                $flag = false;
            }
            $newTerm = $statusElement['terms'][4];
        } else if($key == 4){
            $newTerm = $statusElement['terms'][5];
        } else if($key == 5){
            $newTerm = $statusElement['terms'][6];
        } else if($key == 6){
            $newTerm = $statusElement['terms'][0];
        }
        
        // Exec Stage
        $elementTexts = [];
        $elementTexts[$elementSet->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        
        $count = count($records);
        foreach ($records as $k => $record) {
            $record = update_item($record, $metadata, $elementTexts);
            $msg = __('Element #%d ("%s") of record #%d staged to "%s" (%d/%d).',
                $element->id, $element->name, $record->id, $newTerm, $k + 1, $count);
            $this->_log($msg);
            $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
            if($newTerm == 'Complete'){
                $record->public = true;
                $record->save();
            }
            release_object($record);
        }     
        if($key==0 && !empty($incompleteRecords)){
            $elementTexts = [];
            $newTerm = $statusElement['terms'][1];
            $elementTexts[$elementSet->name][$element->name][] = array(
                'text' => $newTerm,
                'html' => false,
            );
            foreach ($incompleteRecords as $k => $record) {
                $record = update_item($record, $metadata, $elementTexts);
                $msg = __('Element #%d ("%s") of record #%d staged to "%s".',
                $element->id, $element->name, $record->id, 'Incomplete');
                $this->_log($msg);
                $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
                release_object($record);
                $count += 1;
            }
        }
        if($record_type == 'Collection' && ($flag || ($key == 0))){
            $colState = metadata($stageRecord, array('Monitor', 'Metadata Status'));
            if($colState == $statusElement['terms'][$key] ){
                release_object($stageRecord);
                $stageRecord = update_collection(get_record_by_id('Collection',$collectionId),$metadata,$elementTexts);
                $msg = __('Element #%d ("%s") of collection #%d staged to "%s".',
                    $element->id, $element->name, $stageRecord->id, $newTerm);
                $this->_log($msg);
                $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
                if($newTerm == 'Complete'){
                    $stageRecord->public = true;
                    $stageRecord->save();
                }
                release_object($stageRecord);
                $ticket->setStatus($newTerm);
                $ticket->save();
                $count += 1;
                
            }
        } else if($flag && $record_type == 'Item'){
            $ticket->setStatus($newTerm);
            $ticket->save();
        }
        $msg = __('%d records staged to "%s" for element "%s" (#%d).',
            $count, $newTerm, $element->name, $element->id);
        $this->_log($msg);
        $this->createLogMsg(array('entry_id' => $logentry->id, 'msg' => $msg));
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
        _log("$prefix $msg", $priority);
    }

    private function createLogMsg($args){
        $logmsg = new ARIADNEplusLogMsg();
        $logmsg->entry_id = $args['entry_id'];
        $logmsg->msg = $args['msg'];
        $logmsg->save();
    }

}

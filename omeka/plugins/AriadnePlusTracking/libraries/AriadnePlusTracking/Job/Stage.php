<?php
/**
 * The ARIADNEplus Tracking plugin.
 * Based on: CuratorMonitor https://github.com/Daniel-KM/Omeka-plugin-CuratorMonitor
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package AriadnePlusTracking\Job
 */
class AriadnePlusTracking_Job_Stage extends Omeka_Job_AbstractJob
{
    /* Queue name */
    const QUEUE_NAME = 'ariadneplus_tracking_stage';

    /* Log Entry to store log messages*/
    private $_logentry;
    /* Status Element (Metadata Status) */
    private $_statusElement;
    
    /**
     * Execute the stage.
     */
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
        $newTerm = $statusElement['terms'][$key+1];
        // Record
        $record_type = $this->_options['record_type'];
        $record_id = $this->_options['record_id'];
        $stageRecord = get_record_by_id($record_type, $record_id);
        // Ticket
        $this->_updateTicketStatusByRecord($stageRecord, 'In Progress');
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
                    'advanced' => array(
                        array(
                        'element_id' => $element->id,
                        'type' => 'is exactly',
                        'terms' => $term,
                         ),
                        array(
                        'joiner' => 'or',
                        'element_id' => $element->id,
                        'type' => 'is exactly',
                        'terms' => 'Proposed'
                        ))
                    ), 0);
        // Operation
        $operation = null;
        $incompleteRecords = [];
        if($key==0){
            $operation = AriadnePlusLogEntry::OPERATION_ASSIGN;
        } elseif ($key > 0 && $key < 6) {
            $operation = AriadnePlusLogEntry::OPERATION_STAGE;
        } elseif ($key == 6) {
            $operation = AriadnePlusLogEntry::OPERATION_REFRESH;
        }  
        if($operation === null){
          return;
        }
        //Logs
        $this->_logentry = new AriadnePlusLogEntry();
        $this->_logentry->logEvent($stageRecord, $operation, current_user());
        $this->_logentry->save();
        // Stages
        $flag = true;
        if($key == 1 || $key == 0){
            list($records,$incompleteRecords) = $this->_checkMetadataElements($records, $view);
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
        $newTerm = $this->_stageSubRecords(array('records' => $records,
            'incompleteRecords' => $incompleteRecords,
            'term' => $newTerm));
        // Stage Record
        $staged = $this->_stageRecord(array('key' => $key, 'stageRecord' => $stageRecord, 
            'flag' => $flag, 'newTerm' => $newTerm));
        
        if(!$staged) $this->_updateTicketStatusByRecord($stageRecord, $term);
        
    }
    
    /**
     * Stage the records.
     * 
     * @param type $args Parameters
     * @return string New term
     */
    protected function _stageSubRecords($args){
        $newTerm = $args['term'];
        $records = $args['records'];
        $incompleteRecords = $args['incompleteRecords'];
        
        $statusElement = $this->_statusElement;
        $element = $statusElement['element'];
        $elementSet = $element->getElementSet();

        $elementTexts = [];
        $elementTexts[$elementSet->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        $count = count($records);
        foreach ($records as $k => $record) {
            if($newTerm == "Complete") $metadata = array('public' => true) + $metadata;
            $record = update_item($record, $metadata, $elementTexts);
            $msg = __('Element #%d ("%s") of record #%d staged to "%s" (%d/%d).',
                $element->id, $element->name, $record->id, $newTerm, $k + 1, $count);
            $this->_log($msg);
            release_object($record);
        }  
        if(!empty($incompleteRecords)){
            if(isset($metadata['public'])) unset($metadata['public']);
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
            }
        }
        return $newTerm;
    }
    
    /**
     * Stage the master record.
     * 
     * @param type $args
     */
    protected function _stageRecord($args){
        $stageRecord = $args['stageRecord'];
        $key = $args['key'];
        $flag = $args['flag'];
        $newTerm = $args['newTerm'];
        
        $statusElement = $this->_statusElement;
        $element = $statusElement['element'];
        
        $elementTexts = [];
        $elementTexts[$element->getElementSet()->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        $record_type = get_class($stageRecord);
        if($record_type == 'Collection' && ($flag || $key == 0)){
            $collectionid = $stageRecord->id;
            $colState = metadata($stageRecord, array('Monitor', 'Metadata Status'));
            if($colState == $statusElement['terms'][$key]){
                release_object($stageRecord);
                if($newTerm == "Complete") $metadata = array('public' => true) + $metadata;
                $stageRecord = update_collection(get_record_by_id('Collection',$collectionid),$metadata,$elementTexts);
                $msg = __('Collection #%d staged to "%s".', $stageRecord->id, $newTerm);
                $this->_log($msg);
                $this->_updateTicketStatusByRecord($stageRecord, $newTerm);
                release_object($stageRecord);
                return true;
            }
        } else if($flag && $record_type == 'Item'){
            $this->_updateTicketStatusByRecord($stageRecord, $newTerm);
            return true;
        }
        return false;
    }
    

    /**
     * Log a message with generic info.
     *
     * @param string $msg The message to log
     * @param int $priority The priority of the message
     */
    protected function _log($msg, $priority = Zend_Log::INFO)
    {
        $prefix = "[AriadnePlusTracking][Stage]";
        $this->createLogMsg($msg);
        _log("$prefix $msg", $priority);
    }

    /**
     * Create an ARIADNEplus log message.
     * 
     * @param type $msg Message
     */
    private function createLogMsg($msg){
        $logmsg = new AriadnePlusLogMsg();
        $logmsg->entry_id = $this->_logentry->id;
        $logmsg->msg = $msg;
        $logmsg->save();
    }

    /**
     * Checks all metadata elements of a given record.
     * 
     * @param type $records Records
     * @param type $view View
     * @return type Array with valid records and incomplete records
     */
    protected function _checkMetadataElements($records, $view){
        $mandatoryElementsDC = $view->tracking()->getMandatoryDCElements();
        $incompleteRecords = [];
        foreach ($records as $k => $record) {
            //CHECK: DC
            foreach($mandatoryElementsDC as $elementDC) {
                if(empty(metadata($record,array('Dublin Core', $elementDC)))){
                    if(isset($records[$k])){
                      unset($records[$k]);
                      $incompleteRecords[] = $record;
                    }
                    $msg = __('Record #%d is not valid. %s is empty.', $record->id, $elementDC);
                    $this->_log($msg);
                }
            }
        }
        return array($records, $incompleteRecords);
    }
    
    /**
     * Check if record is mapped.
     * 
     * @param type $record Record
     * @return boolean Is mapped?
     */
    protected function _isMapped($record){
        $elementM = 'ID of your metadata transformation';
        if(empty(metadata($record,array('Monitor', $elementM)))){
            $msg = __('%s #%d is not mapped,  %s is empty.',get_class($record), $record->id, $elementM);
            $this->_log($msg);
            return false;
        }
        return true;
    }
    
    /**
     * Check if record is enriched.
     * 
     * @param type $record Record
     * @return boolean Is Enriched?
     */
    protected function _isEnriched($record){
        $elementM = 'URL of your PeriodO collection';
        if(empty(metadata($record,array('Monitor', $elementM)))){
            $msg = __('%s #%d is not enriched, %s is empty.',get_class($record), $record->id, $elementM);
            $this->_log($msg);
            return false;
        }
        return true;
    }
    
    private function _updateTicketStatusByRecord($record, $status){
        //ticket
        $ticket = get_view()->tracking()->getTicketByRecordId($record->id);
        $ticket->setStatus($status);
        $ticket->save();
        release_object($ticket);
    }
}
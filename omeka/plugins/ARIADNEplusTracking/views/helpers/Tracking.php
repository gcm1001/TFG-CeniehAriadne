<?php
/**
 * Helpers for ARIADNEplus Tracking.
 *
 * @package ARIADNEplusTracking
 */
class ARIADNEplusTracking_View_Helper_Tracking extends Zend_View_Helper_Abstract
{
    protected $_elementSetName = 'Monitor';
    protected $_elementSet;
    protected $_statusElements;
    // Simple lists of ids as keys to simplify results.
    protected $_uniques;
    protected $_repeatables;
    protected $_steppables;
    protected $_nonSteppables;
    protected $_withTerms;
    protected $_withoutTerms;
    protected $_defaultTerms;
    protected $_table;

    /**
     * Load the hit table one time only.
     */
    public function __construct()
    {
        $this->_logTable = get_db()->getTable('ARIADNEplusLogEntry');
        $this->_ticketTable = get_db()->getTable('ARIADNEplusTrackingTicket');
    }
    /**
     * Get the helper.
     *
     * @return This view helper.
     */
    public function tracking()
    {
        return $this;
    }

    /**
     * Get the element set of the plugin.
     *
     * @return ElementSet
     */
    public function getElementSet()
    {
        if (empty($this->_elementSet)) {
            $this->_getStatusElements();
        }
        return $this->_elementSet;
    }

    /**
     * Get all elements of the element set with status data, by id.
     *
     * @param boolean|null $unique If null all elements are returned else
     * returns all elements unique or repeatable.
     * @param boolean|null $steppable If null all elements are returned else
     * returns all elements processable or not.
     * @param boolean|null $withTerms If null all elements are returned, else
     * returns all elements with or without terms.
     * @param boolean $onlyNames Returns only the name of elements.
     * @return array
     */
    public function getStatusElements($unique = null, $steppable = null, $withTerms = null, $onlyNames = false)
    {
        $elements = $this->_getStatusElements();

        // Unique/repeatable.
        if ($unique == true) {
            $elements = array_intersect_key($elements, $this->_uniques);
        }
        elseif ($unique === false) {
            $elements = array_intersect_key($elements, $this->_repeatables);
        }

        // Steppable or not.
        if ($steppable == true) {
            $elements = array_intersect_key($elements, $this->_steppables);
        }
        elseif ($unique === false) {
            $elements = array_intersect_key($elements, $this->_nonSteppables);
        }

        // With terms.
        if ($withTerms == true) {
            $elements = array_intersect_key($elements, $this->_withTerms);
        }
        elseif ($withTerms === false) {
            $elements = array_intersect_key($elements, $this->_withoutTerms);
        }

        // Only names.
        if ($onlyNames) {
            foreach ($elements as &$element) {
                $element = $element['name'];
            }
        }

        return $elements;
    }

    /**
     * Get one status element and check it for terms and unique.
     *
     * @param integer $elementId
     * @param boolean|null $unique Return only if the element is unique or
     * repeatable.
     * @param boolean|null $steppable Return only if the element is processable
     * or not.
     * @param boolean|null $withTerms Return only if the element has terms or
     * not.
     * @param boolean $onlyNames Returns only the name of elements.
     * @return array
     */
    public function getStatusElement($elementId, $unique = null, $steppable = null, $withTerms = null, $onlyNames = false)
    {
        $elements = $this->getStatusElements($unique, $steppable, $withTerms, $onlyNames);
        if (isset($elements[$elementId])) {
            return $elements[$elementId];
        }
    }

    /**
     * Get all elements names of the element set, by id.
     *
     * @param boolean|null $unique If null all elements are returned else
     * returns all elements unique or repeatable.
     * @param boolean|null $steppable If null all elements are returned else
     * returns all elements processable or not.
     * @param boolean|null $withTerms If null all elements are returned, else
     * returns all elements with or without terms.
     * @return array
     */
    public function getStatusElementNamesById($unique = null, $steppable = null, $withTerms = null)
    {
        return $this->getStatusElements($unique, $steppable, $withTerms, true);
    }

    /**
     * Reset internal cache to simplify creation of new elements.
     *
     * @return void
     */
    public function resetCache()
    {
        $this->_elementSet = null;
        $this->_getStatusElements();
    }

    /**
     * Gets ticket for a given record.
     * 
     * @param type $record Record
     * @return type Ticket
     */
    public function getRecordTrackingTicket($record)
    {
        $tickets = $this->_ticketTable->findBy(array('record_id' => $record->id));
        return reset($tickets);
    }
    /**
     * Helper to get all status elements.
     *
     * @return array
     */
    protected function _getStatusElements()
    {
        if (empty($this->_elementSet)) {
            $this->_db = get_db();

            $elementSet = $this->_db->getTable('ElementSet')->findByName($this->_elementSetName);
            if (empty($elementSet)) {
                throw new Exception(__('The ARIADNEplus Monitor Element Set has been removed or is unavailable.'));
            }
            $this->_elementSet = $elementSet;

            $elements = $elementSet->getElements();

            $this->_statusElements = array();
            $uniques = json_decode(get_option('ariadneplus_tracking_elements_unique'), true) ?: array();
            $repeatables = array();
            $steppables = json_decode(get_option('ariadneplus_tracking_elements_steppable'), true) ?: array();
            $nonSteppables = array();
            $withTerms = array();
            $withoutTerms = array();
            $defaultTerms = json_decode(get_option('ariadneplus_tracking_elements_default'), true) ?: array();
            $tableVocab = $this->_db->getTable('SimpleVocabTerm');
            foreach ($elements as $element) {
                $this->_statusElements[$element->id] = array();
                $this->_statusElements[$element->id]['name'] = $element->name;
                $this->_statusElements[$element->id]['element'] = $element;
                $this->_statusElements[$element->id]['unique'] = !empty($uniques[$element->id]);
                $repeatables[$element->id] = empty($uniques[$element->id]);
                $this->_statusElements[$element->id]['steppable'] = !empty($steppables[$element->id]);
                $nonSteppables[$element->id] = empty($steppables[$element->id]);
                $vocabTerm = $tableVocab->findByElementId($element->id);
                $this->_statusElements[$element->id]['vocab'] = $vocabTerm;
                $this->_statusElements[$element->id]['terms'] = empty($vocabTerm) || empty($vocabTerm->terms)
                    ? array()
                    : explode(PHP_EOL, $this->_statusElements[$element->id]['vocab']->terms);
                $withTerms[$element->id] = !empty($this->_statusElements[$element->id]['terms']);
                $withoutTerms[$element->id] = empty($this->_statusElements[$element->id]['terms']);
                $this->_statusElements[$element->id]['default'] = isset($defaultTerms[$element->id]) ? $defaultTerms[$element->id] : '';
            }
            $this->_uniques = array_filter($uniques);
            $this->_repeatables = array_filter($repeatables);
            $this->_steppables = array_filter($steppables);
            $this->_nonSteppables = array_filter($nonSteppables);
            $this->_withTerms = array_filter($withTerms);
            $this->_withoutTerms = array_filter($withoutTerms);
            $this->_defaultTerms = array_filter($defaultTerms);
        }

        return $this->_statusElements;
    }
    
    /**
     * Shows logs for a given record.
     * 
     * @param type $record Record
     * @param type $limit Record limit
     * @return string Logs
     */
    public function showlogs($record, $limit = 2)
    {
        $params = array();
        if (is_object($record)) {
            $params['record_type'] = get_class($record);
            $params['record_id'] = $record->id;
        }
        // Check array too.
        elseif (is_array($record) && isset($record['record_type']) && $record['record_id']) {
            $params['record_type'] = Inflector::classify($record['record_type']);
            $params['record_id'] = (integer) $record['record_id'];
        }
        // No record.
        else {
            return '';
        }

        // Reverse order because the most needed infos are recent ones.
        $params['sort_field'] = 'added';
        $params['sort_dir'] = 'd';

        $logEntries = $this->_logTable->findBy($params, $limit);
     
        return $this->view->partial('common/show-ariadne-plus-log.php', array(
            'record_type' => $params['record_type'],
            'record_id' => $params['record_id'],
            'limit' => $limit,
            'logEntries' => $logEntries,
        ));
    }
    
    /**
     * Shows all the tickets.
     * 
     * @param type $limit Limit tickets
     * @return type Tickets
     */
    public function showTickets($limit = 5)
    {
        $params = array();
        // Reverse order because the most needed infos are recent ones.
        $params['sort_field'] = 'added';
        $params['sort_dir'] = 'd';

        $tickets = $this->_ticketTable->findBy($params, $limit);
     
        return $this->view->partial('common/show-ariadne-plus-tickets.php', array(
            'limit' => $limit,
            'tickets' => $tickets,
        ));
    }
    
    /**
     * Show phases.
     * 
     * @param type $args Phase and Record
     * @param type $limit Phase limit
     * @return type View
     */
    public function showPhase($args, $limit = 10)
    {
        $markup = ''; 
        $phase = $args['phase'];
        $record = $args['record'];
        if(!$record){
            return;
        }
        $record_type = get_class($record);
        $ticket = $this->getRecordTrackingTicket($record);
          
        if($phase == 0 || $phase == 1){            
            if(!isset($args['results'])){
                return;
            }
            $elements = $this->getStatusElements(true);
            $element = reset($elements);
            $markup = $this->view->partial('forms/phase-one-form.php',array(
                                            'record' => $record,
                                            'elementId' => $element['element']->id,
                                            'total' => $args['results'],
                                            'hide' => $args['hide'],
                                            ));
        } else if($phase == 2 ){
            $markup = $this->view->partial('forms/phase-two-form.php',array(
                                        'record' => $record,
                                        'ticket' => $ticket,
                                        ));
        } else if($phase == 3){
            $markup = $this->view->partial('forms/phase-three-form.php',array(
                                        'record' => $record,
                                        'ticket' => $ticket,
                                        ));
        } else if($phase == 4){
            $markup = $this->view->partial('forms/phase-four-form.php',array(
                                        'record' => $record,
                                        'ticket' => $ticket,
                                        'body' => $this->generateBodyMail(array('mode' => $ticket->mode, 'record_id' => $record->id, 'record_type' => $record_type)),
                                        ));
        } 
        return $markup;
    }
    
    /**
     * Generates body mail for a given record.
     * 
     * @param type $args Record Identifier and Record Type
     * @return string Body Mail
     */
    public function generateBodyMail($args){
        $record_id = $args['record_id'];
        $record_type = $args['record_type'];
        $mode = $args['mode'];
        if(empty($record_type) || empty($mode)){
            return '';
        }
        $record = get_record_by_id($record_type, $record_id);
        $body = '';
        $body .= "<p>";
        $body .= __("%s %s",$record_type, $record_id);
        $body .= __("<br> ARIADNE Category: %s",metadata($record,array('Monitor', 'ARIADNEplus Category')));
        switch($mode){
          case 'OAI-PMH':
            $body .= __("<br> OAI-PMH url: %s", WEB_ROOT.'/oai-pmh-repository/request?verb=ListRecords&metadataPrefix=oai_qdc&set='.$record_id);
            break;
          case 'XML':
            $body .= __("<br> XML url: %s", WEB_ROOT.'/'.strtolower($record_type).'s/show/'.$record_id.'?output=CIRfull');
            break;
          default:
            $body .= __("No record available");
        }
        $body .= __("<br> Mapping Identifier: %s",metadata($record,array('Monitor', 'ID of your metadata transformation')));
        $jsonurl = metadata($record,array('Monitor', 'GettyAAT mapping'));
        if(!empty($jsonurl)){
            $body .= __("<br> JSON File (GettyAAT): %s",$jsonurl);
        }
        $body .= __("<br> PeriodO Collection: %s",metadata($record,array('Monitor', 'URL of your PeriodO collection')));
        $body .= "</p>";
        return $body;
    }
    /**
     * Returns 1 if record is in ticket, 2 if is part of a ticket (Collection)
     * and 0 if is not in ticket.
     * 
     * @param type $record
     * @return int 
     */
    public function recordInTrackingTicket($record){
        $result = $this->_ticketTable->findBy(array('record_id' => $record->id));
        if($result){
            return 1;
        } else if (get_class($record) == 'Item') {
            $is_part_of = get_collection_for_item($record);
            if($is_part_of){
               $result = $this->_ticketTable->findBy(array('record_id' => $is_part_of->id));
               if($result){
                   return 2;
               }
            }
        }
        return 0;
    }
    
    /**
     * Returns the level of the given status
     * 
     * @param type $status Status
     * @return int Level
     */
    public function getLevelStatus($status){
        switch($status){
            case 'Proposed':
                return 0;
            case 'Incomplete':
                return 1;
            case 'Complete':
                return 2;
            case 'Mapped':
                return 3;     
            case 'Enriched':
                return 4;
            case 'Ready to Publish':
                return 5;
            case 'Published':
                return 6;
            default:
                return -1;
        }
    }
    
    /**
     * Returns all names of all mandatory dublin core elements.
     * 
     * @return type Dublin Core Element Names
     */
    public function getMandatoryDCElements(){
        return array('Identifier','Title','Subject','Language','Date Issued',
            'Date Modified','Rights','Publisher','Contributor','Creator', 
            'Spatial Coverage');
    }
    
    /**
     * Returns all names of Status Element Set 
     * 
     * @return type Element names
     */
    public function getAllElementNames(){
        $statusElements = $this->getStatusElements(true);
        $names = [];
        foreach($statusElements as $element){
            $names[] = $element['element']->name;
        }
        return $names;
    }
    
    /**
     * Returns all terms of status element.
     * 
     * @param type $name Element name
     * @return type Terms
     */
    public function getTermsByName($name){
        $statusElements = $this->getStatusElements(true);
        foreach($statusElements as $element){
            if($element['element']->name == $name){
                return $element['terms'];
            }
        }
        return $statusElements;
    }
}

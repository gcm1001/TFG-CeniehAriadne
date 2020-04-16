<?php
/**
 * Stage the selected element of selected or all matching records to next term.
 */
class AriadnePlusMonitor_Job_Stage extends Omeka_Job_AbstractJob
{
    const QUEUE_NAME = 'ariadneplus_monitor_stage';

    public function perform()
    {
        $this->_db = get_db();

        $element = $this->_options['element'];
        $statusElement = get_view()->monitor()
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
        $url = $this->_options['url'];
        // All is fine.
        $newTerm = $statusElement['terms'][$key + 1];
        $elementSet = $element->getElementSet();
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );
        $collectionId = $this->_options['collection'];
        $collection = get_record_by_id('Collection',$collectionId);
        
        $records = get_records('Item', array('collection' => $collectionId,
            'advanced' => array(array(
                'element_id' => $element->id,
                'type' => 'is exactly',
                'terms' => $term,
            )),
        ), 0);
        
        $flag = true;
        // CHECK: Incomplete > Complete 
        if($key == 1 || $key == 0){
            $mandatoryElementsDC = array('Identifier','Title','Subject','Language','Date','Rights','Publisher','Contributor','Creator', 'Spatial Coverage');
            foreach ($records as $key => $record) {
                //CHECK: DC
                foreach($mandatoryElementsDC as $elementDC) {
                    if(empty(metadata($record,array('Dublin Core', $elementDC)))){
                        if ($key != 0) unset($records[$key]);
                        $this->_log(__('Record #%d is not valid. %s is empty.', $record->id, $elementDC));
                        release_object($record);
                        $flag = false;
                        break;
                    }
                }
            }
            if($flag) $newTerm = $statusElement['terms'][2];
        }
        // CHECK: Complete > Mapped  
        if(($key == 2 || $key == 0 ) && $flag){
            $elementM = 'ID of your metadata transformation';
            if(empty(metadata($collection,array('Monitor', $elementM)))){
                if ($key != 0) unset($records[$k]);
                $this->_log(__('Record #%d is not valid, in its collection %s is empty.', $record->id, $elementM));
                release_object($record);
                $flag = false;
            }
            if($flag) $newTerm = $statusElement['terms'][3];
        }
        // CHECK: Mapped > Enriched [OPTIONAL]
        if(($key == 3 || $key == 0 ) && $flag){
            $elementM = 'URL of your PeriodO collection';
            if(empty(metadata($collection,array('Monitor', $elementM)))){
                if ($key != 0) unset($records[$k]);
                $this->_log(__('Record #%d is not valid, in its collection %s is empty.', $record->id, $elementM));
                release_object($record);
                $flag = false;
            }
            if($flag) $newTerm = $statusElement['terms'][4];
        }
        
        // CHECK: Enriched > Ready to Publish
        if(($key == 4|| $key == 0 ) && $flag){
            $siteTitle  = get_option('site_title');
            $from = get_option('administrator_email');
            $email = get_option('ariadneplus_monitor_email');
            $name = get_option('ariadneplus_monitor_name');
            $subject = __("%s - Metadata Ingestion", $siteTitle);
            $body = '';
            $body .= "<p>";
            $body .= __("- COLLECTION %s", $collection->id);
            $body .= __("<br> > XML url: %s", $url.'/collections/show/'.$collection->id.'?output=CIRcol');
            $body .= __("<br> > OAI-PMH url: %s", $url.'/oai-pmh-repository/request?verb=ListRecords&metadataPrefix=oai_qdc&set='.$collection->id);
            $body .= __("<br> > Mapping: %s",metadata($collection,array('Monitor', 'ID of your metadata transformation')));
            $body .= __("<br> > Matchings to GettyAAT: %s","None");
            $body .= __("<br> > PeriodO Collection: %s",metadata($collection,array('Monitor', 'URL of your PeriodO collection')));
            $body .= "</p>";
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($body);
            $mail->setFrom($from, "$siteTitle Administrator");
            $mail->addTo($email, $name);
            $mail->setSubject($subject);
            $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
            $mail->send();
            $newTerm = $statusElement['terms'][5];
   
        }
        
        // TODO: CHECK: Ready to Publish > Published
        if(($key == 5 || $key == 0 ) && $flag){
            if($flag) $newTerm = $statusElement['terms'][6];
        }
        
        // TODO: CHECK: Published > Proposed
        if(($key == 6 || $key == 0 ) && $flag){
            
            if(!$flag) $newTerm = $statusElement['terms'][0];
        }
        // Exec Stage
        $elementTexts[$elementSet->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        
        $count = count($records);
        foreach ($records as $k => $record) {
            $record = update_item($record, $metadata, $elementTexts);
            $this->_log(__('Element #%d ("%s") of record #%d staged to "%s" (%d/%d).',
                $element->id, $element->name, $record->id, $newTerm, $k + 1, $count));
            release_object($record);
        }        
        $this->_log(__('%d records staged to "%s" for element "%s" (#%d).',
            $count, $newTerm, $element->name, $element->id));
        
        if($flag || $key == 0){
            $colState = metadata($collection, array('Monitor', 'Metadata Status'));
            if($colState == $statusElement['terms'][$key] ){
                release_object($collection);
                $collection = update_collection(get_record_by_id('Collection',$collectionId),$metadata,$elementTexts);
                $this->_log(__('Element #%d ("%s") of collection #%d staged to "%s".',
                    $element->id, $element->name, $collection->id, $newTerm));
                release_object($collection);
            } 
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
        $prefix = "[AriadnePlusMonitor][Stage]";
        _log("$prefix $msg", $priority);
    }


}

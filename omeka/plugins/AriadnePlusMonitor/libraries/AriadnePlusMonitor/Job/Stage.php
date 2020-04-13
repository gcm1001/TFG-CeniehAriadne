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
        $elementTexts[$elementSet->name][$element->name][] = array(
            'text' => $newTerm,
            'html' => false,
        );
        $metadata = array(
            Builder_Item::OVERWRITE_ELEMENT_TEXTS => true,
        );

        $records = get_records('Item', array('collection' => $this->_options['collection'],
            'advanced' => array(array(
                'element_id' => $element->id,
                'type' => 'is exactly',
                'terms' => $term,
            )),
        ), 0);
        
        // CHECK: Incomplete > Complete 
        if($key == 0){
            $mandatoryElementsDC = array('Identifier','Title','Subject','Language','Date','Rights','Publisher','Contributor','Creator', 'Spatial Coverage');
            foreach ($records as $key => $record) {
                //CHECK: DC
                foreach($mandatoryElementsDC as $element) {
                    if(empty(metadata($record,array('Dublin Core', $element)))){
                        unset($records[$key]);
                        release_object($record);
                        $this->_log(__('Record #%d is not valid. %s is empty.', $record->id, $element));
                        break;
                    }
                }
            }
        }
        // CHECK: Complete > Mapped  & Mapped > Enriched [OPTIONAL]
        if($key == 1 || $key == 2){
            $elements = ($key==1) ? array('ID of your metadata transformation') : array('URL of your PeriodO collection');
            foreach ($records as $key => $record) {
                $collection = get_collection_for_item($record);
                if($collection) {
                    foreach($elements as $element) {
                        if(empty(metadata($collection,array('Monitor', $element)))){
                            unset($records[$key]);
                            release_object($record);
                            $this->_log(__('Record #%d is not valid, in its collection %s is empty.', $record->id, $element));
                        }
                    }
                } else {
                    unset($records[$key]);
                    $this->_log(__('Record #%d dont have collection associated', $record->id));
                }
            }
        }
        
        // TODO: CHECK: Enriched > Ready to Publish
        if($key == 3){
            $collections = array();
            foreach ($records as $key => $record) {
                $collection = get_collection_for_item($record);
                if(!array_key_exists($collection->id, $collections)){
                    $collections[$collection->id] = $collection;
                }
            }
            $siteTitle  = get_option('site_title');
            $from = get_option('administrator_email');
            $email = get_option('ariadneplus_monitor_email');
            $name = get_option('ariadneplus_monitor_name');
            $subject = __("%s - Metadata Ingestion", $siteTitle);
            $body = '';
            foreach($collections as $id => $collection){
                $body .= "<p>";
                $body .= __("- COLLECTION %s", $id);
                $body .= __("<br> > XML url: %s", $url.'/collections/show/'.$id.'?output=CIRcol');
                $body .= __("<br> > OAI-PMH url: %s", $url.'/oai-pmh-repository/request?verb=ListRecords&metadataPrefix=oai_qdc&set='.$id);
                $body .= __("<br> > Mapping: %s",metadata($collection,array('Monitor', 'ID of your metadata transformation')));
                $body .= __("<br> > Matchings to GettyAAT: %s","None");
                $body .= __("<br> > PeriodO Collection: %s",metadata($collection,array('Monitor', 'URL of your PeriodO collection')));
                $body .= "</p>";
            }
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($body);
            $mail->setFrom($from, "$siteTitle Administrator");
            $mail->addTo($email, $name);
            $mail->setSubject($subject);
            $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
            $mail->send();
   
        }
        
        // TODO: CHECK: Ready to Publish > Published
        if($key == 4){
            
        }
        // Exec Stage
        $count = count($records);
        foreach ($records as $key => $record) {
            $record = update_item($record, $metadata, $elementTexts);
            $this->_log(__('Element #%d ("%s") of record #%d staged to "%s" (%d/%d).',
                $element->id, $element->name, $record->id, $newTerm, $key + 1, $count));
            release_object($record);
        }

        $this->_log(__('%d records staged to "%s" for element "%s" (#%d).',
            $count, $newTerm, $element->name, $element->id));
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

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

        if (empty($this->_options['records'])) {
            $records = get_records('Item', array('collection' => $this->_options['collection'],
                'advanced' => array(array(
                    'element_id' => $element->id,
                    'type' => 'is exactly',
                    'terms' => $term,
                )),
            ), 0);
        }
        // There is a list of records, so check them.
        else {
            $records = array();
            foreach ($this->_options['records'] as $key => $record) {
                if (is_numeric($record)) {
                    $record = get_record_by_id('Item', $record);
                    if (empty($record)) {
                        $this->_log(__('Record #%d does not exist and has been skipped.', $record));
                        continue;
                    }
                }
                // Check the stage.
                $flag = true;
                $ets = $record->getElementTexts($elementSet->name, $element->name);
                foreach ($ets as $et) {
                    if ($et->text === $newTerm) {
                        $this->_log(__('Record #%d is already staged to "%s" and has been skipped.', $record, $newTerm));
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    $records[] = $record;
                }
            }
        }

        // CHECK: Incomplete > Complete 
        if($key == 0){
            $mandatoryElementsDC = array('Identifier','Title','Subject','Language','Date','Rights','Publisher','Contributor','Creator', 'Spatial Coverage');
            foreach ($records as $key => $record) {
                //CHECK: DC
                foreach($mandatoryElementsDC as $element) {
                    if(empty(metadata($record,array('Dublin Core', $element)))){
                        unset($records[$key]);
                        $this->_log(__('Record #%d is not valid. %s is empty.', $record->id, $element));
                        break;
                    }
                }
            }
        }
        // CHECK: Complete > Mapped
        if($key == 1){
            foreach ($records as $key => $record) {
                if(empty(metadata($record,array('Monitor', 'ID of your metadata transformation')))){
                    unset($records[$key]);
                    $this->_log(__('Record #%d is not valid. %s is empty.', $record->id, 'ID of your metadata transformation'));
                }
            }
        }
        // CHECK: Mapped > Enriched [OPTIONAL]
        if($key == 2){
            $enrichElements = array('URL of your Period0 collection', 'JSON file of your matchings to Getty AAT');
            foreach ($records as $key => $record) {
                foreach($enrichElements as $element) {
                    if(empty(metadata($record,array('Monitor', $element)))){
                        $this->_log(__('Record #%d have not been completly enriched. %s is empty.', $record->id, $element));
                    }
                }
            }
        }
        
        // TODO: CHECK: Enriched > Ready to Publish
        if($key == 3){
            /*
            $siteTitle  = get_option('site_title');
            $from = get_option('administrator_email');
            $email = get_option('ariadne_wp4_email');
            $name = get_option('ariadne_wp4_name');
            $subject = __("%s - Metadata Ingestion", $siteTitle);
            $body = "<p>";
            $body .= __("Mapping: %s",$mapping);
            $body .= __("Matchings to GettyAAT: %s",$gettyATT);
            $body .= __("PeriodO Collection: %s",$periodo);
            $body .= "</p>";
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($body);
            $mail->setFrom($from, "$siteTitle Administrator");
            $mail->addTo($email, $name);
            $mail->setSubject($subject);
            $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
            $mail->send();
            */
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

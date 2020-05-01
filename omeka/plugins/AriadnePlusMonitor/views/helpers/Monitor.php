<?php
/**
 * Helpers for AriadnePlusMonitor.
 *
 * @package AriadnePlusMonitor
 */
class AriadnePlusMonitor_View_Helper_Monitor extends Zend_View_Helper_Abstract
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
        $this->_table = get_db()->getTable('AriadnePlusLogEntry');
    }
    /**
     * Get the helper.
     *
     * @return This view helper.
     */
    public function monitor()
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
                throw new Exception(__('The AriadnePlus Monitor Element Set has been removed or is unavailable.'));
            }
            $this->_elementSet = $elementSet;

            $elements = $elementSet->getElements();

            $this->_statusElements = array();
            $uniques = json_decode(get_option('ariadneplus_monitor_elements_unique'), true) ?: array();
            $repeatables = array();
            $steppables = json_decode(get_option('ariadneplus_monitor_elements_steppable'), true) ?: array();
            $nonSteppables = array();
            $withTerms = array();
            $withoutTerms = array();
            $defaultTerms = json_decode(get_option('ariadneplus_monitor_elements_default'), true) ?: array();
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
    
    public function showlogs($record, $limit = 2)
    {
        $markup = '';
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

        $logEntries = $this->_table->findBy($params, $limit);
        if (!empty($logEntries)) {
            $markup = $this->view->partial('common/showariadnepluslog.php', array(
                'record_type' => $params['record_type'],
                'record_id' => $params['record_id'],
                'limit' => $limit,
                'logEntries' => $logEntries,
            ));
        }

        return $markup;
    }
}

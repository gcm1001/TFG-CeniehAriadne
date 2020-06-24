<?php

class Table_AriadnePlusLogEntry extends Omeka_Db_Table
{
    protected $_target = 'AriadnePlusLogEntry';

    public function getFirstEntryForRecord($record, $operation = null)
    {
        $params = array();
        $params['record'] = $record;
        if ($operation) {
            $params['operation'] = $operation;
        }
        $params['sort_field'] = 'added';
        $params['sort_dir'] = 'a';

        $entries = $this->findBy($params, 1);
        if ($entries) {
            return reset($entries);
        }
    }

    public function getLastEntryForRecord($record, $operation = null)
    {
        $params = array();
        $params['record'] = $record;
        if ($operation) {
            $params['operation'] = $operation;
        }
        $params['sort_field'] = 'added';
        $params['sort_dir'] = 'd';

        $entries = $this->findBy($params, 1);
        if ($entries) {
            return reset($entries);
        }
    }

    public function applySearchFilters($select, $params)
    {
        $alias = $this->getTableAlias();
        $genericParams = array();
        foreach ($params as $key => $value) {
            if ($value === null || (is_string($value) && trim($value) === '')) {
                continue;
            }
            switch ($key) {
                case 'collection':
                    $select->where("`$alias`.`record_id` = ?", $value);
                    break;
                case 'item':
                    $select->where("`$alias`.`record_id` = ?", $value);
                    break;
                default:
                    $genericParams[$key] = $value;
            }
        }

        if (!empty($genericParams)) {
            parent::applySearchFilters($select, $genericParams);
        }
    }

}

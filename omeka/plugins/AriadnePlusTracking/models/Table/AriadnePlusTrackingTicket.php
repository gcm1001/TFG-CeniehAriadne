<?php

class Table_AriadnePlusTrackingTicket extends Omeka_Db_Table
{
    protected $_target = 'AriadnePlusTrackingTicket';

    public function getFirstEntryForRecord($record, $status = null)
    {
        $params = array();
        $params['record'] = $record;
        if ($status) {
            $params['status'] = $status;
        }
        $params['sort_field'] = 'lastmod';
        $params['sort_dir'] = 'a';

        $tickets = $this->findBy($params, 1);
        if ($tickets) {
            return reset($tickets);
        }
    }

    public function getLastEntryForRecord($record, $status = null)
    {
        $params = array();
        $params['record'] = $record;
        if ($status) {
            $params['status'] = $status;
        }
        $params['sort_field'] = 'lastmod';
        $params['sort_dir'] = 'd';

        $tickets = $this->findBy($params, 1);
        if ($tickets) {
            return reset($tickets);
        }
    }

    public function applySearchFilters($select, $params)
    {
        $alias = $this->getTableAlias();
        $genericParams = array();
        foreach ($params as $key => $value) {
            if ($value === null || (is_string($value) && trim($value) == '')) {
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

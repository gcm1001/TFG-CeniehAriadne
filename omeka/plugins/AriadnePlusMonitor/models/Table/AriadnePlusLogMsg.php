<?php

class Table_AriadnePlusLogMsg extends Omeka_Db_Table
{
    protected $_target = 'AriadnePlusLogMsg';

    public function getSelect()
    {
        $select = parent::getSelect();
        $db = $this->_db;

        $alias = $this->getTableAlias();
        $aliasEntry = $this->_db->getTable('AriadnePlusLogEntry')->getTableAlias();

        $select->joinInner(
            array($aliasEntry => $db->AriadnePlusLogEntry),
            "`$aliasEntry`.`id` = `$alias`.`entry_id`",
            array());

        $select->group($alias . '.id');
        return $select;
    }

    public function findByEntry($entry, $sort = 'id')
    {
        $alias = $this->getTableAlias();
        $select = $this->getSelect();

        $this->filterByEntry($select, $entry);
        $this->orderMsgsBy($select, $sort);

        return $this->fetchObjects($select);
    }

    public function filterByEntry(Omeka_Db_Select $select, $entry)
    {
        if (empty($entry)) {
            return;
        }
        if (!is_array($entry)) {
            $entry = array($entry);
        }

        $entries = array();
        foreach ($entry as $e) {
            $entries[] = (integer) (is_object($e) ? $e->id : $e);
        }

        $alias = $this->getTableAlias();

        if (count($entries) == 1) {
            $select->where("`$alias`.`entry_id` = ?", reset($entries));
        }
        else {
            $select->where("`$alias`.`entry_id` IN (?)", $entries);
        }
    }
    
    public function orderMsgsBy($select, $sort = 'id', $dir = 'ASC')
    {
        $alias = $this->getTableAlias();
        $dir = ($dir == 'DESC') ? 'DESC' : 'ASC';
        switch($sort) {
            case 'entry':
            case 'entry_id':
                $select->order("$alias.entry_id $dir");
                break;
            case 'element':
            case 'id':
            default:
                $select->order("$alias.id $dir");
                break;
        }
    }
    
}

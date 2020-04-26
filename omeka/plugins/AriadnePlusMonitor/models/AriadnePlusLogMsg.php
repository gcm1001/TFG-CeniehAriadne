<?php

class AriadnePlusLogMsg extends Omeka_Record_AbstractRecord
{
    const TYPE_ASSIGN = 'assign';
    const TYPE_STAGE = 'stage';
    const TYPE_REFRESH = 'refresh';

    public $id;
    public $entry_id;
    public $msg;

    private $_entry;

    public function getRecord()
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->getRecord();
        }
    }

    public function getEntry()
    {
        if (empty($this->_entry)) {
            $this->_entry = $this->getTable('AriadnePlusLogEntry')->find($this->entry_id);
        }
        return $this->_entry;
    }


    public function displayUser()
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->displayUser();
        }
    }

    public function displayOperation()
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->displayOperation();
        }
    }

    public function displayAdded()
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->displayAdded();
        }
    }

    public function displayCurrentTitle()
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->displayCurrentTitle();
        }
    }

    public function isOwnedBy($user)
    {
        $entry = $this->getEntry();
        if ($entry) {
            return $entry->isOwnedBy($user);
        } else {
            return false;
        }
    }

    public function getProperty($property)
    {
        switch($property) {
            case 'record':
                return $this->getRecord();
            case 'entry':
                return $this->getEntry();
            default:
                return parent::getProperty($property);
        }
    }
    
    public function getResourceId()
    {
        return 'AriadnePlusLogMsgs';
    }
}

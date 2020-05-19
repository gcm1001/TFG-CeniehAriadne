<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ARIADNEplusTrackingTicket extends Omeka_Record_AbstractRecord
{
    public $id;
    public $record_type;
    public $record_id;
    public $user_id;
    public $status; 
    public $mode;
    public $lastmod;
    
    private $_validStatus = array(
        'Proposed',
        'Incomplete',
        'Complete',
        'Mapped',
        'Enriched',
        'Ready to publish',
        'Published',
    );
    
    private $_validRecordTypes = array(
        'Item',
        'Collection',
    );
    
    private $_validModes = array(
        'OAI-PMH',
        'XML',
    );
    
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Owner($this, 'user_id');
        $this->_mixins[] = new Mixin_Timestamp($this, 'lastmod', null);
    }
    
    protected function beforeSave($args)
    {
        if ($this->mode === null) {
            $this->mode = 'UNDEFINED';
        }
    }
    
    public function newTrackingTicket($record, $status, $user)
    {
        if (empty($this->record_type) || empty($this->record_id)) {
            $result = $this->_grabRecord($record);
            if (empty($result)) {
                return false;
            }
        }

        $this->setStatus($status);
        if (empty($this->status)) {
            return false;
        }

        $userId = is_object($user) ? $user->id : $user;
        $this->setUserId($userId);
        
        return true;
    }
    
    protected function _grabRecord($record)
    {
        $record = $this->getRecord($record);
        if (empty($record)) {
            return false;
        }

        if (!$this->isValidRecord(get_class($record), $record->id)) {
            return false;
        }

        $this->setRecordType(get_class($record));
        $this->setRecordId($record->id);

        return true;
    }
    
    public function isValidRecord($recordType = null, $recordId = null)
    {
        if ($recordType === null) {
            $recordType = $this->record_type;
        }
        if ($recordId === null) {
            $recordId = $this->record_id;
        }
        $recordId = (integer) $recordId;
        return !empty($recordId)
            && in_array($recordType, $this->_validRecordTypes);
    }
    
    public function setRecordType($type)
    {
        $this->record_type = $type;
    }

    public function setRecordId($id)
    {
        $this->record_id = (integer) $id;
    }
    
    public function setUserId($id)
    {
        $this->user_id = (integer) $id;
    }

    public function setStatus($status)
    {
        if ($this->_isStatusValid($status)) {
            $this->status = $status;
        }
    }
    
    protected function _isStatusValid($status = null)
    {
        if ($status === null) {
            $status = $this->status;
        }
        return in_array($status, $this->_validStatus);
    }
    
    public function setMode($mode)
    {
        if ($this->_isModeValid($mode)) {
            $this->mode = $mode;
        } 
    }
    
    protected function _isModeValid($mode = null)
    {
        if ($mode === null) {
            $mode= $this->mode;
        }
        return in_array($mode, $this->_validModes);
    } 
    
    public function getRecord($record = null)
    {
        if ($record === null) {
            $recordType = $this->record_type;
            $recordId = $this->record_id;
        } elseif (is_object($record)) {
            return $record;
        } elseif (is_array($record)) {
            if (isset($record['record_type']) && isset($record['record_id'])) {
                $recordType = $record['record_type'];
                $recordId = $record['record_id'];
            }
            elseif (isset($record['type']) && isset($record['id'])) {
                $recordType = $record['type'];
                $recordId = $record['id'];
            }
            elseif (count($record) == 1) {
                $recordId = reset($record);
                $recordType = key($record);
            }
            elseif (count($record) == 2) {
                $recordType = array_shift($record);
                $recordId = array_shift($record);
            }
            else {
                return;
            }
        } else {
            return;
        }

        if (class_exists($recordType)) {
            return $this->getTable($recordType)->find($recordId);
        }
    }
    
    public function displayUser()
    {
        $user = $this->getOwner();
        if (empty($user)) {
            return $this->user_id
                ? __('Deleted user [%d]', $this->user_id)
                : __('Anonymous user');
        }
        return $user->name . ' (' . $user->username . ')';
    }
    
    public function displayStatus()
    {
        switch ($this->status) {
            case $this->_validStatus[0]:
                return __('Proposed');
            case $this->_validStatus[1]:
                return __('Incomplete');
            case $this->_validStatus[2]:
                return __('Complete');
            case $this->_validStatus[3]:
                return __('Mapped');
            case $this->_validStatus[4]:
                return __('Enriched');
            case $this->_validStatus[5]:
                return __('Ready to publish');
            case $this->_validStatus[6]:
                return __('Published');
            default:
                return ucfirst($this->status);
        }
    }
    
    public function displayLastMod()
    {
        return $this->added;
    }
    
    public function displayCurrentTitle()
    {
        $record = $this->getRecord();
        
        $etTitles = $record->getElementTexts('Dublin Core', 'Title');
        return isset($etTitles[0]) ? $etTitles[0]->text : '';
    }
    
    public function getResourceId()
    {
        return 'ARIADNEplusTrackingTickets';
    }
}
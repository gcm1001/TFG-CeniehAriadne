<?php
/**
 * An ARIADNEplus Log entry
 *
 * @package AriadnePlusTracking
 */
class AriadnePlusLogEntry extends Omeka_Record_AbstractRecord
{
    const OPERATION_ASSIGN = 'assign';
    const OPERATION_STAGE = 'stage';
    const OPERATION_REFRESH = 'refresh';


    private $_validRecordTypes = array(
        'Item',
        'Collection',
    );

    public $id;
    public $record_type;
    public $record_id;
    public $part_of;
    public $user_id;
    public $operation;
    public $added;
    
    private $_msgs = array();
    

    protected function _initializeMixins()
    {
        // TODO The acl resource interface is useless?
        $this->_mixins[] = new Mixin_Owner($this, 'user_id');
        $this->_mixins[] = new Mixin_Timestamp($this, 'added', null);
    }

    public function prepareNewEvent($record)
    {
        $result = $this->_logRecord($record);

        return !empty($result);
    }

    public function logEvent($record, $operation, $user)
    {
        if (empty($this->record_type) || empty($this->record_id)) {
            $result = $this->_logRecord($record);
            if (empty($result)) {
                return false;
            }
        }

        $this->setOperation($operation);
        if (empty($this->operation)) {
            return false;
        }

        $this->_setPartOf($record);

        $userId = is_object($user) ? $user->id : $user;
        $this->setUserId($userId);

        return true;
    }

    protected function _logRecord($record)
    {
        $record = $this->getRecord($record);
        if (empty($record)) {
            return false;
        }

        if (!$this->isLoggable(get_class($record), $record->id)) {
            return false;
        }

        $this->setRecordType(get_class($record));
        $this->setRecordId($record->id);

        return true;
    }
    
    public function setRecordType($type)
    {
        $this->record_type = $type;
    }

    public function setRecordId($record_id)
    {
        $this->record_id = (integer) $record_id;
    }

    public function setPartOf($partOf)
    {
        $this->part_of = (integer) $partOf;
    }

    protected function _setPartOf($record)
    {
        switch ($this->record_type) {
            case 'Item':
                $this->setPartOf($record->collection_id);
                break;
            case 'Collection':
            default:
                $this->setPartOf(0);
        }
    }

    public function setUserId($user_id)
    {
        $this->user_id = (integer) $user_id;
    }

    public function setOperation($operation)
    {
        if ($this->_isOperationValid($operation)) {
            $this->operation = $operation;
        }
    }

    public function getRecord($record = null)
    {
        if ($record === null) {
            $recordType = $this->record_type;
            $recordId = $this->record_id;
        } elseif (is_object($record)) {
            return $record;
        } 
        if (class_exists($recordType)) {
            return $this->getTable($recordType)->find($recordId);
        }
    }

    public function getPartOfRecord()
    {
        if (empty($this->part_of)) {
            return;
        }

        if ($this->record_type == 'Item') {
            $record = get_record_by_id('Collection', $this->part_of);
            return $record ?: array(
                'record_type' => 'Collection',
                'record_id' => $this->part_of,
            );
        }
    }

    public function isLoggable($recordType = null, $recordId = null)
    {
        if ($recordType === null) {
            $recordType = $this->record_type;
        }
        if ($recordId === null) {
            $recordId = $this->record_id;
        }
        $intrecordid = (integer) $recordId;
        return !empty($intrecordid)
            && in_array($recordType, $this->_validRecordTypes);
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
    
    public function displayPartOf($asUrl = false, $mode = false)
    {
        $partOf = $this->getPartOfRecord();
        if (empty($partOf)) {
            return;
        }

        if ($this->record_type == 'Item') {
            $title = is_array($partOf)
                ? __('Collection %d [deleted]', $this->part_of)
                : __('Collection %d', $this->part_of);
            return $asUrl
                ? sprintf('<a href="%s">%s</a>',
                    url(array(
                            'type' => 'collections',
                            'id' => $this->part_of,
                        ), 'ariadneplus_record_log', array('mode' => $mode)),
                    $title)
                : $title;
        }
    }

    public function displayOperation()
    {
        switch ($this->operation) {
            case AriadnePlusLogEntry::OPERATION_ASSIGN:
                return __('Assign');
            case AriadnePlusLogEntry::OPERATION_STAGE:
                return __('Stage');
            case AriadnePlusLogEntry::OPERATION_REFRESH:
                return __('Refresh');
            default:
                return ucfirst($this->operation);
        }
    }


    public function displayAdded()
    {
        return $this->added;
    }

    public function displayCurrentTitle()
    {
        $record = $this->getRecord();
        
        $etTitles = $record->getElementTexts('Dublin Core', 'Title');
        return isset($etTitles[0]) ? $etTitles[0]->text : '';
    }
    
    public function displayMsgs()
    {
        $msgs = $this->getMsgs();
        $display = array();
        foreach ($msgs as $msg) {
            $display[] = $msg->msg;
        }
        $display = implode("\n", $display);
        return $display;
    }

    protected function _isOperationValid($operation = null)
    {
        if ($operation === null) {
            $operation = $this->operation;
        }
        return in_array($operation, array(
            AriadnePlusLogEntry::OPERATION_ASSIGN,
            AriadnePlusLogEntry::OPERATION_STAGE,
            AriadnePlusLogEntry::OPERATION_REFRESH,
        ));
    }
    
    public function getProperty($property)
    {
        if($property == 'record') {
            return $this->getRecord();
        }
        return parent::getProperty($property);
    }

    public function getMsgs()
    {
        if (empty($this->_msgs)) {
            $this->_msgs = $this->getTable('AriadnePlusLogMsg')
            ->findByEntry($this->id);
        }
        return $this->_msgs;
    }
    
    public function getResourceId()
    {
        return 'AriadnePlusLogEntries';
    }
}

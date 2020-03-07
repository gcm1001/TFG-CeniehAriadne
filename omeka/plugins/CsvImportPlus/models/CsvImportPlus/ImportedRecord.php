<?php
/**
 * CsvImportPlus_ImportedRecord class - represents an imported record for a specific
 * csv import event
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ImportedRecord extends Omeka_Record_AbstractRecord
{
    public $import_id;
    public $record_type;
    public $record_id;
    public $identifier;

    /**
     * Returns the import id for the imported item.
     *
     * @return int The import id.
     */
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * Returns the record type for the imported record.
     *
     * @return string The record type.
     */
    public function getRecordType()
    {
        return $this->record_type;
    }

    /**
     * Returns the record id for the imported record.
     *
     * @return int The record id.
     */
    public function getRecordId()
    {
        return $this->record_id;
    }

    /**
     * Returns the record.
     *
     * @return Record|null The record.
     */
    public function getRecord()
    {
        return get_db()->getTable($this->record_type)->find($this->record_id);
    }

    /**
     * Returns the identifier for the imported record.
     *
     * @return int The identifier.
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}

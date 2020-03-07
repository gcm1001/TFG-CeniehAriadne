<?php
/**
 * CsvImportPlus_ColumnMap_RecordType class
 *
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_RecordType extends CsvImportPlus_ColumnMap
{
    // Because Omeka is built to display items, Item is the default type.
    const DEFAULT_RECORD_TYPE = 'Item';

    private $_recordType;

    /**
     * @param string $columnName
     */
    public function __construct($columnName)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_RECORD_TYPE;
    }

    /**
     * Map a row to the type of a record.
     *
     * @param array $row The row to map
     * @param array $result
     * @return string|boolean Type of the record.
     */
    public function map($row, $result)
    {
        $result = ucfirst(strtolower(trim($row[$this->_columnName])));

        if (!in_array($result, array('', 'Collection', 'Item', 'File', 'Any'))) {
            return false;
        }

        // TODO Check if this is either Any, either can be an instance of Omeka_Record_AbstractRecord.
        return $result;
    }
}

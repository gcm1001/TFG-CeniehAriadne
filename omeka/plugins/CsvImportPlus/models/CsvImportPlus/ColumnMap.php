<?php
/**
 * CsvImportPlus_ColumnMap class
 * Represents a mapping from a column in a csv file to an item element, file, or
 * tag.
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
abstract class CsvImportPlus_ColumnMap
{
    const TYPE_ACTION = 'Action';
    const TYPE_IDENTIFIER = 'Identifier';
    const TYPE_IDENTIFIER_FIELD = 'IdentifierField';
    const TYPE_RECORD_TYPE = 'RecordType';
    const TYPE_ITEM = 'Item';
    const TYPE_ITEM_TYPE = 'ItemType';
    const TYPE_COLLECTION = 'Collection';
    const TYPE_PUBLIC = 'Public';
    const TYPE_FEATURED = 'Featured';
    const TYPE_ELEMENT = 'Element';
    const TYPE_EXTRA_DATA = 'ExtraData';
    const TYPE_TAG = 'Tag';
    const TYPE_FILE = 'File';

    protected $_columnName;
    protected $_type;

    /**
     * @param string $columnName
     */
    public function __construct($columnName)
    {
        $this->_columnName = $columnName;
    }

    /**
     * Returns the type of column map.
     *
     * @return string The type of column map
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Use the column mapping to convert a CSV row into a value that can be
     * parsed by insert_item() or insert_files_for_item().
     *
     * @param array $row The row in the CSV file
     * @param array $result
     * @return array An array value that can be parsed
     * by insert_item() or insert_files_for_item()
     */
    abstract public function map($row, $result);
}

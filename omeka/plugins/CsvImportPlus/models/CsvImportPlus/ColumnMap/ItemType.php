<?php
/**
 * CsvImportPlus_ColumnMap_ItemType class
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package CsvImportPlus
 */
class CsvImportPlus_ColumnMap_ItemType extends CsvImportPlus_ColumnMap
{
    const DEFAULT_ITEM_TYPE = null;

    private $_itemTypeId;

    /**
     * @param string $columnName
     * @param string $itemType
     */
    public function __construct($columnName, $itemTypeId = null)
    {
        parent::__construct($columnName);
        $this->_type = CsvImportPlus_ColumnMap::TYPE_ITEM_TYPE;
        $this->_itemTypeId = empty($itemTypeId)
            ? self::DEFAULT_ITEM_TYPE
            : $itemTypeId;
    }

    /**
     * Return the item type id.
     *
     * @return int Item type id
     */
    public function getItemTypeId()
    {
        return $this->_itemTypeId;
    }

    /**
     * Map a row to an array that can be parsed by insert_item() or
     * insert_files_for_item().
     *
     * @param array $row The row to map
     * @param array $result
     * @return string The result
     */
    public function map($row, $result)
    {
        $result = trim($row[$this->_columnName]);
        return empty($result)
            ? $this->_itemTypeId
            : $result;
    }
}
